{{--
==================================================
FICHIER : resources/views/reports/index.blade.php
DESCRIPTION : Gestion des rapports d'intervention
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Gestion des rapports')

@push('styles')
<style>
    body {
        background-color: #f7fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .app-layout {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar avec thème orange pour les rapports */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #ea580c 0%, #f97316 50%, #fb923c 100%);
        color: white;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
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
    }

    .sidebar-brand {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }

    .sidebar-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .main-content {
        margin-left: 280px;
        padding: 1.5rem;
        background-color: #f7fafc;
        min-height: 100vh;
        width: calc(100% - 280px);
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
    }

    .stat-icon.total { background: linear-gradient(135deg, #ea580c, #dc2626); color: white; }
    .stat-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-icon.validated { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-icon.monthly { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }

    .reports-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .table-header {
        background: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .filters-row {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .filter-select {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        min-width: 150px;
    }

    .reports-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .reports-table th {
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

    .reports-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .reports-table tr:hover {
        background-color: #f9fafb;
    }

    .report-info {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .report-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        background: linear-gradient(135deg, #ea580c, #f97316);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .report-details h4 {
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    .report-details p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }

    .type-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .type-maintenance { background: #fef3c7; color: #92400e; }
    .type-inspection { background: #dbeafe; color: #1e40af; }
    .type-reparation { background: #fee2e2; color: #991b1b; }
    .type-controle { background: #dcfce7; color: #166534; }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: lowercase;
    }

    .status-en_attente { background: #fef3c7; color: #92400e; }
    .status-en_revision { background: #dbeafe; color: #1e40af; }
    .status-valide { background: #dcfce7; color: #166534; }
    .status-rejete { background: #fee2e2; color: #991b1b; }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .btn-view { background: #eff6ff; color: #1e40af; }
    .btn-edit { background: #fef3c7; color: #92400e; }
    .btn-pdf { background: #fee2e2; color: #991b1b; }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-file-earmark-text"></i>
                <div>
                    <h1 class="sidebar-brand">Rapports</h1>
                    <p class="sidebar-subtitle">ORMVAT</p>
                </div>
            </div>
        </div>

        <nav style="flex: 1; padding: 1rem 0;">
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.7rem; font-weight: 700; color: rgba(255, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1px; padding: 0 1.5rem; margin-bottom: 0.75rem;">Navigation</div>
                <a href="{{ route('dashboard') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-speedometer2" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Tableau de bord
                </a>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.7rem; font-weight: 700; color: rgba(255, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1px; padding: 0 1.5rem; margin-bottom: 0.75rem;">Rapports</div>
                <a href="{{ route('reports.index') }}" style="display: block; color: white; text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500; background: rgba(255, 255, 255, 0.18); border-right: 4px solid #fbbf24;">
                    <i class="bi bi-file-text" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Mes rapports
                </a>
                <a href="{{ route('reports.create') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-plus-circle" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Nouveau rapport
                </a>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.7rem; font-weight: 700; color: rgba(255, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1px; padding: 0 1.5rem; margin-bottom: 0.75rem;">Modules</div>
                <a href="{{ route('tasks.index') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-list-check" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Tâches
                </a>
                <a href="{{ route('events.index') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-calendar-event" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Événements
                </a>
                <a href="{{ route('projects.index') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-folder" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Projets
                </a>
            </div>
        </nav>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" style="display: flex; align-items: center; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 500; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); width: 100%; justify-content: center;">
                    <i class="bi bi-box-arrow-right" style="margin-right: 0.5rem;"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
                        Gestion des Rapports
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        Consultez et gérez tous les rapports d'intervention
                    </p>
                </div>
                <a href="{{ route('reports.create') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #ea580c, #dc2626); color: white; text-decoration: none;">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nouveau Rapport
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="bi bi-file-text"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['total'] ?? 47 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Total Rapports</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="bi bi-clock"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['en_attente'] ?? 8 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">En Attente</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon validated">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['valide'] ?? 35 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Validés</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon monthly">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['ce_mois'] ?? 12 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Ce Mois</div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="reports-table">
            <div class="table-header">
                <h3 class="table-title">Liste des Rapports</h3>

                <div class="filters-row">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Rechercher un rapport..." id="searchInput">
                    </div>

                    <select class="filter-select" id="typeFilter">
                        <option value="">Tous les types</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inspection">Inspection</option>
                        <option value="reparation">Réparation</option>
                        <option value="controle">Contrôle</option>
                    </select>

                    <select class="filter-select" id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_revision">En révision</option>
                        <option value="valide">Validé</option>
                        <option value="rejete">Rejeté</option>
                    </select>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Rapport</th>
                            <th>Type</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTableBody">
                        @forelse($reports ?? [] as $report)
                        <tr>
                            <td>
                                <div class="report-info">
                                    <div class="report-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="report-details">
                                        <h4>{{ $report->titre ?? 'Intervention Station A' }}</h4>
                                        <p>{{ $report->lieu ?? 'Zone A - Station de pompage principale' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-{{ $report->type_intervention ?? 'maintenance' }}">
                                    {{ $report->type_intervention_libelle ?? 'Maintenance' }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">
                                        {{ $report->utilisateur->nom_complet ?? auth()->user()->prenom . ' ' . auth()->user()->nom }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        {{ ucfirst($report->utilisateur->role ?? auth()->user()->role) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $report->statut ?? 'valide' }}">
                                    {{ $report->statut_nom ?? 'Validé' }}
                                </span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ ($report->date_intervention ?? now())->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('reports.show', $report->id ?? 1) }}" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin' || ($report->utilisateur_id ?? auth()->id()) === auth()->id())
                                    <a href="{{ route('reports.edit', $report->id ?? 1) }}" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('reports.pdf', $report->id ?? 1) }}" class="btn-action btn-pdf" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <!-- Exemples de rapports pour la démo -->
                        <tr>
                            <td>
                                <div class="report-info">
                                    <div class="report-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="report-details">
                                        <h4>Maintenance Station A</h4>
                                        <p>Zone A - Station de pompage principale</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-maintenance">Maintenance</span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">
                                        {{ auth()->user()->prenom }} {{ auth()->user()->nom }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-valide">Validé</span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ now()->subDays(2)->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-pdf" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="report-info">
                                    <div class="report-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="report-details">
                                        <h4>Inspection Canal B</h4>
                                        <p>Zone B - Canal secondaire Est</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-inspection">Inspection</span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">Ahmed Bennani</div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">Technicien</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-en_revision">En révision</span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ now()->subDays(1)->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-pdf" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="report-info">
                                    <div class="report-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="report-details">
                                        <h4>Réparation Vanne V-12</h4>
                                        <p>Zone C - Réseau de distribution</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-reparation">Réparation</span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">Fatima Khalil</div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">Technicien</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-en_attente">En attente</span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ now()->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-pdf" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="report-info">
                                    <div class="report-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="report-details">
                                        <h4>Contrôle Qualité Eau</h4>
                                        <p>Zone A - Bassin de traitement</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="type-badge type-controle">Contrôle</span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">Omar Rachid</div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">Technicien</div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-valide">Validé</span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ now()->subDays(3)->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn-action btn-pdf" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
// Fonction de recherche
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#reportsTableBody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filtres
document.getElementById('typeFilter').addEventListener('change', function() {
    filterTable();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#reportsTableBody tr');

    rows.forEach(row => {
        let showRow = true;

        if (typeFilter) {
            const typeBadge = row.querySelector('.type-badge');
            if (!typeBadge || !typeBadge.classList.contains('type-' + typeFilter)) {
                showRow = false;
            }
        }

        if (statusFilter && showRow) {
            const statusBadge = row.querySelector('.status-badge');
            if (!statusBadge || !statusBadge.classList.contains('status-' + statusFilter)) {
                showRow = false;
            }
        }

        row.style.display = showRow ? '' : 'none';
    });
}

// Animation des cartes
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush

{{--
==================================================
FICHIER : resources/views/reports/create.blade.php
DESCRIPTION : Création d'un nouveau rapport d'intervention
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Créer un rapport')

@push('styles')
<style>
    .main-content {
        margin-left: 280px;
        padding: 1.5rem;
        background-color: #f7fafc;
        min-height: 100vh;
        width: calc(100% - 280px);
    }

    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #fed7aa, #fdba74);
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-body {
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 0.5rem;
        color: #ea580c;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }

    .file-upload {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-upload:hover {
        border-color: #ea580c;
        background-color: #fef7f2;
    }

    .file-upload.dragover {
        border-color: #ea580c;
        background-color: #fef7f2;
    }

    .uploaded-files {
        margin-top: 1rem;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .file-info {
        display: flex;
        align-items: center;
    }

    .file-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 4px;
        background: #ea580c;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .btn-remove-file {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
    }

    .btn-group {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ea580c, #dc2626);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
    }

    .type-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .type-option {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .type-option:hover {
        border-color: #ea580c;
        background-color: #fef7f2;
    }

    .type-option.selected {
        border-color: #ea580c;
        background-color: #fef7f2;
    }

    .type-option input[type="radio"] {
        display: none;
    }

    .type-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .type-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .type-desc {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .required::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
                        Créer un Rapport d'Intervention
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        Documentez vos interventions techniques avec précision
                    </p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center;">
                    <i class="bi bi-file-earmark-plus text-orange-600 me-2"></i>
                    Nouveau Rapport d'Intervention
                </h2>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" id="reportForm">
                    @csrf

                    <!-- Informations générales -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-info-circle"></i>
                            Informations Générales
                        </h3>

                        <div class="mb-3">
                            <label for="titre" class="form-label required">Titre du rapport</label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                   id="titre" name="titre" value="{{ old('titre') }}" required
                                   placeholder="Ex: Maintenance pompe station A - Secteur Nord">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_intervention" class="form-label required">Date d'intervention</label>
                                <input type="date" class="form-control @error('date_intervention') is-invalid @enderror"
                                       id="date_intervention" name="date_intervention"
                                       value="{{ old('date_intervention', date('Y-m-d')) }}" required>
                                @error('date_intervention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lieu" class="form-label required">Lieu d'intervention</label>
                                <input type="text" class="form-control @error('lieu') is-invalid @enderror"
                                       id="lieu" name="lieu" value="{{ old('lieu') }}" required
                                       placeholder="Ex: Zone A - Station de pompage principale">
                                @error('lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Type d'intervention -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-tag"></i>
                            Type d'Intervention
                        </h3>

                        <div class="type-options">
                            <div class="type-option" onclick="selectType('maintenance')">
                                <input type="radio" name="type_intervention" value="maintenance" id="type_maintenance">
                                <span class="type-icon">🔧</span>
                                <div class="type-title">Maintenance</div>
                                <div class="type-desc">Entretien préventif ou correctif</div>
                            </div>

                            <div class="type-option" onclick="selectType('inspection')">
                                <input type="radio" name="type_intervention" value="inspection" id="type_inspection">
                                <span class="type-icon">🔍</span>
                                <div class="type-title">Inspection</div>
                                <div class="type-desc">Contrôle et vérification</div>
                            </div>

                            <div class="type-option" onclick="selectType('reparation')">
                                <input type="radio" name="type_intervention" value="reparation" id="type_reparation">
                                <span class="type-icon">⚠️</span>
                                <div class="type-title">Réparation</div>
                                <div class="type-desc">Intervention d'urgence</div>
                            </div>

                            <div class="type-option" onclick="selectType('controle')">
                                <input type="radio" name="type_intervention" value="controle" id="type_controle">
                                <span class="type-icon">✅</span>
                                <div class="type-title">Contrôle</div>
                                <div class="type-desc">Mesures et analyses</div>
                            </div>
                        </div>

                        @error('type_intervention')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description détaillée -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-file-text"></i>
                            Description de l'Intervention
                        </h3>

                        <div class="mb-3">
                            <label for="actions" class="form-label required">Actions réalisées</label>
                            <textarea class="form-control @error('actions') is-invalid @enderror"
                                      id="actions" name="actions" rows="4" required
                                      placeholder="Décrivez en détail les actions que vous avez effectuées...">{{ old('actions') }}</textarea>
                            @error('actions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resultats" class="form-label required">Résultats obtenus</label>
                            <textarea class="form-control @error('resultats') is-invalid @enderror"
                                      id="resultats" name="resultats" rows="3" required
                                      placeholder="Quels sont les résultats de votre intervention ?">{{ old('resultats') }}</textarea>
                            @error('resultats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="problemes" class="form-label">Problèmes rencontrés</label>
                            <textarea class="form-control @error('problemes') is-invalid @enderror"
                                      id="problemes" name="problemes" rows="3"
                                      placeholder="Décrivez les difficultés ou problèmes rencontrés (optionnel)">{{ old('problemes') }}</textarea>
                            @error('problemes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="recommandations" class="form-label">Recommandations</label>
                            <textarea class="form-control @error('recommandations') is-invalid @enderror"
                                      id="recommandations" name="recommandations" rows="3"
                                      placeholder="Vos recommandations pour l'avenir (optionnel)">{{ old('recommandations') }}</textarea>
                            @error('recommandations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Associations -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-link"></i>
                            Associations
                        </h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_tache" class="form-label">Tâche associée</label>
                                <select class="form-select @error('id_tache') is-invalid @enderror" id="id_tache" name="id_tache">
                                    <option value="">Aucune tâche</option>
                                    @if(isset($tasks))
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" {{ old('id_tache') == $task->id ? 'selected' : '' }}>
                                                {{ $task->titre }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_tache')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_evenement" class="form-label">Événement associé</label>
                                <select class="form-select @error('id_evenement') is-invalid @enderror" id="id_evenement" name="id_evenement">
                                    <option value="">Aucun événement</option>
                                    @if(isset($events))
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ old('id_evenement') == $event->id ? 'selected' : '' }}>
                                                {{ $event->titre }} - {{ $event->date_debut->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_evenement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pièces jointes -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-paperclip"></i>
                            Pièces Jointes
                        </h3>

                        <div class="file-upload" id="fileUploadArea">
                            <div>
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                                <p style="color: #6b7280; margin-bottom: 0.5rem;">
                                    <strong>Cliquez pour sélectionner</strong> ou glissez-déposez vos fichiers ici
                                </p>
                                <p style="color: #9ca3af; font-size: 0.875rem;">
                                    Photos, documents PDF, Word acceptés (max 10MB par fichier)
                                </p>
                                <input type="file" id="fileInput" name="pieces_jointes[]" multiple
                                       accept="image/*,.pdf,.doc,.docx" style="display: none;">
                            </div>
                        </div>

                        <div class="uploaded-files" id="uploadedFiles" style="display: none;">
                            <h6 style="margin-bottom: 1rem;">Fichiers sélectionnés :</h6>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="btn-group">
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Créer le Rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sélection du type d'intervention
function selectType(type) {
    document.querySelectorAll('.type-option').forEach(option => {
        option.classList.remove('selected');
    });

    event.currentTarget.classList.add('selected');
    document.getElementById('type_' + type).checked = true;
}

// Gestion des fichiers
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const uploadedFiles = document.getElementById('uploadedFiles');
    let selectedFiles = [];

    // Click sur la zone de upload
    fileUploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Drag & Drop
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');

        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // Sélection de fichiers
    fileInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        handleFiles(files);
    });

    function handleFiles(files) {
        files.forEach(file => {
            if (validateFile(file)) {
                selectedFiles.push(file);
                displayFile(file);
            }
        });

        updateFileInput();

        if (selectedFiles.length > 0) {
            uploadedFiles.style.display = 'block';
        }
    }

    function validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (file.size > maxSize) {
            alert(`Le fichier "${file.name}" est trop volumineux (max 10MB)`);
            return false;
        }

        if (!allowedTypes.some(type => file.type.startsWith(type) || file.type === type)) {
            alert(`Le type de fichier "${file.name}" n'est pas autorisé`);
            return false;
        }

        return true;
    }

    function displayFile(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <div class="file-info">
                <div class="file-icon">
                    <i class="bi bi-${getFileIcon(file)}"></i>
                </div>
                <div>
                    <div style="font-weight: 500; color: #1f2937;">${file.name}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">${formatFileSize(file.size)}</div>
                </div>
            </div>
            <button type="button" class="btn-remove-file" onclick="removeFile('${file.name}')">
                <i class="bi bi-x"></i>
            </button>
        `;

        uploadedFiles.appendChild(fileItem);
    }

    function getFileIcon(file) {
        if (file.type.startsWith('image/')) return 'image';
        if (file.type === 'application/pdf') return 'file-pdf';
        if (file.type.includes('word')) return 'file-word';
        return 'file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    // Fonction globale pour supprimer un fichier
    window.removeFile = function(fileName) {
        selectedFiles = selectedFiles.filter(file => file.name !== fileName);

        // Supprimer l'élément visuel
        const fileItems = uploadedFiles.querySelectorAll('.file-item');
        fileItems.forEach(item => {
            if (item.textContent.includes(fileName)) {
                item.remove();
            }
        });

        updateFileInput();

        if (selectedFiles.length === 0) {
            uploadedFiles.style.display = 'none';
        }
    };

    // Validation du formulaire
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const typeSelected = document.querySelector('input[name="type_intervention"]:checked');
        if (!typeSelected) {
            e.preventDefault();
            alert('Veuillez sélectionner un type d\'intervention');
            return false;
        }
    });
});
</script>
@endpush
