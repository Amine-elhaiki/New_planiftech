<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Journal;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Vérifier si l'utilisateur existe et est actif
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && $user->statut === 'inactif') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Enregistrer la connexion
            $user = Auth::user();
            $user->enregistrerConnexion();

            // Redirection selon le rôle
            $intendedUrl = $request->session()->get('url.intended', '/dashboard');

            return redirect()->intended($intendedUrl)->with('success',
                'Bienvenue, ' . $user->prenom . ' ' . $user->nom . ' !');
        }

        // Enregistrer l'échec de connexion
        Journal::enregistrerErreur("Tentative de connexion échouée pour l'email : " . $credentials['email']);

        throw ValidationException::withMessages([
            'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Enregistrer la déconnexion
        if ($user) {
            Journal::enregistrerAction('connexion', 'Déconnexion de : ' . $user->nom_complet, $user->id);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}

// ==========================================================================

<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Constructor - Seuls les admins peuvent accéder à l'inscription
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,technicien',
            'telephone' => 'nullable|string|max:20',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être admin ou technicien.',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'statut' => 'actif',
            'telephone' => $request->telephone,
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Enregistrer la création dans le journal
        Journal::enregistrerCreation('utilisateur', $user->nom_complet, Auth::id());

        // Créer une notification de bienvenue
        $user->creerNotification(
            'Bienvenue sur PlanifTech',
            'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et commencer à utiliser PlanifTech.',
            'systeme'
        );

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès : ' . $user->nom_complet);
    }
}

// ==========================================================================

<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Event;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->dashboardAdmin();
        } else {
            return $this->dashboardTechnicien();
        }
    }

    private function dashboardAdmin()
    {
        $user = Auth::user();

        // Statistiques générales
        $stats = [
            'total_utilisateurs' => User::count(),
            'utilisateurs_actifs' => User::active()->count(),
            'total_projets' => Project::count(),
            'projets_actifs' => Project::actifs()->count(),
            'total_taches' => Task::count(),
            'taches_en_retard' => Task::enRetard()->count(),
            'total_evenements' => Event::where('date_debut', '>=', now()->startOfMonth())->count(),
            'total_rapports' => Report::where('date_creation', '>=', now()->startOfMonth())->count(),
        ];

        // Projets récents
        $projetsRecents = Project::with('responsable')
                                ->orderBy('date_creation', 'desc')
                                ->take(5)
                                ->get();

        // Tâches critiques (haute priorité ou en retard)
        $tachesCritiques = Task::with(['utilisateur', 'projet'])
                              ->where(function ($query) {
                                  $query->where('priorite', 'haute')
                                        ->orWhere('date_echeance', '<', now());
                              })
                              ->whereIn('statut', ['a_faire', 'en_cours'])
                              ->orderBy('date_echeance')
                              ->take(10)
                              ->get();

        // Événements de la semaine
        $evenementsProchains = Event::with(['organisateur', 'participants'])
                                   ->where('date_debut', '>=', now())
                                   ->where('date_debut', '<=', now()->addDays(7))
                                   ->orderBy('date_debut')
                                   ->take(5)
                                   ->get();

        // Activité récente (derniers rapports)
        $rapportsRecents = Report::with('utilisateur')
                                ->orderBy('date_creation', 'desc')
                                ->take(5)
                                ->get();

        return view('dashboard.admin', compact(
            'stats', 'projetsRecents', 'tachesCritiques',
            'evenementsProchains', 'rapportsRecents'
        ));
    }

    private function dashboardTechnicien()
    {
        $user = Auth::user();

        // Mes statistiques
        $mesStats = [
            'mes_taches_total' => $user->taches()->count(),
            'mes_taches_en_cours' => $user->taches()->where('statut', 'en_cours')->count(),
            'mes_taches_en_retard' => $user->taches()->enRetard()->count(),
            'mes_taches_terminees_mois' => $user->taches()
                                               ->where('statut', 'termine')
                                               ->whereMonth('date_modification', now()->month)
                                               ->count(),
        ];

        // Mes tâches prioritaires
        $mesTachesPrioritaires = $user->taches()
                                     ->where('priorite', 'haute')
                                     ->whereIn('statut', ['a_faire', 'en_cours'])
                                     ->orderBy('date_echeance')
                                     ->take(5)
                                     ->get();

        // Mes tâches du jour et à venir
        $mesTachesAVenir = $user->taches()
                              ->where('date_echeance', '>=', now()->toDateString())
                              ->whereIn('statut', ['a_faire', 'en_cours'])
                              ->orderBy('date_echeance')
                              ->take(10)
                              ->get();

        // Mes événements à venir
        $mesEvenementsAVenir = $user->getEvenementsAVenir()->take(5);

        // Mes projets actifs
        $mesProjetsActifs = $user->getProjetsActifs()->take(5);

        // Mes notifications non lues
        $mesNotifications = $user->getNotificationsNonLuesAttribute()->take(5);

        return view('dashboard.technicien', compact(
            'mesStats', 'mesTachesPrioritaires', 'mesTachesAVenir',
            'mesEvenementsAVenir', 'mesProjetsActifs', 'mesNotifications'
        ));
    }

    /**
     * API pour les statistiques (appelée via AJAX)
     */
    public function stats(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = [
                'taches_par_statut' => [
                    'a_faire' => Task::where('statut', 'a_faire')->count(),
                    'en_cours' => Task::where('statut', 'en_cours')->count(),
                    'termine' => Task::where('statut', 'termine')->count(),
                ],
                'projets_par_statut' => [
                    'planifie' => Project::where('statut', 'planifie')->count(),
                    'en_cours' => Project::where('statut', 'en_cours')->count(),
                    'termine' => Project::where('statut', 'termine')->count(),
                    'suspendu' => Project::where('statut', 'suspendu')->count(),
                ],
                'evenements_par_type' => [
                    'intervention' => Event::where('type', 'intervention')->count(),
                    'reunion' => Event::where('type', 'reunion')->count(),
                    'formation' => Event::where('type', 'formation')->count(),
                    'visite' => Event::where('type', 'visite')->count(),
                ],
            ];
        } else {
            $stats = [
                'mes_taches_par_statut' => $user->nombre_taches_par_statut,
                'mes_projets_par_statut' => $user->nombre_projets_par_statut,
                'taux_completion' => $user->getTauxCompletionTaches(),
            ];
        }

        return response()->json($stats);
    }
}
