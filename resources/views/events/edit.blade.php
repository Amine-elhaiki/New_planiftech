@extends('layouts.app')

@section('title', 'Modifier l\'événement - ' . $event->titre)

@push('styles')
<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .form-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .required::after {
        content: '*';
        color: #ef4444;
        margin-left: 4px;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        outline: none;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .event-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .event-type-card {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        position: relative;
    }

    .event-type-card:hover {
        border-color: #f59e0b;
        background: #fef3c7;
    }

    .event-type-card.selected {
        border-color: #f59e0b;
        background: #f59e0b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .event-type-card input[type="radio"] {
        display: none;
    }

    .event-type-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .priority-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .priority-option {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .priority-option:hover {
        transform: translateY(-1px);
    }

    .priority-option.selected {
        border-color: #f59e0b;
        background: #f59e0b;
        color: white;
    }

    .priority-option input[type="radio"] {
        display: none;
    }

    .priority-normale { border-color: #22c55e; }
    .priority-normale.selected { background: #22c55e; border-color: #22c55e; }
    .priority-haute { border-color: #f59e0b; }
    .priority-haute.selected { background: #f59e0b; border-color: #f59e0b; }
    .priority-urgente { border-color: #ef4444; }
    .priority-urgente.selected { background: #ef4444; border-color: #ef4444; }

    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .status-option {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .status-option:hover {
        transform: translateY(-1px);
    }

    .status-option.selected {
        border-color: #f59e0b;
        background: #f59e0b;
        color: white;
    }

    .status-option input[type="radio"] {
        display: none;
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

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
        box-shadow: 0 4px 14px rgba(107, 114, 128, 0.4);
    }

    .btn-secondary:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
    }

    .btn-danger:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .participants-section {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        background: #f9fafb;
        margin-bottom: 1.5rem;
    }

    .participants-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .participants-search {
        position: relative;
        margin-bottom: 1rem;
    }

    .participants-search input {
        padding-left: 2.5rem;
    }

    .participants-search .bi {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }

    .participants-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .user-item:hover {
        background-color: #f9fafb;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-item input[type="checkbox"] {
        margin-right: 0.75rem;
        transform: scale(1.2);
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .user-role {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .error-alert {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .info-alert {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #92400e;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="edit-container">

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="error-alert">
                <strong>Erreurs de validation :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Message d'information -->
        @if(!$event->peutEtreModifie())
            <div class="info-alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Attention :</strong> Cet événement ne peut plus être modifié car il est {{ $event->statut_libelle }} 
                @if($event->est_passe) ou déjà passé @endif.
                Seules certaines informations peuvent être mises à jour.
            </div>
        @endif

        <!-- Formulaire principal -->
        <div class="form-card">
            <div class="form-header">
                <h1 class="h3 mb-2">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier l'Événement
                </h1>
                <p class="mb-0">Mettre à jour les informations de l'événement "{{ $event->titre }}"</p>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('events.update', $event) }}" id="eventForm">
                    @csrf
                    @method('PUT')

                    <!-- Informations générales -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="titre" class="form-label required">Titre de l'événement</label>
                                <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                       id="titre" name="titre" value="{{ old('titre', $event->titre) }}"
                                       placeholder="Ex: Maintenance pompe station A" required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_projet" class="form-label">Projet associé</label>
                                <select class="form-select @error('id_projet') is-invalid @enderror" id="id_projet" name="id_projet">
                                    <option value="">Aucun projet</option>
                                    @foreach($projets as $project)
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
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label required">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4" required
                                  placeholder="Décrivez l'objectif et les détails de cet événement...">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type d'événement -->
                    <div class="form-group">
                        <label class="form-label required">Type d'événement</label>
                        <div class="event-type-grid">
                            @foreach($typesOptions as $key => $label)
                                <div class="event-type-card {{ old('type', $event->type) === $key ? 'selected' : '' }}"
                                     onclick="selectEventType('{{ $key }}')">
                                    <input type="radio" name="type" value="{{ $key }}" id="type_{{ $key }}"
                                           {{ old('type', $event->type) === $key ? 'checked' : '' }} required>
                                    <i class="event-type-icon {{ match($key) {
                                        'intervention' => 'bi-tools',
                                        'reunion' => 'bi-people',
                                        'formation' => 'bi-book',
                                        'visite' => 'bi-geo-alt',
                                        'maintenance' => 'bi-gear',
                                        'inspection' => 'bi-search',
                                        'audit' => 'bi-clipboard-check',
                                        default => 'bi-calendar-event'
                                    } }}"></i>
                                    <div class="fw-semibold">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="form-group">
                        <label class="form-label required">Statut</label>
                        <div class="status-grid">
                            @foreach($statutsOptions as $key => $label)
                                <div class="status-option {{ old('statut', $event->statut) === $key ? 'selected' : '' }}"
                                     onclick="selectStatus('{{ $key }}')">
                                    <input type="radio" name="statut" value="{{ $key }}" id="statut_{{ $key }}"
                                           {{ old('statut', $event->statut) === $key ? 'checked' : '' }} required>
                                    <div class="fw-semibold">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div class="form-group">
                        <label class="form-label required">Priorité</label>
                        <div class="priority-grid">
                            @foreach($prioritesOptions as $key => $label)
                                <div class="priority-option priority-{{ $key }} {{ old('priorite', $event->priorite) === $key ? 'selected' : '' }}"
                                     onclick="selectPriority('{{ $key }}')">
                                    <input type="radio" name="priorite" value="{{ $key }}" id="priorite_{{ $key }}"
                                           {{ old('priorite', $event->priorite) === $key ? 'checked' : '' }} required>
                                    <div class="fw-semibold">{{ $label }}</div>
                                    <small>{{ match($key) {
                                        'normale' => 'Tâches courantes',
                                        'haute' => 'Important à traiter',
                                        'urgente' => 'Nécessite intervention immédiate',
                                        default => ''
                                    } }}</small>
                                </div>
                            @endforeach
                        </div>
                        @error('priorite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates et lieu -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_debut" class="form-label required">Date et heure de début</label>
                                <input type="datetime-local" class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut" 
                                       value="{{ old('date_debut', $event->date_debut->format('Y-m-d\TH:i')) }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_fin" class="form-label required">Date et heure de fin</label>
                                <input type="datetime-local" class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin" 
                                       value="{{ old('date_fin', $event->date_fin->format('Y-m-d\TH:i')) }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="lieu" class="form-label required">Lieu</label>
                                <input type="text" class="form-control @error('lieu') is-invalid @enderror"
                                       id="lieu" name="lieu" value="{{ old('lieu', $event->lieu) }}"
                                       placeholder="Ex: Station pompage P12, secteur Tadla-Nord" required>
                                @error('lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="coordonnees_gps" class="form-label">Coordonnées GPS</label>
                                <input type="text" class="form-control @error('coordonnees_gps') is-invalid @enderror"
                                       id="coordonnees_gps" name="coordonnees_gps" 
                                       value="{{ old('coordonnees_gps', $event->coordonnees_gps) }}"
                                       placeholder="32.123456, -6.789012">
                                @error('coordonnees_gps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Participants -->
                    <div class="form-group">
                        <label class="form-label">Participants</label>
                        <div class="participants-section">
                            <div class="participants-header">
                                <h6 class="mb-0">Gérer les participants</h6>
                                <small class="text-muted">{{ $event->participants->count() }} participant(s) actuellement</small>
                            </div>

                            <div class="participants-search">
                                <i class="bi bi-search"></i>
                                <input type="text" id="participantSearch" placeholder="Rechercher un utilisateur..."
                                       class="form-control">
                            </div>

                            <div class="participants-list">
                                @php
                                    $currentParticipants = $event->participants->pluck('id_utilisateur')->toArray();
                                @endphp
                                @foreach($utilisateurs as $utilisateur)
                                    <div class="user-item" data-name="{{ strtolower($utilisateur->nom . ' ' . $utilisateur->prenom) }}"
                                         data-role="{{ strtolower($utilisateur->role_libelle) }}">
                                        <input type="checkbox" name="participants[]" value="{{ $utilisateur->id }}"
                                               id="participant_{{ $utilisateur->id }}"
                                               {{ in_array($utilisateur->id, $currentParticipants) ? 'checked' : '' }}>

                                        <div class="user-avatar">
                                            {{ substr($utilisateur->prenom, 0, 1) }}{{ substr($utilisateur->nom, 0, 1) }}
                                        </div>

                                        <div class="user-info">
                                            <div class="user-name">{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</div>
                                            <div class="user-role">{{ $utilisateur->email }}</div>
                                        </div>

                                        @if($utilisateur->id === $event->id_organisateur)
                                            <span class="badge bg-warning">Organisateur</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="action-buttons">
                        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Mettre à jour
                        </button>
                        @if(auth()->user()->role === 'admin')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $event->id }})">
                                <i class="bi bi-trash me-2"></i>
                                Supprimer
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
                <p class="text-danger">Cette action supprimera définitivement l'événement et toutes ses participations.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
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

// Sélection du statut
function selectStatus(status) {
    document.querySelectorAll('.status-option').forEach(option => {
        option.classList.remove('selected');
    });
    event.target.closest('.status-option').classList.add('selected');
    document.querySelector(`input[name="statut"][value="${status}"]`).checked = true;
}

// Recherche de participants
document.getElementById('participantSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(item => {
        const name = item.dataset.name;
        const role = item.dataset.role;

        if (name.includes(searchTerm) || role.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Confirmation de suppression
function confirmDelete(eventId) {
    document.getElementById('deleteForm').action = '/events/' + eventId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Validation des dates
document.getElementById('date_debut').addEventListener('change', function() {
    const dateDebut = this.value;
    const dateFinInput = document.getElementById('date_fin');

    if (dateDebut) {
        dateFinInput.min = dateDebut;

        if (dateFinInput.value && new Date(dateFinInput.value) <= new Date(dateDebut)) {
            const newEndDate = new Date(dateDebut);
            newEndDate.setHours(newEndDate.getHours() + 2);
            dateFinInput.value = newEndDate.toISOString().slice(0, 16);
        }
    }
});

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner automatiquement les options déjà choisies
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType) {
        selectedType.closest('.event-type-card').classList.add('selected');
    }

    const selectedPriority = document.querySelector('input[name="priorite"]:checked');
    if (selectedPriority) {
        selectedPriority.closest('.priority-option').classList.add('selected');
    }

    const selectedStatus = document.querySelector('input[name="statut"]:checked');
    if (selectedStatus) {
        selectedStatus.closest('.status-option').classList.add('selected');
    }
});
</script>
@endpush