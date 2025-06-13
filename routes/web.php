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
    | Gestion des utilisateurs - ACCESSIBLE AUX DEUX RÔLES
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->group(function () {
        // Routes accessibles aux admins ET techniciens (lecture)
        Route::get('/search/api', [UserController::class, 'search'])->name('search');

        // Routes ADMIN SEULEMENT - modification/création
        Route::middleware('admin')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');

            // Actions spéciales admin
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::patch('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
            Route::patch('/{user}/update-password', [UserController::class, 'updatePassword'])->name('update-password');

            // Export et statistiques admin
            Route::get('/export/csv', [UserController::class, 'export'])->name('export');
            Route::get('/export/excel', [UserController::class, 'exportExcel'])->name('export.excel');
            Route::get('/statistics/overview', [UserController::class, 'statistics'])->name('statistics');
        });

            // ✅ 3. ROUTES GÉNÉRALES
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // ✅ 4. ROUTES AVEC PARAMÈTRES EN DERNIER
        Route::get('/{user}', [UserController::class, 'show'])->name('show');

    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des tâches - ORDRE CORRIGÉ
    |--------------------------------------------------------------------------
    */
    Route::prefix('tasks')->name('tasks.')->group(function () {

        // ✅ ROUTES SPÉCIALES EN PREMIER (avant les routes avec paramètres)
        Route::get('/api/list', [TaskController::class, 'api'])->name('api');
        Route::get('/calendar/data', [TaskController::class, 'calendar'])->name('calendar');
        Route::get('/export', [TaskController::class, 'export'])->name('export');

        // Routes admin uniquement - AVANT les routes avec {task}
        Route::middleware('admin')->group(function () {
            Route::get('/create', [TaskController::class, 'create'])->name('create');
            Route::post('/', [TaskController::class, 'store'])->name('store');
        });

        // ✅ ROUTES GÉNÉRALES
        Route::get('/', [TaskController::class, 'index'])->name('index');

        // ✅ ROUTES AVEC PARAMÈTRES EN DERNIER
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');

        // Actions pour tous les utilisateurs sur leurs tâches
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{task}/complete', [TaskController::class, 'markCompleted'])->name('complete');

        // Routes admin pour modification/suppression - APRÈS show
        Route::middleware('admin')->group(function () {
            Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
            Route::put('/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion des événements - CORRIGÉ ET ORGANISÉ
    |--------------------------------------------------------------------------
    */
    Route::prefix('events')->name('events.')->group(function () {

        // ✅ ROUTES SPÉCIALES EN PREMIER
        Route::get('/calendar/data', [EventController::class, 'calendarData'])->name('calendar.data');
        Route::get('/export', [EventController::class, 'export'])->name('export');

        // ✅ ROUTES CRUD PRINCIPALES
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');

        // ✅ ROUTES AVEC PARAMÈTRES {event}
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');

        // ✅ ACTIONS SPÉCIALES SUR UN ÉVÉNEMENT SPÉCIFIQUE
        Route::post('/{event}/confirmer', [EventController::class, 'confirmerParticipation'])->name('confirmer');
        Route::post('/{event}/decliner', [EventController::class, 'declinerParticipation'])->name('decliner');
        Route::post('/{event}/presence', [EventController::class, 'marquerPresence'])->name('presence');
        Route::post('/{event}/duplicate', [EventController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{event}/status', [EventController::class, 'updateStatus'])->name('update-status');
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
        // Routes spéciales AVANT les paramètres
        Route::get('/export/multiple', [ReportController::class, 'exportMultiple'])->name('export.multiple');
        Route::get('/statistics/overview', [ReportController::class, 'statistics'])->name('statistics')->middleware('admin');

        // Routes CRUD principales
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');

        // Routes avec paramètres
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
        Route::get('/{report}/pdf', [ReportController::class, 'exportPdf'])->name('pdf');

        // Gestion des pièces jointes
        Route::get('/attachments/{pieceJointe}/download', [ReportController::class, 'downloadAttachment'])->name('attachments.download');
        Route::delete('/attachments/{pieceJointe}', [ReportController::class, 'deleteAttachment'])->name('attachments.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Administration avancée (Admin uniquement)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Journaux d'activité
        Route::get('/logs', function () {
            return view('admin.logs');
        })->name('logs');

        // Tableau de bord admin
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

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
                try {
                    $tasks = \App\Models\Task::where('titre', 'like', "%{$query}%")
                                            ->limit(5)
                                            ->get(['id', 'titre', 'statut']);
                } catch (\Exception $e) {
                    $tasks = collect();
                }

                // Recherche dans les événements
                try {
                    $events = \App\Models\Event::where('titre', 'like', "%{$query}%")
                                              ->limit(5)
                                              ->get(['id', 'titre', 'statut']);
                } catch (\Exception $e) {
                    $events = collect();
                }

                // Recherche dans les projets
                try {
                    $projects = \App\Models\Project::where('nom', 'like', "%{$query}%")
                                                  ->limit(5)
                                                  ->get(['id', 'nom', 'statut']);
                } catch (\Exception $e) {
                    $projects = collect();
                }

                $results = [
                    'tasks' => $tasks,
                    'events' => $events,
                    'projects' => $projects
                ];
            }

            return response()->json($results);
        })->name('search');

        // API pour les notifications
        Route::get('/notifications', function () {
            try {
                $notifications = \App\Models\Notification::where('destinataire_id', Auth::id())
                                                        ->where('lue', false)
                                                        ->orderBy('date_creation', 'desc')
                                                        ->limit(10)
                                                        ->get();
                return response()->json($notifications);
            } catch (\Exception $e) {
                return response()->json([]);
            }
        })->name('notifications');

        // Marquer notification comme lue
        Route::patch('/notifications/{notification}/read', function ($notificationId) {
            try {
                $notification = \App\Models\Notification::where('id', $notificationId)
                                                       ->where('destinataire_id', Auth::id())
                                                       ->first();
                if ($notification) {
                    $notification->update(['lue' => true, 'date_lecture' => now()]);
                    return response()->json(['success' => true]);
                }
                return response()->json(['success' => false], 404);
            } catch (\Exception $e) {
                return response()->json(['success' => false], 500);
            }
        })->name('notifications.read');
    });
});

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Routes publiques pour les événements (si activées)
Route::prefix('public')->name('public.')->group(function () {
    // Calendrier public (si activé)
    Route::get('/calendar', [EventController::class, 'publicCalendar'])
         ->name('calendar');

    // Événements publics
    Route::get('/events', [EventController::class, 'publicEvents'])
         ->name('events');
});

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
