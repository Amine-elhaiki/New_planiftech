{{--
==================================================
FICHIER : resources/views/tasks/edit.blade.php
DESCRIPTION : Formulaire d'édition d'une tâche existante
AUTEUR : PlanifTech ORMVAT
VERSION : Corrigée sans erreurs CSS/JS
==================================================
--}}

@extends('layouts.app')

@section('title', 'Modifier la tâche')

@push('styles')
<style>
    .form-label {
        font-weight: 500;
        color: #374151;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
    }

    .required-field {
        color: #dc2626;
    }

    .card-info {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #0ea5e9;
    }

    .card-danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-left: 4px solid #ef4444;
    }

    .info-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .status-current {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-a_faire {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-en_cours {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .status-termine {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .progress-task-edit {
        height: 8px;
    }

    .notification-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Modifier la tâche</h1>
            <p class="text-muted mb-0">{{ $task->titre }}</p>
        </div>
        <div>
            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-info me-2">
                <i class="bi bi-eye me-2"></i>Voir les détails
            </a>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Modifier les informations
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.update', $task) }}" id="editTaskForm">
                        @csrf
                        @method('PUT')

                        <!-- Titre et Priorité -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="titre" class="form-label">
                                    Titre de la tâche <span class="required-field">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('titre') is-invalid @enderror"
                                       id="titre"
                                       name="titre"
                                       value="{{ old('titre', $task->titre) }}"
                                       required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="priorite" class="form-label">
                                    Priorité <span class="required-field">*</span>
                                </label>
                                <select class="form-select @error('priorite') is-invalid @enderror"
                                        id="priorite"
                                        name="priorite"
                                        required>
                                    <option value="">Sélectionner...</option>
                                    <option value="basse" {{ old('priorite', $task->priorite) === 'basse' ? 'selected' : '' }}>
                                        🟢 Basse
                                    </option>
                                    <option value="moyenne" {{ old('priorite', $task->priorite) === 'moyenne' ? 'selected' : '' }}>
                                        🟡 Moyenne
                                    </option>
                                    <option value="haute" {{ old('priorite', $task->priorite) === 'haute' ? 'selected' : '' }}>
                                        🔴 Haute
                                    </option>
                                </select>
                                @error('priorite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Description <span class="required-field">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      required>{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attribution, Statut et Progression -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="id_utilisateur" class="form-label">
                                    Assigné à <span class="required-field">*</span>
                                </label>
                                <select class="form-select @error('id_utilisateur') is-invalid @enderror"
                                        id="id_utilisateur"
                                        name="id_utilisateur"
                                        required>
                                    <option value="">Sélectionner un technicien...</option>
                                    @if(isset($users) && $users->count() > 0)
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                    {{ old('id_utilisateur', $task->id_utilisateur) == $user->id ? 'selected' : '' }}>
                                                👤 {{ $user->prenom }} {{ $user->nom }}
                                                ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_utilisateur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="statut" class="form-label">
                                    Statut <span class="required-field">*</span>
                                </label>
                                <select class="form-select @error('statut') is-invalid @enderror"
                                        id="statut"
                                        name="statut"
                                        required>
                                    <option value="a_faire" {{ old('statut', $task->statut) === 'a_faire' ? 'selected' : '' }}>
                                        ⏳ À faire
                                    </option>
                                    <option value="en_cours" {{ old('statut', $task->statut) === 'en_cours' ? 'selected' : '' }}>
                                        🔄 En cours
                                    </option>
                                    <option value="termine" {{ old('statut', $task->statut) === 'termine' ? 'selected' : '' }}>
                                        ✅ Terminé
                                    </option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="progression" class="form-label">Progression (%)</label>
                                <div class="d-flex align-items-center">
                                    <input type="range"
                                           class="form-range me-3"
                                           id="progression"
                                           name="progression"
                                           min="0"
                                           max="100"
                                           value="{{ old('progression', $task->progression) }}"
                                           data-current-value="{{ old('progression', $task->progression) }}">
                                    <span id="progressDisplay" class="badge bg-primary">
                                        {{ old('progression', $task->progression) }}%
                                    </span>
                                </div>
                                @error('progression')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date d'échéance -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_echeance" class="form-label">
                                    Date d'échéance <span class="required-field">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('date_echeance') is-invalid @enderror"
                                       id="date_echeance"
                                       name="date_echeance"
                                       value="{{ old('date_echeance', $task->date_echeance->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Associations -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="id_projet" class="form-label">
                                    Projet associé <small class="text-muted">(optionnel)</small>
                                </label>
                                <select class="form-select @error('id_projet') is-invalid @enderror"
                                        id="id_projet"
                                        name="id_projet">
                                    <option value="">Aucun projet</option>
                                    @if(isset($projects) && $projects->count() > 0)
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}"
                                                    {{ old('id_projet', $task->id_projet) == $project->id ? 'selected' : '' }}>
                                                📁 {{ $project->nom }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_projet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="id_evenement" class="form-label">
                                    Événement associé <small class="text-muted">(optionnel)</small>
                                </label>
                                <select class="form-select @error('id_evenement') is-invalid @enderror"
                                        id="id_evenement"
                                        name="id_evenement">
                                    <option value="">Aucun événement</option>
                                    @if(isset($events) && $events->count() > 0)
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}"
                                                    {{ old('id_evenement', $task->id_evenement) == $event->id ? 'selected' : '' }}>
                                                📅 {{ $event->titre }} - {{ $event->date_debut->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('id_evenement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <!-- Informations actuelles -->
            <div class="card card-info mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>État actuel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Créée le :</strong>
                        <div class="text-muted">{{ $task->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <strong>Dernière modification :</strong>
                        <div class="text-muted">{{ $task->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <strong>Statut actuel :</strong>
                        <div class="mt-1">
                            <span class="status-current status-{{ $task->statut }}">
                                {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <strong>Progression actuelle :</strong>
                        <div class="mt-2">
                            <div class="progress progress-task-edit">
                                <div class="progress-bar bg-primary"
                                     data-current-progress="{{ $task->progression }}">
                                </div>
                            </div>
                            <small class="text-muted">{{ $task->progression }}% terminé</small>
                        </div>
                    </div>
                    @if($task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']))
                    <div class="info-item">
                        <span class="badge bg-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            En retard de {{ $task->date_echeance->diffForHumans() }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Zone de danger -->
            <div class="card card-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Zone de danger
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Actions irréversibles disponibles pour cette tâche.</p>
                    <button class="btn btn-outline-danger w-100"
                            data-task-id="{{ $task->id }}"
                            onclick="confirmDelete(this.dataset.taskId)">
                        <i class="bi bi-trash me-2"></i>Supprimer la tâche
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Zone pour les notifications -->
<div id="notifications-area"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale
    const appConfig = {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        taskId: document.querySelector('[data-task-id]')?.dataset.taskId || ''
    };

    // Initialisation
    initializeProgressBar();
    initializeEventListeners();

    // Fonctions d'initialisation
    function initializeProgressBar() {
        // Initialiser la barre de progression dans la sidebar
        const progressBar = document.querySelector('[data-current-progress]');
        if (progressBar) {
            const currentProgress = progressBar.dataset.currentProgress;
            progressBar.style.width = currentProgress + '%';
        }
    }

    function initializeEventListeners() {
        // Auto-ajustement du statut selon la progression
        const progressSlider = document.getElementById('progression');
        const statutSelect = document.getElementById('statut');

        if (progressSlider && statutSelect) {
            progressSlider.addEventListener('input', function() {
                const value = parseInt(this.value);
                updateProgressDisplay(value);

                // Auto-ajustement du statut
                if (value === 0 && statutSelect.value === 'en_cours') {
                    statutSelect.value = 'a_faire';
                } else if (value === 100) {
                    statutSelect.value = 'termine';
                } else if (value > 0 && value < 100 && statutSelect.value === 'a_faire') {
                    statutSelect.value = 'en_cours';
                }
            });
        }

        // Auto-ajustement de la progression selon le statut
        if (statutSelect && progressSlider) {
            statutSelect.addEventListener('change', function() {
                const currentProgress = parseInt(progressSlider.value);

                if (this.value === 'a_faire' && currentProgress > 0) {
                    progressSlider.value = 0;
                    updateProgressDisplay(0);
                } else if (this.value === 'termine' && currentProgress < 100) {
                    progressSlider.value = 100;
                    updateProgressDisplay(100);
                }
            });
        }

        // Validation du formulaire
        const form = document.getElementById('editTaskForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const dateEcheance = document.getElementById('date_echeance').value;
                const selectedDate = new Date(dateEcheance);
                const now = new Date();

                // Permettre les dates passées pour les tâches existantes, mais alerter
                if (selectedDate <= now) {
                    const confirm = window.confirm(
                        'Attention : La date d\'échéance est dans le passé.\n' +
                        'Voulez-vous vraiment continuer ?'
                    );
                    if (!confirm) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    }

    // Fonction pour mettre à jour l'affichage de la progression
    function updateProgressDisplay(value) {
        const display = document.getElementById('progressDisplay');
        if (display) {
            display.textContent = value + '%';

            // Changer la couleur selon la progression
            display.className = 'badge ';
            if (value < 30) {
                display.className += 'bg-danger';
            } else if (value < 70) {
                display.className += 'bg-warning';
            } else {
                display.className += 'bg-success';
            }
        }
    }

    // Fonction pour confirmer la suppression
    window.confirmDelete = function(taskId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?\n\nCette action est irréversible.')) {
            fetch('/tasks/' + taskId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': appConfig.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    showNotification('Tâche supprimée avec succès', 'success');
                    setTimeout(() => {
                        window.location.href = '/tasks';
                    }, 1500);
                } else {
                    throw new Error('Erreur serveur');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur lors de la suppression de la tâche', 'error');
            });
        }
    };

    // Fonction pour afficher des notifications
    function showNotification(message, type) {
        const notificationsArea = document.getElementById('notifications-area');
        if (!notificationsArea) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';

        const alertElement = document.createElement('div');
        alertElement.className = `alert ${alertClass} alert-dismissible fade show notification-alert`;
        alertElement.innerHTML = `
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        notificationsArea.appendChild(alertElement);

        // Auto-remove après 5 secondes
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
