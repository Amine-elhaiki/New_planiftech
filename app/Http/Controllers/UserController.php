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
        // Supprimer le middleware admin global - on le gère méthode par méthode
    }

    /**
     * Afficher la liste des utilisateurs
     * Accessible aux admins ET techniciens (lecture seule pour techniciens)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
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

        // Si technicien, limiter aux utilisateurs actifs seulement
        if ($user->role === 'technicien') {
            $query->where('statut', 'actif');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('statut') && $user->role === 'admin') {
            $query->where('statut', $request->input('statut'));
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // Ajouter les statistiques pour chaque utilisateur (seulement pour admin)
        if ($user->role === 'admin') {
            foreach ($users as $userItem) {
                $userItem->stats = [
                    'tasks_total' => Task::where('id_utilisateur', $userItem->id)->count(),
                    'tasks_completed' => Task::where('id_utilisateur', $userItem->id)->where('statut', 'termine')->count(),
                    'events_organized' => Event::where('id_organisateur', $userItem->id)->count(),
                    'projects_responsible' => Project::where('id_responsable', $userItem->id)->count(),
                    'reports_submitted' => Report::where('id_utilisateur', $userItem->id)->count()
                ];
            }
        }

        // Statistiques générales (différentes selon le rôle)
        if ($user->role === 'admin') {
            $globalStats = [
                'total_users' => User::count(),
                'active_users' => User::where('statut', 'actif')->count(),
                'inactive_users' => User::where('statut', 'inactif')->count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'technician_users' => User::where('role', 'technicien')->count()
            ];
        } else {
            $globalStats = [
                'total_users' => User::where('statut', 'actif')->count(),
                'active_users' => User::where('statut', 'actif')->count(),
                'technician_users' => User::where('role', 'technicien')->where('statut', 'actif')->count(),
                'admin_users' => User::where('role', 'admin')->where('statut', 'actif')->count()
            ];
        }

        // Vue différente selon le rôle
        $viewName = $user->role === 'admin' ? 'users.index' : 'users.index-readonly';

        return view($viewName, compact('users', 'globalStats'));
    }

    /**
     * Afficher le formulaire de création
     * ADMIN SEULEMENT
     */
    public function create()
    {
        $this->ensureAdmin();
        return view('users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     * ADMIN SEULEMENT
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

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
        $validatedData['date_creation'] = now();
        $validatedData['email_verified_at'] = now();

        $user = User::create($validatedData);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher un utilisateur spécifique
     * Accessible aux admins ET techniciens
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();

        // Si technicien, peut seulement voir son propre profil ou autres utilisateurs actifs
        if ($currentUser->role === 'technicien') {
            if ($user->id !== $currentUser->id && $user->statut !== 'actif') {
                abort(403, 'Accès non autorisé.');
            }
        }

        // Charger les relations (limitées pour techniciens)
        if ($currentUser->role === 'admin') {
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

            // Statistiques détaillées pour admin
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
                    'participated' => 0
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
                                        ->whereMonth('date_creation', now()->month)
                                        ->count()
                ]
            ];

            $recentActivity = $this->getUserRecentActivity($user);
            $viewName = 'users.show';
        } else {
            // Vue simplifiée pour techniciens
            $detailedStats = [
                'tasks' => [
                    'total' => Task::where('id_utilisateur', $user->id)->count(),
                    'termine' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
                ],
                'reports' => [
                    'total' => Report::where('id_utilisateur', $user->id)->count(),
                ]
            ];
            $recentActivity = [];
            $viewName = 'users.show-readonly';
        }

        return view($viewName, compact('user', 'detailedStats', 'recentActivity'));
    }

    /**
     * Afficher le formulaire d'édition
     * ADMIN SEULEMENT
     */
    public function edit(User $user)
    {
        $this->ensureAdmin();
        return view('users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     * ADMIN SEULEMENT
     */
    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

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
     * ADMIN SEULEMENT
     */
    public function updatePassword(Request $request, User $user)
    {
        $this->ensureAdmin();

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
     * ADMIN SEULEMENT
     */
    public function toggleStatus(User $user)
    {
        $this->ensureAdmin();

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
     * ADMIN SEULEMENT
     */
    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

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
     * ADMIN SEULEMENT
     */
    public function export(Request $request)
    {
        $this->ensureAdmin();

        $query = User::query();

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
     * Recherche d'utilisateurs (API)
     * Accessible aux admins ET techniciens
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $userQuery = User::where('statut', 'actif')
                        ->where(function($q) use ($query) {
                            $q->where('nom', 'like', "%{$query}%")
                              ->orWhere('prenom', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%");
                        })
                        ->limit(10);

        $users = $userQuery->get(['id', 'nom', 'prenom', 'email', 'role']);

        return response()->json($users->map(function($user) {
            return [
                'id' => $user->id,
                'text' => $user->prenom . ' ' . $user->nom . ' (' . $user->email . ')',
                'role' => $user->role
            ];
        }));
    }

    /**
     * Statistiques globales des utilisateurs
     * ADMIN SEULEMENT
     */
    public function statistics()
    {
        $this->ensureAdmin();

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
     * ADMIN SEULEMENT
     */
    public function resetPassword(User $user)
    {
        $this->ensureAdmin();

        $temporaryPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($temporaryPassword)
        ]);

        return back()->with('success', "Mot de passe réinitialisé. Nouveau mot de passe temporaire : {$temporaryPassword}");
    }

    /**
     * Méthode privée pour vérifier si l'utilisateur est admin
     */
    private function ensureAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.');
        }
    }

    /**
     * Obtenir l'activité récente d'un utilisateur
     */
    private function getUserRecentActivity(User $user, $limit = 15)
    {
        $activities = collect();

        $recentTasks = Task::where('id_utilisateur', $user->id)
                          ->latest('date_modification')
                          ->limit($limit)
                          ->get()
                          ->map(function($task) {
                              return [
                                  'type' => 'task',
                                  'action' => 'Tâche mise à jour',
                                  'description' => $task->titre,
                                  'status' => $task->statut,
                                  'date' => $task->date_modification,
                                  'icon' => 'bi-list-check',
                                  'color' => 'primary'
                              ];
                          });

        $recentEvents = Event::where('id_organisateur', $user->id)
                            ->latest('date_modification')
                            ->limit($limit)
                            ->get()
                            ->map(function($event) {
                                return [
                                    'type' => 'event',
                                    'action' => 'Événement organisé',
                                    'description' => $event->titre,
                                    'status' => $event->statut,
                                    'date' => $event->date_modification,
                                    'icon' => 'bi-calendar-event',
                                    'color' => 'success'
                                ];
                            });

        $recentReports = Report::where('id_utilisateur', $user->id)
                              ->latest('date_creation')
                              ->limit($limit)
                              ->get()
                              ->map(function($report) {
                                  return [
                                      'type' => 'report',
                                      'action' => 'Rapport soumis',
                                      'description' => $report->titre,
                                      'status' => 'soumis',
                                      'date' => $report->date_creation,
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
}
