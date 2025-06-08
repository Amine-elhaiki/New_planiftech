{{--
==================================================
FICHIER : resources/views/tasks/show.blade.php
DESCRIPTION : Page de détails d'une tâche - Version CSS corrigée
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Détails de la tâche')

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
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --card-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --border-radius: 12px;
        --border-radius-lg: 16px;
    }

    body {
        background-color: var(--gray-50);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header de la page */
    .page-header {
        background: white;
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--gray-200);
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .breadcrumb-nav a {
        color: var(--primary-color);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-nav a:hover {
        color: var(--primary-dark);
    }

    .task-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .task-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .badge {
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge.priority-haute {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .badge.priority-moyenne {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .badge.priority-basse {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge.status-a_faire {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .badge.status-en_cours {
        background: rgba(6, 182, 212, 0.1);
        color: var(--info-color);
        border: 1px solid rgba(6, 182, 212, 0.2);
    }

    .badge.status-termine {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge.overdue {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(239, 68, 68, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    /* Actions */
    .actions-bar {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-outline {
        background: white;
        color: var(--gray-600);
        border: 1px solid var(--gray-300);
    }

    .btn-outline:hover {
        background: var(--gray-50);
        color: var(--gray-900);
    }

    /* Layout principal */
    .main-content {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
    }

    .content-section {
        background: white;
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--gray-200);
        height: fit-content;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
    }

    /* Description */
    .description-text {
        font-size: 1rem;
        line-height: 1.6;
        color: var(--gray-700);
        margin-bottom: 0;
    }

    /* Progression */
    .progress-container {
        position: relative;
        margin-bottom: 1rem;
    }

    .progress-circle {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        position: relative;
    }

    .progress-circle svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }

    .progress-bg {
        stroke: var(--gray-200);
        stroke-width: 8;
        fill: none;
    }

    .progress-bar {
        stroke: var(--primary-color);
        stroke-width: 8;
        fill: none;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .progress-percentage {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        display: block;
    }

    .progress-label {
        font-size: 0.75rem;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .progress-status {
        text-align: center;
        margin-top: 1rem;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    /* Historique */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--gray-200);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0.25rem;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px var(--primary-color);
        background: var(--primary-color);
        z-index: 1;
    }

    .timeline-content h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .timeline-content p {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-bottom: 0;
    }

    /* Sidebar */
    .sidebar-section {
        background: white;
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
        height: fit-content;
    }

    .sidebar-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-title i {
        color: var(--primary-color);
    }

    /* Info items */
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--gray-500);
        font-weight: 500;
        flex-shrink: 0;
        width: 80px;
    }

    .info-value {
        font-size: 0.875rem;
        color: var(--gray-900);
        text-align: right;
        flex-grow: 1;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .user-details h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.125rem;
    }

    .user-details p {
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-bottom: 0;
    }

    /* Actions rapides */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-btn {
        width: 100%;
        justify-content: center;
    }

    /* Zone de danger */
    .danger-zone {
        border: 1px solid rgba(239, 68, 68, 0.2);
        background: rgba(239, 68, 68, 0.02);
    }

    .danger-zone .sidebar-title {
        color: var(--danger-color);
    }

    .danger-zone .sidebar-title i {
        color: var(--danger-color);
    }

    /* Modal */
    .modal-content {
        border: none;
        border-radius: var(--border-radius-lg);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid var(--gray-200);
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        color: var(--gray-900);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--border-radius);
        font-size: 0.875rem;
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

    .range-value {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .main-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .actions-bar {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .task-title {
            font-size: 1.5rem;
        }

        .content-section {
            padding: 1.5rem;
        }

        .sidebar-section {
            padding: 1rem;
        }

        .actions-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .btn {
            justify-content: center;
        }
    }

    /* Notifications */
    .notification {
        background: white;
        border-radius: var(--border-radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--card-shadow-lg);
        border: 1px solid var(--gray-200);
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
</style>
@endpush

@section('content')
<!-- Configuration JavaScript -->
<div id="app-config"
     data-csrf="{{ csrf_token() }}"
     data-base-url="{{ url('/') }}"
     data-task-id="{{ $task->id }}"
     style="display: none;">
</div>

<div class="container-fluid px-4">
    <!-- En-tête de la page -->
    <div class="page-header">
        <div class="breadcrumb-nav">
            <a href="{{ route('tasks.index') }}">
                <i class="bi bi-arrow-left me-1"></i>Tâches
            </a>
            <i class="bi bi-chevron-right"></i>
            <span>Détails</span>
        </div>

        <h1 class="task-title">{{ $task->titre }}</h1>

        <div class="task-badges">
            <span class="badge priority-{{ $task->priorite }}">
                {{ ucfirst($task->priorite) }} priorité
            </span>
            <span class="badge status-{{ $task->statut }}">
                {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
            </span>
            @if($task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']))
                <span class="badge overdue">
                    <i class="bi bi-exclamation-triangle me-1"></i>En retard
                </span>
            @endif
        </div>

        @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
        <div class="actions-bar">
            @if($task->statut !== 'termine')
            <button class="btn btn-success" data-action="complete">
                <i class="bi bi-check-lg"></i>
                Marquer comme terminé
            </button>
            @endif

            <button class="btn btn-primary" data-action="status">
                <i class="bi bi-pencil"></i>
                Modifier le statut
            </button>

            @if(auth()->user()->role === 'admin')
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline">
                <i class="bi bi-gear"></i>
                Modifier
            </a>

            <button class="btn btn-danger" data-action="delete">
                <i class="bi bi-trash"></i>
                Supprimer
            </button>
            @endif
        </div>
        @endif
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <!-- Colonne principale -->
        <div>
            <!-- Description -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="bi bi-file-text"></i>
                    Description
                </h2>
                <p class="description-text">{{ $task->description }}</p>
            </div>

            <!-- Progression -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="bi bi-bar-chart"></i>
                    Progression
                </h2>

                <div class="progress-container">
                    @php
                        $progress = $task->progression ?? 0;
                        $circumference = 2 * pi() * 52;
                        $offset = $circumference - ($progress / 100) * $circumference;
                    @endphp

                    <div class="progress-circle">
                        <svg>
                            <circle class="progress-bg" cx="60" cy="60" r="52"></circle>
                            <circle class="progress-bar" cx="60" cy="60" r="52"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $offset }}"></circle>
                        </svg>
                        <div class="progress-text">
                            <span class="progress-percentage">{{ $progress }}%</span>
                            <span class="progress-label">Terminé</span>
                        </div>
                    </div>

                    <div class="progress-status">
                        @if($progress == 0)
                            Cette tâche n'a pas encore été commencée
                        @elseif($progress < 100)
                            Tâche en cours d'exécution
                        @else
                            Tâche terminée avec succès
                        @endif
                    </div>
                </div>
            </div>

            <!-- Historique -->
            <div class="content-section">
                <h2 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    Historique
                </h2>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>Tâche créée</h4>
                            <p>{{ $task->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    @if($task->updated_at != $task->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h4>Dernière modification</h4>
                            <p>{{ $task->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($task->statut === 'termine')
                    <div class="timeline-item">
                        <div class="timeline-marker" style="background: var(--success-color); box-shadow: 0 0 0 2px var(--success-color);"></div>
                        <div class="timeline-content">
                            <h4>Tâche terminée</h4>
                            <p>{{ $task->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Informations générales -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-info-circle"></i>
                    Informations
                </h3>

                <div class="info-item">
                    <span class="info-label">Échéance</span>
                    <div class="info-value">
                        <div style="font-weight: 600; {{ $task->date_echeance < now() && $task->statut !== 'termine' ? 'color: var(--danger-color);' : '' }}">
                            {{ $task->date_echeance->format('d/m/Y à H:i') }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                            @if($task->date_echeance < now() && $task->statut !== 'termine')
                                En retard de {{ $task->date_echeance->diffForHumans() }}
                            @else
                                {{ $task->date_echeance->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Priorité</span>
                    <span class="info-value">
                        <span class="badge priority-{{ $task->priorite }}" style="font-size: 0.75rem;">
                            {{ ucfirst($task->priorite) }}
                        </span>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Statut</span>
                    <span class="info-value">
                        <span class="badge status-{{ $task->statut }}" style="font-size: 0.75rem;">
                            {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                        </span>
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Progression</span>
                    <span class="info-value" style="font-weight: 600;">{{ $task->progression ?? 0 }}%</span>
                </div>
            </div>

            <!-- Assignation -->
            @if($task->utilisateur)
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-person"></i>
                    Assigné à
                </h3>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ substr($task->utilisateur->prenom, 0, 1) }}{{ substr($task->utilisateur->nom, 0, 1) }}
                    </div>
                    <div class="user-details">
                        <h4>{{ $task->utilisateur->prenom }} {{ $task->utilisateur->nom }}</h4>
                        <p>{{ ucfirst($task->utilisateur->role) }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Associations -->
            @if($task->projet || $task->evenement)
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-link"></i>
                    Associations
                </h3>

                @if($task->projet)
                <div class="info-item">
                    <span class="info-label">Projet</span>
                    <div class="info-value">
                        <a href="{{ route('projects.show', $task->projet) }}" style="color: var(--primary-color); text-decoration: none;">
                            <i class="bi bi-folder me-1"></i>{{ $task->projet->nom }}
                        </a>
                    </div>
                </div>
                @endif

                @if($task->evenement)
                <div class="info-item">
                    <span class="info-label">Événement</span>
                    <div class="info-value">
                        <a href="{{ route('events.show', $task->evenement) }}" style="color: var(--primary-color); text-decoration: none;">
                            <i class="bi bi-calendar-event me-1"></i>{{ $task->evenement->titre }}
                        </a>
                        <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.25rem;">
                            {{ $task->evenement->date_debut->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Actions rapides -->
            @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-lightning"></i>
                    Actions rapides
                </h3>

                <div class="quick-actions">
                    @if($task->statut !== 'termine')
                    <button class="btn btn-success action-btn" data-action="complete">
                        <i class="bi bi-check-circle me-2"></i>
                        Marquer comme terminé
                    </button>
                    @endif

                    <button class="btn btn-primary action-btn" data-action="status">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Mettre à jour le statut
                    </button>

                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline action-btn">
                        <i class="bi bi-pencil me-2"></i>
                        Modifier la tâche
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Zone de danger (Admin seulement) -->
            @if(auth()->user()->role === 'admin')
            <div class="sidebar-section danger-zone">
                <h3 class="sidebar-title">
                    <i class="bi bi-exclamation-triangle"></i>
                    Zone de danger
                </h3>

                <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 1rem;">
                    Cette action est irréversible et supprimera définitivement la tâche.
                </p>

                <button class="btn btn-danger action-btn" data-action="delete">
                    <i class="bi bi-trash me-2"></i>
                    Supprimer la tâche
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

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
                    <div class="mb-3">
                        <label for="modalStatut" class="form-label">Statut</label>
                        <select class="form-select" id="modalStatut" name="statut">
                            <option value="a_faire" {{ $task->statut === 'a_faire' ? 'selected' : '' }}>À faire</option>
                            <option value="en_cours" {{ $task->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ $task->statut === 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalProgression" class="form-label">Progression (%)</label>
                        <input type="range" class="form-range" id="modalProgression"
                               name="progression" min="0" max="100" value="{{ $task->progression ?? 0 }}">
                        <div class="text-center mt-2">
                            <span id="progressValue" class="range-value">{{ $task->progression ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
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
    var taskId = config.getAttribute('data-task-id');

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

            if (action === 'complete') {
                markCompleted();
            } else if (action === 'status') {
                openStatusModal();
            } else if (action === 'delete') {
                deleteTask();
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
    }

    function markCompleted() {
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

    function openStatusModal() {
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

    function deleteTask() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.')) return;

        var url = baseUrl + '/tasks/' + taskId;

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            if (response.ok) {
                showNotification('Tâche supprimée avec succès !', 'success');
                setTimeout(function() {
                    window.location.href = baseUrl + '/tasks';
                }, 1500);
            } else {
                showNotification('Erreur lors de la suppression', 'error');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression', 'error');
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
