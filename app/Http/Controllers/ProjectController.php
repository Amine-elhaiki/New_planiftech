<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des projets
     */
    public function index(Request $request)
    {
        $query = Project::with(['responsable', 'taches', 'evenements']);

        // Filtrage selon le rôle
        if (Auth::user()->role !== 'admin') {
            $query->where(function($q) {
                $q->where('id_responsable', Auth::id())
                  ->orWhereHas('taches', function($taskQuery) {
                      $taskQuery->where('id_utilisateur', Auth::id());
                  })
                  ->orWhereHas('evenements', function($eventQuery) {
                      $eventQuery->where('id_organisateur', Auth::id())
                             ->orWhereHas('participants', function($participantQuery) {
                                 $participantQuery->where('id_utilisateur', Auth::id());
                             });
                  });
            });
        }

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('zone_geographique', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('responsable') && Auth::user()->role === 'admin') {
            $query->where('id_responsable', $request->input('responsable'));
        }

        if ($request->filled('zone')) {
            $query->where('zone_geographique', 'like', "%{$request->input('zone')}%");
        }

        // Tri
        $sortBy = $request->input('sort_by', 'date_debut');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $projects = $query->paginate(12)->withQueryString();

        // Calculer l'avancement pour chaque projet
        foreach ($projects as $project) {
            $project->avancement = $this->calculateProgress($project);
        }

        // Données pour les filtres
        $users = Auth::user()->role === 'admin' ? User::where('statut', 'actif')->get() : collect();

        // Statistiques
        $stats = [
            'total' => Project::count(),
            'planifie' => Project::where('statut', 'planifie')->count(),
            'en_cours' => Project::where('statut', 'en_cours')->count(),
            'termine' => Project::where('statut', 'termine')->count(),
            'suspendu' => Project::where('statut', 'suspendu')->count()
        ];

        return view('projects.index', compact('projects', 'users', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des projets.');
        }

        $users = User::where('statut', 'actif')->get();

        return view('projects.create', compact('users'));
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function store(Request $request)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des projets.');
        }

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:projets,nom',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'zone_geographique' => 'required|string|max:255',
            'id_responsable' => 'required|exists:users,id'
        ], [
            'nom.required' => 'Le nom du projet est obligatoire.',
            'nom.unique' => 'Un projet avec ce nom existe déjà.',
            'description.required' => 'La description est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'id_responsable.required' => 'Le responsable du projet est obligatoire.',
            'id_responsable.exists' => 'Le responsable sélectionné n\'existe pas.'
        ]);

        $validatedData['statut'] = 'planifie';

        $project = Project::create($validatedData);

        return redirect()->route('projects.index')
                        ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Afficher un projet spécifique
     */
    public function show(Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' &&
            $project->id_responsable !== Auth::id() &&
            !$project->taches->contains('id_utilisateur', Auth::id()) &&
            !$this->userParticipatesInProjectEvents($project, Auth::id())) {
            abort(403, 'Vous ne pouvez voir que les projets auxquels vous participez.');
        }

        $project->load([
            'responsable',
            'taches.utilisateur',
            'evenements.organisateur'
        ]);

        // Calculer l'avancement
        $project->avancement = $this->calculateProgress($project);

        // Statistiques des tâches
        $taskStats = [
            'total' => $project->taches->count(),
            'a_faire' => $project->taches->where('statut', 'a_faire')->count(),
            'en_cours' => $project->taches->where('statut', 'en_cours')->count(),
            'termine' => $project->taches->where('statut', 'termine')->count(),
            'en_retard' => $project->taches->filter(function($task) {
                return $task->date_echeance < Carbon::today() &&
                       in_array($task->statut, ['a_faire', 'en_cours']);
            })->count()
        ];

        // Statistiques des événements
        $eventStats = [
            'total' => $project->evenements->count(),
            'planifie' => $project->evenements->where('statut', 'planifie')->count(),
            'en_cours' => $project->evenements->where('statut', 'en_cours')->count(),
            'termine' => $project->evenements->where('statut', 'termine')->count(),
            'annule' => $project->evenements->where('statut', 'annule')->count()
        ];

        // Activités récentes
        $recentActivities = $this->getProjectActivities($project);

        return view('projects.show', compact('project', 'taskStats', 'eventStats', 'recentActivities'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier les projets.');
        }

        $users = User::where('statut', 'actif')->get();

        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier les projets.');
        }

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:projets,nom,' . $project->id,
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'zone_geographique' => 'required|string|max:255',
            'statut' => 'required|in:planifie,en_cours,termine,suspendu',
            'id_responsable' => 'required|exists:users,id'
        ]);

        $project->update($validatedData);

        return redirect()->route('projects.index')
                        ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Supprimer un projet
     */
    public function destroy(Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer les projets.');
        }

        // Vérifier s'il y a des tâches ou événements associés
        if ($project->taches()->count() > 0 || $project->evenements()->count() > 0) {
            return back()->withErrors(['error' => 'Impossible de supprimer un projet contenant des tâches ou des événements.']);
        }

        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Calculer l'avancement d'un projet
     */
    private function calculateProgress(Project $project)
    {
        $totalTasks = $project->taches->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $project->taches->where('statut', 'termine')->count();

        return round(($completedTasks / $totalTasks) * 100, 1);
    }

    /**
     * Vérifier si un utilisateur participe aux événements du projet
     */
    private function userParticipatesInProjectEvents(Project $project, $userId)
    {
        foreach ($project->evenements as $event) {
            if ($event->id_organisateur === $userId) {
                return true;
            }
            // Cette vérification nécessiterait de charger les participants
            // Pour simplifier, on retourne false ici
        }
        return false;
    }

    /**
     * Obtenir les activités récentes d'un projet
     */
    private function getProjectActivities(Project $project, $limit = 10)
    {
        $activities = collect();

        // Tâches récemment créées ou mises à jour
        $recentTasks = $project->taches()
                              ->with('utilisateur')
                              ->latest('date_modification')
                              ->limit($limit)
                              ->get()
                              ->map(function($task) {
                                  return [
                                      'type' => 'task',
                                      'message' => "Tâche mise à jour: {$task->titre}",
                                      'user' => $task->utilisateur->prenom . ' ' . $task->utilisateur->nom,
                                      'date' => $task->date_modification,
                                      'icon' => 'bi-list-check',
                                      'color' => 'primary',
                                      'details' => "Statut: {$task->statut}"
                                  ];
                              });

        // Événements récemment créés ou mis à jour
        $recentEvents = $project->evenements()
                               ->with('organisateur')
                               ->latest('date_modification')
                               ->limit($limit)
                               ->get()
                               ->map(function($event) {
                                   return [
                                       'type' => 'event',
                                       'message' => "Événement mis à jour: {$event->titre}",
                                       'user' => $event->organisateur->prenom . ' ' . $event->organisateur->nom,
                                       'date' => $event->date_modification,
                                       'icon' => 'bi-calendar-event',
                                       'color' => 'success',
                                       'details' => "Statut: {$event->statut}"
                                   ];
                               });

        return $activities->concat($recentTasks)
                         ->concat($recentEvents)
                         ->sortByDesc('date')
                         ->take($limit)
                         ->values();
    }

    /**
     * Changer le statut d'un projet
     */
    public function updateStatus(Request $request, Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $project->id_responsable !== Auth::id()) {
            abort(403, 'Seuls les administrateurs et le responsable peuvent changer le statut.');
        }

        $validatedData = $request->validate([
            'statut' => 'required|in:planifie,en_cours,termine,suspendu'
        ]);

        $project->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'project' => $project
        ]);
    }

    /**
     * Obtenir le rapport d'avancement d'un projet
     */
    public function report(Project $project)
    {
        if (Auth::user()->role !== 'admin' && $project->id_responsable !== Auth::id()) {
            abort(403, 'Seuls les administrateurs et le responsable peuvent voir le rapport.');
        }

        $project->load([
            'responsable',
            'taches.utilisateur',
            'evenements.organisateur'
        ]);

        // Données pour le rapport
        $data = [
            'project' => $project,
            'avancement' => $this->calculateProgress($project),
            'tasks_by_status' => $project->taches->groupBy('statut'),
            'tasks_by_priority' => $project->taches->groupBy('priorite'),
            'events_by_type' => $project->evenements->groupBy('type'),
            'timeline' => $this->getProjectTimeline($project),
            'team_members' => $this->getTeamMembers($project)
        ];

        return view('projects.report', $data);
    }

    /**
     * Obtenir la timeline d'un projet
     */
    private function getProjectTimeline(Project $project)
    {
        $timeline = collect();

        // Ajouter les tâches à la timeline
        foreach ($project->taches as $task) {
            $timeline->push([
                'date' => $task->date_echeance,
                'type' => 'task',
                'title' => $task->titre,
                'status' => $task->statut,
                'assignee' => $task->utilisateur->prenom . ' ' . $task->utilisateur->nom
            ]);
        }

        // Ajouter les événements à la timeline
        foreach ($project->evenements as $event) {
            $timeline->push([
                'date' => $event->date_debut,
                'type' => 'event',
                'title' => $event->titre,
                'status' => $event->statut,
                'organizer' => $event->organisateur->prenom . ' ' . $event->organisateur->nom
            ]);
        }

        return $timeline->sortBy('date')->values();
    }

    /**
     * Obtenir les membres de l'équipe du projet
     */
    private function getTeamMembers(Project $project)
    {
        $members = collect();

        // Responsable du projet
        $members->push($project->responsable);

        // Utilisateurs assignés aux tâches
        foreach ($project->taches as $task) {
            if ($task->utilisateur && !$members->contains('id', $task->utilisateur->id)) {
                $members->push($task->utilisateur);
            }
        }

        // Organisateurs d'événements
        foreach ($project->evenements as $event) {
            if ($event->organisateur && !$members->contains('id', $event->organisateur->id)) {
                $members->push($event->organisateur);
            }
        }

        return $members->unique('id')->values();
    }

    /**
     * Archiver un projet
     */
    public function archive(Project $project)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent archiver les projets.');
        }

        $project->update(['statut' => 'termine']);

        return back()->with('success', 'Projet archivé avec succès.');
    }
}
