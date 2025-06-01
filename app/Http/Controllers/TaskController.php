<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Tri
        $sortBy = $request->input('sort_by', 'date_echeance');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(15)->withQueryString();

        // Données pour les filtres
        $users = Auth::user()->role === 'admin' ? User::where('statut', 'actif')->get() : collect();
        $projects = Project::where('statut', '!=', 'termine')->get();

        // Statistiques
        $stats = [
            'total' => $query->count(),
            'a_faire' => Task::where('statut', 'a_faire')->count(),
            'en_cours' => Task::where('statut', 'en_cours')->count(),
            'termine' => Task::where('statut', 'termine')->count(),
            'en_retard' => Task::where('date_echeance', '<', Carbon::today())
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

        return view('tasks.create', compact('users', 'projects', 'events'));
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
            'description.required' => 'La description est obligatoire.',
            'date_echeance.required' => 'La date d\'échéance est obligatoire.',
            'date_echeance.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou dans le futur.',
            'priorite.required' => 'La priorité est obligatoire.',
            'id_utilisateur.required' => 'L\'assignation à un utilisateur est obligatoire.',
            'id_utilisateur.exists' => 'L\'utilisateur sélectionné n\'existe pas.'
        ]);

        $validatedData['statut'] = 'a_faire';
        $validatedData['progression'] = 0;

        $task = Task::create($validatedData);

        return redirect()->route('tasks.index')
                        ->with('success', 'Tâche créée avec succès.');
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
        ]);

        $task->update($validatedData);

        return redirect()->route('tasks.index')
                        ->with('success', 'Tâche mise à jour avec succès.');
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

        $task->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'task' => $task->load(['utilisateur', 'projet'])
        ]);
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

        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success', 'Tâche supprimée avec succès.');
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
                'utilisateur' => $task->utilisateur->prenom . ' ' . $task->utilisateur->nom,
                'projet' => $task->projet ? $task->projet->nom : null,
                'can_edit' => Auth::user()->role === 'admin' || $task->id_utilisateur === Auth::id()
            ];
        });

        return response()->json($tasks);
    }

    /**
     * Marquer une tâche comme terminée rapidement
     */
    public function markCompleted(Task $task)
    {
        if (Auth::user()->role !== 'admin' && $task->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres tâches.');
        }

        $task->update([
            'statut' => 'termine',
            'progression' => 100
        ]);

        return back()->with('success', 'Tâche marquée comme terminée.');
    }

    /**
     * Obtenir les tâches d'un utilisateur pour le calendrier
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
                'extendedProps' => [
                    'description' => $task->description,
                    'statut' => $task->statut,
                    'priorite' => $task->priorite,
                    'progression' => $task->progression
                ]
            ];
        });

        return response()->json($tasks);
    }
}
