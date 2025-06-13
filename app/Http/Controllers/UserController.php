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
            'role' => 'required|in:admin,technicien,chef_projet',
            'statut' => 'required|in:actif,inactif'
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 100 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 100 caractères.',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.'
        ]);

        // Hash du mot de passe
        $validatedData['password'] = Hash::make($validatedData['password']);
        
        // Définir la date de vérification email
        $validatedData['email_verified_at'] = now();

        // Créer l'utilisateur
        $user = User::create($validatedData);

        // Créer une notification pour l'utilisateur
        if (class_exists('App\Models\Notification')) {
            \App\Models\Notification::create([
                'titre' => 'Bienvenue sur PlanifTech',
                'message' => 'Votre compte a été créé avec succès. Bienvenue dans l\'équipe PlanifTech ORMVAT !',
                'type' => 'systeme',
                'destinataire_id' => $user->id,
                'lue' => false,
            ]);
        }

        // Enregistrer l'action dans les logs
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerCreation(
                'utilisateur', 
                $user->prenom . ' ' . $user->nom . ' (' . $user->email . ') - Rôle: ' . $user->role_libelle
            );
        }

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur "' . $user->prenom . ' ' . $user->nom . '" créé avec succès.');
    }

    /**
     * Afficher un utilisateur spécifique
     * Accessible aux admins ET techniciens
     */
    // public function show(User $user)
    // {
    //     $currentUser = Auth::user();

    //     // Si technicien, peut seulement voir son propre profil ou autres utilisateurs actifs
    //     if ($currentUser->role === 'technicien') {
    //         if ($user->id !== $currentUser->id && $user->statut !== 'actif') {
    //             abort(403, 'Accès non autorisé.');
    //         }
    //     }

    //     // Charger les relations (limitées pour techniciens)
    //     // if ($currentUser->role === 'admin') {
    //     //     $user->load([
    //     //         'taches' => function($query) {
    //     //             $query->latest()->limit(10);
    //     //         },
    //     //         'evenementsOrganises' => function($query) {
    //     //             $query->latest()->limit(5);
    //     //         },
    //     //         'projetsResponsable' => function($query) {
    //     //             $query->latest()->limit(5);
    //     //         },
    //     //         'rapports' => function($query) {
    //     //             $query->latest()->limit(5);
    //     //         }
    //     //     ]);

    //     //     // Statistiques détaillées pour admin
    //     //     $detailedStats = [
    //     //         'tasks' => [
    //     //             'total' => Task::where('id_utilisateur', $user->id)->count(),
    //     //             'a_faire' => Task::where('id_utilisateur', $user->id)->where('statut', 'a_faire')->count(),
    //     //             'en_cours' => Task::where('id_utilisateur', $user->id)->where('statut', 'en_cours')->count(),
    //     //             'termine' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
    //     //             'en_retard' => Task::where('id_utilisateur', $user->id)
    //     //                               ->where('date_echeance', '<', now())
    //     //                               ->whereIn('statut', ['a_faire', 'en_cours'])
    //     //                               ->count()
    //     //         ],
    //     //         'events' => [
    //     //             'organized' => Event::where('id_organisateur', $user->id)->count(),
    //     //             'participated' => 0
    //     //         ],
    //     //         'projects' => [
    //     //             'responsible' => Project::where('id_responsable', $user->id)->count(),
    //     //             'involved' => Project::whereHas('taches', function($q) use ($user) {
    //     //                 $q->where('id_utilisateur', $user->id);
    //     //             })->distinct()->count()
    //     //         ],
    //     //         'reports' => [
    //     //             'total' => Report::where('id_utilisateur', $user->id)->count(),
    //     //             'this_month' => Report::where('id_utilisateur', $user->id)
    //     //                                 ->whereMonth('date_creation', now()->month)
    //     //                                 ->count()
    //     //         ]
    //     //     ];

    //     //     $recentActivity = $this->getUserRecentActivity($user);
    //     //     $viewName = 'users.show';
    //     // } else {
    //     //     // Vue simplifiée pour techniciens
    //     //     $detailedStats = [
    //     //         'tasks' => [
    //     //             'total' => Task::where('id_utilisateur', $user->id)->count(),
    //     //             'termine' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
    //     //         ],
    //     //         'reports' => [
    //     //             'total' => Report::where('id_utilisateur', $user->id)->count(),
    //     //         ]
    //     //     ];
    //     //     $recentActivity = [];
    //     //     $viewName = 'users.show-readonly';
    //     // }

    //     // return view($viewName, compact('user', 'detailedStats', 'recentActivity'));

    //      // Statistiques détaillées
    //     $detailedStats = [
    //         'tasks' => [
    //             'total' => Task::where('id_utilisateur', $user->id)->count(),
    //             'a_faire' => Task::where('id_utilisateur', $user->id)->where('statut', 'a_faire')->count(),
    //             'en_cours' => Task::where('id_utilisateur', $user->id)->where('statut', 'en_cours')->count(),
    //             'termine' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
    //             'en_retard' => Task::where('id_utilisateur', $user->id)
    //                               ->where('date_echeance', '<', now())
    //                               ->whereIn('statut', ['a_faire', 'en_cours'])
    //                               ->count()
    //         ],
    //         'events' => [
    //             'organized' => Event::where('id_organisateur', $user->id)->count(),
    //             'participated' => 0
    //         ],
    //         'projects' => [
    //             'responsible' => Project::where('id_responsable', $user->id)->count(),
    //             'involved' => Project::whereHas('taches', function($q) use ($user) {
    //                 $q->where('id_utilisateur', $user->id);
    //             })->distinct()->count()
    //         ],
    //         'reports' => [
    //             'total' => Report::where('id_utilisateur', $user->id)->count(),
    //             'this_month' => Report::where('id_utilisateur', $user->id)
    //                                 ->whereMonth('created_at', now()->month)
    //                                 ->count()
    //         ]
    //     ];

    //     $recentTasks = Task::where('id_utilisateur', $user->id)
    //                       ->latest()
    //                       ->limit(5)
    //                       ->get();

    //     $recentReports = Report::where('id_utilisateur', $user->id)
    //                           ->latest()
    //                           ->limit(5)
    //                           ->get();

    //     return view('users.show', compact('user', 'detailedStats', 'recentTasks', 'recentReports'));

    // }

public function show(User $user)
{
    $currentUser = Auth::user();

    // Si technicien, peut seulement voir son propre profil ou autres utilisateurs actifs
    if ($currentUser->role === 'technicien') {
        if ($user->id !== $currentUser->id && $user->statut !== 'actif') {
            abort(403, 'Accès non autorisé.');
        }
    }

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
                                ->whereMonth('created_at', now()->month)
                                ->count()
        ]
    ];

    $recentTasks = Task::where('id_utilisateur', $user->id)
                      ->latest()
                      ->limit(5)
                      ->get();

    $recentReports = Report::where('id_utilisateur', $user->id)
                          ->latest()
                          ->limit(5)
                          ->get();

    return view('users.show', compact('user', 'detailedStats', 'recentTasks', 'recentReports'));
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
        'role' => 'required|in:admin,technicien,chef_projet',
        'statut' => 'required|in:actif,inactif'
    ], [
        'nom.required' => 'Le nom est obligatoire.',
        'prenom.required' => 'Le prénom est obligatoire.',
        'email.required' => 'L\'adresse email est obligatoire.',
        'email.email' => 'L\'adresse email doit être valide.',
        'email.unique' => 'Cette adresse email est déjà utilisée.',
        'role.required' => 'Le rôle est obligatoire.',
        'role.in' => 'Le rôle sélectionné n\'est pas valide.',
        'statut.required' => 'Le statut est obligatoire.',
        'statut.in' => 'Le statut sélectionné n\'est pas valide.'
    ]);

    // Empêcher la modification de son propre rôle/statut
    if ($user->id === auth()->id()) {
        unset($validatedData['role'], $validatedData['statut']);
    }

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

    // Empêcher la suppression de son propre compte
    if ($user->id === auth()->id()) {
        return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
    }

    // Vérifier si l'utilisateur peut être supprimé (n'a pas de données associées critiques)
    $hasData = $this->userHasAssociatedData($user);
    
    if ($hasData['has_data']) {
        return back()->withErrors([
            'error' => 'Impossible de supprimer cet utilisateur car il a des données associées : ' . 
                      implode(', ', $hasData['data_types']) . '. ' .
                      'Vous devez d\'abord supprimer ou réassigner ces éléments.'
        ]);
    }

    try {
        // Sauvegarder les informations pour les logs
        $userName = $user->prenom . ' ' . $user->nom;
        $userEmail = $user->email;
        $userRole = $user->role_libelle ?? ucfirst(str_replace('_', ' ', $user->role));
        
        // Démarrer une transaction pour assurer la cohérence
        \DB::transaction(function () use ($user) {
            
            // 1. Supprimer les notifications de l'utilisateur
            if (class_exists('App\Models\Notification')) {
                \App\Models\Notification::where('destinataire_id', $user->id)->delete();
            }
            
            // 2. Supprimer les entrées de journal où l'utilisateur est l'auteur
            if (class_exists('App\Models\Journal')) {
                \App\Models\Journal::where('utilisateur_id', $user->id)->delete();
            }
            
            // 3. Supprimer les participations aux événements
            if (class_exists('App\Models\ParticipantEvent')) {
                \App\Models\ParticipantEvent::where('id_utilisateur', $user->id)->delete();
            }
            
            // 4. Supprimer les pièces jointes des rapports de l'utilisateur
            if (class_exists('App\Models\PieceJointe')) {
                $rapportIds = \App\Models\Report::where('id_utilisateur', $user->id)->pluck('id');
                $piecesJointes = \App\Models\PieceJointe::whereIn('id_rapport', $rapportIds)->get();
                
                foreach ($piecesJointes as $pieceJointe) {
                    $pieceJointe->supprimer(); // Supprime le fichier et l'enregistrement
                }
            }
            
            // 5. Supprimer l'utilisateur (les contraintes ON DELETE devraient gérer le reste)
            $user->delete();
        });

        // Enregistrer l'action dans les logs (avec l'admin qui a fait la suppression)
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerSuppression(
                'utilisateur', 
                "{$userName} ({$userEmail}) - Rôle: {$userRole}"
            );
        }

        return redirect()->route('users.index')
                        ->with('success', "L'utilisateur \"{$userName}\" a été supprimé avec succès.");
                        
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
        
        return back()->withErrors([
            'error' => 'Erreur lors de la suppression : ' . $e->getMessage()
        ]);
    }
}


/**
 * Vérifier si un utilisateur a des données associées critiques
 */
private function userHasAssociatedData(User $user)
{
    $dataTypes = [];
    $hasData = false;

    // Vérifier les tâches assignées
    if (\App\Models\Task::where('id_utilisateur', $user->id)->exists()) {
        $dataTypes[] = 'tâches assignées';
        $hasData = true;
    }

    // Vérifier les projets en tant que responsable
    if (\App\Models\Project::where('id_responsable', $user->id)->exists()) {
        $dataTypes[] = 'projets (responsable)';
        $hasData = true;
    }

    // Vérifier les événements organisés
    if (\App\Models\Event::where('id_organisateur', $user->id)->exists()) {
        $dataTypes[] = 'événements organisés';
        $hasData = true;
    }

    // Vérifier les rapports importants (derniers 30 jours)
    $recentReports = \App\Models\Report::where('id_utilisateur', $user->id)
                                      ->where('date_creation', '>=', now()->subDays(30))
                                      ->exists();
    if ($recentReports) {
        $dataTypes[] = 'rapports récents';
        $hasData = true;
    }

    return [
        'has_data' => $hasData,
        'data_types' => $dataTypes
    ];
}

/**
 * Forcer la suppression d'un utilisateur (avec réassignation)
 * ADMIN SEULEMENT - Fonction avancée
 */
public function forceDestroy(User $user, Request $request)
{
    $this->ensureAdmin();

    // Empêcher la suppression de son propre compte
    if ($user->id === auth()->id()) {
        return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
    }

    $validatedData = $request->validate([
        'reassign_to' => 'nullable|exists:users,id',
        'confirm_deletion' => 'required|accepted'
    ]);

    try {
        \DB::transaction(function () use ($user, $validatedData) {
            $reassignToUserId = $validatedData['reassign_to'] ?? null;
            
            // Réassigner les données si un utilisateur de remplacement est spécifié
            if ($reassignToUserId) {
                // Réassigner les tâches
                \App\Models\Task::where('id_utilisateur', $user->id)
                                ->update(['id_utilisateur' => $reassignToUserId]);
                
                // Réassigner les projets
                \App\Models\Project::where('id_responsable', $user->id)
                                  ->update(['id_responsable' => $reassignToUserId]);
                
                // Réassigner les événements
                \App\Models\Event::where('id_organisateur', $user->id)
                                 ->update(['id_organisateur' => $reassignToUserId]);
                
                // Réassigner les rapports
                \App\Models\Report::where('id_utilisateur', $user->id)
                                  ->update(['id_utilisateur' => $reassignToUserId]);
            } else {
                // Supprimer les données non critiques
                \App\Models\Task::where('id_utilisateur', $user->id)->delete();
                \App\Models\Report::where('id_utilisateur', $user->id)->delete();
                
                // Marquer les projets et événements comme "sans responsable"
                \App\Models\Project::where('id_responsable', $user->id)
                                  ->update(['id_responsable' => null]);
                \App\Models\Event::where('id_organisateur', $user->id)
                                 ->update(['statut' => 'annule']);
            }
            
            // Supprimer les données personnelles
            $this->cleanUserPersonalData($user);
            
            // Supprimer l'utilisateur
            $user->delete();
        });

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur supprimé avec succès. Les données ont été réassignées.');
                        
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Erreur lors de la suppression forcée : ' . $e->getMessage()]);
    }
}

/**
 * Nettoyer les données personnelles d'un utilisateur
 */
private function cleanUserPersonalData(User $user)
{
    // Supprimer les notifications
    if (class_exists('App\Models\Notification')) {
        \App\Models\Notification::where('destinataire_id', $user->id)->delete();
    }
    
    // Supprimer les participations aux événements
    if (class_exists('App\Models\ParticipantEvent')) {
        \App\Models\ParticipantEvent::where('id_utilisateur', $user->id)->delete();
    }
    
    // Anonymiser les entrées de journal (garder pour audit)
    if (class_exists('App\Models\Journal')) {
        \App\Models\Journal::where('utilisateur_id', $user->id)
                          ->update([
                              'utilisateur_id' => null,
                              'description' => \DB::raw("CONCAT('[UTILISATEUR SUPPRIMÉ] ', description)")
                          ]);
    }
}

/**
 * Désactiver un utilisateur au lieu de le supprimer (alternative recommandée)
 */
public function deactivate(User $user)
{
    $this->ensureAdmin();

    if ($user->id === auth()->id()) {
        return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
    }

    try {
        $userName = $user->prenom . ' ' . $user->nom;
        
        $user->update([
            'statut' => 'inactif',
            'email' => $user->email . '_DISABLED_' . now()->timestamp,
            'password' => \Hash::make(\Str::random(32)) // Mot de passe aléatoire
        ]);

        // Enregistrer l'action
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerModification(
                'utilisateur', 
                'Désactivation définitive de : ' . $userName
            );
        }

        return redirect()->route('users.index')
                        ->with('success', "L'utilisateur \"{$userName}\" a été désactivé avec succès.");
        
    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Erreur lors de la désactivation : ' . $e->getMessage()]);
    }
}

/**
 * Exporter les utilisateurs en CSV avec colonnes correctement séparées
 */
public function export(Request $request)
{
    $this->ensureAdmin();

    $query = User::query();

    // Appliquer les filtres
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query->get();

    // Créer le contenu CSV avec séparateurs corrects
    $csvContent = [];
    
    // En-tête du CSV
    $csvContent[] = [
        'Nom',
        'Prénom', 
        'Email',
        'Téléphone',
        'Rôle',
        'Statut',
        'Date de création',
        'Dernière mise à jour'
    ];

    // Données des utilisateurs
    foreach ($users as $user) {
        $csvContent[] = [
            $this->escapeCsvField($user->nom),
            $this->escapeCsvField($user->prenom),
            $this->escapeCsvField($user->email),
            $this->escapeCsvField($user->telephone ?? 'Non renseigné'),
            $this->escapeCsvField($this->getRoleLibelle($user->role)),
            $this->escapeCsvField($user->statut === 'actif' ? 'Actif' : 'Inactif'),
            $this->escapeCsvField($user->created_at->format('d/m/Y H:i')),
            $this->escapeCsvField($user->updated_at->format('d/m/Y H:i'))
        ];
    }

    // Convertir le tableau en chaîne CSV
    $csv = '';
    foreach ($csvContent as $row) {
        $csv .= implode(',', $row) . "\n";
    }

    // Ajouter le BOM UTF-8 pour Excel
    $csv = "\xEF\xBB\xBF" . $csv;

    $filename = 'utilisateurs_' . now()->format('Y-m-d_H-i-s') . '.csv';

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

/**
 * Échapper les champs CSV pour éviter les problèmes de formatage
 */
private function escapeCsvField($field)
{
    // Convertir en chaîne si ce n'est pas déjà le cas
    $field = (string) $field;
    
    // Si le champ contient des virgules, guillemets ou retours à la ligne, l'encadrer de guillemets
    if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
        // Échapper les guillemets en les doublant
        $field = str_replace('"', '""', $field);
        $field = '"' . $field . '"';
    }
    
    return $field;
}

/**
 * Obtenir le libellé du rôle
 */
private function getRoleLibelle($role)
{
    $roles = [
        'admin' => 'Administrateur',
        'chef_projet' => 'Chef de Projet',
        'technicien' => 'Technicien'
    ];

    return $roles[$role] ?? ucfirst(str_replace('_', ' ', $role));
}

/**
 * Exporter avec format Excel (optionnel)
 */
public function exportExcel(Request $request)
{
    $this->ensureAdmin();

    $query = User::query();

    // Appliquer les mêmes filtres
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query->get();

    // Créer un fichier CSV optimisé pour Excel
    $filename = 'utilisateurs_' . now()->format('Y-m-d_H-i-s') . '.csv';
    $handle = fopen('php://temp', 'w+');

    // BOM UTF-8 pour Excel
    fwrite($handle, "\xEF\xBB\xBF");

    // En-tête
    fputcsv($handle, [
        'Nom',
        'Prénom',
        'Email', 
        'Téléphone',
        'Rôle',
        'Statut',
        'Date de création',
        'Dernière mise à jour',
        'Nombre de tâches',
        'Nombre de rapports'
    ], ';'); // Utiliser ; pour une meilleure compatibilité Excel française

    // Données
    foreach ($users as $user) {
        fputcsv($handle, [
            $user->nom,
            $user->prenom,
            $user->email,
            $user->telephone ?? 'Non renseigné',
            $this->getRoleLibelle($user->role),
            $user->statut === 'actif' ? 'Actif' : 'Inactif',
            $user->created_at->format('d/m/Y H:i'),
            $user->updated_at->format('d/m/Y H:i'),
            $user->taches()->count() ?? 0,
            $user->rapports()->count() ?? 0
        ], ';');
    }

    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
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
