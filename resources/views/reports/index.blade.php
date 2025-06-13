{{-- filepath: c:\les_cours\Laravel\Projet-Amine\New_planiftech\resources\views\reports\index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports - PlanifTech ORMVAT')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
       SIDEBAR STYLES - Adaptatif selon le rôle
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

    .sidebar.admin {
        background: linear-gradient(180deg, #4c51bf 0%, #667eea 50%, #764ba2 100%);
    }

    .sidebar.technicien {
        background: linear-gradient(180deg, #047857 0%, #059669 50%, #10b981 100%);
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
       MAIN CONTENT
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

    /* Hero Section */
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
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        box-shadow: 0 10px 30px rgba(4, 120, 87, 0.3);
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

    /* Report Cards */
    .report-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .report-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
    }

    .report-status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-en_attente {
        background: #fef3c7;
        color: #d97706;
    }

    .status-valide {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-rejete {
        background: #fee2e2;
        color: #dc2626;
    }

    .report-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        margin-right: 6rem;
        color: #1f2937;
    }

    .report-description {
        color: #6b7280;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .report-info {
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

    .report-footer {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        color: white;
    }

    .action-btn.primary:hover {
        color: white;
    }

    /* Sidebar Filters */
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
        color: white;
    }

    .btn-primary.technicien {
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        box-shadow: 0 4px 14px rgba(4, 120, 87, 0.4);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    /* Empty State */
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
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
    }

    /* FAB */
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
        background: linear-gradient(135deg, #047857 0%, #10b981 100%);
        box-shadow: 0 10px 25px rgba(4, 120, 87, 0.3);
    }

    .fab:hover {
        transform: scale(1.1);
    }

    .fab.admin:hover {
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }

    .fab.technicien:hover {
        box-shadow: 0 15px 35px rgba(4, 120, 87, 0.4);
    }

    /* Mobile */
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

        .report-info {
            grid-template-columns: 1fr;
        }

        .report-footer {
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
                    </a>
                @endif
                <a href="{{ route('events.index') }}" class="nav-item">
                    <i class="bi bi-calendar-event"></i>
                    Événements
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item active {{ $roleClass }}">
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
            <!-- Hero Section -->
            <div class="hero-section {{ $roleClass }}">
                <div class="hero-content">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="display-5 mb-2">
                                <i class="bi bi-file-text me-3"></i>
                                {{ $isAdmin ? 'Gestion des Rapports' : 'Mes Rapports' }}
                            </h1>
                            <p class="lead mb-0">
                                {{ $isAdmin ? 'Vue d\'ensemble de tous les rapports d\'intervention' : 'Gérez vos rapports d\'intervention' }}
                            </p>
                        </div>
                        <a href="{{ route('reports.create') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-plus-lg me-2"></i>
                            Nouveau Rapport
                        </a>
                    </div>

                    <!-- Statistiques -->
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
                                    <span class="hero-stat-number">{{ $stats['en_attente'] ?? 0 }}</span>
                                    <div class="hero-stat-label">En attente</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['valides'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Validés</div>
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
                                    <div class="hero-stat-label">Mes Rapports</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['en_attente'] ?? 0 }}</span>
                                    <div class="hero-stat-label">En attente</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['valides'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Validés</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="hero-stat">
                                    <span class="hero-stat-number">{{ $stats['ce_mois'] ?? 0 }}</span>
                                    <div class="hero-stat-label">Ce mois</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    @if($reports->count() > 0)
                        @foreach($reports as $report)
                            <div class="report-card">
                                <div class="report-card-header">
                                    <span class="report-status-badge status-{{ $report->statut }}">
                                        {{ ucfirst(str_replace('_', ' ', $report->statut)) }}
                                    </span>
                                    <h3 class="report-title">{{ $report->titre }}</h3>
                                    <p class="report-description">{{ Str::limit($report->actions, 120) }}</p>
                                </div>

                                <div class="report-info">
                                    <div class="info-item">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $report->date_intervention->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>{{ Str::limit($report->lieu, 30) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-tools"></i>
                                        <span>{{ $report->type_intervention }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-person"></i>
                                        <span>{{ $report->utilisateur->nom_complet ?? 'Inconnu' }}</span>
                                    </div>
                                </div>

                                <div class="report-footer">
                                    <div class="d-flex align-items-center">
                                        @if($report->piecesJointes->count() > 0)
                                            <span class="text-muted me-3">
                                                <i class="bi bi-paperclip me-1"></i>
                                                {{ $report->piecesJointes->count() }} fichier(s)
                                            </span>
                                        @endif
                                        <span class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $report->date_creation->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="d-flex">
                                        <a href="{{ route('reports.show', $report) }}" class="action-btn primary {{ $roleClass }}" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($isAdmin || $report->id_utilisateur === auth()->id())
                                            @if($report->statut === 'en_attente')
                                                <a href="{{ route('reports.edit', $report) }}" class="action-btn" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if($isAdmin)
                                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="ms-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon {{ $roleClass }}">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <h3 class="mb-3">Aucun rapport trouvé</h3>
                            <p class="text-muted">Vous n'avez pas encore créé de rapports d'intervention.</p>
                            <a href="{{ route('reports.create') }}" class="btn btn-primary {{ $roleClass }}">
                                <i class="bi bi-plus-lg me-2"></i>
                                Créer un nouveau rapport
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Filters -->
                <div class="col-lg-3">
                    <div class="sidebar-card">
                        <h5 class="mb-3">Filtres</h5>
                        <form method="GET" action="{{ route('reports.index') }}">
                            <input type="text" name="search" class="filter-input {{ $roleClass }}" placeholder="Rechercher par titre ou lieu" value="{{ request('search') }}">
                            
                            <select name="statut" class="filter-input {{ $roleClass }}">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                            </select>

                            @if($isAdmin && isset($users))
                                <select name="user_id" class="filter-input {{ $roleClass }}">
                                    <option value="">Tous les utilisateurs</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nom_complet }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            <select name="type_intervention" class="filter-input {{ $roleClass }}">
                                <option value="">Tous les types</option>
                                @foreach($typesIntervention as $type)
                                    <option value="{{ $type }}" {{ request('type_intervention') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-primary {{ $roleClass }} w-100 mb-2">
                                <i class="bi bi-search me-2"></i>
                                Rechercher
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light {{ $roleClass }} w-100">
                                <i class="bi bi-x-lg me-2"></i>
                                Réinitialiser les filtres
                            </a>
                        </form>
                    </div>

                    <div class="sidebar-card">
                        <h5 class="mb-3">Statut</h5>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('reports.index', ['statut' => 'en_attente']) }}" class="nav-item {{ request('statut') === 'en_attente' ? 'active' : '' }}">
                                    <i class="bi bi-hourglass-split"></i>
                                    En attente
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index', ['statut' => 'valide']) }}" class="nav-item {{ request('statut') === 'valide' ? 'active' : '' }}">
                                    <i class="bi bi-check-circle"></i>
                                    Validés
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index', ['statut' => 'rejete']) }}" class="nav-item {{ request('statut') === 'rejete' ? 'active' : '' }}">
                                    <i class="bi bi-x-circle"></i>
                                    Rejetés
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="nav-item {{ !request('statut') ? 'active' : '' }}">
                                    <i class="bi bi-file-text"></i>
                                    Tous les rapports
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if($isAdmin && isset($users))
                        <div class="sidebar-card">
                            <h5 class="mb-3">Rapports par utilisateur</h5>
                            <ul class="list-unstyled">
                                @foreach($users as $user)
                                    <li>
                                        <a href="{{ route('reports.index', ['user_id' => $user->id]) }}" class="nav-item {{ request('user_id') == $user->id ? 'active' : '' }}">
                                            <i class="bi bi-person"></i>
                                            {{ $user->nom_complet }}
                                            @if($user->reports_count > 0)
                                                <span class="nav-badge">{{ $user->reports_count }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Floating Action Button -->
    <button class="fab {{ $roleClass }}" onclick="window.location.href='{{ route('reports.create') }}'" title="Créer un rapport">
        <i class="bi bi-plus-lg"></i>
    </button>
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
    // Animation d'entrée pour les cartes
    const reportCards = document.querySelectorAll('.report-card');
    reportCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Auto-submit du formulaire de filtres quand on change les selects
    document.querySelectorAll('select[name="statut"], select[name="user_id"], select[name="type_intervention"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Recherche avec délai
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});
</script>
@endpush