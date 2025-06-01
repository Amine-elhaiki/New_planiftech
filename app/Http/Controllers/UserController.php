<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Task;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Models\Report;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Middleware personnalisé pour vérifier le rôle admin
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Ajouter les statistiques pour chaque utilisateur
        foreach ($users as $user) {
            $user->stats = [
                'tasks_total' => Task::where('id_utilisateur', $user->id)->count(),
                'tasks_completed' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
                'events_organized' => Event::where('id_organisateur', $user->id)->count(),
                'projects_responsible' => Project::where('id_responsable', $user->id)->count(),
                'reports_submitted' => Report::where('id_utilisateur', $user->id)->count()
            ];
        }

        // Statistiques générales
        $globalStats = [
            'total_users' => User::count(),
            'active_users' => User::where('statut', 'actif')->count(),
            'inactive_users' => User::where('statut', 'inactif')->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'technician_users' => User::where('role', 'technicien')->count()
        ];

        return view('users.index', compact('users', 'globalStats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,technicien',
            'statut' => 'required|in:actif,inactif'
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher un utilisateur spécifique
     */
    public function show(User $user)
    {
        // Charger les relations
        $user->load([
            'taches' => function($query) {
                $query->latest()->limit(10);
            },
            'evenementsOrganises' => function($query) {
                $query->latest()->limit(5);
            },
            'projetsResponsable' => function($query) {
                $query->latest()->limit(5);
            },
            'rapports' => function($query) {
                $query->latest()->limit(5);
            }
        ]);

        // Statistiques détaillées
        $detailedStats = [
            'tasks' => [
                'total' => Task::where('id_utilisateur', $user->id)->count(),
                'a_faire' => Task::where('id_utilisateur', $user->id)->where('statut', 'a_faire')->count(),
                'en_cours' => Task::where('id_utilisateur', $user->id)->where('statut', 'en_cours')->count(),
                'termine' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
                'en_retard' => Task::where('id_utilisateur', $user->id)
                                  ->where('date_echeance', '<', now())
                                  ->whereIn('statut', ['a_faire', 'en_cours'])
                                  ->count()
            ],
            'events' => [
                'organized' => Event::where('id_organisateur', $user->id)->count(),
                'participated' => Event::whereHas('participants', function($q) use ($user) {
                    $q->where('id_utilisateur', $user->id);
                })->count()
            ],
            'projects' => [
                'responsible' => Project::where('id_responsable', $user->id)->count(),
                'involved' => Project::whereHas('taches', function($q) use ($user) {
                    $q->where('id_utilisateur', $user->id);
                })->distinct()->count()
            ],
            'reports' => [
                'total' => Report::where('id_utilisateur', $user->id)->count(),
                'this_month' => Report::where('id_utilisateur', $user->id)
                                    ->whereMonth('created_at', now()->month)
                                    ->count()
            ]
        ];

        // Activité récente
        $recentActivity = $this->getUserRecentActivity($user);

        return view('users.show', compact('user', 'detailedStats', 'recentActivity'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($user->id)
            ],
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,technicien',
            'statut' => 'required|in:actif,inactif'
        ]);

        $user->update($validatedData);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe d'un utilisateur
     */
    public function updatePassword(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(User $user)
    {
        // Empêcher la désactivation de son propre compte
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas modifier votre propre statut.']);
        }

        $newStatus = $user->statut === 'actif' ? 'inactif' : 'actif';
        $user->update(['statut' => $newStatus]);

        $message = $newStatus === 'actif' ? 'Utilisateur activé avec succès.' : 'Utilisateur désactivé avec succès.';

        return back()->with('success', $message);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        // Vérifier s'il y a des données liées
        $hasData = Task::where('id_utilisateur', $user->id)->exists() ||
                   Event::where('id_organisateur', $user->id)->exists() ||
                   Project::where('id_responsable', $user->id)->exists() ||
                   Report::where('id_utilisateur', $user->id)->exists();

        if ($hasData) {
            return back()->withErrors(['error' => 'Impossible de supprimer un utilisateur ayant des données associées. Désactivez-le plutôt.']);
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Exporter la liste des utilisateurs
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $users = $query->get();

        $csv = "Nom,Prénom,Email,Téléphone,Rôle,Statut,Date de création\n";

        foreach ($users as $user) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $user->nom,
                $user->prenom,
                $user->email,
                $user->telephone ?? '',
                $user->role,
                $user->statut,
                $user->created_at->format('d/m/Y')
            );
        }

        $filename = 'utilisateurs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Obtenir l'activité récente d'un utilisateur
     */
    private function getUserRecentActivity(User $user, $limit = 15)
    {
        $activities = collect();

        // Tâches récentes
        $recentTasks = Task::where('id_utilisateur', $user->id)
                          ->latest('updated_at')
                          ->limit($limit)
                          ->get()
                          ->map(function($task) {
                              return [
                                  'type' => 'task',
                                  'action' => 'Tâche mise à jour',
                                  'description' => $task->titre,
                                  'status' => $task->statut,
                                  'date' => $task->updated_at,
                                  'icon' => 'bi-list-check',
                                  'color' => 'primary'
                              ];
                          });

        // Événements organisés récents
        $recentEvents = Event::where('id_organisateur', $user->id)
                            ->latest('updated_at')
                            ->limit($limit)
                            ->get()
                            ->map(function($event) {
                                return [
                                    'type' => 'event',
                                    'action' => 'Événement organisé',
                                    'description' => $event->titre,
                                    'status' => $event->statut,
                                    'date' => $event->updated_at,
                                    'icon' => 'bi-calendar-event',
                                    'color' => 'success'
                                ];
                            });

        // Rapports récents
        $recentReports = Report::where('id_utilisateur', $user->id)
                              ->latest('created_at')
                              ->limit($limit)
                              ->get()
                              ->map(function($report) {
                                  return [
                                      'type' => 'report',
                                      'action' => 'Rapport soumis',
                                      'description' => $report->titre,
                                      'status' => 'soumis',
                                      'date' => $report->created_at,
                                      'icon' => 'bi-file-text',
                                      'color' => 'info'
                                  ];
                              });

        return $activities->concat($recentTasks)
                         ->concat($recentEvents)
                         ->concat($recentReports)
                         ->sortByDesc('date')
                         ->take($limit)
                         ->values();
    }

    /**
     * Statistiques globales des utilisateurs
     */
    public function statistics()
    {
        $stats = [
            'users_by_role' => User::selectRaw('role, COUNT(*) as count')
                                  ->groupBy('role')
                                  ->pluck('count', 'role'),

            'users_by_status' => User::selectRaw('statut, COUNT(*) as count')
                                    ->groupBy('statut')
                                    ->pluck('count', 'statut'),

            'registrations_by_month' => User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                           ->groupBy('year', 'month')
                                           ->orderBy('year')
                                           ->orderBy('month')
                                           ->get(),

            'most_active_users' => User::withCount(['taches', 'rapports'])
                                      ->orderByDesc('taches_count')
                                      ->limit(10)
                                      ->get(),

            'user_activity_summary' => [
                'total_tasks' => Task::count(),
                'total_events' => Event::count(),
                'total_reports' => Report::count(),
                'total_projects' => Project::count()
            ]
        ];

        return view('users.statistics', compact('stats'));
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(User $user)
    {
        $temporaryPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        // Dans un vrai projet, vous enverriez le mot de passe par email
        // Pour la démo, on l'affiche dans le message de succès

        return back()->with('success', "Mot de passe réinitialisé. Nouveau mot de passe temporaire : {$temporaryPassword}");
    }

    /**
     * Recherche d'utilisateurs (API)
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('statut', 'actif')
                    ->where(function($q) use ($query) {
                        $q->where('nom', 'like', "%{$query}%")
                          ->orWhere('prenom', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->limit(10)
                    ->get(['id', 'nom', 'prenom', 'email', 'role']);

        return response()->json($users->map(function($user) {
            return [
                'id' => $user->id,
                'text' => $user->prenom . ' ' . $user->nom . ' (' . $user->email . ')',
                'role' => $user->role
            ];
        }));
    }
}
