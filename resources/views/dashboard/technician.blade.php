{{--
==================================================
FICHIER : resources/views/dashboard/technician.blade.php
DESCRIPTION : Tableau de bord technicien avec sidebar améliorée
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Espace Technicien')

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
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
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
        font-size: 2.2rem;
        margin-right: 0.75rem;
        color: #fbbf24;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }

    .sidebar-brand {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .sidebar-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
    }

    .sidebar-user {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        margin-top: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-avatar-sidebar {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        margin-right: 0.75rem;
        font-size: 1rem;
        border: 3px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .user-info-sidebar h4 {
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        margin: 0 0 0.125rem 0;
    }

    .user-info-sidebar p {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        font-weight: 500;
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
        font-size: 0.7rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 1.5rem;
        margin-bottom: 0.75rem;
    }

    .nav-item {
        display: block;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        padding: 0.875rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        position: relative;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.12);
        color: white;
        text-decoration: none;
        transform: translateX(5px);
    }

    .nav-item.active {
        background: rgba(255, 255, 255, 0.18);
        color: white;
        border-right: 4px solid #fbbf24;
        box-shadow: inset 0 0 20px rgba(255,255,255,0.1);
    }

    .nav-item i {
        width: 1.5rem;
        margin-right: 0.75rem;
        font-size: 1.1rem;
        text-align: center;
    }

    .nav-badge {
        background: #dc2626;
        color: white;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        margin-left: auto;
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        width: 100%;
        justify-content: center;
    }

    .logout-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: white;
        text-decoration: none;
        border-color: rgba(239, 68, 68, 0.3);
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
        max-width: 1400px;
        margin: 0 auto;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(5, 150, 105, 0.3);
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% { transform: translateX(0) translateY(0); }
        100% { transform: translateX(-100px) translateY(-100px); }
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .welcome-content h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-content p {
        margin: 0;
        opacity: 0.95;
        font-size: 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.75rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .stat-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.4rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-icon.completed { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-icon.progress { background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; }
    .stat-icon.urgent { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .stat-icon.reports { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }

    .stat-content h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        margin: 0 0 0.5rem 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
        line-height: 1;
    }

    .stat-trend {
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
        color: #059669;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .quick-actions {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .section-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .action-list {
        padding: 1.5rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        text-decoration: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-bottom: 0.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .action-btn:last-child {
        margin-bottom: 0;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }

    .action-btn.create { background: linear-gradient(135deg, #10b981, #059669); }
    .action-btn.view { background: linear-gradient(135deg, #3b82f6, #1e40af); }
    .action-btn.planning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .action-btn.contacts { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .action-btn i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    .tasks-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .task-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        border-left: 4px solid #10b981;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin: 1rem 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .task-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .task-card.urgent { border-left-color: #dc2626; }
    .task-card.high { border-left-color: #d97706; }
    .task-card.normal { border-left-color: #3b82f6; }
    .task-card.low { border-left-color: #10b981; }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .task-title {
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        flex: 1;
        font-size: 1rem;
    }

    .priority-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 1rem;
    }

    .priority-urgent { background-color: #fee2e2; color: #991b1b; }
    .priority-high { background-color: #fef3c7; color: #92400e; }
    .priority-normal { background-color: #dbeafe; color: #1e40af; }
    .priority-low { background-color: #dcfce7; color: #166534; }

    .task-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .task-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.5;
    }

    .reports-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-content {
        overflow-x: auto;
    }

    .reports-table table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .reports-table th {
        text-align: left;
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .reports-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .reports-table tr:last-child td {
        border-bottom: none;
    }

    .reports-table tr:hover {
        background-color: #f9fafb;
    }

    .report-info h4 {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    .report-info p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: lowercase;
    }

    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-progress { background-color: #dbeafe; color: #1e40af; }
    .status-completed { background-color: #dcfce7; color: #166534; }
    .status-revision { background-color: #fef3c7; color: #92400e; }

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

        .content-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .welcome-banner {
            padding: 1.5rem;
        }

        .welcome-content h2 {
            font-size: 1.5rem;
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
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1.25rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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
                    <p>Technicien Terrain</p>
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
                    @php
                        try {
                            $urgentTasks = auth()->user()->taches()->where('priorite', 'urgent')->count();
                            if ($urgentTasks > 0) {
                                echo '<span class="nav-badge">'.$urgentTasks.'</span>';
                            }
                        } catch (\Exception $e) {
                            echo '<span class="nav-badge">1</span>';
                        }
                    @endphp
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
                <div class="nav-section-title">Outils</div>
                <a href="#" class="nav-item">
                    <i class="bi bi-camera"></i>
                    Photos terrain
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-map"></i>
                    Cartes zones
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
                    <p>Bienvenue dans votre espace technicien. Vous avez
                        @php
                            try {
                                echo auth()->user()->taches()->where('statut', 'en_attente')->count() ?? 3;
                            } catch (\Exception $e) {
                                echo '3';
                            }
                        @endphp
                        tâches en attente aujourd'hui.
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completed">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Tâches terminées</h3>
                            <div class="stat-value">
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
                            </div>
                            <div class="stat-trend">
                                <i class="bi bi-arrow-up"></i> +2 cette semaine
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon progress">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>En cours</h3>
                            <div class="stat-value">
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
                            </div>
                            <div class="stat-trend">
                                <i class="bi bi-arrow-right"></i> En progression
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon urgent">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Urgentes</h3>
                            <div class="stat-value">
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
                            </div>
                            <div class="stat-trend">
                                <i class="bi bi-exclamation-circle"></i> Attention requise
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon reports">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Rapports ce mois</h3>
                            <div class="stat-value">
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
                            </div>
                            <div class="stat-trend">
                                <i class="bi bi-arrow-up"></i> +3 vs mois dernier
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="section-header">
                        <h3 class="section-title">Actions rapides</h3>
                    </div>
                    <div class="action-list">
                        <a href="{{ route('reports.create') }}" class="action-btn create">
                            <i class="bi bi-plus-circle"></i>
                            Créer un rapport
                        </a>

                        <a href="{{ route('tasks.index') }}" class="action-btn view">
                            <i class="bi bi-list-check"></i>
                            Voir mes tâches
                        </a>

                        <a href="{{ route('events.index') }}" class="action-btn planning">
                            <i class="bi bi-calendar-event"></i>
                            Planning du jour
                        </a>

                        <a href="#" class="action-btn contacts">
                            <i class="bi bi-phone"></i>
                            Contacts urgents
                        </a>
                    </div>
                </div>

                <!-- Priority Tasks -->
                <div class="tasks-section">
                    <div class="section-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h3 class="section-title">Mes tâches prioritaires</h3>
                            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                    </div>

                    <div class="task-card urgent">
                        <div class="task-header">
                            <h4 class="task-title">Réparation pompe Station A</h4>
                            <span class="priority-badge priority-urgent">Urgent</span>
                        </div>
                        <div class="task-meta">
                            <span><i class="bi bi-geo-alt me-1"></i>Zone A - Station de pompage principale</span>
                            <span><i class="bi bi-clock me-1"></i>Échéance: Aujourd'hui 16h00</span>
                        </div>
                        <p class="task-description">Défaillance de la pompe principale. Intervention urgente requise pour maintenir l'irrigation.</p>
                    </div>

                    <div class="task-card high">
                        <div class="task-header">
                            <h4 class="task-title">Inspection canal Zone B</h4>
                            <span class="priority-badge priority-high">Priorité élevée</span>
                        </div>
                        <div class="task-meta">
                            <span><i class="bi bi-geo-alt me-1"></i>Zone B - Canal principal</span>
                            <span><i class="bi bi-clock me-1"></i>Échéance: Demain 10h00</span>
                        </div>
                        <p class="task-description">Inspection de routine du canal principal après les dernières pluies.</p>
                    </div>

                    <div class="task-card normal">
                        <div class="task-header">
                            <h4 class="task-title">Maintenance préventive équipements</h4>
                            <span class="priority-badge priority-normal">Normal</span>
                        </div>
                        <div class="task-meta">
                            <span><i class="bi bi-geo-alt me-1"></i>Atelier technique</span>
                            <span><i class="bi bi-clock me-1"></i>Échéance: Cette semaine</span>
                        </div>
                        <p class="task-description">Maintenance préventive des équipements selon le planning mensuel.</p>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="reports-table">
                <div class="section-header">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <h3 class="section-title">Mes derniers rapports</h3>
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                    </div>
                </div>
                <div class="table-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Rapport</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="report-info">
                                        <h4>Intervention pompe P-001</h4>
                                        <p>Zone A - Station principale</p>
                                    </div>
                                </td>
                                <td>Maintenance corrective</td>
                                <td><span class="status-badge status-completed">validé</span></td>
                                <td>{{ now()->subDays(1)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="report-info">
                                        <h4>Inspection canal C-205</h4>
                                        <p>Zone C - Canal secondaire</p>
                                    </div>
                                </td>
                                <td>Inspection de routine</td>
                                <td><span class="status-badge status-revision">en révision</span></td>
                                <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="report-info">
                                        <h4>Réparation vanne V-112</h4>
                                        <p>Zone B - Réseau de distribution</p>
                                    </div>
                                </td>
                                <td>Intervention urgente</td>
                                <td><span class="status-badge status-completed">validé</span></td>
                                <td>{{ now()->subDays(3)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="report-info">
                                        <h4>Nettoyage filtres Station B</h4>
                                        <p>Zone B - Station de filtrage</p>
                                    </div>
                                </td>
                                <td>Maintenance préventive</td>
                                <td><span class="status-badge status-pending">en attente</span></td>
                                <td>{{ now()->subDays(4)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="report-info">
                                        <h4>Contrôle débit Station C</h4>
                                        <p>Zone C - Station de mesure</p>
                                    </div>
                                </td>
                                <td>Contrôle qualité</td>
                                <td><span class="status-badge status-progress">en cours</span></td>
                                <td>{{ now()->subDays(5)->format('d/m/Y') }}</td>
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
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Animation pour les cartes de tâches
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, 300 + (index * 100));
    });

    // Animation pour les boutons d'action rapide
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            btn.style.transition = 'all 0.4s ease';
            btn.style.opacity = '1';
            btn.style.transform = 'translateX(0)';
        }, 400 + (index * 100));
    });

    // Animation pour les sections
    const sections = document.querySelectorAll('.reports-table, .quick-actions, .tasks-section');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';

        setTimeout(() => {
            section.style.transition = 'all 0.6s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 600 + (index * 200));
    });

    // Animation pour les lignes du tableau
    const tableRows = document.querySelectorAll('.reports-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-10px)';

        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 1000 + (index * 50));
    });
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
                taskCard.style.opacity = '0.7';
                taskCard.style.transform = 'scale(0.98)';
            }, 1000);
        }
    }
}

// Fonction pour afficher les détails d'une tâche
function showTaskDetails(taskId) {
    // Ici vous pouvez ouvrir un modal avec les détails de la tâche
    console.log('Afficher les détails de la tâche ' + taskId);
}

// Animation pulse pour les badges urgents
function animateUrgentBadges() {
    const urgentBadges = document.querySelectorAll('.priority-urgent, .nav-badge');
    urgentBadges.forEach(badge => {
        badge.style.animation = 'pulse 2s infinite';
    });
}

// CSS pour l'animation pulse
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Démarrer l'animation des badges urgents
setTimeout(animateUrgentBadges, 2000);

// Mise à jour automatique des statistiques (optionnel)
function updateDashboardStats() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
        stat.style.transform = 'scale(1.02)';
        stat.style.color = '#10b981';
        setTimeout(() => {
            stat.style.transform = 'scale(1)';
            stat.style.color = '#1f2937';
        }, 300);
    });
}

// Mettre à jour les stats toutes les 5 minutes (optionnel)
// setInterval(updateDashboardStats, 300000);

// Fonction pour afficher une notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        <i class="bi bi-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Simulation de notifications en temps réel
setTimeout(() => {
    showNotification('Nouveau rapport validé !', 'success');
}, 10000);
</script>
@endpush
