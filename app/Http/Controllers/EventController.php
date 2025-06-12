<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use App\Models\Project;
use App\Models\ParticipantEvent;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des événements
     */
    public function index(Request $request)
    {
        $query = Event::with(['organisateur', 'participants.utilisateur', 'projet']);

        // Filtrage selon le rôle
        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('id_organisateur', Auth::id())
                  ->orWhereHas('participants', function($participantQuery) {
                      $participantQuery->where('id_utilisateur', Auth::id());
                  });
            });
        }

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->input('priorite'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_debut', [
                $request->input('date_debut') . ' 00:00:00',
                $request->input('date_fin') . ' 23:59:59'
            ]);
        }

        // Vue par défaut
        $view = $request->input('view', 'cards');
        $events = $query->orderBy('date_debut', 'asc')->paginate(12)->withQueryString();

        // Statistiques pour le dashboard
        $stats = $this->getEventStats();

        return view('events.index', compact('events', 'view', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $users = User::where('statut', 'actif')->get();
        $projects = Project::all();

        return view('events.create', compact('users', 'projects'));
    }

    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:intervention,reunion,formation,visite',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'coordonnees_gps' => 'nullable|string|max:100',
            'priorite' => 'required|in:normale,haute,urgente',
            'id_projet' => 'nullable|exists:projects,id',
            'participants' => 'array',
            'participants.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            // Créer l'événement
            $eventData = $validatedData;
            $eventData['id_organisateur'] = Auth::id();
            $eventData['statut'] = 'planifie';

            $participants = $eventData['participants'] ?? [];
            unset($eventData['participants']);

            $event = Event::create($eventData);

            // Ajouter l'organisateur comme participant
            ParticipantEvent::create([
                'id_evenement' => $event->id,
                'id_utilisateur' => Auth::id(),
                'statut_presence' => 'confirme'
            ]);

            // Ajouter les autres participants
            foreach ($participants as $participantId) {
                if ($participantId != Auth::id()) {
                    ParticipantEvent::create([
                        'id_evenement' => $event->id,
                        'id_utilisateur' => $participantId,
                        'statut_presence' => 'invite'
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('events.index')->with('success', 'Événement créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'événement.'])->withInput();
        }
    }

    /**
     * Afficher un événement spécifique
     */
    public function show(Event $event)
    {
        // Vérifier les permissions
        if (!$this->canViewEvent($event)) {
            abort(403);
        }

        $event->load(['organisateur', 'participants.utilisateur', 'projet']);

        return view('events.show', compact('event'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Event $event)
    {
        // Vérifier les permissions
        if (!$this->canEditEvent($event)) {
            abort(403);
        }

        $users = User::where('statut', 'actif')->get();
        $projects = Project::all();
        $event->load('participants');

        return view('events.edit', compact('event', 'users', 'projects'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        // Vérifier les permissions
        if (!$this->canEditEvent($event)) {
            abort(403);
        }

        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:intervention,reunion,formation,visite',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'coordonnees_gps' => 'nullable|string|max:100',
            'statut' => 'required|in:planifie,en_cours,termine,annule,reporte',
            'priorite' => 'required|in:normale,haute,urgente',
            'id_projet' => 'nullable|exists:projects,id',
            'participants' => 'array',
            'participants.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            $eventData = $validatedData;
            $participants = $eventData['participants'] ?? [];
            unset($eventData['participants']);

            $event->update($eventData);

            // Mettre à jour les participants
            $this->updateParticipants($event, $participants);

            DB::commit();

            return redirect()->route('events.index')->with('success', 'Événement mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour.'])->withInput();
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        // Vérifier les permissions
        if (!$this->canEditEvent($event)) {
            abort(403);
        }

        try {
            $event->delete();
            return redirect()->route('events.index')->with('success', 'Événement supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression.']);
        }
    }

    /**
     * Obtenir les événements pour le calendrier (AJAX)
     */
    public function calendarData(Request $request)
    {
        $query = Event::with('organisateur');

        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('id_organisateur', Auth::id())
                  ->orWhereHas('participants', function($participantQuery) {
                      $participantQuery->where('id_utilisateur', Auth::id());
                  });
            });
        }

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('date_debut', [
                $request->input('start'),
                $request->input('end')
            ]);
        }

        $events = $query->get()->map(function($event) {
            $color = match($event->type) {
                'intervention' => '#dc3545',
                'reunion' => '#007bff',
                'formation' => '#28a745',
                'visite' => '#fd7e14',
                default => '#6c757d'
            };

            return [
                'id' => $event->id,
                'title' => $event->titre,
                'start' => $event->date_debut->toISOString(),
                'end' => $event->date_fin->toISOString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'description' => $event->description,
                    'type' => $event->type,
                    'lieu' => $event->lieu,
                    'statut' => $event->statut,
                    'priorite' => $event->priorite,
                    'organisateur' => $event->organisateur->prenom . ' ' . $event->organisateur->nom
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Export des événements en CSV
     */
    public function export(Request $request)
    {
        $query = Event::with(['organisateur', 'projet']);

        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('id_organisateur', Auth::id())
                  ->orWhereHas('participants', function($participantQuery) {
                      $participantQuery->where('id_utilisateur', Auth::id());
                  });
            });
        }

        // Appliquer les filtres si présents
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $events = $query->orderBy('date_debut', 'asc')->get();

        $csv = "Titre,Type,Date début,Date fin,Lieu,Statut,Priorité,Organisateur,Projet\n";

        foreach ($events as $event) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                '"' . str_replace('"', '""', $event->titre) . '"',
                $event->type_nom,
                $event->date_debut->format('d/m/Y H:i'),
                $event->date_fin->format('d/m/Y H:i'),
                '"' . str_replace('"', '""', $event->lieu) . '"',
                $event->statut_nom,
                $event->priorite_nom,
                $event->organisateur->prenom . ' ' . $event->organisateur->nom,
                $event->projet ? $event->projet->nom : ''
            );
        }

        $filename = 'evenements_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ==================== MÉTHODES PRIVÉES ====================

    /**
     * Mettre à jour les participants d'un événement
     */
    private function updateParticipants(Event $event, array $participants)
    {
        // Supprimer les anciens participants (sauf l'organisateur)
        ParticipantEvent::where('id_evenement', $event->id)
                       ->where('id_utilisateur', '!=', $event->id_organisateur)
                       ->delete();

        // Ajouter les nouveaux participants
        foreach ($participants as $participantId) {
            if ($participantId != $event->id_organisateur) {
                ParticipantEvent::create([
                    'id_evenement' => $event->id,
                    'id_utilisateur' => $participantId,
                    'statut_presence' => 'invite'
                ]);
            }
        }
    }

    /**
     * Vérifier si l'utilisateur peut voir l'événement
     */
    private function canViewEvent(Event $event)
    {
        return Auth::user()->role === 'admin' ||
               $event->id_organisateur === Auth::id() ||
               $event->participants->contains('id_utilisateur', Auth::id());
    }

    /**
     * Vérifier si l'utilisateur peut modifier l'événement
     */
    private function canEditEvent(Event $event)
    {
        return Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id();
    }

    /**
     * Obtenir les statistiques des événements
     */
    private function getEventStats()
    {
        $query = Event::query();

        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('id_organisateur', Auth::id())
                  ->orWhereHas('participants', function($participantQuery) {
                      $participantQuery->where('id_utilisateur', Auth::id());
                  });
            });
        }

        return [
            'total' => $query->count(),
            'aujourd_hui' => (clone $query)->whereDate('date_debut', today())->count(),
            'cette_semaine' => (clone $query)->whereBetween('date_debut', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'ce_mois' => (clone $query)->whereMonth('date_debut', now()->month)
                                      ->whereYear('date_debut', now()->year)->count(),
            'urgents' => (clone $query)->where('priorite', 'urgente')->count(),
        ];
    }
}
