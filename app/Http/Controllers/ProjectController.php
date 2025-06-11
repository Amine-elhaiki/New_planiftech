<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $this->middleware('can:admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'updateStatus', 'archive']);
    }

    /**
     * Afficher la liste des projets
     */
    public function index(Request $request)
    {
        $query = Project::with(['responsable', 'taches', 'evenements']);

        // Filtrage selon le rôle
        if (Auth::user()->role !== 'admin') {
            $query->where('id_responsable', Auth::id());
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

        if ($request->filled('responsable')) {
            $query->where('id_responsable', $request->input('responsable'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_debut', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        $projects = $query->orderBy('date_debut', 'desc')->paginate(12)->withQueryString();

        // Statistiques
        $stats = $this->getProjectStats();

        // Données pour les filtres
        $responsables = User::where('statut', 'actif')->get();

        return view('projects.index', compact('projects', 'stats', 'responsables'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $users = User::where('statut', 'actif')->get();
        return view('projects.create', compact('users'));
    }

    /**
     * Enregistrer un nouveau projet
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'required|string',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after:date_debut',
            'zone_geographique' => 'required|string|max:100',
            'id_responsable' => 'required|exists:users,id'
        ], [
            'nom.required' => 'Le nom du projet est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'id_responsable.required' => 'Le responsable est obligatoire.'
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
        if (Auth::user()->role !== 'admin' && $project->id_responsable !== Auth::id()) {
            abort(403, 'Vous ne pouvez voir que vos propres projets.');
        }

        $project->load(['responsable', 'taches.utilisateur', 'evenements.organisateur']);

        // Statistiques du projet
        $stats = [
            'total_taches' => $project->taches->count(),
            'taches_terminees' => $project->taches->where('statut', 'termine')->count(),
            'taches_en_cours' => $project->taches->where('statut', 'en_cours')->count(),
            'taches_en_retard' => $project->taches->where('date_echeance', '<', now())
                                                 ->whereIn('statut', ['a_faire', 'en_cours'])->count(),
            'total_evenements' => $project->evenements->count(),
            'evenements_planifies' => $project->evenements->where('statut', 'planifie')->count(),
        ];

        return view('projects.show', compact('project', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Project $project)
    {
        $users = User::where('statut', 'actif')->get();
        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'zone_geographique' => 'required|string|max:100',
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
        if ($project->taches()->count() > 0 || $project->evenements()->count() > 0) {
            return back()->withErrors(['error' => 'Impossible de supprimer un projet avec des tâches ou événements associés.']);
        }

        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Changer le statut d'un projet
     */
    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'statut' => 'required|in:planifie,en_cours,termine,suspendu'
        ]);

        $project->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut du projet mis à jour.');
    }

    /**
     * Archiver un projet
     */
    public function archive(Project $project)
    {
        $project->update(['statut' => 'termine']);

        return back()->with('success', 'Projet archivé avec succès.');
    }

    /**
     * Générer un rapport de projet
     */
    public function report(Project $project)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $project->id_responsable !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $project->load(['responsable', 'taches.utilisateur', 'evenements.organisateur']);

        return view('projects.report', compact('project'));
    }

    /**
     * Obtenir les statistiques des projets
     */
    private function getProjectStats()
    {
        $query = Project::query();

        if (Auth::user()->role !== 'admin') {
            $query->where('id_responsable', Auth::id());
        }

        return [
            'total' => $query->count(),
            'en_cours' => (clone $query)->where('statut', 'en_cours')->count(),
            'planifies' => (clone $query)->where('statut', 'planifie')->count(),
            'termines' => (clone $query)->where('statut', 'termine')->count(),
            'en_retard' => (clone $query)->where('date_fin', '<', now())
                                        ->where('statut', '!=', 'termine')->count(),
        ];
    }
}
