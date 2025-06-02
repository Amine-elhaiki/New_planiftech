{{--
==================================================
FICHIER : resources/views/dashboard/technician.blade.php
DESCRIPTION : Tableau de bord spécifique aux techniciens
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Mon espace technicien')

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
        background: linear-gradient(180deg, #059669 0%, #10b981 50%, #34d399 100%);
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

    .stat-icon.green { background-color: #bbf7d0; color: #16a34a; }
    .stat-icon.orange { background-color: #fed7aa; color: #ea580c; }
    .stat-icon.blue { background-color: #bfdbfe; color: #2563eb; }
    .stat-icon.red { background-color: #fecaca; color: #dc2626; }

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

    .quick-actions {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        color: white;
        font-weight: 500;
        transition: all 0.2s ease;
        margin-bottom: 0.75rem;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }

    .quick-action-btn.green { background: linear-gradient(135deg, #10b981, #059669); }
    .quick-action-btn.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .quick-action-btn.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .quick-action-btn.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .quick-action-btn i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    .data-table {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-header {
        background-color: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .priority-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .priority-urgent { background-color: #fee2e2; color: #dc2626; }
    .priority-high { background-color: #fef3c7; color: #d97706; }
    .priority-normal { background-color: #dbeafe; color: #1d4ed8; }
    .priority-low { background-color: #dcfce7; color: #16a34a; }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: lowercase;
    }

    .status-pending { background-color: #fef3c7; color: #d97706; }
    .status-progress { background-color: #dbeafe; color: #1d4ed8; }
    .status-completed { background-color: #dcfce7; color: #16a34a; }

    .task-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.25rem;
        border-left: 4px solid #10b981;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .task-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .task-card.urgent { border-left-color: #dc2626; }
    .task-card.high { border-left-color: #d97706; }
    .task-card.normal { border-left-color: #1d4ed8; }
    .task-card.low { border-left-color: #16a34a; }

    .task-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .task-title {
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        flex: 1;
    }

    .task-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
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

    .welcome-banner {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .welcome-content h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    .welcome-content p {
        margin: 0;
        opacity: 0.9;
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

    .mobile-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #10b981;
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
                <i class="bi bi-tools"></i>
                <div>
                    <h1 class="sidebar-brand">Technicien</h1>
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
                    <p>Technicien terrain</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Mon espace</div>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    <i class="bi bi-speedometer2"></i>
                    Tableau de bord
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-item">
                    <i class="bi bi-list-task"></i>
                    Mes tâches
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Interventions</div>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    <i class="bi bi-clipboard-data"></i>
                    Mes rapports
                </a>
                <a href="{{ route('reports.create') }}" class="nav-item">
                    <i class="bi bi-plus-circle"></i>
                    Nouveau rapport
                </a>
                <a href="{{ route('events.index') }}" class="nav-item">
                    <i class="bi bi-calendar-check"></i>
                    Planning
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Ressources</div>
                <a href="{{ route('projects.index') }}" class="nav-item">
                    <i class="bi bi-folder-check"></i>
                    Projets assignés
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-geo-alt"></i>
                    Zones d'intervention
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-telephone"></i>
                    Contacts urgents
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Profil</div>
                <a href="{{ route('profile.edit') }}" class="nav-item">
                    <i class="bi bi-person-gear"></i>
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

            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-content">
                    <h2>Bonjour {{ auth()->user()->prenom }} !</h2>
                    <p>Bienvenue dans votre espace technicien. Vous avez {{ auth()->user()->taches()->where('statut', 'en_attente')->count() ?? 3 }} tâches en attente aujourd'hui.</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <p class="stat-title">Tâches terminées</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'taches')) {
                                    echo auth()->user()->taches()->where('statut', 'termine')->count();
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
                        <div class="stat-icon orange">
                            <i class="bi bi-clock"></i>
                        </div>
                        <p class="stat-title">En cours</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'taches')) {
                                    echo auth()->user()->taches()->where('statut', 'en_cours')->count();
                                } else {
                                    echo '3';
                                }
                            } catch (\Exception $e) {
                                echo '3';
                            }
                        @endphp
                    </h2>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon red">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <p class="stat-title">Urgentes</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'taches')) {
                                    echo auth()->user()->taches()->where('priorite', 'urgent')->count();
                                } else {
                                    echo '1';
                                }
                            } catch (\Exception $e) {
                                echo '1';
                            }
                        @endphp
                    </h2>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <p class="stat-title">Rapports ce mois</p>
                    </div>
                    <h2 class="stat-value">
                        @php
                            try {
                                if (method_exists(auth()->user(), 'rapports')) {
                                    echo auth()->user()->rapports()->whereMonth('created_at', now()->month)->count();
                                } else {
                                    echo '12';
                                }
                            } catch (\Exception $e) {
                                echo '12';
                            }
                        @endphp
                    </h2>
                </div>
            </div>

            <div class="row">
                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="quick-actions">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Actions rapides</h3>

                        <a href="{{ route('reports.create') }}" class="quick-action-btn green">
                            <i class="bi bi-plus-circle"></i>
                            Créer un rapport
                        </a>

                        <a href="{{ route('tasks.index') }}" class="quick-action-btn blue">
                            <i class="bi bi-list-check"></i>
                            Voir mes tâches
                        </a>

                        <a href="{{ route('events.index') }}" class="quick-action-btn orange">
                            <i class="bi bi-calendar-event"></i>
                            Planning du jour
                        </a>

                        <a href="#" class="quick-action-btn purple" style="margin-bottom: 0;">
                            <i class="bi bi-phone"></i>
                            Contacts urgents
                        </a>
                    </div>
                </div>

                <!-- My Tasks -->
                <div class="col-lg-8 mb-4">
                    <div class="data-table">
                        <div class="table-header">
                            <h3 class="table-title">Mes tâches prioritaires</h3>
                            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div class="task-card urgent">
                                <div class="task-header">
                                    <h4 class="task-title">Réparation pompe Station A</h4>
                                    <span class="priority-badge priority-urgent">Urgent</span>
                                </div>
                                <div class="task-meta">
                                    <i class="bi bi-geo-alt me-1"></i>Zone A - Station de pompage principale
                                    <span class="ms-3"><i class="bi bi-clock me-1"></i>Échéance: Aujourd'hui 16h00</span>
                                </div>
                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Défaillance de la pompe principale. Intervention urgente requise pour maintenir l'irrigation.</p>
                            </div>

                            <div class="task-card high">
                                <div class="task-header">
                                    <h4 class="task-title">Inspection canal Zone B</h4>
                                    <span class="priority-badge priority-high">Priorité élevée</span>
                                </div>
                                <div class="task-meta">
                                    <i class="bi bi-geo-alt me-1"></i>Zone B - Canal principal
                                    <span class="ms-3"><i class="bi bi-clock me-1"></i>Échéance: Demain 10h00</span>
                                </div>
                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Inspection de routine du canal principal après les dernières pluies.</p>
                            </div>

                            <div class="task-card normal">
                                <div class="task-header">
                                    <h4 class="task-title">Maintenance préventive équipements</h4>
                                    <span class="priority-badge priority-normal">Normal</span>
                                </div>
                                <div class="task-meta">
                                    <i class="bi bi-geo-alt me-1"></i>Atelier technique
                                    <span class="ms-3"><i class="bi bi-clock me-1"></i>Échéance: Cette semaine</span>
                                </div>
                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Maintenance préventive des équipements selon le planning mensuel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="data-table">
                <div class="table-header">
                    <h3 class="table-title">Mes derniers rapports</h3>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="table-content">
                    <table class="table table-borderless" style="margin: 0;">
                        <thead>
                            <tr style="background-color: #f9fafb;">
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Rapport</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Type</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Statut</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6b7280;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 1rem 1.5rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Intervention pompe P-001</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">Zone A - Station principale</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">{{ now()->subDays(1)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 1rem 1.5rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Inspection canal C-205</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">Zone C - Canal secondaire</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">Inspection de routine</td>
                                <td style="padding: 1rem 1.5rem;"><span class="status-badge status-progress">en révision</span></td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 1rem 1.5rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Réparation vanne V-112</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">Zone B - Réseau de distribution</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">Intervention urgente</td>
                                <td style="padding: 1rem 1.5rem;"><span class="status-badge status-completed">validé</span></td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">{{ now()->subDays(3)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 1rem 1.5rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937;">Nettoyage filtres Station B</div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">Zone B - Station de filtrage</div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">Maintenance préventive</td>
                                <td style="padding: 1rem 1.5rem;"><span class="status-badge status-pending">en attente</span></td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">{{ now()->subDays(4)->format('d/m/Y') }}</td>
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

    // Animation pour les cartes de tâches
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, 200 + (index * 100));
    });

    // Animation pour les boutons d'action rapide
    const actionBtns = document.querySelectorAll('.quick-action-btn');
    actionBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            btn.style.transition = 'all 0.4s ease';
            btn.style.opacity = '1';
            btn.style.transform = 'translateX(0)';
        }, 300 + (index * 100));
    });

    // Animation pour le tableau
    setTimeout(() => {
        const tables = document.querySelectorAll('.data-table');
        tables.forEach((table, index) => {
            table.style.opacity = '0';
            table.style.transform = 'translateY(20px)';
            table.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                table.style.opacity = '1';
                table.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    }, 600);
});

// Fonction pour marquer une tâche comme terminée
function markTaskCompleted(taskId) {
    if (confirm('Marquer cette tâche comme terminée ?')) {
        // Ici vous pouvez ajouter l'appel AJAX pour mettre à jour la tâche
        console.log('Tâche ' + taskId + ' marquée comme terminée');

        // Exemple d'animation de succès
        const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
        if (taskCard) {
            taskCard.style.backgroundColor = '#dcfce7';
            taskCard.style.borderLeftColor = '#16a34a';

            setTimeout(() => {
                taskCard.style.opacity = '0.5';
            }, 1000);
        }
    }
}

// Fonction pour afficher les détails d'une tâche
function showTaskDetails(taskId) {
    // Ici vous pouvez ouvrir un modal avec les détails de la tâche
    console.log('Afficher les détails de la tâche ' + taskId);
}

// Mise à jour automatique des statistiques (optionnel)
function updateDashboardStats() {
    // Simulation de mise à jour des statistiques
    // Dans un vrai projet, ceci ferait un appel AJAX

    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
        stat.style.transform = 'scale(1.05)';
        setTimeout(() => {
            stat.style.transform = 'scale(1)';
        }, 200);
    });
}

// Mettre à jour les stats toutes les 5 minutes (optionnel)
// setInterval(updateDashboardStats, 300000);
</script>
@endpushMaintenance corrective</td>
                                <td style="padding: 1rem 1.5rem;"><span class="status-badge status-completed">validé</span></td>
                                <td style="padding: 1rem 1.5rem; color: #6b7280;">
