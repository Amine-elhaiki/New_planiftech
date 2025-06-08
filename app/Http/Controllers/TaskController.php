<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\Event;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des tâches
     */
    public function index(Request $request)
    {
        $query = Task::with(['utilisateur', 'projet', 'evenement']);

        // Filtrage selon le rôle
        if (Auth::user()->role !== 'admin') {
            $query->where('id_utilisateur', Auth::id());
        }

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->input('priorite'));
        }

        if ($request->filled('utilisateur') && Auth::user()->role === 'admin') {
            $query->where('id_utilisateur', $request->input('utilisateur'));
        }

        if ($request->filled('projet')) {
            $query->where('id_projet', $request->input('projet'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_echeance', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        // Tri sécurisé
        $sortBy = $request->input('sort_by', 'date_echeance');
        $sortOrder = $request->input('sort_order', 'asc');

        $allowedSortFields = ['date_echeance', 'priorite', 'statut', 'titre', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'date_echeance';
        }

        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(15)->withQueryString();

        // Données pour les filtres
        $users = Auth::user()->role === 'admin' ? User::where('statut', 'actif')->get() : collect();
        $projects = Project::where('statut', '!=', 'termine')->get();

        // Statistiques corrigées
        $baseStatsQuery = Auth::user()->role === 'admin'
            ? Task::query()
            : Task::where('id_utilisateur', Auth::id());

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'a_faire' => (clone $baseStatsQuery)->where('statut', 'a_faire')->count(),
            'en_cours' => (clone $baseStatsQuery)->where('statut', 'en_cours')->count(),
            'termine' => (clone $baseStatsQuery)->where('statut', 'termine')->count(),
            'en_retard' => (clone $baseStatsQuery)->where('date_echeance', '<', Carbon::today())
                                                  ->whereIn('statut', ['a_faire', 'en_cours'])
                                                  ->count()
        ];

        return view('tasks.index', compact('tasks', 'users', 'projects', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des tâches.');
        }

        $users = User::where('statut', 'actif')->get();
        $projects = Project::where('statut', '!=', 'termine')->get();
        $events = Event::where('statut', 'planifie')->get();

        // ✅ CORRECTION: Ajout des statistiques pour la vue
        $stats = [
            'a_faire' => Task::where('statut', 'a_faire')->count(),
            'en_cours' => Task::where('statut', 'en_cours')->count(),
        ];

        return view('tasks.create', compact('users', 'projects', 'events', 'stats'));
    }

    /**
     * Enregistrer une nouvelle tâche
     */
    public function store(Request $request)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent créer des tâches.');
        }

        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_echeance' => 'required|date|after_or_equal:today',
            'priorite' => 'required|in:basse,moyenne,haute',
            'id_utilisateur' => 'required|exists:users,id',
            'id_projet' => 'nullable|exists:projects,id',
            'id_evenement' => 'nullable|exists:events,id'
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est obligatoire.',
            'date_echeance.required' => 'La date d\'échéance est obligatoire.',
            'date_echeance.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
            'priorite.required' => 'La priorité est obligatoire.',
            'priorite.in' => 'La priorité doit être basse, moyenne ou haute.',
            'id_utilisateur.required' => 'L\'assignation à un utilisateur est obligatoire.',
            'id_utilisateur.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'id_projet.exists' => 'Le projet sélectionné n\'existe pas.',
            'id_evenement.exists' => 'L\'événement sélectionné n\'existe pas.'
        ]);

        $validatedData['statut'] = 'a_faire';
        $validatedData['progression'] = 0;

        // ✅ CORRECTION: Gestion d'erreurs avec transaction
        try {
            DB::beginTransaction();

            $task = Task::create($validatedData);

            // Log de l'activité
            Log::info('Nouvelle tâche créée', [
                'task_id' => $task->id,
                'created_by' => Auth::id(),
                'assigned_to' => $validatedData['id_utilisateur']
            ]);

            DB::commit();

            // ✅ Message plus informatif
            $assignedUser = User::find($validatedData['id_utilisateur']);
            return redirect()->route('tasks.index')
                           ->with('success', 'Tâche créée avec succès et assignée à ' . $assignedUser->prenom . ' ' . $assignedUser->nom . '.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création tâche', ['error' => $e->getMessage()]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Une erreur est survenue lors de la création de la tâche. Veuillez réessayer.');
        }
    }

    /**
     * Afficher une tâche spécifique
     */
    public function show(Task $task)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $task->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez voir que vos propres tâches.');
        }

        $task->load(['utilisateur', 'projet', 'evenement']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Task $task)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier les tâches.');
        }

        $users = User::where('statut', 'actif')->get();
        $projects = Project::where('statut', '!=', 'termine')->get();
        $events = Event::where('statut', 'planifie')->get();

        return view('tasks.edit', compact('task', 'users', 'projects', 'events'));
    }

    /**
     * Mettre à jour une tâche
     */
    public function update(Request $request, Task $task)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent modifier les tâches.');
        }

        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_echeance' => 'required|date',
            'priorite' => 'required|in:basse,moyenne,haute',
            'statut' => 'required|in:a_faire,en_cours,termine',
            'progression' => 'required|integer|min:0|max:100',
            'id_utilisateur' => 'required|exists:users,id',
            'id_projet' => 'nullable|exists:projects,id',
            'id_evenement' => 'nullable|exists:events,id'
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'date_echeance.required' => 'La date d\'échéance est obligatoire.',
            'priorite.required' => 'La priorité est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'progression.required' => 'La progression est obligatoire.',
            'progression.integer' => 'La progression doit être un nombre entier.',
            'progression.min' => 'La progression ne peut pas être négative.',
            'progression.max' => 'La progression ne peut pas dépasser 100%.',
            'id_utilisateur.required' => 'L\'assignation est obligatoire.',
            'id_utilisateur.exists' => 'L\'utilisateur sélectionné n\'existe pas.'
        ]);

        // ✅ CORRECTION: Gestion d'erreurs avec transaction
        try {
            DB::beginTransaction();

            $oldAssignee = $task->id_utilisateur;
            $task->update($validatedData);

            // Log si changement d'assignation
            if ($oldAssignee !== $validatedData['id_utilisateur']) {
                Log::info('Tâche réassignée', [
                    'task_id' => $task->id,
                    'old_assignee' => $oldAssignee,
                    'new_assignee' => $validatedData['id_utilisateur'],
                    'updated_by' => Auth::id()
                ]);
            }

            DB::commit();

            return redirect()->route('tasks.index')
                           ->with('success', 'Tâche mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur mise à jour tâche', ['error' => $e->getMessage()]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Mettre à jour le statut d'une tâche (pour les techniciens)
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $task->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres tâches.');
        }

        $validatedData = $request->validate([
            'statut' => 'required|in:a_faire,en_cours,termine',
            'progression' => 'required|integer|min:0|max:100'
        ]);

        // Ajuster automatiquement la progression selon le statut
        if ($validatedData['statut'] === 'a_faire') {
            $validatedData['progression'] = 0;
        } elseif ($validatedData['statut'] === 'termine') {
            $validatedData['progression'] = 100;
        }

        try {
            $task->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès.',
                'task' => $task->load(['utilisateur', 'projet'])
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut.'
            ], 500);
        }
    }

    /**
     * Marquer une tâche comme terminée rapidement
     */
    public function markCompleted(Task $task)
    {
        if (Auth::user()->role !== 'admin' && $task->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres tâches.');
        }

        try {
            $task->update([
                'statut' => 'termine',
                'progression' => 100
            ]);

            // ✅ CORRECTION: Support JSON et web
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tâche marquée comme terminée.'
                ]);
            }

            return back()->with('success', 'Tâche marquée comme terminée.');

        } catch (\Exception $e) {
            Log::error('Erreur completion tâche', ['error' => $e->getMessage()]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour.'
                ], 500);
            }

            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Supprimer une tâche
     */
    public function destroy(Task $task)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer les tâches.');
        }

        try {
            DB::beginTransaction();

            $taskTitle = $task->titre;
            $task->delete();

            Log::info('Tâche supprimée', [
                'task_title' => $taskTitle,
                'deleted_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('tasks.index')
                           ->with('success', 'Tâche "' . $taskTitle . '" supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur suppression tâche', ['error' => $e->getMessage()]);

            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression de la tâche.');
        }
    }

    /**
     * Obtenir les tâches en format JSON (pour API)
     */
    public function api(Request $request)
    {
        $query = Task::with(['utilisateur', 'projet']);

        if (Auth::user()->role !== 'admin') {
            $query->where('id_utilisateur', Auth::id());
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $tasks = $query->get()->map(function($task) {
            return [
                'id' => $task->id,
                'titre' => $task->titre,
                'description' => $task->description,
                'statut' => $task->statut,
                'priorite' => $task->priorite,
                'progression' => $task->progression,
                'date_echeance' => $task->date_echeance->format('Y-m-d'),
                // ✅ CORRECTION: Gestion utilisateur null
                'utilisateur' => $task->utilisateur ?
                    $task->utilisateur->prenom . ' ' . $task->utilisateur->nom :
                    'Non assigné',
                'projet' => $task->projet ? $task->projet->nom : null,
                'can_edit' => Auth::user()->role === 'admin' || $task->id_utilisateur === Auth::id(),
                'is_overdue' => $task->date_echeance < Carbon::today() &&
                               in_array($task->statut, ['a_faire', 'en_cours'])
            ];
        });

        return response()->json($tasks);
    }

    /**
     * Obtenir les tâches pour le calendrier
     */
    public function calendar(Request $request)
    {
        $query = Task::query();

        if (Auth::user()->role !== 'admin') {
            $query->where('id_utilisateur', Auth::id());
        }

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('date_echeance', [
                $request->input('start'),
                $request->input('end')
            ]);
        }

        $tasks = $query->get()->map(function($task) {
            $color = match($task->priorite) {
                'haute' => '#dc3545',    // rouge
                'moyenne' => '#fd7e14',  // orange
                'basse' => '#28a745'     // vert
            };

            return [
                'id' => $task->id,
                'title' => $task->titre,
                'start' => $task->date_echeance->format('Y-m-d'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'url' => route('tasks.show', $task),
                'extendedProps' => [
                    'description' => $task->description,
                    'statut' => $task->statut,
                    'priorite' => $task->priorite,
                    'progression' => $task->progression,
                    // ✅ CORRECTION: Gestion utilisateur null
                    'utilisateur' => $task->utilisateur ?
                        $task->utilisateur->prenom . ' ' . $task->utilisateur->nom :
                        'Non assigné'
                ]
            ];
        });

        return response()->json($tasks);
    }
}
