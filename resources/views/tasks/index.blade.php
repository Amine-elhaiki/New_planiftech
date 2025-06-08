{{--
==================================================
FICHIER : resources/views/tasks/index.blade.php
DESCRIPTION : Liste des tâches avec filtres et statistiques
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Gestion des tâches')

@push('styles')
<style>
    .task-card {
        transition: all 0.2s ease;
        border-left: 4px solid #e2e8f0;
    }

    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .task-card.priority-basse { border-left-color: #16a34a; }
    .task-card.priority-moyenne { border-left-color: #f59e0b; }
    .task-card.priority-haute { border-left-color: #dc2626; }

    .progress-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-a_faire { background-color: #fef3c7; color: #d97706; }
    .status-en_cours { background-color: #dbeafe; color: #1d4ed8; }
    .status-termine { background-color: #dcfce7; color: #16a34a; }

    .priority-badge {
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.625rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .priority-basse { background-color: #dcfce7; color: #16a34a; }
    .priority-moyenne { background-color: #fef3c7; color: #d97706; }
    .priority-haute { background-color: #fee2e2; color: #dc2626; }

    .overdue {
        background-color: #fef2f2 !important;
        border-left-color: #dc2626 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestion des tâches</h1>
            <p class="text-muted mb-0">Suivez et gérez vos tâches techniques</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <div>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle tâche
            </a>
        </div>
        @endif
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-list-task fs-1"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['a_faire'] }}</h3>
                    <p class="text-muted mb-0">À faire</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-arrow-repeat fs-1"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['en_cours'] }}</h3>
                    <p class="text-muted mb-0">En cours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['termine'] }}</h3>
                    <p class="text-muted mb-0">Terminées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Titre, description...">
                    </div>
                    <div class="col-md-2">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="">Tous</option>
                            <option value="a_faire" {{ request('statut') === 'a_faire' ? 'selected' : '' }}>À faire</option>
                            <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="priorite" class="form-label">Priorité</label>
                        <select class="form-select" id="priorite" name="priorite">
                            <option value="">Toutes</option>
                            <option value="basse" {{ request('priorite') === 'basse' ? 'selected' : '' }}>Basse</option>
                            <option value="moyenne" {{ request('priorite') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="haute" {{ request('priorite') === 'haute' ? 'selected' : '' }}>Haute</option>
                        </select>
                    </div>
                    @if(auth()->user()->role === 'admin' && $users->count() > 0)
                    <div class="col-md-2">
                        <label for="utilisateur" class="form-label">Utilisateur</label>
                        <select class="form-select" id="utilisateur" name="utilisateur">
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
                        <label for="projet" class="form-label">Projet</label>
                        <select class="form-select" id="projet" name="projet">
                            <option value="">Tous</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('projet') == $project->id ? 'selected' : '' }}>
                                    {{ $project->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des tâches -->
    <div class="row">
        @forelse($tasks as $task)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card task-card priority-{{ $task->priorite }} h-100 {{ $task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']) ? 'overdue' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">
                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                {{ $task->titre }}
                            </a>
                        </h5>
                        <div class="d-flex gap-1">
                            <span class="priority-badge priority-{{ $task->priorite }}">
                                {{ ucfirst($task->priorite) }}
                            </span>
                            @if($task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']))
                                <span class="badge bg-danger">En retard</span>
                            @endif
                        </div>
                    </div>

                    <p class="card-text text-muted small mb-3">
                        {{ Str::limit($task->description, 100) }}
                    </p>

                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="progress-circle bg-light me-3">
                                    {{ $task->progression }}%
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar
                                            @if($task->progression < 30) bg-danger
                                            @elseif($task->progression < 70) bg-warning
                                            @else bg-success @endif"
                                            style="width: {{ $task->progression }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="status-badge status-{{ $task->statut }}">
                            {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                        </span>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            {{ $task->date_echeance->format('d/m/Y') }}
                        </small>
                    </div>

                    @if($task->utilisateur)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>
                            {{ $task->utilisateur->prenom }} {{ $task->utilisateur->nom }}
                        </small>
                    </div>
                    @endif

                    @if($task->projet)
                    <div class="mt-1">
                        <small class="text-muted">
                            <i class="bi bi-folder me-1"></i>
                            {{ $task->projet->nom }}
                        </small>
                    </div>
                    @endif

                    <!-- Actions rapides -->
                    @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
                    <div class="mt-3 d-flex gap-2">
                        @if($task->statut !== 'termine')
                        <button class="btn btn-sm btn-outline-success" onclick="markCompleted({{ $task->id }})">
                            <i class="bi bi-check-circle"></i>
                        </button>
                        @endif

                        <button class="btn btn-sm btn-outline-primary" onclick="updateStatus({{ $task->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune tâche trouvée</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'statut', 'priorite', 'utilisateur', 'projet']))
                            Essayez de modifier vos critères de recherche.
                        @else
                            @if(auth()->user()->role === 'admin')
                                Commencez par créer votre première tâche.
                            @else
                                Aucune tâche ne vous a été assignée pour le moment.
                            @endif
                        @endif
                    </p>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Créer une tâche
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tasks->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Modal de mise à jour du statut -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mettre à jour le statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="taskId">
                    <div class="mb-3">
                        <label for="modalStatut" class="form-label">Statut</label>
                        <select class="form-select" id="modalStatut" name="statut" required>
                            <option value="a_faire">À faire</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalProgression" class="form-label">Progression (%)</label>
                        <input type="range" class="form-range" id="modalProgression" name="progression"
                               min="0" max="100" value="0" oninput="updateProgressValue(this.value)">
                        <div class="text-center mt-2">
                            <span id="progressValue">0</span>%
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Marquer une tâche comme terminée
function markCompleted(taskId) {
    if (confirm('Marquer cette tâche comme terminée ?')) {
        fetch(`/tasks/${taskId}/complete`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour');
        });
    }
}

// Ouvrir le modal de mise à jour du statut
function updateStatus(taskId) {
    document.getElementById('taskId').value = taskId;
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Mettre à jour la valeur de progression affichée
function updateProgressValue(value) {
    document.getElementById('progressValue').textContent = value;

    // Ajuster automatiquement le statut selon la progression
    const statutSelect = document.getElementById('modalStatut');
    if (value == 0) {
        statutSelect.value = 'a_faire';
    } else if (value == 100) {
        statutSelect.value = 'termine';
    } else {
        statutSelect.value = 'en_cours';
    }
}

// Soumettre le formulaire de mise à jour du statut
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const taskId = document.getElementById('taskId').value;
    const formData = new FormData(this);

    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour');
    });
});

// Ajuster automatiquement le statut quand la progression change
document.getElementById('modalProgression').addEventListener('input', function() {
    updateProgressValue(this.value);
});
</script>
@endpush

{{--
==================================================
FICHIER : resources/views/tasks/create.blade.php
DESCRIPTION : Formulaire de création d'une nouvelle tâche
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Créer une tâche')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Créer une nouvelle tâche</h1>
            <p class="text-muted mb-0">Assignez une nouvelle tâche à un technicien</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de la tâche <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                           id="titre" name="titre" value="{{ old('titre') }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priorite" class="form-label">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priorite') is-invalid @enderror"
                                            id="priorite" name="priorite" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="basse" {{ old('priorite') === 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="moyenne" {{ old('priorite') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="haute" {{ old('priorite') === 'haute' ? 'selected' : '' }}>Haute</option>
                                    </select>
                                    @error('priorite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_echeance" class="form-label">Date d'échéance <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('date_echeance') is-invalid @enderror"
                                           id="date_echeance" name="date_echeance"
                                           value="{{ old('date_echeance', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                                    @error('date_echeance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_utilisateur" class="form-label">Assigné à <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_utilisateur') is-invalid @enderror"
                                            id="id_utilisateur" name="id_utilisateur" required>
                                        <option value="">Sélectionner un technicien...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('id_utilisateur') == $user->id ? 'selected' : '' }}>
                                                {{ $user->prenom }} {{ $user->nom }} ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_utilisateur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_projet" class="form-label">Projet associé</label>
                                    <select class="form-select @error('id_projet') is-invalid @enderror"
                                            id="id_projet" name="id_projet">
                                        <option value="">Aucun projet</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('id_projet') == $project->id ? 'selected' : '' }}>
                                                {{ $project->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_projet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_evenement" class="form-label">Événement associé</label>
                                    <select class="form-select @error('id_evenement') is-invalid @enderror"
                                            id="id_evenement" name="id_evenement">
                                        <option value="">Aucun événement</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ old('id_evenement') == $event->id ? 'selected' : '' }}>
                                                {{ $event->titre }} - {{ $event->date_debut->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_evenement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Créer la tâche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Conseils</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">Titre efficace</h6>
                        <p class="small text-muted mb-0">Utilisez un titre clair et concis qui décrit l'action à effectuer.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Description détaillée</h6>
                        <p class="small text-muted mb-0">Incluez tous les détails nécessaires : localisation, équipements, procédures...</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-primary">Priorité appropriée</h6>
                        <p class="small text-muted mb-0">
                            <span class="badge bg-danger me-1">Haute</span> Urgences<br>
                            <span class="badge bg-warning me-1">Moyenne</span> Planifiées<br>
                            <span class="badge bg-success me-1">Basse</span> Maintenance
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{--
==================================================
FICHIER : resources/views/tasks/show.blade.php
DESCRIPTION : Détails d'une tâche
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Détails de la tâche')

@push('styles')
<style>
    .progress-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 600;
        position: relative;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .priority-indicator {
        width: 4px;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        border-radius: 0 0.375rem 0.375rem 0;
    }

    .priority-basse { background-color: #16a34a; }
    .priority-moyenne { background-color: #f59e0b; }
    .priority-haute { background-color: #dc2626; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">{{ $task->titre }}</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $task->priorite === 'haute' ? 'danger' : ($task->priorite === 'moyenne' ? 'warning' : 'success') }} text-uppercase">
                    {{ $task->priorite }} priorité
                </span>
                <span class="badge bg-{{ $task->statut === 'termine' ? 'success' : ($task->statut === 'en_cours' ? 'primary' : 'secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                </span>
                @if($task->date_echeance < now() && in_array($task->statut, ['a_faire', 'en_cours']))
                    <span class="badge bg-danger">En retard</span>
                @endif
            </div>
        </div>

        @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-gear me-2"></i>Actions
            </button>
            <ul class="dropdown-menu">
                @if(auth()->user()->role === 'admin')
                <li>
                    <a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                        <i class="bi bi-pencil me-2"></i>Modifier
                    </a>
                </li>
                @endif
                @if($task->statut !== 'termine')
                <li>
                    <button class="dropdown-item" onclick="markCompleted({{ $task->id }})">
                        <i class="bi bi-check-circle me-2"></i>Marquer comme terminé
                    </button>
                </li>
                @endif
                <li>
                    <button class="dropdown-item" onclick="updateStatus({{ $task->id }})">
                        <i class="bi bi-arrow-repeat me-2"></i>Changer le statut
                    </button>
                </li>
                @if(auth()->user()->role === 'admin')
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button class="dropdown-item text-danger" onclick="deleteTask({{ $task->id }})">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </li>
                @endif
            </ul>
        </div>
        @endif
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Description</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $task->description }}</p>
                </div>
            </div>

            <!-- Progression -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Progression</h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="progress-circle bg-light">
                                {{ $task->progression }}%
                            </div>
                        </div>
                        <div class="col">
                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar
                                    @if($task->progression < 30) bg-danger
                                    @elseif($task->progression < 70) bg-warning
                                    @else bg-success @endif"
                                    style="width: {{ $task->progression }}%">
                                </div>
                            </div>
                            <p class="text-muted mb-0">
                                @if($task->progression == 0)
                                    Pas encore commencé
                                @elseif($task->progression < 100)
                                    En cours d'exécution
                                @else
                                    Tâche terminée
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique (placeholder) -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Tâche créée</h6>
                                <p class="text-muted mb-0">{{ $task->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @if($task->updated_at != $task->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dernière modification</h6>
                                <p class="text-muted mb-0">{{ $task->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Informations générales -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Assigné à :</div>
                        <div class="col-sm-7">
                            @if($task->utilisateur)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 32px; height: 32px;">
                                        <span class="text-white fw-bold small">
                                            {{ substr($task->utilisateur->prenom, 0, 1) }}{{ substr($task->utilisateur->nom, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $task->utilisateur->prenom }} {{ $task->utilisateur->nom }}</div>
                                        <small class="text-muted">{{ ucfirst($task->utilisateur->role) }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Échéance :</div>
                        <div class="col-sm-7">
                            <span class="{{ $task->date_echeance < now() && $task->statut !== 'termine' ? 'text-danger fw-bold' : '' }}">
                                {{ $task->date_echeance->format('d/m/Y à H:i') }}
                            </span>
                            @if($task->date_echeance < now() && $task->statut !== 'termine')
                                <br><small class="text-danger">En retard de {{ $task->date_echeance->diffForHumans() }}</small>
                            @else
                                <br><small class="text-muted">{{ $task->date_echeance->diffForHumans() }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Priorité :</div>
                        <div class="col-sm-7">
                            <span class="badge bg-{{ $task->priorite === 'haute' ? 'danger' : ($task->priorite === 'moyenne' ? 'warning' : 'success') }}">
                                {{ ucfirst($task->priorite) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Statut :</div>
                        <div class="col-sm-7">
                            <span class="badge bg-{{ $task->statut === 'termine' ? 'success' : ($task->statut === 'en_cours' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5 text-muted">Progression :</div>
                        <div class="col-sm-7">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $task->progression }}%"></div>
                            </div>
                            <small class="text-muted">{{ $task->progression }}% terminé</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Associations -->
            @if($task->projet || $task->evenement)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-link me-2"></i>Associations</h6>
                </div>
                <div class="card-body">
                    @if($task->projet)
                    <div class="mb-3">
                        <div class="text-muted mb-1">Projet :</div>
                        <a href="{{ route('projects.show', $task->projet) }}" class="text-decoration-none">
                            <i class="bi bi-folder me-2"></i>{{ $task->projet->nom }}
                        </a>
                    </div>
                    @endif

                    @if($task->evenement)
                    <div>
                        <div class="text-muted mb-1">Événement :</div>
                        <a href="{{ route('events.show', $task->evenement) }}" class="text-decoration-none">
                            <i class="bi bi-calendar-event me-2"></i>{{ $task->evenement->titre }}
                        </a>
                        <br><small class="text-muted">{{ $task->evenement->date_debut->format('d/m/Y') }}</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions rapides -->
            @if(auth()->user()->role === 'admin' || $task->id_utilisateur === auth()->id())
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Actions rapides</h6>
                </div>
                <div class="card-body">
                    @if($task->statut !== 'termine')
                    <button class="btn btn-success w-100 mb-2" onclick="markCompleted({{ $task->id }})">
                        <i class="bi bi-check-circle me-2"></i>Marquer comme terminé
                    </button>
                    @endif

                    <button class="btn btn-primary w-100 mb-2" onclick="updateStatus({{ $task->id }})">
                        <i class="bi bi-arrow-repeat me-2"></i>Mettre à jour le statut
                    </button>

                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-pencil me-2"></i>Modifier la tâche
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de mise à jour du statut -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mettre à jour le statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="taskId" value="{{ $task->id }}">
                    <div class="mb-3">
                        <label for="modalStatut" class="form-label">Statut</label>
                        <select class="form-select" id="modalStatut" name="statut" required>
                            <option value="a_faire" {{ $task->statut === 'a_faire' ? 'selected' : '' }}>À faire</option>
                            <option value="en_cours" {{ $task->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ $task->statut === 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalProgression" class="form-label">Progression (%)</label>
                        <input type="range" class="form-range" id="modalProgression" name="progression"
                               min="0" max="100" value="{{ $task->progression }}" oninput="updateProgressValue(this.value)">
                        <div class="text-center mt-2">
                            <span id="progressValue">{{ $task->progression }}</span>%
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Marquer une tâche comme terminée
function markCompleted(taskId) {
    if (confirm('Marquer cette tâche comme terminée ?')) {
        fetch(`/tasks/${taskId}/complete`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour');
        });
    }
}

// Ouvrir le modal de mise à jour du statut
function updateStatus(taskId) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

// Mettre à jour la valeur de progression affichée
function updateProgressValue(value) {
    document.getElementById('progressValue').textContent = value;

    // Ajuster automatiquement le statut selon la progression
    const statutSelect = document.getElementById('modalStatut');
    if (value == 0) {
        statutSelect.value = 'a_faire';
    } else if (value == 100) {
        statutSelect.value = 'termine';
    } else if (statutSelect.value === 'a_faire') {
        statutSelect.value = 'en_cours';
    }
}

// Soumettre le formulaire de mise à jour du statut
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const taskId = document.getElementById('taskId').value;
    const formData = new FormData(this);

    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour');
    });
});

// Supprimer une tâche
function deleteTask(taskId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.')) {
        fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/tasks';
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.125rem;
    top: 1.5rem;
    bottom: -0.5rem;
    width: 2px;
    background-color: #e2e8f0;
}

.timeline-marker {
    position: absolute;
    left: -1.375rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #e2e8f0;
}
</style>
@endpush

{{--
==================================================
FICHIER : resources/views/tasks/edit.blade.php
DESCRIPTION : Formulaire d'édition d'une tâche
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Modifier la tâche')

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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre de la tâche <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                           id="titre" name="titre" value="{{ old('titre', $task->titre) }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priorite" class="form-label">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priorite') is-invalid @enderror"
                                            id="priorite" name="priorite" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="basse" {{ old('priorite', $task->priorite) === 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="moyenne" {{ old('priorite', $task->priorite) === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                                        <option value="haute" {{ old('priorite', $task->priorite) === 'haute' ? 'selected' : '' }}>Haute</option>
                                    </select>
                                    @error('priorite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required>{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_echeance" class="form-label">Date d'échéance <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('date_echeance') is-invalid @enderror"
                                           id="date_echeance" name="date_echeance"
                                           value="{{ old('date_echeance', $task->date_echeance->format('Y-m-d\TH:i')) }}" required>
                                    @error('date_echeance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('statut') is-invalid @enderror"
                                            id="statut" name="statut" required>
                                        <option value="a_faire" {{ old('statut', $task->statut) === 'a_faire' ? 'selected' : '' }}>À faire</option>
                                        <option value="en_cours" {{ old('statut', $task->statut) === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                        <option value="termine" {{ old('statut', $task->statut) === 'termine' ? 'selected' : '' }}>Terminé</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="progression" class="form-label">Progression (%)</label>
                                    <input type="range" class="form-range" id="progression" name="progression"
                                           min="0" max="100" value="{{ old('progression', $task->progression) }}"
                                           oninput="updateProgressValue(this.value)">
                                    <div class="text-center mt-2">
                                        <span id="progressValue">{{ old('progression', $task->progression) }}</span>%
                                    </div>
                                    @error('progression')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_utilisateur" class="form-label">Assigné à <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_utilisateur') is-invalid @enderror"
                                            id="id_utilisateur" name="id_utilisateur" required>
                                        <option value="">Sélectionner un technicien...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('id_utilisateur', $task->id_utilisateur) == $user->id ? 'selected' : '' }}>
                                                {{ $user->prenom }} {{ $user->nom }} ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_utilisateur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_projet" class="form-label">Projet associé</label>
                                    <select class="form-select @error('id_projet') is-invalid @enderror"
                                            id="id_projet" name="id_projet">
                                        <option value="">Aucun projet</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('id_projet', $task->id_projet) == $project->id ? 'selected' : '' }}>
                                                {{ $project->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_projet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_evenement" class="form-label">Événement associé</label>
                                    <select class="form-select @error('id_evenement') is-invalid @enderror"
                                            id="id_evenement" name="id_evenement">
                                        <option value="">Aucun événement</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ old('id_evenement', $task->id_evenement) == $event->id ? 'selected' : '' }}>
                                                {{ $event->titre }} - {{ $event->date_debut->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_evenement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informations actuelles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>État actuel</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Créée le :</small>
                        <div>{{ $task->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Dernière modification :</small>
                        <div>{{ $task->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Statut actuel :</small>
                        <div>
                            <span class="badge bg-{{ $task->statut === 'termine' ? 'success' : ($task->statut === 'en_cours' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->statut)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Progression actuelle :</small>
                        <div class="progress mt-1" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $task->progression }}%"></div>
                        </div>
                        <small class="text-muted">{{ $task->progression }}%</small>
                    </div>
                </div>
            </div>

            <!-- Zone de danger -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Zone de danger</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Actions irréversibles disponibles pour cette tâche.</p>
                    <button class="btn btn-outline-danger w-100" onclick="deleteTask({{ $task->id }})">
                        <i class="bi bi-trash me-2"></i>Supprimer la tâche
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mettre à jour la valeur de progression affichée
function updateProgressValue(value) {
    document.getElementById('progressValue').textContent = value;

    // Ajuster automatiquement le statut selon la progression
    const statutSelect = document.getElementById('statut');
    if (value == 0) {
        statutSelect.value = 'a_faire';
    } else if (value == 100) {
        statutSelect.value = 'termine';
    } else if (statutSelect.value === 'a_faire') {
        statutSelect.value = 'en_cours';
    }
}

// Supprimer une tâche
function deleteTask(taskId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.')) {
        fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/tasks';
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

// Ajuster automatiquement le statut quand la progression change
document.getElementById('progression').addEventListener('input', function() {
    updateProgressValue(this.value);
});

// Ajuster automatiquement la progression quand le statut change
document.getElementById('statut').addEventListener('change', function() {
    const progressSlider = document.getElementById('progression');
    if (this.value === 'a_faire') {
        progressSlider.value = 0;
        updateProgressValue(0);
    } else if (this.value === 'termine') {
        progressSlider.value = 100;
        updateProgressValue(100);
    }
});
</script>
@endpush
