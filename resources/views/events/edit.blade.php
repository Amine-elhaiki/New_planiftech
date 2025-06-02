@extends('layouts.app')

@section('title', 'Modifier l\'Événement')

@push('styles')
<style>
    .participant-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        background-color: #f8f9fa;
    }

    .participant-item:hover {
        background-color: #e9ecef;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .event-type-card {
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .event-type-card:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .event-type-card.selected {
        border-color: #007bff;
        background-color: #e7f1ff;
    }

    .priority-option {
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
    }

    .priority-option:hover {
        background-color: #f8f9fa;
    }

    .priority-option.selected {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .history-item {
        border-left: 3px solid #007bff;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }

    .required-field::after {
        content: '*';
        color: #dc3545;
        margin-left: 2px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Modifier l'Événement
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ $event->titre }}</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i>
                Voir
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informations de l'Événement
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('events.update', $event) }}" id="editEventForm">
                        @csrf
                        @method('PUT')

                        <!-- Informations de base -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titre" class="form-label required-field">Titre de l'événement</label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                           id="titre" name="titre" value="{{ old('titre', $event->titre) }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="statut" class="form-label required-field">Statut</label>
                                    <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                        @foreach(App\Models\Event::$statuts as $key => $label)
                                            <option value="{{ $key }}" {{ old('statut', $event->statut) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Type d'événement -->
                        <div class="mb-4">
                            <label class="form-label required-field">Type d'événement</label>
                            <div class="row">
                                @foreach(App\Models\Event::$types as $key => $label)
                                    <div class="col-md-3 mb-2">
                                        <div class="event-type-card p-3 text-center {{ old('type', $event->type) === $key ? 'selected' : '' }}"
                                             onclick="selectEventType('{{ $key }}')">
                                            <i class="bi bi-{{ $key === 'intervention' ? 'tools' : ($key === 'reunion' ? 'people' : ($key === 'formation' ? 'book' : 'geo-alt')) }} fs-4 text-primary d-block mb-2"></i>
                                            <div class="fw-bold">{{ $label }}</div>
                                            <input type="radio" name="type" value="{{ $key }}"
                                                   {{ old('type', $event->type) === $key ? 'checked' : '' }} style="display: none;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label required-field">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priorité -->
                        <div class="mb-4">
                            <label class="form-label required-field">Priorité</label>
                            <div class="row">
                                @foreach(App\Models\Event::$priorites as $key => $label)
                                    <div class="col-md-4">
                                        <div class="priority-option {{ old('priorite', $event->priorite) === $key ? 'selected' : '' }}"
                                             onclick="selectPriority('{{ $key }}')">
                                            <div class="fw-bold">{{ $label }}</div>
                                            <input type="radio" name="priorite" value="{{ $key }}"
                                                   {{ old('priorite', $event->priorite) === $key ? 'checked' : '' }} style="display: none;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('priorite')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Planification -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-calendar-date text-primary me-2"></i>
                            Planification
                        </h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label required-field">Date et heure de début</label>
                                <input type="datetime-local" class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut"
                                       value="{{ old('date_debut', $event->date_debut->format('Y-m-d\TH:i')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_fin" class="form-label required-field">Date et heure de fin</label>
                                <input type="datetime-local" class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin"
                                       value="{{ old('date_fin', $event->date_fin->format('Y-m-d\TH:i')) }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Lieu -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="lieu" class="form-label required-field">Lieu</label>
                                <input type="text" class="form-control @error('lieu') is-invalid @enderror"
                                       id="lieu" name="lieu" value="{{ old('lieu', $event->lieu) }}" required>
                                @error('lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="coordonnees_gps" class="form-label">Coordonnées GPS</label>
                                <input type="text" class="form-control @error('coordonnees_gps') is-invalid @enderror"
                                       id="coordonnees_gps" name="coordonnees_gps"
                                       value="{{ old('coordonnees_gps', $event->coordonnees_gps) }}"
                                       placeholder="Ex: 32.4816, -6.7929">
                                @error('coordonnees_gps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Projet associé -->
                        <div class="mb-4">
                            <label for="id_projet" class="form-label">Projet associé</label>
                            <select class="form-select @error('id_projet') is-invalid @enderror" id="id_projet" name="id_projet">
                                <option value="">Aucun projet</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                            {{ old('id_projet', $event->id_projet) == $project->id ? 'selected' : '' }}>
                                        {{ $project->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_projet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Participants -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-people text-primary me-2"></i>
                            Participants
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Utilisateurs disponibles</label>
                                <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($users as $user)
                                        @if($user->id !== $event->id_organisateur)
                                            @php
                                                $isParticipant = $event->participants->contains('id_utilisateur', $user->id);
                                            @endphp
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="participants[]"
                                                       value="{{ $user->id }}" id="participant{{ $user->id }}"
                                                       {{ $isParticipant ? 'checked' : '' }}>
                                                <label class="form-check-label" for="participant{{ $user->id }}">
                                                    <strong>{{ $user->prenom }} {{ $user->nom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $user->email }} - {{ ucfirst($user->role) }}</small>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Participants actuels</label>
                                <div class="border rounded p-2" style="min-height: 300px;">
                                    <!-- Organisateur -->
                                    <div class="participant-item">
                                        <div class="flex-grow-1">
                                            <strong>{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $event->organisateur->email }}</small>
                                        </div>
                                        <span class="badge bg-primary">Organisateur</span>
                                    </div>

                                    <!-- Participants -->
                                    @foreach($event->participants as $participation)
                                        @if($participation->id_utilisateur !== $event->id_organisateur)
                                            <div class="participant-item">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $participation->utilisateur->prenom }} {{ $participation->utilisateur->nom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $participation->utilisateur->email }}</small>
                                                </div>
                                                <span class="badge status-badge {{ $participation->classe_statut }}">
                                                    {{ $participation->statut_presence_nom }}
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                @if($event->statut !== 'termine' && $event->statut !== 'annule')
                                    <button type="button" class="btn btn-outline-warning" onclick="postponeEvent()">
                                        <i class="bi bi-calendar-x me-1"></i>
                                        Reporter
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="cancelEvent()">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Annuler
                                    </button>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    Annuler
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations supplémentaires -->
        <div class="col-lg-4">
            <!-- Informations sur l'événement -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Créé le</small>
                        <div>{{ $event->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Dernière modification</small>
                        <div>{{ $event->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Durée</small>
                        <div>{{ $event->duree }} minutes</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Participants confirmés</small>
                        <div>{{ $event->nombre_participants_confirmes }} / {{ $event->participants->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.duplicate', $event) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-files me-1"></i>
                            Dupliquer l'événement
                        </a>
                        @if($event->statut === 'planifie')
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="markAsCompleted()">
                                <i class="bi bi-check-circle me-1"></i>
                                Marquer comme terminé
                            </button>
                        @endif
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-eye me-1"></i>
                            Voir les détails
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            @if($event->taches->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-list-check text-primary me-2"></i>
                        Tâches Associées
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Total des tâches</small>
                        <div class="fw-bold">{{ $event->taches->count() }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Terminées</small>
                        <div class="fw-bold text-success">{{ $event->taches->where('statut', 'termine')->count() }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">En cours</small>
                        <div class="fw-bold text-primary">{{ $event->taches->where('statut', 'en_cours')->count() }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de report -->
<div class="modal fade" id="postponeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporter l'Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('events.postpone', $event) }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="new_date_debut" class="form-label">Nouvelle date de début</label>
                            <input type="datetime-local" class="form-control" id="new_date_debut" name="date_debut" required>
                        </div>
                        <div class="col-md-6">
                            <label for="new_date_fin" class="form-label">Nouvelle date de fin</label>
                            <input type="datetime-local" class="form-control" id="new_date_fin" name="date_fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Reporter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sélection du type d'événement
function selectEventType(type) {
    document.querySelectorAll('.event-type-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.event-type-card').classList.add('selected');
    document.querySelector(`input[name="type"][value="${type}"]`).checked = true;
}

// Sélection de la priorité
function selectPriority(priority) {
    document.querySelectorAll('.priority-option').forEach(option => {
        option.classList.remove('selected');
    });
    event.target.closest('.priority-option').classList.add('selected');
    document.querySelector(`input[name="priorite"][value="${priority}"]`).checked = true;
}

// Reporter l'événement
function postponeEvent() {
    const modal = new bootstrap.Modal(document.getElementById('postponeModal'));
    modal.show();
}

// Annuler l'événement
function cancelEvent() {
    if (confirm('Êtes-vous sûr de vouloir annuler cet événement ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("events.cancel", $event) }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

// Marquer comme terminé
function markAsCompleted() {
    if (confirm('Êtes-vous sûr de vouloir marquer cet événement comme terminé ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("events.markCompleted", $event) }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

// Validation des dates
document.getElementById('date_debut').addEventListener('change', function() {
    const dateDebut = new Date(this.value);
    const dateFin = document.getElementById('date_fin');

    // Mettre à jour la date de fin minimale
    dateFin.min = this.value;

    // Si la date de fin est antérieure, l'ajuster
    if (dateFin.value && new Date(dateFin.value) <= dateDebut) {
        const newEndDate = new Date(dateDebut.getTime() + 60 * 60 * 1000); // +1 heure
        dateFin.value = newEndDate.toISOString().slice(0, 16);
    }
});

// Validation du formulaire
document.getElementById('editEventForm').addEventListener('submit', function(e) {
    const dateDebut = new Date(document.getElementById('date_debut').value);
    const dateFin = new Date(document.getElementById('date_fin').value);

    if (dateFin <= dateDebut) {
        e.preventDefault();
        alert('La date de fin doit être postérieure à la date de début.');
        return false;
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour la date de fin minimale au chargement
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    if (dateDebut.value) {
        dateFin.min = dateDebut.value;
    }
});
</script>
@endpush
