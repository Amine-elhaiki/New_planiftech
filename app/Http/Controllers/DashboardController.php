<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Event;
use App\Models\Project;
use App\Models\Report;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Statistiques générales
        $stats = [
            'totalTasks' => 0,
            'completedTasks' => 0,
            'pendingTasks' => 0,
            'overdueTasks' => 0,
            'todayEvents' => 0,
            'activeProjects' => 0,
            'recentReports' => 0
        ];

        // Données spécifiques selon le rôle
        if ($user->role === 'admin') {
            // Statistiques pour administrateur
            $stats['totalTasks'] = Task::count();
            $stats['completedTasks'] = Task::where('statut', 'termine')->count();
            $stats['pendingTasks'] = Task::whereIn('statut', ['a_faire', 'en_cours'])->count();
            $stats['overdueTasks'] = Task::where('date_echeance', '<', $today)
                                          ->whereIn('statut', ['a_faire', 'en_cours'])
                                          ->count();
            $stats['todayEvents'] = Event::whereDate('date_debut', $today)->count();
            $stats['activeProjects'] = Project::where('statut', 'en_cours')->count();
            $stats['recentReports'] = Report::where('created_at', '>=', $today->subDays(7))->count();

            // Données pour les graphiques
            $tasksData = $this->getAdminTasksData();
            $projectsData = $this->getAdminProjectsData();
            $recentActivities = $this->getRecentActivities();
        } else {
            // Statistiques pour technicien
            $stats['totalTasks'] = Task::where('id_utilisateur', $user->id)->count();
            $stats['completedTasks'] = Task::where('id_utilisateur', $user->id)
                                          ->where('statut', 'termine')->count();
            $stats['pendingTasks'] = Task::where('id_utilisateur', $user->id)
                                        ->whereIn('statut', ['a_faire', 'en_cours'])->count();
            $stats['overdueTasks'] = Task::where('id_utilisateur', $user->id)
                                        ->where('date_echeance', '<', $today)
                                        ->whereIn('statut', ['a_faire', 'en_cours'])
                                        ->count();

            // Événements du technicien
            $stats['todayEvents'] = Event::whereHas('participants', function($query) use ($user) {
                                              $query->where('id_utilisateur', $user->id);
                                          })
                                          ->whereDate('date_debut', $today)
                                          ->count();

            $tasksData = $this->getTechnicianTasksData($user->id);
            $projectsData = $this->getTechnicianProjectsData($user->id);
            $recentActivities = $this->getTechnicianActivities($user->id);
        }

        // Tâches urgentes/prioritaires
        $urgentTasks = $this->getUrgentTasks($user);

        // Événements de la semaine
        $weekEvents = $this->getWeekEvents($user);

        return view('dashboard.index', compact(
            'stats',
            'tasksData',
            'projectsData',
            'urgentTasks',
            'weekEvents',
            'recentActivities'
        ));
    }

    private function getAdminTasksData()
    {
        return [
            'total' => Task::count(),
            'a_faire' => Task::where('statut', 'a_faire')->count(),
            'en_cours' => Task::where('statut', 'en_cours')->count(),
            'termine' => Task::where('statut', 'termine')->count(),
            'en_retard' => Task::where('date_echeance', '<', Carbon::today())
                              ->whereIn('statut', ['a_faire', 'en_cours'])
                              ->count()
        ];
    }

    private function getTechnicianTasksData($userId)
    {
        return [
            'total' => Task::where('id_utilisateur', $userId)->count(),
            'a_faire' => Task::where('id_utilisateur', $userId)->where('statut', 'a_faire')->count(),
            'en_cours' => Task::where('id_utilisateur', $userId)->where('statut', 'en_cours')->count(),
            'termine' => Task::where('id_utilisateur', $userId)->where('statut', 'termine')->count(),
            'en_retard' => Task::where('id_utilisateur', $userId)
                              ->where('date_echeance', '<', Carbon::today())
                              ->whereIn('statut', ['a_faire', 'en_cours'])
                              ->count()
        ];
    }

    private function getAdminProjectsData()
    {
        return Project::selectRaw('statut, COUNT(*) as count')
                     ->groupBy('statut')
                     ->pluck('count', 'statut')
                     ->toArray();
    }

    private function getTechnicianProjectsData($userId)
    {
        return Project::where('id_responsable', $userId)
                     ->selectRaw('statut, COUNT(*) as count')
                     ->groupBy('statut')
                     ->pluck('count', 'statut')
                     ->toArray();
    }

    private function getUrgentTasks($user)
    {
        $query = Task::where('priorite', 'haute')
                    ->whereIn('statut', ['a_faire', 'en_cours'])
                    ->orderBy('date_echeance', 'asc')
                    ->limit(5);

        if ($user->role !== 'admin') {
            $query->where('id_utilisateur', $user->id);
        }

        return $query->with('utilisateur', 'projet')->get();
    }

    private function getWeekEvents($user)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $query = Event::whereBetween('date_debut', [$startOfWeek, $endOfWeek])
                     ->orderBy('date_debut', 'asc')
                     ->limit(10);

        if ($user->role !== 'admin') {
            $query->whereHas('participants', function($q) use ($user) {
                $q->where('id_utilisateur', $user->id);
            });
        }

        return $query->with('organisateur')->get();
    }

    private function getRecentActivities($limit = 10)
    {
        // Récupérer les activités récentes (tâches créées, rapports soumis, etc.)
        $activities = collect();

        // Tâches récemment créées
        $recentTasks = Task::with('utilisateur')
                          ->latest()
                          ->limit($limit)
                          ->get()
                          ->map(function($task) {
                              return [
                                  'type' => 'task_created',
                                  'message' => "Nouvelle tâche créée: {$task->titre}",
                                  'user' => $task->utilisateur->prenom . ' ' . $task->utilisateur->nom,
                                  'date' => $task->created_at,
                                  'icon' => 'bi-list-check',
                                  'color' => 'primary'
                              ];
                          });

        // Rapports récemment soumis
        $recentReports = Report::with('utilisateur')
                              ->latest()
                              ->limit($limit)
                              ->get()
                              ->map(function($report) {
                                  return [
                                      'type' => 'report_submitted',
                                      'message' => "Rapport soumis: {$report->titre}",
                                      'user' => $report->utilisateur->prenom . ' ' . $report->utilisateur->nom,
                                      'date' => $report->created_at,
                                      'icon' => 'bi-file-text',
                                      'color' => 'info'
                                  ];
                              });

        return $activities->concat($recentTasks)
                         ->concat($recentReports)
                         ->sortByDesc('date')
                         ->take($limit)
                         ->values();
    }

    private function getTechnicianActivities($userId, $limit = 10)
    {
        $activities = collect();

        // Tâches du technicien
        $userTasks = Task::where('id_utilisateur', $userId)
                        ->latest()
                        ->limit($limit)
                        ->get()
                        ->map(function($task) {
                            return [
                                'type' => 'my_task',
                                'message' => "Tâche assignée: {$task->titre}",
                                'date' => $task->created_at,
                                'icon' => 'bi-list-check',
                                'color' => 'primary'
                            ];
                        });

        // Rapports du technicien
        $userReports = Report::where('id_utilisateur', $userId)
                            ->latest()
                            ->limit($limit)
                            ->get()
                            ->map(function($report) {
                                return [
                                    'type' => 'my_report',
                                    'message' => "Rapport soumis: {$report->titre}",
                                    'date' => $report->created_at,
                                    'icon' => 'bi-file-text',
                                    'color' => 'success'
                                ];
                            });

        return $activities->concat($userTasks)
                         ->concat($userReports)
                         ->sortByDesc('date')
                         ->take($limit)
                         ->values();
    }
}
