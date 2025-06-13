<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\Task;
use App\Models\Event;
use App\Models\PieceJointe;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des rapports
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        // Query de base selon le rôle
        if ($isAdmin) {
            $query = Report::with(['utilisateur', 'tache', 'evenement', 'piecesJointes']);
        } else {
            $query = Report::with(['utilisateur', 'tache', 'evenement', 'piecesJointes'])
                          ->where('id_utilisateur', $user->id);
        }

        // Appliquer les filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%")
                  ->orWhere('type_intervention', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('user_id') && $isAdmin) {
            $query->where('id_utilisateur', $request->user_id);
        }

        if ($request->filled('type_intervention')) {
            $query->where('type_intervention', $request->type_intervention);
        }

        // Tri par date d'intervention décroissante
        $query->orderBy('date_intervention', 'desc');

        // Pagination
        $reports = $query->paginate(12)->withQueryString();

        // Statistiques
        if ($isAdmin) {
            $stats = [
                'total' => Report::count(),
                'en_attente' => Report::where('statut', 'en_attente')->count(),
                'valides' => Report::where('statut', 'valide')->count(),
                'rejetes' => Report::where('statut', 'rejete')->count(),
                'cette_semaine' => Report::whereBetween('date_intervention', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'ce_mois' => Report::whereBetween('date_intervention', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->count(),
            ];

            // Liste des utilisateurs avec nombre de rapports pour le filtre
            $users = User::withCount('rapports')
                        ->where('role', '!=', 'admin')
                        ->orderBy('nom')
                        ->get();
        } else {
            $userReportQuery = Report::where('id_utilisateur', $user->id);
            
            $stats = [
                'total' => $userReportQuery->count(),
                'en_attente' => $userReportQuery->where('statut', 'en_attente')->count(),
                'valides' => $userReportQuery->where('statut', 'valide')->count(),
                'rejetes' => $userReportQuery->where('statut', 'rejete')->count(),
                'ce_mois' => $userReportQuery->whereMonth('date_intervention', now()->month)->count(),
            ];

            $users = collect();
        }

        // Types d'intervention disponibles
        $typesIntervention = Report::select('type_intervention')
                                  ->distinct()
                                  ->pluck('type_intervention')
                                  ->filter()
                                  ->sort()
                                  ->values();

        return view('reports.index', compact(
            'reports', 
            'stats', 
            'users', 
            'typesIntervention',
            'isAdmin'
        ));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les tâches assignées à l'utilisateur (non terminées)
        $taches = Task::where('id_utilisateur', $user->id)
                     ->whereIn('statut', ['a_faire', 'en_cours'])
                     ->orderBy('date_echeance')
                     ->get();

        // Récupérer les événements où l'utilisateur participe (futurs ou récents)
        $evenements = Event::where('id_organisateur', $user->id)
                          ->orWhereHas('participants', function($q) use ($user) {
                              $q->where('id_utilisateur', $user->id);
                          })
                          ->where('date_debut', '>=', now()->subDays(30))
                          ->orderBy('date_debut', 'desc')
                          ->get();

        // Types d'intervention prédéfinis
        $typesIntervention = [
            'Maintenance préventive',
            'Maintenance corrective',
            'Installation',
            'Réparation',
            'Inspection',
            'Formation',
            'Support technique',
            'Audit',
            'Autre'
        ];

        return view('reports.create', compact('taches', 'evenements', 'typesIntervention'));
    }

    /**
     * Enregistrer un nouveau rapport
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:100',
            'date_intervention' => 'required|date|before_or_equal:today',
            'lieu' => 'required|string|max:100',
            'type_intervention' => 'required|string|max:50',
            'actions' => 'required|string',
            'resultats' => 'required|string',
            'problemes' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'id_tache' => 'nullable|exists:tasks,id',
            'id_evenement' => 'nullable|exists:events,id',
            'pieces_jointes.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
        ]);

        DB::beginTransaction();
        try {
            // Créer le rapport
            $report = Report::create([
                'titre' => $request->titre,
                'date_intervention' => $request->date_intervention,
                'lieu' => $request->lieu,
                'type_intervention' => $request->type_intervention,
                'actions' => $request->actions,
                'resultats' => $request->resultats,
                'problemes' => $request->problemes,
                'recommandations' => $request->recommandations,
                'id_utilisateur' => Auth::id(),
                'id_tache' => $request->id_tache,
                'id_evenement' => $request->id_evenement,
                'statut' => 'en_attente',
            ]);

            // Traiter les pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filename = time() . '_' . $originalName;
                    $path = $file->storeAs('reports/' . $report->id, $filename, 'public');

                    PieceJointe::create([
                        'nom_fichier' => $filename,
                        'nom_original' => $originalName,
                        'type_fichier' => $file->getClientOriginalExtension(),
                        'taille' => $file->getSize(),
                        'chemin' => $path,
                        'mime_type' => $file->getMimeType(),
                        'id_rapport' => $report->id,
                    ]);
                }
            }

            // Mettre à jour le statut de la tâche si liée
            if ($request->id_tache) {
                $tache = Task::find($request->id_tache);
                if ($tache && $tache->statut !== 'termine') {
                    $tache->update(['statut' => 'termine', 'date_fin_reelle' => now()]);
                }
            }

            DB::commit();

            return redirect()->route('reports.index')
                           ->with('success', 'Rapport créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la création du rapport : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un rapport
     */
    public function show(Report $report)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->role !== 'admin' && $report->id_utilisateur !== $user->id) {
            abort(403, 'Accès non autorisé à ce rapport.');
        }

        $report->load(['utilisateur', 'tache', 'evenement', 'piecesJointes']);

        return view('reports.show', compact('report'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Report $report)
    {
        $user = Auth::user();

        // Vérifier les permissions (seul l'auteur peut modifier si en attente)
        if ($user->role !== 'admin' && ($report->id_utilisateur !== $user->id || $report->statut !== 'en_attente')) {
            abort(403, 'Vous ne pouvez pas modifier ce rapport.');
        }

        // Récupérer les tâches et événements
        $taches = Task::where('id_utilisateur', $report->id_utilisateur)
                     ->whereIn('statut', ['a_faire', 'en_cours', 'termine'])
                     ->orderBy('date_echeance')
                     ->get();

        $evenements = Event::where('id_organisateur', $report->id_utilisateur)
                          ->orWhereHas('participants', function($q) use ($report) {
                              $q->where('id_utilisateur', $report->id_utilisateur);
                          })
                          ->orderBy('date_debut', 'desc')
                          ->get();

        $typesIntervention = [
            'Maintenance préventive',
            'Maintenance corrective',
            'Installation',
            'Réparation',
            'Inspection',
            'Formation',
            'Support technique',
            'Audit',
            'Autre'
        ];

        return view('reports.edit', compact('report', 'taches', 'evenements', 'typesIntervention'));
    }

    /**
     * Mettre à jour un rapport
     */
    public function update(Request $request, Report $report)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->role !== 'admin' && ($report->id_utilisateur !== $user->id || $report->statut !== 'en_attente')) {
            abort(403, 'Vous ne pouvez pas modifier ce rapport.');
        }

        $request->validate([
            'titre' => 'required|string|max:100',
            'date_intervention' => 'required|date|before_or_equal:today',
            'lieu' => 'required|string|max:100',
            'type_intervention' => 'required|string|max:50',
            'actions' => 'required|string',
            'resultats' => 'required|string',
            'problemes' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'id_tache' => 'nullable|exists:tasks,id',
            'id_evenement' => 'nullable|exists:events,id',
            'pieces_jointes.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour le rapport
            $report->update([
                'titre' => $request->titre,
                'date_intervention' => $request->date_intervention,
                'lieu' => $request->lieu,
                'type_intervention' => $request->type_intervention,
                'actions' => $request->actions,
                'resultats' => $request->resultats,
                'problemes' => $request->problemes,
                'recommandations' => $request->recommandations,
                'id_tache' => $request->id_tache,
                'id_evenement' => $request->id_evenement,
            ]);

            // Traiter les nouvelles pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filename = time() . '_' . $originalName;
                    $path = $file->storeAs('reports/' . $report->id, $filename, 'public');

                    PieceJointe::create([
                        'nom_fichier' => $filename,
                        'nom_original' => $originalName,
                        'type_fichier' => $file->getClientOriginalExtension(),
                        'taille' => $file->getSize(),
                        'chemin' => $path,
                        'mime_type' => $file->getMimeType(),
                        'id_rapport' => $report->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('reports.show', $report)
                           ->with('success', 'Rapport mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un rapport
     */
    public function destroy(Report $report)
    {
        $user = Auth::user();

        // Seul l'admin peut supprimer
        if ($user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent supprimer des rapports.');
        }

        DB::beginTransaction();
        try {
            // Supprimer les fichiers associés
            foreach ($report->piecesJointes as $pieceJointe) {
                Storage::disk('public')->delete($pieceJointe->chemin);
            }

            // Supprimer le rapport (les pièces jointes seront supprimées en cascade)
            $report->delete();

            DB::commit();

            return redirect()->route('reports.index')
                           ->with('success', 'Rapport supprimé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * ✅ CORRIGÉ : Valider un rapport (admin seulement) - Méthode renommée
     */
    public function validateReport(Report $report)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent valider des rapports.');
        }

        $report->update(['statut' => 'valide']);

        return back()->with('success', 'Rapport validé avec succès !');
    }

    /**
     * Rejeter un rapport (admin seulement)
     */
    public function reject(Request $request, Report $report)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent rejeter des rapports.');
        }

        $request->validate([
            'motif_rejet' => 'required|string|max:500'
        ]);

        $report->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->motif_rejet
        ]);

        return back()->with('success', 'Rapport rejeté.');
    }

    /**
     * Télécharger une pièce jointe
     */
    public function downloadAttachment(PieceJointe $pieceJointe)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->role !== 'admin' && $pieceJointe->rapport->id_utilisateur !== $user->id) {
            abort(403, 'Accès non autorisé à ce fichier.');
        }

        $filePath = storage_path('app/public/' . $pieceJointe->chemin);

        if (!file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        return response()->download($filePath, $pieceJointe->nom_original);
    }

    /**
     * Supprimer une pièce jointe
     */
    public function deleteAttachment(PieceJointe $pieceJointe)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($user->role !== 'admin' && 
            ($pieceJointe->rapport->id_utilisateur !== $user->id || $pieceJointe->rapport->statut !== 'en_attente')) {
            abort(403, 'Vous ne pouvez pas supprimer ce fichier.');
        }

        try {
            // Supprimer le fichier physique
            Storage::disk('public')->delete($pieceJointe->chemin);
            
            // Supprimer l'enregistrement
            $pieceJointe->delete();

            return back()->with('success', 'Fichier supprimé avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du fichier.');
        }
    }
}