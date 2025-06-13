@extends('layouts.app')

@section('title', 'Événements - PlanifTech ORMVAT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
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
       SIDEBAR STYLES ADAPTATIFS
       ============================================ */
    .sidebar {
        width: 280px;
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

    /* Couleurs selon le rôle */
    .sidebar.admin {
        background: linear-gradient(180deg, #4c51bf 0%, #667eea 50%, #764ba2 100%);
    }

    .sidebar.technicien {
        background: linear-gradient(180deg, #059669 0%, #10b981 50%, #34d399 100%);
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
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }

    .sidebar-logo i.admin {
        color: #e0e7ff;
    }

    .sidebar-logo i.technicien {
        color: #fbbf24;
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

    .user-avatar-sidebar.admin {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #3730a3;
    }

    .user-avatar-sidebar.technicien {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
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
        box-shadow: inset 0 0 20px rgba(255,255,255,0.1);
    }

    .nav-item.active.admin {
        border-right: 4px solid #e0e7ff;
    }

    .nav-item.active.technicien {
        border-right: 4px solid #fbbf24;
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

    /* Hero Section adaptatif */
    .hero-section {
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .hero-section.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .hero-section.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        box-shadow: 0 10px 30px rgba(5, 150, 105, 0.3);
    }

    .hero-section::before {
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

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-stat {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 0.5rem;
        backdrop-filter: blur(10px);
    }

    .hero-stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .hero-stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    /* Tabs adaptatifs */
    .custom-tabs {
        background: white;
        border-radius: 15px;
        padding: 0.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        gap: 0.5rem;
    }

    .custom-tab {
        flex: 1;
        padding: 1rem;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-tab.active.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: scale(1.02);
    }

    .custom-tab.active.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
        transform: scale(1.02);
    }

    .custom-tab:hover:not(.active) {
        background: #f3f4f6;
    }

    /* Event Cards */
    .event-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .event-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
    }

    .event-type-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-intervention {
        background: #fee2e2;
        color: #dc2626;
    }

    .type-reunion {
        background: #dbeafe;
        color: #2563eb;
    }

    .type-formation {
        background: #dcfce7;
        color: #16a34a;
    }

    .type-visite {
        background: #fed7aa;
        color: #ea580c;
    }

    .type-maintenance {
        background: #fef3c7;
        color: #d97706;
    }

    .type-inspection {
        background: #e0e7ff;
        color: #4338ca;
    }

    .type-audit {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .event-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        margin-right: 6rem;
        color: #1f2937;
    }

    .event-description {
        color: #6b7280;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .priority-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .priority-normale { background: #6b7280; }
    .priority-haute { background: #f59e0b; }
    .priority-urgente { background: #ef4444; }

    .status-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-planifie {
        background: #f3f4f6;
        color: #4b5563;
    }

    .status-en_cours {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-termine {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-annule {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-reporte {
        background: #fef3c7;
        color: #d97706;
    }

    .event-info {
        padding: 1.5rem;
        background: #f9fafb;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .event-footer {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .participants-count {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: #f3f4f6;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        margin-left: 0.5rem;
    }

    .action-btn:hover {
        background: #374151;
        color: white;
        transform: scale(1.1);
    }

    .action-btn.primary.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .action-btn.primary.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .action-btn.primary:hover {
        color: white;
    }

    /* Sidebar et filtres */
    .sidebar-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-input:focus.admin {
        border-color: #667eea;
    }

    .filter-input:focus.technicien {
        border-color: #10b981;
    }

    .calendar-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-height: 600px;
    }

    /* FAB adaptatif */
    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .fab.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .fab.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
    }

    .fab:hover {
        transform: scale(1.1);
    }

    .fab.admin:hover {
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }

    .fab.technicien:hover {
        box-shadow: 0 15px 35px rgba(5, 150, 105, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .empty-icon.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .empty-icon.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-light {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-light:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-primary.admin {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
    }

    .btn-primary.technicien {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    /* Mobile Toggle */
    .mobile-toggle {
        display: none;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .mobile-toggle {
            display: block;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1.2rem;
            color: white;
        }

        .mobile-toggle.admin {
            background: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .mobile-toggle.technicien {
            background: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .hero-section {
            padding: 1.5rem;
        }

        .custom-tabs {
            flex-direction: column;
        }

        .event-info {
            grid-template-columns: 1fr;
        }

        .event-footer {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="app-layout">
    @php
        $userRole = auth()->user()->role;
        $isAdmin = $userRole === 'admin';
        $roleClass = $isAdmin ? 'admin' : 'technicien';
    @endphp

    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle {{ $roleClass }}" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Adaptative -->
    <aside class="sidebar {{ $roleClass }}" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-water {{ $roleClass }}"></i>
                <div>
                    <h1 class="sidebar-brand">PlanifTech</h1>
                    <p class="sidebar-subtitle">ORMVAT</p>
                </div>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar-sidebar {{ $roleClass }}">
                    {{ substr(auth()->user()->prenom, 0, 1) }}{{ substr(auth()->user()->nom, 0, 1) }}
                </div>
                <div class="user-info-sidebar">
                    <h4>{{ auth()->user()->nom_complet }}</h4>
                    <p>{{ $isAdmin ? 'Administrateur' : 'Technicien' }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Navigation</div>
                <a href="{{ route('dashboard') }}" class="nav-item">
                    <i class="bi bi-house"></i>
                    Tableau de bord
                </a>
                @if($isAdmin)
                    <a href="{{ route('tasks.index') }}" class="nav-item">
                        <i class="bi bi-list-check"></i>
                        Gestion des Tâches
                    </a>
                @else
                    <a href="{{ route('tasks.index') }}" class="nav-item">
                        <i class="bi bi-list-check"></i>
                        Mes Tâches
                        @if(isset($stats['tasks']['pending']) && $stats['tasks']['pending'] > 0)
                            <span class="nav-badge">{{ $stats['tasks']['pending'] }}</span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('events.index') }}" class="nav-item active {{ $roleClass }}">
                    <i class="bi bi-calendar-event"></i>
                    Événements
                    @if(isset($stats['invited']) && $stats['invited'] > 0)
                        <span class="nav-badge">{{ $stats['invited'] }}</span>
                    @endif
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    <i class="bi bi-file-text"></i>
                    {{ $isAdmin ? 'Tous les Rapports' : 'Mes Rapports' }}
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Gestion</div>
                <a href="{{ route('projects.index') }}" class="nav-item">
                    <i class="bi bi-folder"></i>
                    Projets
                </a>
                <a href="{{ route('users.index') }}" class="nav-item">
                    <i class="bi bi-people"></i>
                    {{ $isAdmin ? 'Gestion Utilisateurs' : 'Équipe' }}
                </a>
            </div>

            @if($isAdmin)
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="{{ route('admin.logs') }}" class="nav-item">
                    <i class="bi bi-journal-text"></i>
                    Journaux d'activité
                </a>
            </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Compte</div>
                <a href="{{ route('profile.edit') }}" class="nav-item">
                    <i class="bi bi-person-gear"></i>
                    Mon Profil
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
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
            <!-- Hero Section Adaptative -->
            <div class="hero-section {{ $roleClass }}">
                <div class="hero-content">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="display-5 mb-2">
                                <i class="bi bi-calendar-event me-3"></i>
                                {{ $isAdmin ? 'Gestion des Événements' : 'Mes Événements' }}
                            </h1>
                            <p class="lead mb-0">
                                {{ $isAdmin ? 'Vue d\'ensemble de tous les événements PlanifTech' : 'Gérez vos événements et invitations' }}
                            </p>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technicien')
                            <a href="{{ route('events.create') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-plus-lg me-2"></i>
                                Nouvel Événement
                            </a>
                        @endif
                    </div>

                    <!-- Statistiques Adaptatives -->
                    <div class="row">
                        @if($isAdmin)
                            <div class="col-md-2">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['total'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Total</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['planifies'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Planifiés</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['en_cours'] ?? 0 }}</span>
                                    <div class="hero-stat-label">En cours</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['cette_semaine'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Cette semaine</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['ce_mois'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Ce mois</div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['total'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Mes Événements</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['organises'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Organisés</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['confirmes'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Confirmés</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['a_venir'] ?? 0 }}</span>
                                    <div class="hero-stat-label">À venir</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="custom-tabs">
                <button class="custom-tab {{ $view === 'cards' ? 'active ' . $roleClass : '' }}" onclick="switchView('cards')">
                    <i class="bi bi-grid-3x3-gap me-2"></i>
                    Vue Cartes
                </button>
                <button class="custom-tab {{ $view === 'calendar' ? 'active ' . $roleClass : '' }}" onclick="switchView('calendar')">
                    <i class="bi bi-calendar3 me-2"></i>
                    Calendrier
                </button>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Cards View -->
                    <div id="cards-view" class="{{ $view === 'cards' ? '' : 'd-none' }}">
                        @if($events->count() > 0)
                            @foreach($events as $event)
                                <div class="event-card">
                                    <div class="event-card-header">
                                        <span class="event-type-badge type-{{ $event->type }}">
                                            {{ $event->type_libelle }}
                                        </span>
                                        <h3 class="event-title">{{ $event->titre }}</h3>
                                        <p class="event-description">{{ Str::limit($event->description, 120) }}</p>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="priority-dot priority-{{ $event->priorite }}"></span>
                                            <span class="me-3">{{ ucfirst($event->priorite) }}</span>
                                            <span class="status-pill status-{{ $event->statut }}">
                                                {{ ucfirst(str_replace('_', ' ', $event->statut)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="event-info">
                                        <div class="info-item">
                                            <i class="bi bi-calendar3"></i>
                                            <span>{{ $event->date_debut->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-clock"></i>
                                            <span>{{ $event->date_debut->diffInHours($event->date_fin) }}h</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ Str::limit($event->lieu, 30) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-person"></i>
                                            <span>{{ $event->organisateur->nom_complet ?? 'Inconnu' }}</span>
                                        </div>
                                    </div>

                                    <div class="event-footer">
                                        <div class="participants-count">
                                            <i class="bi bi-people me-1"></i>
                                            {{ $event->participants->count() ?? 0 }} participant(s)
                                        </div>
                                        <div class="d-flex">
                                            <a href="{{ route('events.show', $event) }}" class="action-btn primary {{ $roleClass }}" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($isAdmin || $event->id_organisateur === auth()->id())
                                                <a href="{{ route('events.edit', $event) }}" class="action-btn" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete({{ $event->id }})" class="action-btn" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $events->withQueryString()->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon {{ $roleClass }}">
                                    <i class="bi bi-calendar-x"></i>
                                </div>
                                <h3>{{ $isAdmin ? 'Aucun événement trouvé' : 'Aucun événement' }}</h3>
                                <p class="text-muted">
                                    {{ $isAdmin ? 'Aucun événement ne correspond à vos critères de recherche.' : 'Vous n\'avez pas encore d\'événements planifiés.' }}
                                </p>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technicien')
                                    <a href="{{ route('events.create') }}" class="btn btn-primary {{ $roleClass }}">
                                        <i class="bi bi-plus-lg me-2"></i>
                                        Créer votre premier événement
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Calendar View -->
                    <div id="calendar-view" class="{{ $view === 'calendar' ? '' : 'd-none' }}">
                        <div class="calendar-container">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Filters -->
                <div class="col-lg-3">
                    <!-- Filters -->
                    <div class="sidebar-card">
                        <h5 class="mb-3">
                            <i class="bi bi-funnel me-2"></i>
                            Filtres
                        </h5>
                        <form id="filterForm" method="GET">
                            <input type="hidden" name="view" value="{{ $view }}" id="viewInput">
                            
                            <div class="mb-3">
                                <label class="form-label">Recherche</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       class="filter-input {{ $roleClass }}" placeholder="Titre, description, lieu...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="filter-input">
                                    <option value="">Tous les types</option>
                                    @foreach($typesOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="filter-input">
                                    <option value="">Tous les statuts</option>
                                    @foreach($statutsOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Priorité</label>
                                <select name="priorite" class="filter-input">
                                    <option value="">Toutes les priorités</option>
                                    @foreach($prioritesOptions as $key => $label)
                                        <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(!$isAdmin)
                                <div class="mb-3">
                                    <label class="form-label">Ma participation</label>
                                    <select name="participation" class="filter-input">
                                        <option value="">Toutes</option>
                                        <option value="invite" {{ request('participation') === 'invite' ? 'selected' : '' }}>
                                            En attente
                                        </option>
                                        <option value="confirme" {{ request('participation') === 'confirme' ? 'selected' : '' }}>
                                            Confirmée
                                        </option>
                                        <option value="decline" {{ request('participation') === 'decline' ? 'selected' : '' }}>
                                            Déclinée
                                        </option>
                                    </select>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary {{ $roleClass }} w-100">
                                <i class="bi bi-search me-2"></i>
                                Filtrer
                            </button>
                        </form>
                    </div>

                    <!-- Quick Actions -->
                    <div class="sidebar-card">
                        <h5 class="mb-3">
                            <i class="bi bi-lightning me-2"></i>
                            Actions rapides
                        </h5>
                        <div class="d-grid gap-2">
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technicien')
                                <a href="{{ route('events.create') }}" class="btn btn-primary {{ $roleClass }}">
                                    <i class="bi bi-plus-lg me-2"></i>
                                    Nouveau
                                </a>
                            @endif
                            @if($isAdmin)
                                <a href="{{ route('events.export', request()->query()) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-download me-2"></i>
                                    Exporter
                                </a>
                            @endif
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Actualiser
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Floating Action Button -->
@if(auth()->user()->role === 'admin' || auth()->user()->role === 'technicien')
    <button class="fab {{ $roleClass }}" onclick="window.location.href='{{ route('events.create') }}'" title="Créer un événement">
        <i class="bi bi-plus-lg"></i>
    </button>
@endif

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    const userRole = '{{ $userRole }}';
    const isAdmin = userRole === 'admin';

    // Toggle sidebar pour mobile
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    };

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

    // Initialize Calendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                list: 'Liste'
            },
            events: '{{ route('events.calendar.data') }}',
            eventClick: function(info) {
                window.location.href = '/events/' + info.event.id;
            },
            dateClick: function(info) {
                if (isAdmin || '{{ auth()->user()->role }}' === 'technicien') {
                    const createUrl = new URL('{{ route('events.create') }}', window.location.origin);
                    createUrl.searchParams.set('date', info.dateStr);
                    window.location.href = createUrl.toString();
                }
            },
            eventDidMount: function(info) {
                // Personnaliser l'apparence des événements selon le rôle
                if (userRole === 'admin') {
                    info.el.style.borderColor = '#667eea';
                } else {
                    info.el.style.borderColor = '#10b981';
                }
            }
        });

        calendar.render();
    }

    // View Switching
    window.switchView = function(view) {
        // Hide all views
        document.querySelectorAll('[id$="-view"]').forEach(function(el) {
            el.classList.add('d-none');
        });

        // Show selected view
        document.getElementById(view + '-view').classList.remove('d-none');

        // Update active tab
        document.querySelectorAll('.custom-tab').forEach(function(tab) {
            tab.classList.remove('active', 'admin', 'technicien');
        });
        const activeTab = document.querySelector('[onclick="switchView(\'' + view + '\')"]');
        activeTab.classList.add('active', userRole);

        // Update URL
        var url = new URL(window.location);
        url.searchParams.set('view', view);
        window.history.replaceState({}, '', url);

        // Update hidden input
        document.getElementById('viewInput').value = view;

        // Initialize calendar if needed
        if (view === 'calendar' && !calendar) {
            setTimeout(initCalendar, 100);
        }
    };

    // Delete Confirmation
    window.confirmDelete = function(eventId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
            var form = document.getElementById('deleteForm');
            form.action = '/events/' + eventId;
            form.submit();
        }
    };

    // Auto-submit filters on change
    document.querySelectorAll('#filterForm select').forEach(function(select) {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Search with delay
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 800);
        });
    }

    // Initialize calendar if calendar view is active
    if (!document.getElementById('calendar-view').classList.contains('d-none')) {
        initCalendar();
    }

    // Animation pour les cartes d'événements
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush