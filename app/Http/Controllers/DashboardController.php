<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Models\Event;
use App\Models\Report;
use App\Models\ParticipantEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\DashboardRequest;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord approprié selon le rôle de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers le bon dashboard selon le rôle
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'technicien') {
            return $this->technicianDashboard();
        }

        // Rôle par défaut
        return $this->technicianDashboard();
    }

    /**
     * Dashboard pour les administrateurs
     */
    private function adminDashboard()
    {
        // Statistiques générales
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('statut', 'actif')->count(),
                'inactive' => User::where('statut', 'inactif')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'tasks' => [
                'total' => Task::count(),
                'pending' => Task::where('statut', 'a_faire')->count(),
                'in_progress' => Task::where('statut', 'en_cours')->count(),
                'completed' => Task::where('statut', 'termine')->count(),
                'overdue' => Task::where('date_echeance', '<', now())
                               ->whereIn('statut', ['a_faire', 'en_cours'])
                               ->count(),
            ],
            'projects' => [
                'total' => Project::count(),
                'active' => Project::whereIn('statut', ['planifie', 'en_cours'])->count(),
                'completed' => Project::where('statut', 'termine')->count(),
            ],
            'events' => [
                'total' => Event::count(),
                'upcoming' => Event::where('date_debut', '>', now())->count(),
                'today' => Event::whereDate('date_debut', today())->count(),
            ]
        ];

        // Utilisateurs récents
        $recentUsers = User::latest()->limit(10)->get();

        return view('dashboard.index', compact('stats', 'recentUsers'));
    }
    /**
     * Dashboard pour les techniciens
     */
    private function technicianDashboard()
    {
        $user = Auth::user();

        // ✅ STATISTIQUES POUR TECHNICIEN
        $stats = [
            'tasks' => [
                'total' => Task::where('id_utilisateur', $user->id)->count(),
                'pending' => Task::where('id_utilisateur', $user->id)->where('statut', 'a_faire')->count(),
                'in_progress' => Task::where('id_utilisateur', $user->id)->where('statut', 'en_cours')->count(),
                'completed' => Task::where('id_utilisateur', $user->id)->where('statut', 'termine')->count(),
                'overdue' => Task::where('id_utilisateur', $user->id)
                               ->where('date_echeance', '<', now())
                               ->whereIn('statut', ['a_faire', 'en_cours'])
                               ->count(),
            ],
            'events' => [
                'total' => $this->getUserEventsCount($user->id),
                'organized' => Event::where('id_organisateur', $user->id)->count(),
                'invited' => $this->getUserInvitationsCount($user->id, 'invite'),
                'confirmed' => $this->getUserInvitationsCount($user->id, 'confirme'),
                'upcoming' => $this->getUserUpcomingEventsCount($user->id),
                'today' => $this->getUserTodayEventsCount($user->id),
            ],
            'reports' => [
                'total' => Report::where('id_utilisateur', $user->id)->count(),
                'this_month' => Report::where('id_utilisateur', $user->id)
                                    ->whereMonth('created_at', now()->month)
                                    ->count(),
                'this_week' => Report::where('id_utilisateur', $user->id)
                                   ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                   ->count(),
            ]
        ];

        // ✅ TÂCHES RÉCENTES DU TECHNICIEN
        $recentTasks = Task::where('id_utilisateur', $user->id)
                          ->orderBy('date_echeance', 'asc')
                          ->limit(5)
                          ->get();

        // ✅ ÉVÉNEMENTS EN ATTENTE DE RÉPONSE
        $pendingInvitations = $this->getUserPendingInvitations($user->id);

        // ✅ ÉVÉNEMENTS À VENIR
        $upcomingEvents = $this->getUserUpcomingEvents($user->id);

        // ✅ ÉVÉNEMENTS CONFIRMÉS AUJOURD'HUI
        $todayEvents = $this->getUserTodayEvents($user->id);

        // ✅ RAPPORTS RÉCENTS
        $recentReports = Report::where('id_utilisateur', $user->id)
                              ->latest()
                              ->limit(3)
                              ->get();

        return view('dashboard.technician', compact(
            'stats', 
            'recentTasks', 
            'pendingInvitations',
            'upcomingEvents',
            'todayEvents',
            'recentReports'
        ));
    }


    
    // ✅ MÉTHODES UTILITAIRES POUR LES ÉVÉNEMENTS

    /**
     * Obtenir le nombre total d'événements de l'utilisateur
     */
    private function getUserEventsCount($userId)
    {
        return Event::where(function($query) use ($userId) {
            $query->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('id_utilisateur', $userId);
                  });
        })->count();
    }

        /**
     * Obtenir le nombre d'invitations par statut
     */
    private function getUserInvitationsCount($userId, $status)
    {
        return ParticipantEvent::where('id_utilisateur', $userId)
                              ->where('statut_presence', $status)
                              ->count();
    }

     /**
     * Obtenir le nombre d'événements à venir
     */
    private function getUserUpcomingEventsCount($userId)
    {
        return Event::where(function($query) use ($userId) {
            $query->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('id_utilisateur', $userId);
                  });
        })->where('date_debut', '>', now())->count();
    }

    /**
     * Obtenir le nombre d'événements aujourd'hui
     */
    private function getUserTodayEventsCount($userId)
    {
        return Event::where(function($query) use ($userId) {
            $query->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('id_utilisateur', $userId);
                  });
        })->whereDate('date_debut', today())->count();
    }

    /**
     * Obtenir les invitations en attente de réponse
     */
    private function getUserPendingInvitations($userId)
    {
        return Event::whereHas('participants', function($query) use ($userId) {
            $query->where('id_utilisateur', $userId)
                  ->where('statut_presence', 'invite');
        })->with(['participants' => function($query) use ($userId) {
            $query->where('id_utilisateur', $userId);
        }])->orderBy('date_debut')->limit(5)->get();
    }

    /**
     * Obtenir les événements à venir confirmés
     */
    private function getUserUpcomingEvents($userId)
    {
        return Event::where(function($query) use ($userId) {
            $query->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('id_utilisateur', $userId)
                        ->whereIn('statut_presence', ['confirme', 'organisateur']);
                  });
        })->where('date_debut', '>', now())
          ->orderBy('date_debut')
          ->limit(5)
          ->get();
    }

    /**
     * Obtenir les événements d'aujourd'hui
     */
    private function getUserTodayEvents($userId)
    {
        return Event::where(function($query) use ($userId) {
            $query->where('id_organisateur', $userId)
                  ->orWhereHas('participants', function($q) use ($userId) {
                      $q->where('id_utilisateur', $userId);
                  });
        })->whereDate('date_debut', today())
          ->orderBy('date_debut')
          ->get();
    }

    /**
     * Obtenir le nombre de tâches d'un utilisateur par statut
     */
    private function getUserTaskCount($user, $status)
    {
        try {
            if (method_exists($user, 'taches')) {
                return $user->taches()->where('statut', $status)->count();
            }
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
        }

        // Valeurs par défaut selon le statut
        $defaults = [
            'termine' => 8,
            'en_cours' => 3,
            'en_attente' => 2,
        ];

        return $defaults[$status] ?? 0;
    }

    /**
     * Obtenir le nombre de tâches urgentes d'un utilisateur
     */
    private function getUserUrgentTasks($user)
    {
        try {
            if (method_exists($user, 'taches')) {
                return $user->taches()->where('priorite', 'urgent')->count();
            }
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
        }

        return 1; // Valeur par défaut
    }

    /**
     * Obtenir le nombre de rapports du mois pour un utilisateur
     */
    private function getUserMonthlyReports($user)
    {
        try {
            if (method_exists($user, 'rapports')) {
                return $user->rapports()->whereMonth('created_at', now()->month)->count();
            }
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
        }

        return 12; // Valeur par défaut
    }

    /**
     * Obtenir les tâches prioritaires d'un technicien
     */
    private function getPriorityTasks($user)
    {
        // Tâches d'exemple - à remplacer par de vraies données
        return [
            [
                'id' => 1,
                'title' => 'Réparation pompe Station A',
                'priority' => 'urgent',
                'zone' => 'Zone A - Station de pompage principale',
                'deadline' => 'Aujourd\'hui 16h00',
                'description' => 'Défaillance de la pompe principale. Intervention urgente requise pour maintenir l\'irrigation.',
                'status' => 'en_attente'
            ],
            [
                'id' => 2,
                'title' => 'Inspection canal Zone B',
                'priority' => 'high',
                'zone' => 'Zone B - Canal principal',
                'deadline' => 'Demain 10h00',
                'description' => 'Inspection de routine du canal principal après les dernières pluies.',
                'status' => 'en_attente'
            ],
            [
                'id' => 3,
                'title' => 'Maintenance préventive équipements',
                'priority' => 'normal',
                'zone' => 'Atelier technique',
                'deadline' => 'Cette semaine',
                'description' => 'Maintenance préventive des équipements selon le planning mensuel.',
                'status' => 'en_cours'
            ],
        ];
    }

    /**
     * Obtenir les derniers rapports d'un technicien
     */
    private function getRecentReports($user)
    {
        // Rapports d'exemple - à remplacer par de vraies données
        return [
            [
                'id' => 1,
                'title' => 'Intervention pompe P-001',
                'zone' => 'Zone A - Station principale',
                'type' => 'Maintenance corrective',
                'status' => 'validé',
                'date' => now()->subDays(1)
            ],
            [
                'id' => 2,
                'title' => 'Inspection canal C-205',
                'zone' => 'Zone C - Canal secondaire',
                'type' => 'Inspection de routine',
                'status' => 'en révision',
                'date' => now()->subDays(2)
            ],
            [
                'id' => 3,
                'title' => 'Réparation vanne V-112',
                'zone' => 'Zone B - Réseau de distribution',
                'type' => 'Intervention urgente',
                'status' => 'validé',
                'date' => now()->subDays(3)
            ],
            [
                'id' => 4,
                'title' => 'Nettoyage filtres Station B',
                'zone' => 'Zone B - Station de filtrage',
                'type' => 'Maintenance préventive',
                'status' => 'en attente',
                'date' => now()->subDays(4)
            ],
        ];
    }

    /**
     * Afficher le dashboard admin (route spécifique)
     */
    public function admin()
    {
        // Vérifier que l'utilisateur est admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé');
        }

        return $this->adminDashboard();
    }

    /**
     * Afficher le dashboard technicien (route spécifique)
     */
    public function technician()
    {
        // Vérifier que l'utilisateur est technicien
        if (Auth::user()->role !== 'technicien') {
            abort(403, 'Accès non autorisé');
        }

        return $this->technicianDashboard();
    }
}
