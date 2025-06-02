<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function adminDashboard()
    {
        $user = Auth::user();

        // Statistiques pour admin
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_technicians' => \App\Models\User::where('role', 'technicien')->count(),
            'active_users' => \App\Models\User::where('statut', 'actif')->count(),
            'total_tasks' => 0, // À remplacer par le vrai modèle
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'urgent_tasks' => 0,
            'total_reports' => 0,
            'monthly_reports' => 0,
            'total_projects' => 0,
            'active_projects' => 0,
        ];

        // Activités récentes pour admin
        $recentActivities = [
            [
                'user' => $user,
                'action' => 'Connexion au système',
                'status' => 'actif',
                'date' => now(),
                'type' => 'login'
            ],
            [
                'user' => (object) ['prenom' => 'Système', 'nom' => 'ORMVAT', 'role' => 'system'],
                'action' => 'Sauvegarde automatique',
                'status' => 'terminé',
                'date' => now()->subHours(2),
                'type' => 'backup'
            ],
            [
                'user' => (object) ['prenom' => 'Ahmed', 'nom' => 'Bennani', 'role' => 'technicien'],
                'action' => 'Rapport d\'intervention soumis',
                'status' => 'en révision',
                'date' => now()->subHours(4),
                'type' => 'report'
            ],
        ];

        return view('dashboard.index', compact('stats', 'recentActivities'));
    }

    /**
     * Dashboard pour les techniciens
     */
    public function technicianDashboard()
    {
        $user = Auth::user();

        // Statistiques pour technicien
        $stats = [
            'completed_tasks' => $this->getUserTaskCount($user, 'termine'),
            'in_progress_tasks' => $this->getUserTaskCount($user, 'en_cours'),
            'urgent_tasks' => $this->getUserUrgentTasks($user),
            'monthly_reports' => $this->getUserMonthlyReports($user),
        ];

        // Tâches prioritaires pour le technicien
        $priorityTasks = $this->getPriorityTasks($user);

        // Derniers rapports du technicien
        $recentReports = $this->getRecentReports($user);

        return view('dashboard.technician', compact('stats', 'priorityTasks', 'recentReports'));
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
