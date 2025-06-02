<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PlanifTech') - ORMVAT</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Styles personnalisés -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 1.5rem;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-header small {
            opacity: 0.8;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
            border-left-color: rgba(255,255,255,0.3);
        }

        .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.15);
            border-left-color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .content-wrapper {
            padding: 0 2rem 2rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease;
        }

        .sidebar-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .user-menu .dropdown-toggle::after {
            display: none;
        }

        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Animations d'entrée */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Styles pour les alertes */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid var(--info-color);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4>PlanifTech</h4>
                    <small>ORMVAT</small>
                </div>
                <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <ul class="sidebar-nav list-unstyled">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>

            <!-- Événements -->
            <li class="nav-item">
                <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span>Événements</span>
                    @if(isset($urgentEventsCount) && $urgentEventsCount > 0)
                        <span class="badge-notification">{{ $urgentEventsCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Tâches (à développer) -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-list-check"></i>
                    <span>Tâches</span>
                </a>
            </li>

            <!-- Projets (à développer) -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-folder"></i>
                    <span>Projets</span>
                </a>
            </li>

            <!-- Rapports (à développer) -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-file-text"></i>
                    <span>Rapports</span>
                </a>
            </li>

            <!-- Séparateur -->
            <li class="nav-item">
                <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 1.5rem;">
            </li>

            <!-- Administration (admin seulement) -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            @endif
        </ul>

        <!-- Bouton de réduction de la sidebar -->
        <div class="position-absolute bottom-0 w-100 p-3 d-none d-lg-block">
            <button class="sidebar-toggle w-100" onclick="toggleSidebarCollapse()">
                <i class="bi bi-chevron-left" id="collapseIcon"></i>
            </button>
        </div>
    </nav>

    <!-- Overlay pour mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Contenu principal -->
    <main class="main-content" id="mainContent">
        <!-- Barre supérieure -->
        <div class="topbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list text-dark"></i>
                </button>
                <div>
                    <h5 class="mb-0">@yield('title', 'PlanifTech')</h5>
                    <small class="text-muted">{{ now()->format('l j F Y') }}</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell text-dark fs-5"></i>
                        <span class="badge-notification">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            Nouvel événement créé
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                            Tâche en retard
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Rapport validé
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="#">Voir toutes</a></li>
                    </ul>
                </div>

                <!-- Menu utilisateur -->
                <div class="dropdown user-menu">
                    <button class="btn btn-link d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="text-end">
                            <div class="fw-bold">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                            <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                        </div>
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <span class="text-white fw-bold">{{ substr(Auth::user()->prenom, 0, 1) }}</span>
                        </div>
                        <i class="bi bi-chevron-down text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-person me-2"></i>Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i>Paramètres
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Zone de contenu -->
        <div class="content-wrapper fade-in">
            <!-- Messages d'alerte -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Erreurs détectées :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personnalisés -->
    <script>
        // Toggle sidebar mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Toggle sidebar collapse (desktop)
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const icon = document.getElementById('collapseIcon');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            } else {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            }
        }

        // Fermer les alertes automatiquement
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);

        // Mettre à jour l'heure
        function updateTime() {
            const now = new Date();
            const timeElement = document.querySelector('.topbar small');
            if (timeElement) {
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                timeElement.textContent = now.toLocaleDateString('fr-FR', options);
            }
        }

        // Mettre à jour l'heure toutes les minutes
        setInterval(updateTime, 60000);
        updateTime();
    </script>

    @stack('scripts')
</body>
</html>
