<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil - redirection vers login ou dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Routes d'authentification
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Page de connexion
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Traitement de la connexion
    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();

            // Vérifier si l'utilisateur est actif
           if (Auth::user()->statut !== 'actif') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    })->name('login.post');
});

// Déconnexion
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Gestion du profil utilisateur
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des tâches
    |--------------------------------------------------------------------------
    */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');

        // Actions pour tous les utilisateurs
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{task}/complete', [TaskController::class, 'markCompleted'])->name('complete');

        // API et vues spéciales
        Route::get('/api/list', [TaskController::class, 'api'])->name('api');
        Route::get('/calendar/data', [TaskController::class, 'calendar'])->name('calendar');

        // Routes admin uniquement
        Route::middleware('admin')->group(function () {
            Route::get('/create', [TaskController::class, 'create'])->name('create');
            Route::post('/', [TaskController::class, 'store'])->name('store');
            Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::put('/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des événements
    |--------------------------------------------------------------------------
    */
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');

        // Actions spéciales pour les événements
        Route::patch('/{event}/participation', [EventController::class, 'updateParticipation'])->name('participation');
        Route::patch('/{event}/complete', [EventController::class, 'markCompleted'])->name('complete');
        Route::patch('/{event}/cancel', [EventController::class, 'cancel'])->name('cancel');
        Route::patch('/{event}/postpone', [EventController::class, 'postpone'])->name('postpone');

        // API pour le calendrier
        Route::get('/calendar/data', [EventController::class, 'calendar'])->name('calendar');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des projets
    |--------------------------------------------------------------------------
    */
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/report', [ProjectController::class, 'report'])->name('report');

        // Actions spéciales pour les projets
        Route::patch('/{project}/status', [ProjectController::class, 'updateStatus'])->name('update-status');

        // Routes admin uniquement
        Route::middleware('admin')->group(function () {
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::patch('/{project}/archive', [ProjectController::class, 'archive'])->name('archive');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des rapports
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');

        // Gestion des pièces jointes
        Route::get('/attachments/{pieceJointe}/download', [ReportController::class, 'downloadAttachment'])->name('attachments.download');
        Route::delete('/attachments/{pieceJointe}', [ReportController::class, 'deleteAttachment'])->name('attachments.delete');

        // Export et statistiques
        Route::get('/{report}/pdf', [ReportController::class, 'exportPdf'])->name('pdf');
        Route::post('/export/multiple', [ReportController::class, 'exportMultiple'])->name('export.multiple');

        // Statistiques admin uniquement
        Route::get('/statistics/overview', [ReportController::class, 'statistics'])->name('statistics')->middleware('admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Administration des utilisateurs (Admin uniquement)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Gestion des utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

            // Actions spéciales
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::patch('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
            Route::patch('/{user}/update-password', [UserController::class, 'updatePassword'])->name('update-password');

            // Export et recherche
            Route::get('/export/csv', [UserController::class, 'export'])->name('export');
            Route::get('/search/api', [UserController::class, 'search'])->name('search');
            Route::get('/statistics/overview', [UserController::class, 'statistics'])->name('statistics');
        });

        // Journaux d'activité
        Route::get('/logs', function () {
            return view('admin.logs');
        })->name('logs');

        // Tableau de bord admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Configuration système
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', function () {
                return view('admin.settings.index');
            })->name('index');

            Route::get('/general', function () {
                return view('admin.settings.general');
            })->name('general');

            Route::get('/security', function () {
                return view('admin.settings.security');
            })->name('security');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Routes API internes
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        // API pour les données du dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // API pour la recherche globale
        Route::get('/search', function () {
            $query = request()->input('q', '');
            $results = [];

            if (strlen($query) >= 2) {
                // Recherche dans les tâches
                $tasks = \App\Models\Task::where('titre', 'like', "%{$query}%")
                                        ->limit(5)
                                        ->get(['id', 'titre', 'statut']);

                // Recherche dans les événements
                $events = \App\Models\Event::where('titre', 'like', "%{$query}%")
                                          ->limit(5)
                                          ->get(['id', 'titre', 'statut']);

                // Recherche dans les projets
                $projects = \App\Models\Project::where('nom', 'like', "%{$query}%")
                                              ->limit(5)
                                              ->get(['id', 'nom', 'statut']);

                $results = [
                    'tasks' => $tasks,
                    'events' => $events,
                    'projects' => $projects
                ];
            }

            return response()->json($results);
        })->name('search');

        // API pour les notifications (futures)
        Route::get('/notifications', function () {
            return response()->json([]);
        })->name('notifications');
    });

    /*
    |--------------------------------------------------------------------------
    | Alias pour compatibilité
    |--------------------------------------------------------------------------
    */

    // Alias pour la gestion des utilisateurs (accessible via le menu principal)
    Route::get('/users', function () {
        return redirect()->route('admin.users.index');
    })->name('users.index')->middleware('admin');
});

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Page d'information sur l'application
Route::get('/about', function () {
    return view('about');
})->name('about');

// Page de contact/support
Route::get('/support', function () {
    return view('support');
})->name('support');

// Health check pour le monitoring
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health');

/*
|--------------------------------------------------------------------------
| Routes de fallback
|--------------------------------------------------------------------------
*/

// Route de fallback pour les URL non trouvées
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json(['message' => 'Route non trouvée'], 404);
    }

    return view('errors.404');
});
Route::middleware(['auth'])->group(function () {

    // Dashboard principal - redirige selon le rôle
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard spécifique admin
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
         ->middleware('role:admin')
         ->name('admin.dashboard');

    // Dashboard spécifique technicien
    Route::get('/technician/dashboard', [DashboardController::class, 'technician'])
         ->middleware('role:technicien')
         ->name('technician.dashboard');

    // Alias pour compatibilité
    Route::get('/technicien/dashboard', [DashboardController::class, 'technician'])
         ->middleware('role:technicien')
         ->name('technicien.dashboard');
});
