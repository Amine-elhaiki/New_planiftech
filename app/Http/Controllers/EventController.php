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
    public function __construct()
    {
        $this->middleware('auth');
    }

    

    /**
     * Afficher la liste des événements
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'cards');
        $user = Auth::user();
        
        $query = Event::with(['organisateur', 'projet', 'participants'])
                     ->orderBy('date_debut', 'asc');

        // ✅ FILTRAGE SELON LE RÔLE
        if ($user->role !== 'admin') {
            // Pour les non-admin : voir seulement leurs événements
            $query->where(function($q) use ($user) {
                $q->where('id_organisateur', $user->id)
                  ->orWhereHas('participants', function($subQ) use ($user) {
                      $subQ->where('id_utilisateur', $user->id);
                  });
            });
        }

        // Appliquer les filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        // Filtrer par statut de participation pour les non-admin
        if ($user->role !== 'admin' && $request->filled('participation')) {
            $participation = $request->participation;
            $query->whereHas('participants', function($q) use ($user, $participation) {
                $q->where('id_utilisateur', $user->id)
                  ->where('statut_presence', $participation);
            });
        }

        // Pagination
        $events = $query->paginate(12)->withQueryString();

        // ✅ STATISTIQUES PERSONNALISÉES PAR RÔLE
        if ($user->role === 'admin') {
            $stats = [
                'total' => Event::count(),
                'planifies' => Event::where('statut', 'planifie')->count(),
                'en_cours' => Event::where('statut', 'en_cours')->count(),
                'termines' => Event::where('statut', 'termine')->count(),
                'cette_semaine' => Event::whereBetween('date_debut', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'ce_mois' => Event::whereBetween('date_debut', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->count(),
            ];
        } else {
            // Statistiques pour l'utilisateur connecté uniquement
            $userEventQuery = Event::where(function($q) use ($user) {
                $q->where('id_organisateur', $user->id)
                  ->orWhereHas('participants', function($subQ) use ($user) {
                      $subQ->where('id_utilisateur', $user->id);
                  });
            });

            $stats = [
                'total' => $userEventQuery->count(),
                'organises' => Event::where('id_organisateur', $user->id)->count(),
                'confirmes' => ParticipantEvent::where('id_utilisateur', $user->id)
                                               ->where('statut_presence', 'confirme')
                                               ->count(),
                'a_venir' => $userEventQuery->where('date_debut', '>', now())->count(),
            ];
        }

        $typesOptions = Event::getTypesDisponibles();
        $statutsOptions = Event::getStatutsDisponibles();
        $prioritesOptions = Event::getPrioritesDisponibles();

        // ✅ PASSER LES INFORMATIONS DE RÔLE À LA VUE
        $roleInfo = [
            'role' => $user->role,
            'isAdmin' => $user->role === 'admin',
            'roleClass' => $user->role === 'admin' ? 'admin' : 'technicien',
        ];

        return view('events.index', compact(
            'events', 
            'stats', 
            'view', 
            'typesOptions', 
            'statutsOptions', 
            'prioritesOptions',
            'roleInfo'
        ));
    }

    /**
     * Afficher un événement spécifique
     */
    public function show(Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && 
            $event->id_organisateur !== $user->id &&
            !$event->participants()->where('id_utilisateur', $user->id)->exists()) {
            abort(403, 'Vous n\'avez pas l\'autorisation de voir cet événement.');
        }

        $event->load(['organisateur', 'participants.utilisateur', 'projet']);

        return view('events.show', compact('event'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $utilisateurs = User::where('statut', 'actif')->orderBy('nom')->get();
        $projets = Project::whereIn('statut', ['planifie', 'en_cours'])->orderBy('nom')->get();
        
        $typesOptions = Event::getTypesDisponibles();
        $prioritesOptions = Event::getPrioritesDisponibles();

        return view('events.create', compact('utilisateurs', 'projets', 'typesOptions', 'prioritesOptions'));
    }

    /**
     * Enregistrer un nouvel événement
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:' . implode(',', array_keys(Event::getTypesDisponibles())),
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'coordonnees_gps' => 'nullable|string|max:100',
            'priorite' => 'required|in:' . implode(',', array_keys(Event::getPrioritesDisponibles())),
            'id_projet' => 'nullable|exists:projects,id',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $validatedData['id_organisateur'] = Auth::id();
            $validatedData['statut'] = 'planifie';

            $event = Event::create($validatedData);

            // Ajouter l'organisateur comme participant confirmé
            $event->ajouterParticipant(Auth::id(), 'confirme', 'organisateur');

            // Ajouter les autres participants
            if ($request->filled('participants')) {
                foreach ($request->participants as $userId) {
                    if ($userId != Auth::id()) {
                        $event->ajouterParticipant($userId, 'invite', 'participant');
                        $this->creerNotificationParticipant($event, $userId);
                    }
                }
            }

            DB::commit();

            return redirect()->route('events.index')
                            ->with('success', 'Événement créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'événement: ' . $e->getMessage());
            
            return back()->withInput()
                        ->withErrors(['error' => 'Erreur lors de la création de l\'événement.']);
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $event->id_organisateur !== $user->id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cet événement.');
        }

        if (!$event->peutEtreModifie()) {
            return redirect()->route('events.show', $event)
                           ->with('error', 'Cet événement ne peut plus être modifié.');
        }

        $utilisateurs = User::where('statut', 'actif')->orderBy('nom')->get();
        $projets = Project::whereIn('statut', ['planifie', 'en_cours'])->orderBy('nom')->get();
        
        $typesOptions = Event::getTypesDisponibles();
        $statutsOptions = Event::getStatutsDisponibles();
        $prioritesOptions = Event::getPrioritesDisponibles();

        $event->load('participants');

        return view('events.edit', compact(
            'event', 
            'utilisateurs', 
            'projets', 
            'typesOptions', 
            'statutsOptions', 
            'prioritesOptions'
        ));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $event->id_organisateur !== $user->id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cet événement.');
        }

        if (!$event->peutEtreModifie()) {
            return redirect()->route('events.show', $event)
                           ->with('error', 'Cet événement ne peut plus être modifié.');
        }

        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:' . implode(',', array_keys(Event::getTypesDisponibles())),
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'coordonnees_gps' => 'nullable|string|max:100',
            'statut' => 'required|in:' . implode(',', array_keys(Event::getStatutsDisponibles())),
            'priorite' => 'required|in:' . implode(',', array_keys(Event::getPrioritesDisponibles())),
            'id_projet' => 'nullable|exists:projects,id',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $event->update($validatedData);

            // Mettre à jour les participants si fournis
            if ($request->has('participants')) {
                $this->updateParticipants($event, $request->participants ?? []);
            }

            DB::commit();

            return redirect()->route('events.show', $event)
                            ->with('success', 'Événement mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'événement: ' . $e->getMessage());
            
            return back()->withInput()
                        ->withErrors(['error' => 'Erreur lors de la mise à jour de l\'événement.']);
        }
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $event->id_organisateur !== $user->id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer cet événement.');
        }

        if (!$event->peutEtreSupprime()) {
            return redirect()->route('events.show', $event)
                           ->with('error', 'Cet événement ne peut pas être supprimé.');
        }

        DB::beginTransaction();
        try {
            $eventTitle = $event->titre;

            // Supprimer les participations associées
            $event->participants()->delete();

            // Supprimer l'événement
            $event->delete();

            DB::commit();

            return redirect()->route('events.index')
                            ->with('success', 'Événement supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'événement: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'événement.']);
        }
    }

    /**
     * Confirmer la participation à un événement
     */
    public function confirmerParticipation(Request $request, Event $event)
    {
        $userId = auth()->id();
        $participant = $event->participants()->where('id_utilisateur', $userId)->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Vous n\'êtes pas invité à cet événement.']);
        }

        if ($participant->statut_presence !== 'invite') {
            return back()->withErrors(['error' => 'Vous avez déjà répondu à cette invitation.']);
        }

        DB::beginTransaction();
        try {
            $participant->update([
                'statut_presence' => 'confirme',
                'date_reponse' => now(),
                'commentaire' => $request->input('commentaire')
            ]);

            // Notifier l'organisateur
            if ($event->organisateur && $event->organisateur->id !== $userId) {
                $this->creerNotificationOrganisateur($event, $userId, 'confirme');
            }

            DB::commit();

            return back()->with('success', 'Votre participation a été confirmée.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la confirmation.']);
        }
    }

    /**
     * Décliner la participation à un événement
     */
    public function declinerParticipation(Request $request, Event $event)
    {
        $userId = auth()->id();
        $participant = $event->participants()->where('id_utilisateur', $userId)->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Vous n\'êtes pas invité à cet événement.']);
        }

        if ($participant->statut_presence !== 'invite') {
            return back()->withErrors(['error' => 'Vous avez déjà répondu à cette invitation.']);
        }

        DB::beginTransaction();
        try {
            $participant->update([
                'statut_presence' => 'decline',
                'date_reponse' => now(),
                'commentaire' => $request->input('commentaire')
            ]);

            // Notifier l'organisateur
            if ($event->organisateur && $event->organisateur->id !== $userId) {
                $this->creerNotificationOrganisateur($event, $userId, 'decline');
            }

            DB::commit();

            return back()->with('success', 'Votre réponse a été enregistrée.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement de votre réponse.']);
        }
    }

    /**
     * Marquer la présence d'un participant
     */
    public function marquerPresence(Request $request, Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $event->id_organisateur !== $user->id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de gérer cet événement.');
        }

        $validatedData = $request->validate([
            'participant_id' => 'required|exists:users,id',
            'statut' => 'required|in:present,absent,excuse'
        ]);

        $participant = $event->participants()
                            ->where('id_utilisateur', $validatedData['participant_id'])
                            ->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Participant non trouvé.']);
        }

        $participant->update([
            'statut_presence' => $validatedData['statut']
        ]);

        return back()->with('success', 'Présence mise à jour avec succès.');
    }

    /**
     * Dupliquer un événement
     */
    public function duplicate(Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && 
            $event->id_organisateur !== $user->id &&
            !$event->participants()->where('id_utilisateur', $user->id)->exists()) {
            abort(403, 'Vous n\'avez pas l\'autorisation de dupliquer cet événement.');
        }

        DB::beginTransaction();
        try {
            $newEvent = $event->replicate();
            $newEvent->titre = $event->titre . ' (Copie)';
            $newEvent->date_debut = $event->date_debut->addWeek();
            $newEvent->date_fin = $event->date_fin->addWeek();
            $newEvent->statut = 'planifie';
            $newEvent->id_organisateur = Auth::id();
            $newEvent->save();

            // Dupliquer les participants (sauf l'ancien organisateur)
            foreach ($event->participants as $participant) {
                if ($participant->role_evenement !== 'organisateur') {
                    $newEvent->ajouterParticipant($participant->id_utilisateur, 'invite', $participant->role_evenement);
                }
            }

            // Ajouter le nouvel organisateur
            $newEvent->ajouterParticipant(Auth::id(), 'confirme', 'organisateur');

            DB::commit();

            return redirect()->route('events.edit', $newEvent)
                            ->with('success', 'Événement dupliqué avec succès. Vous pouvez maintenant le modifier.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la duplication de l\'événement: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Erreur lors de la duplication de l\'événement.']);
        }
    }

    /**
     * Mettre à jour le statut d'un événement
     */
    public function updateStatus(Request $request, Event $event)
    {
        // ✅ Vérification simple sans policy
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $event->id_organisateur !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Permission refusée'], 403);
        }

        $validatedData = $request->validate([
            'statut' => 'required|in:' . implode(',', array_keys(Event::getStatutsDisponibles()))
        ]);

        try {
            $event->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'event' => $event
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    /**
     * Données pour le calendrier (AJAX)
     */
    public function calendarData(Request $request)
    {
        $user = Auth::user();
        $query = Event::with('organisateur');

        // Filtrer selon le rôle
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('id_organisateur', $user->id)
                  ->orWhereHas('participants', function($participantQuery) use ($user) {
                      $participantQuery->where('id_utilisateur', $user->id);
                  });
            });
        }

        $events = $query->get()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->titre,
                'start' => $event->date_debut->toISOString(),
                'end' => $event->date_fin->toISOString(),
                'backgroundColor' => $this->getEventColor($event),
                'borderColor' => $this->getEventColor($event),
                'url' => route('events.show', $event->id),
                'extendedProps' => [
                    'type' => $event->type,
                    'priorite' => $event->priorite,
                    'statut' => $event->statut,
                    'lieu' => $event->lieu,
                    'organisateur' => $event->organisateur->nom_complet ?? 'Inconnu'
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Exporter les événements en CSV (Admin seulement)
     */
    public function export(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        $query = Event::with(['organisateur', 'projet']);

        // Appliquer les filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        $events = $query->orderBy('date_debut')->get();

        $csvData = [];
        $csvData[] = [
            'Titre',
            'Type',
            'Date début',
            'Date fin',
            'Lieu',
            'Statut',
            'Priorité',
            'Organisateur',
            'Projet',
            'Participants'
        ];

        foreach ($events as $event) {
            $csvData[] = [
                $event->titre,
                $event->type_libelle,
                $event->date_debut->format('d/m/Y H:i'),
                $event->date_fin->format('d/m/Y H:i'),
                $event->lieu,
                $event->statut_libelle,
                $event->priorite_libelle,
                $event->organisateur->nom_complet ?? 'Inconnu',
                $event->projet->nom ?? 'Aucun',
                $event->nombre_total_participants
            ];
        }

        $csv = '';
        foreach ($csvData as $row) {
            $csv .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        $csv = "\xEF\xBB\xBF" . $csv;
        $filename = 'evenements_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // MÉTHODES UTILITAIRES PRIVÉES

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
            $event->ajouterParticipant($userId, 'invite', 'participant');
            $this->creerNotificationParticipant($event, $userId);
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
     * Créer une notification pour un participant
     */
    private function creerNotificationParticipant(Event $event, $userId)
    {
        if (class_exists('App\Models\Notification')) {
            try {
                Notification::create([
                    'titre' => 'Invitation à un événement',
                    'message' => "Vous êtes invité à l'événement '{$event->titre}' prévu le " .
                               $event->date_debut->format('d/m/Y à H:i') . " à {$event->lieu}.",
                    'type' => 'evenement',
                    'destinataire_id' => $userId,
                    'lue' => false
                ]);
            } catch (\Exception $e) {
                Log::warning('Impossible de créer la notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Créer une notification pour l'organisateur
     */
    private function creerNotificationOrganisateur(Event $event, $userId, $action)
    {
        if (class_exists('App\Models\Notification')) {
            try {
                $user = User::find($userId);
                $actionText = $action === 'confirme' ? 'confirmé sa participation' : 'décliné l\'invitation';
                
                Notification::create([
                    'titre' => 'Réponse à une invitation',
                    'message' => "{$user->nom_complet} a {$actionText} à l'événement \"{$event->titre}\".",
                    'type' => 'evenement',
                    'destinataire_id' => $event->id_organisateur,
                    'lue' => false
                ]);
            } catch (\Exception $e) {
                Log::warning('Impossible de créer la notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Obtenir la couleur d'un événement pour le calendrier
     */
    private function getEventColor(Event $event)
    {
        return match($event->priorite) {
            'urgente' => '#ef4444',
            'haute' => '#f59e0b',
            'normale' => '#3b82f6',
            default => '#6b7280'
        };
    }
}