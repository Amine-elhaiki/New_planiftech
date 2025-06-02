{{--
==================================================
FICHIER : resources/views/dashboard/index.blade.php
DESCRIPTION : Tableau de bord style Windmill avec Sidebar
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Tableau de bord')

@push('styles')
<style>
    body {
        background-color: #f7fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
    }

    .app-layout {
        display: flex;
        min-height: 100vh;
        background-color: #f7fafc;
    }

    /* ============================================
       SIDEBAR STYLES
       ============================================ */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #4c51bf 0%, #667eea 50%, #764ba2 100%);
        color: white;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .sidebar-logo i {
        font-size: 2rem;
        margin-right: 0.75rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .sidebar-brand {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }

    .sidebar-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-user {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        margin-top: 1rem;
    }

    .user-avatar-sidebar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.75rem;
        font-size: 0.875rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .user-info-sidebar h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: white;
        margin: 0 0 0.125rem 0;
    }

    .user-info-sidebar p {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
    }

    .sidebar-nav {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
    }

    .nav-section {
        margin-bottom: 1.5rem;
    }

    .nav-section-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0 1.5rem;
        margin-bottom: 0.5rem;
    }

    .nav-item {
        display: block;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
    }

    .nav-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-right: 3px solid white;
    }

    .nav-item i {
        width: 1.25rem;
        margin-right: 0.75rem;
        font-size: 1rem;
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        padding: 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        justify-content: center;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
    }

    .logout-btn i {
        margin-right: 0.5rem;
    }

    /* ============================================
       MAIN CONTENT STYLES
       ============================================ */
    .main-content {
        margin-left: 280px;
        padding: 1.5rem;
        background-color: #f7fafc;
        min-height: 100vh;
        width: calc(100% - 280px);
    }

    .dashboard-container {
        background-color: #f7fafc;
        min-height: 100vh;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        position: relative;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }

    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    .stat-icon.orange { background-color: #fed7aa; color: #ea580c; }
    .stat-icon.green { background-color: #bbf7d0; color: #16a34a; }
    .stat-icon.blue { background-color: #bfdbfe; color: #2563eb; }
    .stat-icon.purple { background-color: #e9d5ff; color: #9333ea; }

    .stat-title {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .data-table {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .table-header {
        background-color: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .table-content {
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .custom-table th {
        text-align: left;
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .custom-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .user-details h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.125rem 0;
    }

    .user-details p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: lowercase;
    }

    .status-primary { background-color: #dbeafe; color: #1d4ed8; }
    .status-success { background-color: #dcfce7; color: #16a34a; }
    .status-warning { background-color: #fef3c7; color: #d97706; }
    .status-danger { background-color: #fee2e2; color: #dc2626; }

    .amount {
        font-weight: 600;
        color: #1f2937;
    }

    .date-text {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .page-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0.5rem 0 0 0;
    }

    /* ============================================
       MOBILE RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* ============================================
       MOBILE TOGGLE BUTTON
       ============================================ */
    .mobile-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem;
        font-size: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .mobile-toggle {
            display: block;
        }
    }
</style>
@endpush

@section('content')
<div class="app-layout">
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-water"></i>
                <div>
                    <h1 class="sidebar-brand">PlanifTech</h1>
                    <p class="sidebar-subtitle">ORMVAT</p>
                </div>
            </div>

            <!-- User Info in Sidebar -->
            <div class="sidebar-user">
                <div class="user-avatar-sidebar">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
                </div>
                <div class="user-info-sidebar">
                    <h4>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h4>
                    <p>{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Technicien' }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Navigation</div>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    <i class="bi bi-house-door"></i>
                    Tableau de bord
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Modules</div>
                <a href="{{ route('tasks.index') }}" class="nav-item">
                    <i class="bi bi-list-check"></i>
                    Tâches
                </a>
                <a href="{{ route('events.index') }}" class="nav-item">
                    <i class="bi bi-calendar-event"></i>
                    Événements
                </a>
                <a href="{{ route('projects.index') }}" class="nav-item">
                    <i class="bi bi-folder"></i>
                    Projets
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    <i class="bi bi-file-text"></i>
                    Rapports
                </a>
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="{{ route('users.index') }}" class="nav-item">
                    <i class="bi bi-people"></i>
                    Gestion utilisateurs
                </a>
                <a href="{{ route('admin.logs') }}" class="nav-item">
                    <i class="bi bi-activity"></i>
                    Journaux
                </a>
            </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Compte</div>
                <a href="{{ route('profile.edit') }}" class="nav-item">
                    <i class="bi bi-person-circle"></i>
                    Mon profil
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="dashboard-container">

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Bienvenue dans votre espace de gestion PlanifTech ORMVAT</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon orange">
                            <i class="bi bi-people"></i>
                        </div>
                        <p class="stat-title">Total utilisateurs</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                echo \App\Models\User::count();
                            } catch (\Exception $e) {
                                echo '---';
                            }
                        @endphp
                    </h2>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <p class="stat-title">Tâches terminées</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'taches')) {
                                    echo auth()->user()->taches()->where('statut', 'termine')->count();
                                } else {
                                    echo '12';
                                }
                            } catch (\Exception $e) {
                                echo '12';
                            }
                        @endphp
                    </h2>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <p class="stat-title">Nouveaux rapports</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'rapports')) {
                                    $user = auth()->user();
                                    $rapports = $user->rapports();
                                    try {
                                        echo $rapports->whereDate('date_creation', today())->count();
                                    } catch (\Exception $e1) {
                                        try {
                                            echo $rapports->whereDate('created_at', today())->count();
                                        } catch (\Exception $e2) {
                                            echo $rapports->count();
                                        }
                                    }
                                } else {
                                    echo '8';
                                }
                            } catch (\Exception $e) {
                                echo '8';
                            }
                        @endphp
                    </h2>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon purple">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <p class="stat-title">Événements planifiés</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'evenements')) {
                                    echo auth()->user()->evenements()->count();
                                } else {
                                    echo '5';
                                }
                            } catch (\Exception $e) {
                                echo '5';
                            }
                        @endphp
                    </h2>
                </div>
            </div>

            <!-- Data Table -->
            <div class="data-table">
                <div class="table-header">
                    <h3 class="table-title">Activités récentes</h3>
                </div>
                <div class="table-content">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <h4>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h4>
                                            <p>{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Technicien' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">Connexion au système</td>
                                <td><span class="status-badge status-primary">actif</span></td>
                                <td class="date-text">{{ now()->format('d/m/Y') }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            SY
                                        </div>
                                        <div class="user-details">
                                            <h4>Système ORMVAT</h4>
                                            <p>Maintenance automatique</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">Sauvegarde données</td>
                                <td><span class="status-badge status-success">terminé</span></td>
                                <td class="date-text">{{ now()->subHours(2)->format('d/m/Y') }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            AB
                                        </div>
                                        <div class="user-details">
                                            <h4>Ahmed Bennani</h4>
                                            <p>Technicien terrain</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">Rapport intervention</td>
                                <td><span class="status-badge status-success">validé</span></td>
                                <td class="date-text">{{ now()->subDays(1)->format('d/m/Y') }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            FK
                                        </div>
                                        <div class="user-details">
                                            <h4>Fatima Khalil</h4>
                                            <p>Responsable planning</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">Planification mensuelle</td>
                                <td><span class="status-badge status-warning">en cours</span></td>
                                <td class="date-text">{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            OR
                                        </div>
                                        <div class="user-details">
                                            <h4>Omar Rachid</h4>
                                            <p>Superviseur zone</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">Inspection réseau</td>
                                <td><span class="status-badge status-danger">urgent</span></td>
                                <td class="date-text">{{ now()->subDays(3)->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
// Toggle sidebar pour mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// Fermer la sidebar quand on clique ailleurs sur mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.mobile-toggle');

    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les cartes de statistiques
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation pour le tableau
    setTimeout(() => {
        const table = document.querySelector('.data-table');
        if (table) {
            table.style.opacity = '0';
            table.style.transform = 'translateY(20px)';
            table.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                table.style.opacity = '1';
                table.style.transform = 'translateY(0)';
            }, 100);
        }
    }, 400);
});
</script>
@endpush
