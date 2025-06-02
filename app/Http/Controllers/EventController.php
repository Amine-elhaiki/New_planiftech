<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use App\Models\Project;
use App\Models\ParticipantEvent;
use Carbon\Carbon;

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
                $request->input('date_debut'),
                $request->input('date_fin') . ' 23:59:59'
            ]);
        }

        // Vue par défaut : calendrier ou liste
        $view = $request->input('view', 'calendar');

        if ($view === 'list') {
            $events = $query->orderBy('date_debut', 'asc')->paginate(15)->withQueryString();
        } else {
            $events = $query->get();
        }

        // Statistiques pour le dashboard
        $stats = $this->getEventStats();

        // Données pour les filtres
        $users = User::where('statut', 'actif')->get();
        $projects = Project::where('statut', '!=', 'termine')->get();

        return view('events.index', compact('events', 'users', 'projects', 'view', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $users = User::where('statut', 'actif')->get();
        $projects = Project::where('statut', '!=', 'termine')->get();

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
            'date_debut' => 'required|date|after_or_equal:now',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'coordonnees_gps' => 'nullable|string|max:100',
            'priorite' => 'required|in:normale,haute,urgente',
            'id_projet' => 'nullable|exists:projects,id',
            'participants' => 'array',
            'participants.*' => 'exists:users,id'
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'type.required' => 'Le type d\'événement est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' => 'La date de début doit être maintenant ou dans le futur.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'lieu.required' => 'Le lieu est obligatoire.',
            'priorite.required' => 'La priorité est obligatoire.'
        ]);

        // Créer l'événement
        $eventData = $validatedData;
        $eventData['id_organisateur'] = Auth::id();
        $eventData['statut'] = 'planifie';
        unset($eventData['participants']);

        DB::beginTransaction();
        try {
            $event = Event::create($eventData);

            // Ajouter les participants
            if (isset($validatedData['participants'])) {
                foreach ($validatedData['participants'] as $participantId) {
                    ParticipantEvent::create([
                        'id_evenement' => $event->id,
                        'id_utilisateur' => $participantId,
                        'statut_presence' => 'invite'
                    ]);
                }
            }

            // Ajouter l'organisateur comme participant automatiquement
            ParticipantEvent::firstOrCreate([
                'id_evenement' => $event->id,
                'id_utilisateur' => Auth::id()
            ], [
                'statut_presence' => 'confirme'
            ]);

            DB::commit();

            return redirect()->route('events.index')
                            ->with('success', 'Événement créé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'événement.']);
        }
    }

    /**
     * Afficher un événement spécifique
     */
    public function show(Event $event)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' &&
            $event->id_organisateur !== Auth::id() &&
            !$event->participants->contains('id_utilisateur', Auth::id())) {
            abort(403, 'Vous ne pouvez voir que vos propres événements.');
        }

        $event->load(['organisateur', 'participants.utilisateur', 'projet', 'taches']);

        return view('events.show', compact('event'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Event $event)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent modifier l\'événement.');
        }

        $users = User::where('statut', 'actif')->get();
        $projects = Project::where('statut', '!=', 'termine')->get();
        $event->load('participants');

        return view('events.edit', compact('event', 'users', 'projects'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent modifier l\'événement.');
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
            // Mettre à jour l'événement
            $eventData = $validatedData;
            unset($eventData['participants']);
            $event->update($eventData);

            // Mettre à jour les participants
            if (isset($validatedData['participants'])) {
                // Supprimer les anciens participants (sauf l'organisateur)
                ParticipantEvent::where('id_evenement', $event->id)
                                ->where('id_utilisateur', '!=', $event->id_organisateur)
                                ->delete();

                // Ajouter les nouveaux participants
                foreach ($validatedData['participants'] as $participantId) {
                    if ($participantId != $event->id_organisateur) {
                        ParticipantEvent::create([
                            'id_evenement' => $event->id,
                            'id_utilisateur' => $participantId,
                            'statut_presence' => 'invite'
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('events.index')
                            ->with('success', 'Événement mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de l\'événement.']);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent supprimer l\'événement.');
        }

        DB::beginTransaction();
        try {
            // Supprimer les participants
            ParticipantEvent::where('id_evenement', $event->id)->delete();

            // Supprimer l'événement
            $event->delete();

            DB::commit();

            return redirect()->route('events.index')
                            ->with('success', 'Événement supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'événement.']);
        }
    }

    /**
     * Gérer la participation à un événement
     */
    public function updateParticipation(Request $request, Event $event)
    {
        $validatedData = $request->validate([
            'statut_presence' => 'required|in:invite,confirme,decline,present,absent'
        ]);

        $participation = ParticipantEvent::where('id_evenement', $event->id)
                                       ->where('id_utilisateur', Auth::id())
                                       ->first();

        if (!$participation) {
            return response()->json(['error' => 'Vous n\'êtes pas invité à cet événement.'], 403);
        }

        $participation->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Participation mise à jour avec succès.',
            'participation' => $participation
        ]);
    }

    /**
     * Obtenir les événements pour le calendrier
     */
    public function calendar(Request $request)
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
                'intervention' => '#dc3545',    // rouge
                'reunion' => '#007bff',         // bleu
                'formation' => '#28a745',       // vert
                'visite' => '#fd7e14'           // orange
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
     * Marquer un événement comme terminé
     */
    public function markCompleted(Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent marquer l\'événement comme terminé.');
        }

        $event->update(['statut' => 'termine']);

        return back()->with('success', 'Événement marqué comme terminé.');
    }

    /**
     * Annuler un événement
     */
    public function cancel(Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent annuler l\'événement.');
        }

        $event->update(['statut' => 'annule']);

        return back()->with('success', 'Événement annulé.');
    }

    /**
     * Reporter un événement
     */
    public function postpone(Request $request, Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent reporter l\'événement.');
        }

        $validatedData = $request->validate([
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut'
        ]);

        $event->update([
            'date_debut' => $validatedData['date_debut'],
            'date_fin' => $validatedData['date_fin'],
            'statut' => 'reporte'
        ]);

        return back()->with('success', 'Événement reporté avec succès.');
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
            'ce_mois' => (clone $query)->whereMonth('date_debut', now()->month)->count(),
            'planifies' => (clone $query)->where('statut', 'planifie')->count(),
            'en_cours' => (clone $query)->where('statut', 'en_cours')->count(),
            'termines' => (clone $query)->where('statut', 'termine')->count(),
            'urgents' => (clone $query)->where('priorite', 'urgente')->count(),
            'par_type' => (clone $query)->select('type', DB::raw('COUNT(*) as count'))
                                       ->groupBy('type')
                                       ->pluck('count', 'type')
                                       ->toArray()
        ];
    }

    /**
     * Dupliquer un événement
     */
    public function duplicate(Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->id_organisateur !== Auth::id()) {
            abort(403, 'Seuls l\'organisateur et les administrateurs peuvent dupliquer l\'événement.');
        }

        DB::beginTransaction();
        try {
            // Créer une copie de l'événement
            $newEvent = $event->replicate();
            $newEvent->titre = $event->titre . ' (Copie)';
            $newEvent->statut = 'planifie';
            $newEvent->date_debut = $event->date_debut->addWeek();
            $newEvent->date_fin = $event->date_fin->addWeek();
            $newEvent->save();

            // Copier les participants
            foreach ($event->participants as $participant) {
                ParticipantEvent::create([
                    'id_evenement' => $newEvent->id,
                    'id_utilisateur' => $participant->id_utilisateur,
                    'statut_presence' => 'invite'
                ]);
            }

            DB::commit();

            return redirect()->route('events.edit', $newEvent)
                            ->with('success', 'Événement dupliqué avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la duplication de l\'événement.']);
        }
    }

    /**
     * Export des événements
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

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_debut', [
                $request->input('date_debut'),
                $request->input('date_fin') . ' 23:59:59'
            ]);
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
}
