{{--
==================================================
FICHIER : resources/views/projects/index.blade.php
DESCRIPTION : Gestion des projets
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Gestion des projets')

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

    /* Sidebar avec thème violet pour les projets */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
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

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .project-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .project-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #7c3aed, #8b5cf6);
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .project-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .project-zone {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .project-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-planifie { background: #fef3c7; color: #92400e; }
    .status-en_cours { background: #dbeafe; color: #1e40af; }
    .status-termine { background: #dcfce7; color: #166534; }
    .status-suspendu { background: #fee2e2; color: #991b1b; }

    .project-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .project-progress {
        margin-bottom: 1.5rem;
    }

    .progress-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .progress-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .progress-percent {
        font-size: 0.875rem;
        font-weight: 600;
        color: #7c3aed;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #7c3aed, #8b5cf6);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .project-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }

    .meta-item {
        text-align: center;
    }

    .meta-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #7c3aed;
        display: block;
    }

    .meta-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .project-dates {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .project-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .btn-action {
        padding: 0.5rem 1rem;
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
    .btn-delete { background: #fee2e2; color: #991b1b; }

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

    .stat-icon.total { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }
    .stat-icon.active { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
    .stat-icon.completed { background: linear-gradient(135deg, #059669, #047857); color: white; }
    .stat-icon.tasks { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
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
                <i class="bi bi-folder-check"></i>
                <div>
                    <h1 class="sidebar-brand">Projets</h1>
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
                <div style="font-size: 0.7rem; font-weight: 700; color: rgba(255, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1px; padding: 0 1.5rem; margin-bottom: 0.75rem;">Projets</div>
                <a href="{{ route('projects.index') }}" style="display: block; color: white; text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500; background: rgba(255, 255, 255, 0.18); border-right: 4px solid #fbbf24;">
                    <i class="bi bi-folder" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Gestion projets
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
                <a href="{{ route('reports.index') }}" style="display: block; color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.875rem 1.5rem; font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-file-text" style="width: 1.5rem; margin-right: 0.75rem;"></i>
                    Rapports
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
                        Gestion des Projets
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        Suivez l'avancement de vos projets hydrauliques et agricoles
                    </p>
                </div>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('projects.create') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; text-decoration: none;">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nouveau Projet
                </a>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="bi bi-folder"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['total'] ?? 12 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Total Projets</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="bi bi-play-circle"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['en_cours'] ?? 5 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">En Cours</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['termine'] ?? 6 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Terminés</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon tasks">
                    <i class="bi bi-list-task"></i>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #1f2937;">{{ $stats['taches'] ?? 48 }}</div>
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500; text-transform: uppercase;">Tâches Total</div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="projects-grid">
            @forelse($projects ?? [] as $project)
            <div class="project-card">
                <div class="project-header">
                    <div>
                        <h3 class="project-title">{{ $project->nom ?? 'Modernisation Station A' }}</h3>
                        <p class="project-zone">{{ $project->zone_geographique ?? 'Zone A - Secteur Nord' }}</p>
                    </div>
                    <span class="project-status status-{{ $project->statut ?? 'en_cours' }}">
                        {{ ucfirst(str_replace('_', ' ', $project->statut ?? 'en_cours')) }}
                    </span>
                </div>

                <p class="project-description">
                    {{ Str::limit($project->description ?? 'Modernisation complète de la station de pompage avec installation de nouveaux équipements et amélioration du système de distribution.', 120) }}
                </p>

                <div class="project-progress">
                    <div class="progress-header">
                        <span class="progress-label">Avancement</span>
                        <span class="progress-percent">{{ $project->pourcentage_avancement ?? 65 }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: {{ $project->pourcentage_avancement ?? 65 }}%"></div>
                    </div>
                </div>

                <div class="project-meta">
                    <div class="meta-item">
                        <span class="meta-number">{{ $project->taches_count ?? 8 }}</span>
                        <span class="meta-label">Tâches</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-number">{{ $project->evenements_count ?? 3 }}</span>
                        <span class="meta-label">Événements</span>
                    </div>
                </div>

                <div class="project-dates">
                    <i class="bi bi-calendar me-1"></i>
                    {{ ($project->date_debut ?? now()->subMonths(2))->format('d/m/Y') }} →
                    {{ ($project->date_fin ?? now()->addMonths(1))->format('d/m/Y') }}
                </div>

                <div class="project-actions">
                    <a href="{{ route('projects.show', $project->id ?? 1) }}" class="btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>
                        Voir
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('projects.edit', $project->id ?? 1) }}" class="btn-action btn-edit">
                        <i class="bi bi-pencil me-1"></i>
                        Modifier
                    </a>
                    <button onclick="confirmDelete({{ $project->id ?? 1 }})" class="btn-action btn-delete" style="border: none;">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <!-- Exemple de projets pour la démo -->
            <div class="project-card">
                <div class="project-header">
                    <div>
                        <h3 class="project-title">Modernisation Station A</h3>
                        <p class="project-zone">Zone A - Secteur Nord</p>
                    </div>
                    <span class="project-status status-en_cours">En Cours</span>
                </div>

                <p class="project-description">
                    Modernisation complète de la station de pompage avec installation de nouveaux équipements et amélioration du système de distribution.
                </p>

                <div class="project-progress">
                    <div class="progress-header">
                        <span class="progress-label">Avancement</span>
                        <span class="progress-percent">75%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 75%"></div>
                    </div>
                </div>

                <div class="project-meta">
                    <div class="meta-item">
                        <span class="meta-number">12</span>
                        <span class="meta-label">Tâches</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-number">5</span>
                        <span class="meta-label">Événements</span>
                    </div>
                </div>

                <div class="project-dates">
                    <i class="bi bi-calendar me-1"></i>
                    01/09/2024 → 31/12/2024
                </div>

                <div class="project-actions">
                    <a href="#" class="btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>
                        Voir
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="#" class="btn-action btn-edit">
                        <i class="bi bi-pencil me-1"></i>
                        Modifier
                    </a>
                    <button class="btn-action btn-delete" style="border: none;">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </button>
                    @endif
                </div>
            </div>

            <div class="project-card">
                <div class="project-header">
                    <div>
                        <h3 class="project-title">Extension Réseau Sud</h3>
                        <p class="project-zone">Zone C - Secteur Sud</p>
                    </div>
                    <span class="project-status status-planifie">Planifié</span>
                </div>

                <p class="project-description">
                    Extension du réseau d'irrigation pour couvrir les nouvelles zones agricoles développées dans le secteur sud.
                </p>

                <div class="project-progress">
                    <div class="progress-header">
                        <span class="progress-label">Avancement</span>
                        <span class="progress-percent">25%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 25%"></div>
                    </div>
                </div>

                <div class="project-meta">
                    <div class="meta-item">
                        <span class="meta-number">18</span>
                        <span class="meta-label">Tâches</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-number">8</span>
                        <span class="meta-label">Événements</span>
                    </div>
                </div>

                <div class="project-dates">
                    <i class="bi bi-calendar me-1"></i>
                    15/01/2025 → 30/06/2025
                </div>

                <div class="project-actions">
                    <a href="#" class="btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>
                        Voir
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="#" class="btn-action btn-edit">
                        <i class="bi bi-pencil me-1"></i>
                        Modifier
                    </a>
                    <button class="btn-action btn-delete" style="border: none;">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </button>
                    @endif
                </div>
            </div>

            <div class="project-card">
                <div class="project-header">
                    <div>
                        <h3 class="project-title">Réhabilitation Canal Principal</h3>
                        <p class="project-zone">Zone B - Canal Central</p>
                    </div>
                    <span class="project-status status-termine">Terminé</span>
                </div>

                <p class="project-description">
                    Réhabilitation complète du canal principal d'irrigation avec renforcement des berges et amélioration de l'étanchéité.
                </p>

                <div class="project-progress">
                    <div class="progress-header">
                        <span class="progress-label">Avancement</span>
                        <span class="progress-percent">100%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                </div>

                <div class="project-meta">
                    <div class="meta-item">
                        <span class="meta-number">15</span>
                        <span class="meta-label">Tâches</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-number">6</span>
                        <span class="meta-label">Événements</span>
                    </div>
                </div>

                <div class="project-dates">
                    <i class="bi bi-calendar me-1"></i>
                    01/03/2024 → 31/08/2024
                </div>

                <div class="project-actions">
                    <a href="#" class="btn-action btn-view">
                        <i class="bi bi-eye me-1"></i>
                        Voir
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="#" class="btn-action btn-edit">
                        <i class="bi bi-pencil me-1"></i>
                        Modifier
                    </a>
                    <button class="btn-action btn-delete" style="border: none;">
                        <i class="bi bi-trash me-1"></i>
                        Supprimer
                    </button>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
    </main>
</div>

<!-- Form de suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(projectId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?\n\nCette action supprimera également toutes les tâches et événements associés.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/projects/${projectId}`;
        form.submit();
    }
}

// Animation des cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.project-card');
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
FICHIER : resources/views/projects/create.blade.php
DESCRIPTION : Création d'un nouveau projet
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Créer un projet')

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
        max-width: 800px;
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
        background: linear-gradient(135deg, #e9d5ff, #ddd6fe);
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
                        Créer un Nouveau Projet
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        Organisez vos interventions en projets structurés
                    </p>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 8px; background: #6b7280; color: white; text-decoration: none;">
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0; display: flex; align-items: center;">
                    <i class="bi bi-folder-plus text-purple-600 me-2"></i>
                    Informations du Projet
                </h2>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <!-- Informations générales -->
                    <div class="form-section">
                        <h3 class="section-title">Informations Générales</h3>

                        <div class="mb-3">
                            <label for="nom" class="form-label" style="font-weight: 500; color: #374151;">
                                Nom du projet <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                   id="nom" name="nom" value="{{ old('nom') }}" required
                                   placeholder="Ex: Modernisation Station de Pompage A"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label" style="font-weight: 500; color: #374151;">
                                Description <span style="color: #dc2626;">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required
                                      placeholder="Décrivez les objectifs et le périmètre du projet..."
                                      style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="zone_geographique" class="form-label" style="font-weight: 500; color: #374151;">
                                Zone géographique <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" class="form-control @error('zone_geographique') is-invalid @enderror"
                                   id="zone_geographique" name="zone_geographique" value="{{ old('zone_geographique') }}" required
                                   placeholder="Ex: Zone A - Secteur Nord, Périmètre Tadla"
                                   style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">
                            @error('zone_geographique')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Planification -->
                    <div class="form-section">
                        <h3 class="section-title">Planification</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_debut" class="form-label" style="font-weight: 500; color: #374151;">
                                    Date de début <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required
                                       style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_fin" class="form-label" style="font-weight: 500; color: #374151;">
                                    Date de fin prévue <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required
                                       style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Responsabilité -->
                    <div class="form-section">
                        <h3 class="section-title">Responsabilité</h3>

                        <div class="mb-3">
                            <label for="id_responsable" class="form-label" style="font-weight: 500; color: #374151;">
                                Responsable du projet <span style="color: #dc2626;">*</span>
                            </label>
                            <select class="form-select @error('id_responsable') is-invalid @enderror"
                                    id="id_responsable" name="id_responsable" required
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px;">
                                <option value="">Sélectionner un responsable...</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('id_responsable') == $user->id ? 'selected' : '' }}>
                                            {{ $user->prenom }} {{ $user->nom }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="{{ auth()->id() }}" selected>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</option>
                                @endif
                            </select>
                            @error('id_responsable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('projects.index') }}"
                           style="padding: 0.75rem 1.5rem; border-radius: 8px; background: #6b7280; color: white; text-decoration: none; display: inline-flex; align-items: center;">
                            Annuler
                        </a>
                        <button type="submit"
                                style="padding: 0.75rem 1.5rem; border-radius: 8px; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; display: inline-flex; align-items: center;">
                            <i class="bi bi-check-lg me-2"></i>
                            Créer le Projet
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
document.addEventListener('DOMContentLoaded', function() {
    // Validation des dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
        if (dateFin.value && dateFin.value < this.value) {
            dateFin.value = this.value;
        }
    });

    // Initialiser la date min pour la date de fin
    if (dateDebut.value) {
        dateFin.min = dateDebut.value;
    }
});
</script>
@endpush
