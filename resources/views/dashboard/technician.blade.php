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
    .stat-icon.events { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
    .stat-icon.invitations { background: linear-gradient(135deg, #ec4899, #db2777); color: white; }

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
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .section-card {
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
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 0.5rem;
        color: #10b981;
    }

    .section-body {
        padding: 1.5rem;
    }

    /* ============================================
       ÉVÉNEMENTS STYLES
       ============================================ */
    .event-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid #e5e7eb;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .event-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .event-item.invitation {
        border-left-color: #f59e0b;
        background: #fef3c7;
    }

    .event-item.confirmed {
        border-left-color: #10b981;
        background: #d1fae5;
    }

    .event-item.today {
        border-left-color: #3b82f6;
        background: #dbeafe;
    }

    .event-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
        color: white;
    }

    .event-icon.invitation { background: #f59e0b; }
    .event-icon.confirmed { background: #10b981; }
    .event-icon.today { background: #3b82f6; }

    .event-details {
        flex: 1;
    }

    .event-title {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
    }

    .event-meta {
        font-size: 0.8rem;
        color: #6b7280;
        margin: 0;
    }

    .event-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-event {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-confirm {
        background: #10b981;
        color: white;
    }

    .btn-confirm:hover {
        background: #059669;
        color: white;
        text-decoration: none;
    }

    .btn-decline {
        background: #ef4444;
        color: white;
    }

    .btn-decline:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }

    .btn-view {
        background: #6b7280;
        color: white;
    }

    .btn-view:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* ============================================
       TASK STYLES
       ============================================ */
    .task-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        border-left: 4px solid #e5e7eb;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .task-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .task-item.haute {
        border-left-color: #f59e0b;
    }

    .task-item.moyenne {
        border-left-color: #3b82f6;
    }

    .task-item.basse {
        border-left-color: #10b981;
    }

    .task-priority {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
        color: white;
    }

    .task-priority.haute { background: #f59e0b; }
    .task-priority.moyenne { background: #3b82f6; }
    .task-priority.basse { background: #10b981; }

    .task-content {
        flex: 1;
    }

    .task-title {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
    }

    .task-deadline {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .task-status {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-a_faire {
        background: #fef3c7;
        color: #92400e;
    }

    .status-en_cours {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-termine {
        background: #d1fae5;
        color: #065f46;
    }

    /* ============================================
       MODAL STYLES
       ============================================ */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        border-radius: 12px 12px 0 0;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 700;
        display: flex;
        align-items: center;
    }

    .modal-title i {
        margin-right: 0.5rem;
    }

    .btn-close {
        color: white;
        opacity: 0.8;
        filter: invert(1);
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        border-top: 1px solid #e5e7eb;
        padding: 1.5rem 2rem;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
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

        .content-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .mobile-toggle {
        display: none;
    }

    @media (max-width: 768px) {
        .mobile-toggle {
            display: block;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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

            <div class="sidebar-user">
                <div class="user-avatar-sidebar">
                    {{ substr(auth()->user()->prenom, 0, 1) }}{{ substr(auth()->user()->nom, 0, 1) }}
                </div>
                <div class="user-info-sidebar">
                    <h4>{{ auth()->user()->nom_complet }}</h4>
                    <p>{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Navigation</div>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    <i class="bi bi-house"></i>
                    Tableau de bord
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-item">
                    <i class="bi bi-list-check"></i>
                    Mes Tâches
                    @if($stats['tasks']['pending'] > 0)
                        <span class="nav-badge">{{ $stats['tasks']['pending'] }}</span>
                    @endif
                </a>
                <a href="{{ route('events.index') }}" class="nav-item">
                    <i class="bi bi-calendar-event"></i>
                    Événements
                    @if($stats['events']['invited'] > 0)
                        <span class="nav-badge">{{ $stats['events']['invited'] }}</span>
                    @endif
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    <i class="bi bi-file-text"></i>
                    Rapports
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
                    Équipe
                </a>
            </div>

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
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-content">
                    <h2>Bonjour {{ auth()->user()->prenom }} 👋</h2>
                    <p>Bienvenue sur votre espace PlanifTech ORMVAT. Voici un aperçu de vos activités.</p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="stats-grid">
                <!-- Tâches -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completed">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Mes Tâches</h3>
                            <div class="stat-value">{{ $stats['tasks']['total'] }}</div>
                            <div class="stat-trend">{{ $stats['tasks']['completed'] }} terminées</div>
                        </div>
                    </div>
                </div>

                <!-- Événements -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon events">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Événements</h3>
                            <div class="stat-value">{{ $stats['events']['total'] }}</div>
                            <div class="stat-trend">{{ $stats['events']['upcoming'] }} à venir</div>
                        </div>
                    </div>
                </div>

                <!-- Invitations en attente -->
                @if($stats['events']['invited'] > 0)
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon invitations">
                            <i class="bi bi-envelope-exclamation"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Invitations</h3>
                            <div class="stat-value">{{ $stats['events']['invited'] }}</div>
                            <div class="stat-trend">En attente de réponse</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rapports -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon reports">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Rapports</h3>
                            <div class="stat-value">{{ $stats['reports']['total'] }}</div>
                            <div class="stat-trend">{{ $stats['reports']['this_month'] }} ce mois</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu Principal -->
            <div class="content-grid">
                <!-- Invitations en attente -->
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-envelope-exclamation"></i>
                            Invitations en attente
                        </h3>
                    </div>
                    <div class="section-body">
                        @if($pendingInvitations->count() > 0)
                            @foreach($pendingInvitations as $event)
                                @php
                                    $participation = $event->participants->first();
                                @endphp
                                <div class="event-item invitation">
                                    <div class="event-icon invitation">
                                        <i class="bi bi-calendar-question"></i>
                                    </div>
                                    <div class="event-details">
                                        <h4 class="event-title">{{ $event->titre }}</h4>
                                        <p class="event-meta">
                                            {{ $event->date_debut->format('d/m/Y à H:i') }} - {{ $event->lieu }}
                                        </p>
                                    </div>
                                    <div class="event-actions">
                                        <button type="button" class="btn-event btn-confirm" 
                                                onclick="showParticipationModal('confirmer', {{ $event->id }})">
                                            <i class="bi bi-check-lg"></i> Confirmer
                                        </button>
                                        <button type="button" class="btn-event btn-decline" 
                                                onclick="showParticipationModal('decliner', {{ $event->id }})">
                                            <i class="bi bi-x-lg"></i> Décliner
                                        </button>
                                        <a href="{{ route('events.show', $event) }}" class="btn-event btn-view">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="bi bi-calendar-check"></i>
                                <p>Aucune invitation en attente</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Événements à venir -->
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-calendar-week"></i>
                            Événements à venir
                        </h3>
                    </div>
                    <div class="section-body">
                        @if($upcomingEvents->count() > 0)
                            @foreach($upcomingEvents as $event)
                                <div class="event-item confirmed">
                                    <div class="event-icon confirmed">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="event-details">
                                        <h4 class="event-title">{{ $event->titre }}</h4>
                                        <p class="event-meta">
                                            {{ $event->date_debut->format('d/m/Y à H:i') }} - {{ $event->lieu }}
                                        </p>
                                    </div>
                                    <div class="event-actions">
                                        <a href="{{ route('events.show', $event) }}" class="btn-event btn-view">
                                            <i class="bi bi-eye"></i> Voir
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <p>Aucun événement à venir</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Événements d'aujourd'hui -->
            @if($todayEvents->count() > 0)
            <div class="section-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="bi bi-calendar-day"></i>
                        Événements d'aujourd'hui
                    </h3>
                </div>
                <div class="section-body">
                    @foreach($todayEvents as $event)
                        <div class="event-item today">
                            <div class="event-icon today">
                                <i class="bi bi-calendar-day"></i>
                            </div>
                            <div class="event-details">
                                <h4 class="event-title">{{ $event->titre }}</h4>
                                <p class="event-meta">
                                    {{ $event->date_debut->format('H:i') }} - {{ $event->date_fin->format('H:i') }} | {{ $event->lieu }}
                                </p>
                            </div>
                            <div class="event-actions">
                                <a href="{{ route('events.show', $event) }}" class="btn-event btn-view">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tâches récentes -->
            <div class="content-grid">
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-list-task"></i>
                            Mes tâches prioritaires
                        </h3>
                    </div>
                    <div class="section-body">
                        @if($recentTasks->count() > 0)
                            @foreach($recentTasks as $task)
                                <div class="task-item {{ $task->priorite }}">
                                    <div class="task-priority {{ $task->priorite }}">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="task-content">
                                        <h4 class="task-title">{{ $task->titre }}</h4>
                                        <p class="task-deadline">
                                            Échéance: {{ $task->date_echeance->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <span class="task-status status-{{ $task->statut }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="bi bi-check2-all"></i>
                                <p>Toutes vos tâches sont à jour !</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Rapports récents -->
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-file-text"></i>
                            Rapports récents
                        </h3>
                    </div>
                    <div class="section-body">
                        @if($recentReports->count() > 0)
                            @foreach($recentReports as $report)
                                <div class="task-item">
                                    <div class="task-priority basse">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="task-content">
                                        <h4 class="task-title">{{ $report->titre }}</h4>
                                        <p class="task-deadline">
                                            {{ $report->date_intervention->format('d/m/Y') }} - {{ $report->lieu }}
                                        </p>
                                    </div>
                                    <a href="{{ route('reports.show', $report) }}" class="btn-event btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="bi bi-file-plus"></i>
                                <p>Aucun rapport récent</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal de participation -->
<div class="modal fade" id="participationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participationModalTitle">
                    <i class="bi bi-calendar-check"></i>
                    Confirmer la participation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="participationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="participationQuestion">Voulez-vous confirmer votre participation à cet événement ?</p>
                    
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                  placeholder="Ajoutez un commentaire si vous le souhaitez..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="participationConfirmBtn">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
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

// Fonction pour afficher le modal de participation
function showParticipationModal(action, eventId) {
    const modal = new bootstrap.Modal(document.getElementById('participationModal'));
    const form = document.getElementById('participationForm');
    const title = document.getElementById('participationModalTitle');
    const question = document.getElementById('participationQuestion');
    const btn = document.getElementById('participationConfirmBtn');

    if (action === 'confirmer') {
        title.innerHTML = '<i class="bi bi-check-circle"></i> Confirmer votre participation';
        question.textContent = 'Voulez-vous confirmer votre participation à cet événement ?';
        btn.textContent = 'Confirmer ma participation';
        btn.className = 'btn btn-success';
        form.action = `/events/${eventId}/confirmer`;
    } else {
        title.innerHTML = '<i class="bi bi-x-circle"></i> Décliner l\'invitation';
        question.textContent = 'Voulez-vous décliner votre participation à cet événement ?';
        btn.textContent = 'Décliner l\'invitation';
        btn.className = 'btn btn-danger';
        form.action = `/events/${eventId}/decliner`;
    }

    document.getElementById('commentaire').value = '';
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les cartes de statistiques
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Animation pour les sections
    const sections = document.querySelectorAll('.section-card');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            section.style.transition = 'all 0.6s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 600 + (index * 200));
    });
});

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
        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Rafraîchir automatiquement les invitations toutes les 5 minutes
setInterval(() => {
    // Vérifier s'il y a de nouvelles invitations (optionnel)
    fetch('/api/notifications/check', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.new_invitations > 0) {
            showNotification(`Vous avez ${data.new_invitations} nouvelle(s) invitation(s) !`, 'info');
            // Mettre à jour le badge dans la sidebar
            const badge = document.querySelector('.nav-item[href*="events"] .nav-badge');
            if (badge) {
                badge.textContent = data.new_invitations;
                badge.style.display = 'inline-block';
            }
        }
    })
    .catch(error => {
        console.log('Erreur lors de la vérification des notifications:', error);
    });
}, 300000); // 5 minutes
</script>
@endpush