<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Project;
use App\Models\ParticipantEvent;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements
     */
    public function index(Request $request)
    {
        $query = Event::with(['organisateur', 'projet', 'participants.utilisateur']);

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        // Filtre par priorité
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->input('priorite'));
        }

        // Filtre par organisateur
        if ($request->filled('organisateur')) {
            $query->where('id_organisateur', $request->input('organisateur'));
        }

        // Filtre par projet
        if ($request->filled('projet')) {
            $query->where('id_projet', $request->input('projet'));
        }

        // Filtre par utilisateur (événements où il participe)
        if ($request->filled('utilisateur')) {
            $userId = $request->input('utilisateur');
            $query->where(function($q) use ($userId) {
                $q->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($participantQuery) use ($userId) {
                      $participantQuery->where('id_utilisateur', $userId);
                  });
            });
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->input('date_debut') . ' 00:00:00');
        }

        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->input('date_fin') . ' 23:59:59');
        }

        // Vue par défaut
        $view = $request->input('view', 'cards');
        $events = $query->orderBy('date_debut', 'asc')->paginate(12)->withQueryString();

        // Statistiques pour le dashboard
        $stats = $this->getEventStats();

        return view('events.index', compact('events', 'view', 'stats'));
    }

    /**
     * Données pour le calendrier (AJAX)
     */
    public function calendarData(Request $request)
    {
        $events = Event::with(['organisateur'])
            ->whereBetween('date_debut', [
                $request->input('start'),
                $request->input('end')
            ])
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->titre,
                    'start' => $event->date_debut->toISOString(),
                    'end' => $event->date_fin->toISOString(),
                    'backgroundColor' => $this->getEventColor($event),
                    'borderColor' => $this->getEventColor($event),
                    'textColor' => '#fff',
                    'url' => route('events.show', $event)
                ];
            });

        return response()->json($events);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $users = User::where('statut', 'actif')->orderBy('nom')->get();
        $projects = Project::whereIn('statut', ['planifie', 'en_cours'])->orderBy('nom')->get();

        return view('events.create', compact('users', 'projects'));
    }

    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'type' => 'required|in:intervention,reunion,formation,visite',
                'date_debut' => 'required|date|after:now',
                'date_fin' => 'required|date|after:date_debut',
                'lieu' => 'required|string|max:255',
                'coordonnees_gps' => 'nullable|string|max:100',
                'priorite' => 'required|in:normale,haute,urgente',
                'id_projet' => 'nullable|exists:projects,id',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:users,id'
            ], [
                'titre.required' => 'Le titre est obligatoire.',
                'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
                'description.required' => 'La description est obligatoire.',
                'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
                'type.required' => 'Le type d\'événement est obligatoire.',
                'type.in' => 'Le type d\'événement sélectionné est invalide.',
                'date_debut.required' => 'La date de début est obligatoire.',
                'date_debut.after' => 'La date de début doit être dans le futur.',
                'date_fin.required' => 'La date de fin est obligatoire.',
                'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
                'lieu.required' => 'Le lieu est obligatoire.',
                'lieu.max' => 'Le lieu ne peut pas dépasser 255 caractères.',
                'priorite.required' => 'La priorité est obligatoire.',
                'priorite.in' => 'La priorité sélectionnée est invalide.',
                'id_projet.exists' => 'Le projet sélectionné n\'existe pas.',
                'participants.array' => 'La liste des participants est invalide.',
                'participants.*.exists' => 'Un des participants sélectionnés n\'existe pas.'
            ]);

            DB::beginTransaction();

            // Créer l'événement
            $eventData = $validatedData;
            $eventData['id_organisateur'] = Auth::id();
            $eventData['statut'] = 'planifie';

            $participants = $eventData['participants'] ?? [];
            unset($eventData['participants']);

            $event = Event::create($eventData);

            // Ajouter l'organisateur comme participant avec rôle organisateur
            $this->ajouterParticipant($event->id, Auth::id(), 'confirme', 'organisateur');

            // Ajouter les autres participants
            foreach ($participants as $participantId) {
                if ($participantId != Auth::id()) {
                    $this->ajouterParticipant($event->id, $participantId, 'invite', 'participant');
                }
            }

            // Créer des notifications pour les participants
            $this->creerNotificationsParticipants($event, $participants);

            DB::commit();

            return redirect()->route('events.index')
                           ->with('success', 'Événement créé avec succès.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création de l\'événement: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'événement: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Afficher un événement spécifique
     */
    public function show(Event $event)
    {
        // Vérifier les permissions
        if (!$this->canViewEvent($event)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de voir cet événement.');
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
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cet événement.');
        }

        $users = User::where('statut', 'actif')->orderBy('nom')->get();
        $projects = Project::whereIn('statut', ['planifie', 'en_cours'])->orderBy('nom')->get();
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
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cet événement.');
        }

        try {
            $validatedData = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'type' => 'required|in:intervention,reunion,formation,visite',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after:date_debut',
                'lieu' => 'required|string|max:255',
                'coordonnees_gps' => 'nullable|string|max:100',
                'statut' => 'required|in:planifie,en_cours,termine,annule,reporte',
                'priorite' => 'required|in:normale,haute,urgente',
                'id_projet' => 'nullable|exists:projects,id',
                'participants' => 'nullable|array',
                'participants.*' => 'exists:users,id'
            ]);

            DB::beginTransaction();

            $eventData = $validatedData;
            $participants = $eventData['participants'] ?? [];
            unset($eventData['participants']);

            $event->update($eventData);

            // Mettre à jour les participants
            $this->updateParticipants($event, $participants);

            DB::commit();

            return redirect()->route('events.index')
                           ->with('success', 'Événement mis à jour avec succès.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à jour de l\'événement: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        // Vérifier les permissions
        if (!$this->canEditEvent($event)) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer cet événement.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les participants (soft delete)
            $event->participants()->delete();

            // Supprimer l'événement
            $event->delete();

            DB::commit();

            return redirect()->route('events.index')
                           ->with('success', 'Événement supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la suppression de l\'événement: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    /**
     * Confirmer la participation à un événement
     */
    public function confirmerParticipation(Request $request, Event $event)
    {
        $userId = Auth::id();
        $participant = $event->participants()->where('id_utilisateur', $userId)->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Vous n\'êtes pas invité à cet événement.']);
        }

        $participant->confirmerPresence($request->input('commentaire'));

        return back()->with('success', 'Votre participation a été confirmée.');
    }

    /**
     * Décliner la participation à un événement
     */
    public function declinerParticipation(Request $request, Event $event)
    {
        $userId = Auth::id();
        $participant = $event->participants()->where('id_utilisateur', $userId)->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Vous n\'êtes pas invité à cet événement.']);
        }

        $participant->declinerInvitation($request->input('commentaire'));

        return back()->with('success', 'Votre participation a été déclinée.');
    }

    // MÉTHODES UTILITAIRES PRIVÉES

    /**
     * Vérifier si l'utilisateur peut voir l'événement
     */
    private function canViewEvent(Event $event)
    {
        $user = Auth::user();

        // Admin peut tout voir
        if ($user->role === 'admin') {
            return true;
        }

        // Organisateur peut voir son événement
        if ($event->id_organisateur === $user->id) {
            return true;
        }

        // Participant peut voir l'événement
        return $event->participants()->where('id_utilisateur', $user->id)->exists();
    }

    /**
     * Vérifier si l'utilisateur peut modifier l'événement
     */
    private function canEditEvent(Event $event)
    {
        $user = Auth::user();

        // Admin peut tout modifier
        if ($user->role === 'admin') {
            return true;
        }

        // Organisateur peut modifier son événement si pas terminé/annulé
        if ($event->id_organisateur === $user->id && $event->peutEtreModifie()) {
            return true;
        }

        return false;
    }

    /**
     * Ajouter un participant à un événement
     */
    private function ajouterParticipant($eventId, $userId, $statut = 'invite', $role = 'participant')
    {
        return ParticipantEvent::create([
            'id_evenement' => $eventId,
            'id_utilisateur' => $userId,
            'statut_presence' => $statut,
            'role_evenement' => $role,
            'date_invitation' => now(),
            'notification_envoyee' => false,
            'rappel_envoye' => false
        ]);
    }

    /**
     * Mettre à jour la liste des participants
     */
    private function updateParticipants(Event $event, array $participantIds)
    {
        // Récupérer les participants actuels (sauf l'organisateur)
        $currentParticipants = $event->participants()
            ->where('role_evenement', '!=', 'organisateur')
            ->pluck('id_utilisateur')
            ->toArray();

        // Participants à ajouter
        $toAdd = array_diff($participantIds, $currentParticipants);
        foreach ($toAdd as $userId) {
            $this->ajouterParticipant($event->id, $userId);
        }

        // Participants à supprimer
        $toRemove = array_diff($currentParticipants, $participantIds);
        if (!empty($toRemove)) {
            $event->participants()
                ->whereIn('id_utilisateur', $toRemove)
                ->where('role_evenement', '!=', 'organisateur')
                ->delete();
        }
    }

    /**
     * Créer des notifications pour les participants
     */
    private function creerNotificationsParticipants(Event $event, array $participantIds)
    {
        foreach ($participantIds as $participantId) {
            if ($participantId != Auth::id()) {
                Notification::create([
                    'titre' => 'Invitation à un événement',
                    'message' => "Vous êtes invité à l'événement '{$event->titre}' prévu le " .
                               $event->date_debut->format('d/m/Y à H:i'),
                    'type' => 'evenement',
                    'destinataire_id' => $participantId,
                    'lue' => false
                ]);
            }
        }
    }

    /**
     * Obtenir les statistiques des événements
     */
    private function getEventStats()
    {
        $userId = Auth::id();
        $role = Auth::user()->role;

        $query = Event::query();

        // Si pas admin, filtrer par utilisateur
        if ($role !== 'admin') {
            $query->where(function($q) use ($userId) {
                $q->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($participantQuery) use ($userId) {
                      $participantQuery->where('id_utilisateur', $userId);
                  });
            });
        }

        return [
            'total' => $query->count(),
            'planifies' => $query->where('statut', 'planifie')->count(),
            'en_cours' => $query->where('statut', 'en_cours')->count(),
            'termines' => $query->where('statut', 'termine')->count(),
            'a_venir' => $query->where('date_debut', '>', now())->count(),
            'ce_mois' => $query->whereMonth('date_debut', now()->month)
                              ->whereYear('date_debut', now()->year)->count()
        ];
    }

    /**
     * Obtenir la couleur d'un événement pour le calendrier
     */
    private function getEventColor(Event $event)
    {
        return match($event->type) {
            'intervention' => '#dc3545', // Rouge
            'reunion' => '#0d6efd',      // Bleu
            'formation' => '#198754',    // Vert
            'visite' => '#fd7e14',       // Orange
            default => '#6c757d'         // Gris
        };
    }
}
