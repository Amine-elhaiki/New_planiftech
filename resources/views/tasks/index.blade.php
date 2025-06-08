{{--
==================================================
FICHIER : resources/views/tasks/index.blade.php
DESCRIPTION : Page index des tâches - Design clean et moderne
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Gestion des tâches')

@push('styles')
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-light: #6366f1;
        --primary-dark: #3730a3;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --light-bg: #f8fafc;
        --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --card-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --border-radius: 12px;
        --border-radius-lg: 16px;
    }

    body {
        background-color: var(--light-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header moderne */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Cards statistiques */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-lg);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--card-color);
    }

    .stat-card.total { --card-color: var(--primary-color); }
    .stat-card.pending { --card-color: var(--warning-color); }
    .stat-card.progress { --card-color: var(--info-color); }
    .stat-card.completed { --card-color: var(--success-color); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .stat-icon.total { background: rgba(79, 70, 229, 0.1); color: var(--primary-color); }
    .stat-icon.pending { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }
    .stat-icon.progress { background: rgba(6, 182, 212, 0.1); color: var(--info-color); }
    .stat-icon.completed { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Section filtres */
    .filters-section {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .search-box {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1rem;
    }

    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: var(--border-radius);
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .btn-filter {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-filter:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Cards des tâches */
    .tasks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .task-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        height: fit-content;
    }

    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-lg);
    }

    .task-card.priority-haute {
        border-left: 4px solid var(--danger-color);
    }

    .task-card.priority-moyenne {
        border-left: 4px solid var(--warning-color);
    }

    .task-card.priority-basse {
        border-left: 4px solid var(--success-color);
    }

    .task-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .task-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .task-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .progress-circle {
        width: 60px;
        height: 60px;
        position: relative;
        flex-shrink: 0;
    }

    .progress-bg {
        stroke: #e5e7eb;
        stroke-width: 4;
        fill: none;
    }

    .progress-bar {
        stroke: var(--primary-color);
        stroke-width: 4;
        fill: none;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
        font-weight: 600;
        color: #4b5563;
    }

    .task-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .status-a_faire {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .status-en_cours {
        background: rgba(6, 182, 212, 0.1);
        color: var(--info-color);
    }

    .status-termine {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .task-date {
        font-size: 0.875rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .task-assignee {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .assignee-name {
        font-size: 0.875rem;
        color: #4b5563;
        font-weight: 500;
    }

    .task-project {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .task-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .action-btn.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .action-btn.success:hover {
        background: var(--success-color);
        color: white;
    }

    .action-btn.primary {
        background: rgba(79, 70, 229, 0.1);
        color: var(--primary-color);
    }

    .action-btn.primary:hover {
        background: var(--primary-color);
        color: white;
    }

    .action-btn.info {
        background: rgba(6, 182, 212, 0.1);
        color: var(--info-color);
    }

    .action-btn.info:hover {
        background: var(--info-color);
        color: white;
    }

    /* État vide */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--border-radius);
        border: 1px solid #e5e7eb;
        grid-column: 1 / -1;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #9ca3af;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        color: white;
        transform: translateY(-1px);
    }

    /* Bouton flottant */
    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 56px;
        height: 56px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        font-size: 1.5rem;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .fab:hover {
        background: var(--primary-dark);
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.6);
    }

    /* Modal */
    .modal-content {
        border: none;
        border-radius: var(--border-radius-lg);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        color: #1f2937;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #d1d5db;
        border-radius: var(--border-radius);
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .form-range {
        accent-color: var(--primary-color);
    }

    .progress-value {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Notifications */
    .notification {
        background: white;
        border-radius: var(--border-radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--card-shadow-lg);
        border: 1px solid #e5e7eb;
        margin-bottom: 0.5rem;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
    }

    .notification.success {
        border-left: 4px solid var(--success-color);
    }

    .notification.error {
        border-left: 4px solid var(--danger-color);
    }

    .notification.show {
        transform: translateX(0);
    }

    /* Overdue indicator */
    .overdue-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--danger-color);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .tasks-grid {
            grid-template-columns: 1fr;
        }

        .fab {
            bottom: 1rem;
            right: 1rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Configuration pour JavaScript -->
<div id="app-config"
     data-csrf="{{ csrf_token() }}"
     data-base-url="{{ url('/') }}"
     style="display: none;">
</div>

<div class="container-fluid px-4">
    <!-- En-tête de page -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center position-relative">
            <div>
                <h1>Gestion des Tâches</h1>
                <p>Organisez et suivez vos interventions techniques efficacement</p>
            </div>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('tasks.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg"></i>
                Nouvelle Tâche
            </a>
            @endif
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon total">
                <i class="bi bi-list-task"></i>
            </div>
            <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total des tâches</div>
        </div>

        <div class="stat-card pending">
            <div class="stat-icon pending">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-number">{{ $stats['a_faire'] ?? 0 }}</div>
            <div class="stat-label">À faire</div>
        </div>

        
         <div class="stat-card completed">
             <div class="stat-icon progress">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="stat-number">{{ $stats['en_cours'] ?? 0 }}</div>
            <div class="stat-label">En cours</div>
        </div>

        <div class="stat-card completed">
            <div class="stat-icon completed">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-number">{{ $stats['termine'] ?? 0 }}</div>
            <div class="stat-label">Terminées</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <form method="GET" action="{{ route('tasks.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" name="search"
                               value="{{ request('search') }}"
                               placeholder="Rechercher une tâche...">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="filter-select" name="statut" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <option value="a_faire" {{ request('statut') === 'a_faire' ? 'selected' : '' }}>À faire</option>
                        <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Priorité</label>
                    <select class="filter-select" name="priorite" onchange="this.form.submit()">
                        <option value="">Toutes</option>
                        <option value="basse" {{ request('priorite') === 'basse' ? 'selected' : '' }}>Basse</option>
                        <option value="moyenne" {{ request('priorite') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="haute" {{ request('priorite') === 'haute' ? 'selected' : '' }}>Haute</option>
                    </select>
                </div>

                @if(auth()->user()->role === 'admin' && isset($users) && $users->count() > 0)
                <div class="col-md-2">
                    <label class="form-label">Technicien</label>
                    <select class="filter-select" name="utilisateur" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('utilisateur') == $user->id ? 'selected' : '' }}>
                                {{ $user->prenom }} {{ $user->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <button type="submit" class="btn-filter w-100">
                        <i class="bi bi-funnel me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Liste des tâches -->
    <div class="tasks-grid">
        @forelse($tasks as $task)
        @php
            $isOverdue = $task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']);
            $progress = $task->progression ?? 0;
            $circumference = 2 * pi() * 26;
            $offset = $circumference - ($progress / 100) * $circumference;
        @endphp

        <div class="task-card priority-{{ $task->priorite }}">
            @if($isOverdue)
                <div class="overdue-badge">En retard</div>
            @endif

            <div class="task-header">
                <div style="flex: 1;">
                    <h3 class="task-title">{{ $task->titre }}</h3>
                    <p class="task-description">{{ Str::limit($task->description, 120) }}</p>
                </div>

                <div class="progress-circle">
                    <svg width="60" height="60">
                        <circle class="progress-bg" cx="30" cy="30" r="26"></circle>
                        <circle class="progress-bar" cx="30" cy="30" r="26"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $offset }}"></circle>
                    </svg>
                    <div class="progress-text">{{ $progress }}%</div>
                </div>
            </div>

            <div class="task-meta">
                <span class="status-badge status-{{ $task->statut }}">
                    {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                </span>
                <div class="task-date">
                    <i class="bi bi-calendar3"></i>
                    {{ $task->date_echeance->format('d/m/Y') }}
                </div>
            </div>

            @if($task->utilisateur)
            <div class="task-assignee">
                <div class="avatar">
                    {{ substr($task->utilisateur->prenom, 0, 1) }}{{ substr($task->utilisateur->nom, 0, 1) }}
                </div>
                <span class="assignee-name">{{ $task->utilisateur->prenom }} {{ $task->utilisateur->nom }}</span>
            </div>
            @endif

            @if($task->projet)
            <div class="task-project">
                <i class="bi bi-folder2"></i>
                {{ $task->projet->nom }}
            </div>
            @endif

            @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
            <div class="task-actions">
                @if($task->statut !== 'termine')
                <button class="action-btn success"
                        data-action="complete"
                        data-task-id="{{ $task->id }}"
                        title="Marquer comme terminé">
                    <i class="bi bi-check-lg"></i>
                </button>
                @endif

                <button class="action-btn primary"
                        data-action="status"
                        data-task-id="{{ $task->id }}"
                        title="Modifier le statut">
                    <i class="bi bi-pencil"></i>
                </button>

                <a href="{{ route('tasks.show', $task) }}"
                   class="action-btn info"
                   title="Voir les détails">
                    <i class="bi bi-eye"></i>
                </a>
            </div>
            @endif
        </div>

        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h3 class="empty-title">Aucune tâche trouvée</h3>
            <p class="empty-description">
                @if(request()->hasAny(['search', 'statut', 'priorite', 'utilisateur']))
                    Aucune tâche ne correspond à vos critères de recherche.
                @else
                    @if(auth()->user()->role === 'admin')
                        Commencez par créer votre première tâche.
                    @else
                        Aucune tâche ne vous a été assignée pour le moment.
                    @endif
                @endif
            </p>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('tasks.create') }}" class="btn-primary">
                <i class="bi bi-plus-lg"></i>
                Créer une tâche
            </a>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($tasks) && $tasks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Bouton flottant -->
@if(auth()->user()->role === 'admin')
<a href="{{ route('tasks.create') }}" class="fab" title="Nouvelle tâche">
    <i class="bi bi-plus-lg"></i>
</a>
@endif

<!-- Modal de mise à jour du statut -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-gear me-2"></i>Mettre à jour le statut
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="taskId">

                    <div class="mb-3">
                        <label for="modalStatut" class="form-label">Statut</label>
                        <select class="form-select" id="modalStatut" name="statut">
                            <option value="a_faire">À faire</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalProgression" class="form-label">Progression (%)</label>
                        <input type="range" class="form-range" id="modalProgression"
                               name="progression" min="0" max="100" value="0">
                        <div class="text-center mt-2">
                            <span id="progressValue" class="progress-value">0%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Zone des notifications -->
<div id="notifications" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    var config = document.getElementById('app-config');
    var csrfToken = config.getAttribute('data-csrf');
    var baseUrl = config.getAttribute('data-base-url');

    var statusModal = null;

    // Initialisation
    initModal();
    initEventListeners();

    function initModal() {
        var modalEl = document.getElementById('statusModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            statusModal = new bootstrap.Modal(modalEl);
        }
    }

    function initEventListeners() {
        // Boutons d'action
        document.addEventListener('click', function(e) {
            var button = e.target.closest('[data-action]');
            if (!button) return;

            var action = button.getAttribute('data-action');
            var taskId = button.getAttribute('data-task-id');

            if (action === 'complete') {
                markCompleted(taskId);
            } else if (action === 'status') {
                openStatusModal(taskId);
            }
        });

        // Slider de progression
        var progressSlider = document.getElementById('modalProgression');
        if (progressSlider) {
            progressSlider.addEventListener('input', function() {
                updateProgressDisplay(this.value);
            });
        }

        // Formulaire de statut
        var statusForm = document.getElementById('statusForm');
        if (statusForm) {
            statusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitStatusForm();
            });
        }

        // Recherche avec délai
        var searchInput = document.querySelector('[name="search"]');
        if (searchInput) {
            var searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                var form = this.form;
                searchTimeout = setTimeout(function() {
                    form.submit();
                }, 500);
            });
        }
    }

    function markCompleted(taskId) {
        if (!confirm('Marquer cette tâche comme terminée ?')) return;

        var url = baseUrl + '/tasks/' + taskId + '/complete';

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showNotification('Tâche marquée comme terminée !', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification('Erreur lors de la mise à jour', 'error');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la mise à jour', 'error');
        });
    }

    function openStatusModal(taskId) {
        document.getElementById('taskId').value = taskId;
        if (statusModal) {
            statusModal.show();
        }
    }

    function updateProgressDisplay(value) {
        var progressValue = document.getElementById('progressValue');
        if (progressValue) {
            progressValue.textContent = value + '%';
        }

        // Auto-ajustement du statut
        var statusSelect = document.getElementById('modalStatut');
        if (statusSelect) {
            if (value == 0) {
                statusSelect.value = 'a_faire';
            } else if (value == 100) {
                statusSelect.value = 'termine';
            } else if (statusSelect.value === 'a_faire') {
                statusSelect.value = 'en_cours';
            }
        }
    }

    function submitStatusForm() {
        var taskId = document.getElementById('taskId').value;
        var formData = new FormData(document.getElementById('statusForm'));
        var url = baseUrl + '/tasks/' + taskId + '/status';

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                if (statusModal) {
                    statusModal.hide();
                }
                showNotification('Statut mis à jour avec succès !', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification('Erreur lors de la mise à jour', 'error');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la mise à jour', 'error');
        });
    }

    function showNotification(message, type) {
        var container = document.getElementById('notifications');
        if (!container) return;

        var notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.innerHTML =
            '<div class="d-flex align-items-center justify-content-between">' +
                '<div class="d-flex align-items-center">' +
                    '<i class="bi bi-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + ' me-2"></i>' +
                    message +
                '</div>' +
                '<button type="button" class="btn-close ms-2" onclick="this.parentElement.parentElement.remove()"></button>' +
            '</div>';

        container.appendChild(notification);

        // Animation d'entrée
        setTimeout(function() {
            notification.classList.add('show');
        }, 100);

        // Auto-suppression
        setTimeout(function() {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 4000);
    }
});
</script>
@endpush
