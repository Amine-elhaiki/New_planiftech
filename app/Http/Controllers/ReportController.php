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
        $query = Report::with(['utilisateur', 'tache', 'evenement', 'piecesJointes']);

        // Filtrage selon le rôle
        if (Auth::user()->role !== 'admin') {
            $query->where('id_utilisateur', Auth::id());
        }

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%")
                  ->orWhere('type_intervention', 'like', "%{$search}%")
                  ->orWhere('actions', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type_intervention')) {
            $query->where('type_intervention', $request->input('type_intervention'));
        }

        if ($request->filled('utilisateur') && Auth::user()->role === 'admin') {
            $query->where('id_utilisateur', $request->input('utilisateur'));
        }

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_intervention', [
                $request->input('date_debut'),
                $request->input('date_fin')
            ]);
        }

        // Tri
        $sortBy = $request->input('sort_by', 'date_intervention');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $reports = $query->paginate(15)->withQueryString();

        // Types d'intervention pour le filtre
        $interventionTypes = Report::distinct()->pluck('type_intervention')->filter();

        // Données pour les filtres
        $users = Auth::user()->role === 'admin' ? User::where('statut', 'actif')->get() : collect();

        return view('reports.index', compact('reports', 'interventionTypes', 'users'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        // Récupérer la tâche ou l'événement si spécifié
        $task = $request->filled('task_id') ? Task::findOrFail($request->input('task_id')) : null;
        $event = $request->filled('event_id') ? Event::findOrFail($request->input('event_id')) : null;

        return view('reports.create', compact('task', 'event'));
    }

    /**
     * Enregistrer un nouveau rapport
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'date_intervention' => 'required|date|before_or_equal:today',
            'lieu' => 'required|string|max:255',
            'type_intervention' => 'required|string|max:100',
            'actions' => 'required|string',
            'resultats' => 'required|string',
            'problemes' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'id_tache' => 'nullable|exists:taches,id',
            'id_evenement' => 'nullable|exists:evenements,id',
            'pieces_jointes.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120' // 5MB max
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'date_intervention.required' => 'La date d\'intervention est obligatoire.',
            'date_intervention.before_or_equal' => 'La date d\'intervention ne peut pas être dans le futur.',
            'lieu.required' => 'Le lieu est obligatoire.',
            'type_intervention.required' => 'Le type d\'intervention est obligatoire.',
            'actions.required' => 'La description des actions est obligatoire.',
            'resultats.required' => 'La description des résultats est obligatoire.',
            'pieces_jointes.*.mimes' => 'Les fichiers doivent être de type: jpg, jpeg, png, pdf, doc, docx.',
            'pieces_jointes.*.max' => 'Chaque fichier ne doit pas dépasser 5 MB.'
        ]);

        // Vérifier les permissions pour la tâche/événement
        if (isset($validatedData['id_tache'])) {
            $task = Task::findOrFail($validatedData['id_tache']);
            if (Auth::user()->role !== 'admin' && $task->id_utilisateur !== Auth::id()) {
                abort(403, 'Vous ne pouvez créer un rapport que pour vos propres tâches.');
            }
        }

        if (isset($validatedData['id_evenement'])) {
            $event = Event::findOrFail($validatedData['id_evenement']);
            if (Auth::user()->role !== 'admin' &&
                $event->id_organisateur !== Auth::id() &&
                !$event->participants->contains('id_utilisateur', Auth::id())) {
                abort(403, 'Vous ne pouvez créer un rapport que pour vos propres événements.');
            }
        }

        $validatedData['id_utilisateur'] = Auth::id();

        try {
            DB::beginTransaction();

            $report = Report::create($validatedData);

            // Traiter les pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('reports/' . $report->id, $filename, 'public');

                    PieceJointe::create([
                        'nom_fichier' => $file->getClientOriginalName(),
                        'type_fichier' => $file->getClientMimeType(),
                        'taille' => $file->getSize(),
                        'chemin' => $path,
                        'id_rapport' => $report->id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('reports.index')
                            ->with('success', 'Rapport d\'intervention créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la création du rapport.']);
        }
    }

    /**
     * Afficher un rapport spécifique
     */
    public function show(Report $report)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez voir que vos propres rapports.');
        }

        $report->load(['utilisateur', 'tache.projet', 'evenement', 'piecesJointes']);

        return view('reports.show', compact('report'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Report $report)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres rapports.');
        }

        // Empêcher la modification des rapports anciens (plus de 48h)
        if ($report->date_creation->diffInHours(now()) > 48 && Auth::user()->role !== 'admin') {
            abort(403, 'Vous ne pouvez modifier un rapport que dans les 48h suivant sa création.');
        }

        $tasks = Task::where('id_utilisateur', Auth::id())->get();
        $events = Event::where('id_organisateur', Auth::id())
                      ->orWhereHas('participants', function($q) {
                          $q->where('id_utilisateur', Auth::id());
                      })
                      ->get();

        return view('reports.edit', compact('report', 'tasks', 'events'));
    }

    /**
     * Mettre à jour un rapport
     */
    public function update(Request $request, Report $report)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres rapports.');
        }

        // Empêcher la modification des rapports anciens
        if ($report->date_creation->diffInHours(now()) > 48 && Auth::user()->role !== 'admin') {
            abort(403, 'Vous ne pouvez modifier un rapport que dans les 48h suivant sa création.');
        }

        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'date_intervention' => 'required|date|before_or_equal:today',
            'lieu' => 'required|string|max:255',
            'type_intervention' => 'required|string|max:100',
            'actions' => 'required|string',
            'resultats' => 'required|string',
            'problemes' => 'nullable|string',
            'recommandations' => 'nullable|string',
            'id_tache' => 'nullable|exists:taches,id',
            'id_evenement' => 'nullable|exists:evenements,id',
            'pieces_jointes.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $report->update($validatedData);

            // Traiter les nouvelles pièces jointes
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('reports/' . $report->id, $filename, 'public');

                    PieceJointe::create([
                        'nom_fichier' => $file->getClientOriginalName(),
                        'type_fichier' => $file->getClientMimeType(),
                        'taille' => $file->getSize(),
                        'chemin' => $path,
                        'id_rapport' => $report->id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('reports.index')
                            ->with('success', 'Rapport mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du rapport.']);
        }
    }

    /**
     * Supprimer un rapport
     */
    public function destroy(Report $report)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres rapports.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les fichiers physiques
            foreach ($report->piecesJointes as $pieceJointe) {
                Storage::disk('public')->delete($pieceJointe->chemin);
                $pieceJointe->delete();
            }

            // Supprimer le dossier s'il est vide
            Storage::disk('public')->deleteDirectory('reports/' . $report->id);

            $report->delete();

            DB::commit();

            return redirect()->route('reports.index')
                            ->with('success', 'Rapport supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erreur lors de la suppression du rapport.']);
        }
    }

    /**
     * Télécharger une pièce jointe
     */
    
    public function downloadAttachment(PieceJointe $pieceJointe)
    {
        // Vérifier les permissions
        $report = $pieceJointe->rapport;
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez télécharger que vos propres fichiers.');
        }

        if (!Storage::disk('public')->exists($pieceJointe->chemin)) {
            abort(404, 'Fichier non trouvé.');
        }

        // Obtenir le chemin complet du fichier
        $filePath = Storage::disk('public')->path($pieceJointe->chemin);

        // Vérifier que le fichier existe physiquement
        if (!file_exists($filePath)) {
            abort(404, 'Fichier non trouvé sur le serveur.');
        }

        // Retourner le téléchargement du fichier
        return response()->download($filePath, $pieceJointe->nom_fichier, [
            'Content-Type' => $pieceJointe->type_fichier,
        ]);
    }


    /**
     * Supprimer une pièce jointe
     */
    public function deleteAttachment(PieceJointe $pieceJointe)
    {
        // Vérifier les permissions
        $report = $pieceJointe->rapport;
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres fichiers.');
        }

        try {
            Storage::disk('public')->delete($pieceJointe->chemin);
            $pieceJointe->delete();

            return response()->json(['success' => true, 'message' => 'Pièce jointe supprimée.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
        }
    }

    /**
     * Exporter un rapport en PDF
     */
    public function exportPdf(Report $report)
    {
        // Vérifier les permissions
        if (Auth::user()->role !== 'admin' && $report->id_utilisateur !== Auth::id()) {
            abort(403, 'Vous ne pouvez exporter que vos propres rapports.');
        }

        $report->load(['utilisateur', 'tache.projet', 'evenement', 'piecesJointes']);

        // Générer le contenu HTML pour le PDF
        $html = view('reports.pdf', compact('report'))->render();

        // Pour cette version, nous retournons le HTML
        // Dans un vrai projet, vous utiliseriez une bibliothèque PDF comme dompdf
        $filename = 'rapport_' . $report->id . '_' . Carbon::now()->format('Y-m-d') . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Exporter plusieurs rapports
     */
    public function exportMultiple(Request $request)
    {
        $reportIds = $request->input('report_ids', []);

        if (empty($reportIds)) {
            return back()->withErrors(['error' => 'Aucun rapport sélectionné.']);
        }

        $query = Report::with(['utilisateur', 'tache.projet', 'evenement'])
                      ->whereIn('id', $reportIds);

        // Filtrer selon les permissions
        if (Auth::user()->role !== 'admin') {
            $query->where('id_utilisateur', Auth::id());
        }

        $reports = $query->get();

        if ($reports->isEmpty()) {
            return back()->withErrors(['error' => 'Aucun rapport trouvé ou autorisé.']);
        }

        // Générer le contenu HTML pour les rapports multiples
        $html = view('reports.multiple-pdf', compact('reports'))->render();

        $filename = 'rapports_' . Carbon::now()->format('Y-m-d_H-i-s') . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Statistiques des rapports
     */
    public function statistics(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Seuls les administrateurs peuvent voir les statistiques.');
        }

        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $stats = [
            'total_reports' => Report::whereBetween('date_intervention', [$startDate, $endDate])->count(),
            'reports_by_month' => Report::whereBetween('date_intervention', [$startDate, $endDate])
                                       ->selectRaw('YEAR(date_intervention) as year, MONTH(date_intervention) as month, COUNT(*) as count')
                                       ->groupBy('year', 'month')
                                       ->orderBy('year')
                                       ->orderBy('month')
                                       ->get(),
            'reports_by_type' => Report::whereBetween('date_intervention', [$startDate, $endDate])
                                      ->selectRaw('type_intervention, COUNT(*) as count')
                                      ->groupBy('type_intervention')
                                      ->get(),
            'reports_by_user' => Report::with('utilisateur')
                                      ->whereBetween('date_intervention', [$startDate, $endDate])
                                      ->selectRaw('id_utilisateur, COUNT(*) as count')
                                      ->groupBy('id_utilisateur')
                                      ->get(),
            'top_locations' => Report::whereBetween('date_intervention', [$startDate, $endDate])
                                    ->selectRaw('lieu, COUNT(*) as count')
                                    ->groupBy('lieu')
                                    ->orderByDesc('count')
                                    ->limit(10)
                                    ->get()
        ];

        return view('reports.statistics', compact('stats', 'startDate', 'endDate'));
    }
}
