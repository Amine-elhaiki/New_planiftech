@extends('layouts.app')

@section('title', 'Créer un Événement')

@push('styles')
<style>
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        position: relative;
    }

    .step:not(:last-child)::after {
        content: '';
        width: 3rem;
        height: 2px;
        background-color: #dee2e6;
        margin: 0 1rem;
    }

    .step.active::after {
        background-color: #007bff;
    }

    .step-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid #dee2e6;
    }

    .step.active .step-circle {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .step.completed .step-circle {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .form-section {
        display: none;
    }

    .form-section.active {
        display: block;
    }

    .required-field::after {
        content: '*';
        color: #dc3545;
        margin-left: 2px;
    }

    .participant-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        background-color: #f8f9fa;
    }

    .participant-item:hover {
        background-color: #e9ecef;
    }

    .location-suggestion {
        cursor: pointer;
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .location-suggestion:hover {
        background-color: #f8f9fa;
    }

    .conflict-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .event-type-card {
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        padding: 1.5rem;
        text-align: center;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .event-type-card:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }

    .event-type-card.selected {
        border-color: #007bff;
        background-color: #e7f1ff;
        transform: translateY(-2px);
    }

    .priority-option {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        background: white;
    }

    .priority-option:hover {
        background-color: #f8f9fa;
        border-color: #007bff;
    }

    .priority-option.selected {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .datetime-input {
        min-width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-calendar-plus text-primary me-2"></i>
                Créer un Nouvel Événement
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                    <li class="breadcrumb-item active">Créer</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Retour
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <!-- Indicateur d'étapes -->
                    <div class="step-indicator">
                        <div class="step active" id="step-1">
                            <div class="step-circle">1</div>
                        </div>
                        <div class="step" id="step-2">
                            <div class="step-circle">2</div>
                        </div>
                        <div class="step" id="step-3">
                            <div class="step-circle">3</div>
                        </div>
                    </div>

                    <form id="eventForm" method="POST" action="{{ route('events.store') }}">
                        @csrf

                        <!-- Étape 1: Informations générales -->
                        <div class="form-section active" id="section-1">
                            <h4 class="mb-4">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                Informations Générales
                            </h4>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label required-field">Titre de l'événement</label>
                                        <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                               id="titre" name="titre" value="{{ old('titre') }}"
                                               placeholder="Ex: Maintenance pompe station A" required>
                                        @error('titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="priorite" class="form-label required-field">Priorité</label>
                                        <select class="form-select @error('priorite') is-invalid @enderror"
                                                id="priorite" name="priorite" required>
                                            <option value="">Sélectionner...</option>
                                            @foreach(App\Models\Event::$priorites as $key => $label)
                                                <option value="{{ $key }}" {{ old('priorite', 'normale') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('priorite')
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
                                            <div class="event-type-card {{ old('type') === $key ? 'selected' : '' }}"
                                                 onclick="selectEventType('{{ $key }}')">
                                                <i class="bi bi-{{ $key === 'intervention' ? 'tools' : ($key === 'reunion' ? 'people' : ($key === 'formation' ? 'book' : 'geo-alt')) }} fs-2 text-primary d-block mb-2"></i>
                                                <div class="fw-bold">{{ $label }}</div>
                                                <input type="radio" name="type" value="{{ $key }}"
                                                       {{ old('type') === $key ? 'checked' : '' }} style="display: none;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label required-field">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required
                                          placeholder="Décrivez en détail l'objet de cet événement...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="id_projet" class="form-label">Projet associé (optionnel)</label>
                                <select class="form-select @error('id_projet') is-invalid @enderror" id="id_projet" name="id_projet">
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

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                    Suivant
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 2: Planification -->
                        <div class="form-section" id="section-2">
                            <h4 class="mb-4">
                                <i class="bi bi-calendar-date text-primary me-2"></i>
                                Planification
                            </h4>

                            <!-- Alerte de conflit (sera masquée par défaut) -->
                            <div class="conflict-warning d-none" id="conflictWarning">
                                <h6 class="text-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Conflit d'horaires détecté
                                </h6>
                                <p id="conflictMessage" class="mb-0"></p>
                            </div>

                            <!-- Dates et heures avec datetime-local -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_debut" class="form-label required-field">Date et heure de début</label>
                                        <input type="datetime-local"
                                               class="form-control datetime-input @error('date_debut') is-invalid @enderror"
                                               id="date_debut"
                                               name="date_debut"
                                               value="{{ old('date_debut', now()->format('Y-m-d\TH:i')) }}"
                                               min="{{ now()->format('Y-m-d\TH:i') }}"
                                               required>
                                        @error('date_debut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_fin" class="form-label required-field">Date et heure de fin</label>
                                        <input type="datetime-local"
                                               class="form-control datetime-input @error('date_fin') is-invalid @enderror"
                                               id="date_fin"
                                               name="date_fin"
                                               value="{{ old('date_fin', now()->addHour()->format('Y-m-d\TH:i')) }}"
                                               min="{{ now()->format('Y-m-d\TH:i') }}"
                                               required>
                                        @error('date_fin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Lieu -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="lieu" class="form-label required-field">Lieu</label>
                                        <input type="text" class="form-control @error('lieu') is-invalid @enderror"
                                               id="lieu" name="lieu" value="{{ old('lieu') }}"
                                               placeholder="Ex: Station de pompage A, Bureau principal..." required>

                                        <!-- Suggestions de lieux -->
                                        <div class="dropdown-menu w-100" id="locationSuggestions" style="display: none;">
                                            <div class="location-suggestion" data-location="Station de pompage A">Station de pompage A</div>
                                            <div class="location-suggestion" data-location="Station de pompage B">Station de pompage B</div>
                                            <div class="location-suggestion" data-location="Bureau principal ORMVAT">Bureau principal ORMVAT</div>
                                            <div class="location-suggestion" data-location="Salle de réunion">Salle de réunion</div>
                                            <div class="location-suggestion" data-location="Terrain - Zone A">Terrain - Zone A</div>
                                            <div class="location-suggestion" data-location="Terrain - Zone B">Terrain - Zone B</div>
                                        </div>

                                        @error('lieu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="coordonnees_gps" class="form-label">
                                            Coordonnées GPS
                                            <small class="text-muted">(optionnel)</small>
                                        </label>
                                        <input type="text" class="form-control @error('coordonnees_gps') is-invalid @enderror"
                                               id="coordonnees_gps" name="coordonnees_gps" value="{{ old('coordonnees_gps') }}"
                                               placeholder="Ex: 32.4816, -6.7929">
                                        @error('coordonnees_gps')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep(1)">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Précédent
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                    Suivant
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Étape 3: Participants -->
                        <div class="form-section" id="section-3">
                            <h4 class="mb-4">
                                <i class="bi bi-people text-primary me-2"></i>
                                Participants
                            </h4>

                            <div class="mb-3">
                                <label class="form-label">Sélectionner les participants</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="participantSearch"
                                           placeholder="Rechercher un utilisateur...">
                                </div>
                                <small class="text-muted">Vous serez automatiquement ajouté comme organisateur</small>
                            </div>

                            <!-- Liste des utilisateurs disponibles -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Utilisateurs disponibles</h6>
                                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;" id="availableUsers">
                                        @foreach($users as $user)
                                            @if($user->id !== Auth::id())
                                                <div class="form-check user-item" data-user-id="{{ $user->id }}"
                                                     data-user-name="{{ strtolower($user->prenom . ' ' . $user->nom) }}">
                                                    <input class="form-check-input" type="checkbox" name="participants[]"
                                                           value="{{ $user->id }}" id="user{{ $user->id }}">
                                                    <label class="form-check-label" for="user{{ $user->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <strong>{{ $user->prenom }} {{ $user->nom }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $user->email }} - {{ ucfirst($user->role) }}</small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>Participants sélectionnés</h6>
                                    <div class="border rounded p-2" style="min-height: 300px;" id="selectedParticipants">
                                        <!-- Organisateur (ajouté automatiquement) -->
                                        <div class="participant-item">
                                            <div class="flex-grow-1">
                                                <strong>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Auth::user()->email }} - Organisateur</small>
                                            </div>
                                            <span class="badge bg-primary">Organisateur</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions rapides -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllTechnicians()">
                                    <i class="bi bi-check-all me-1"></i>
                                    Sélectionner tous les techniciens
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                                    <i class="bi bi-x-lg me-1"></i>
                                    Désélectionner tout
                                </button>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep(2)">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Précédent
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Créer l'Événement
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;

// Navigation entre les étapes
function nextStep(step) {
    if (validateCurrentStep()) {
        showStep(step);
    }
}

function previousStep(step) {
    showStep(step);
}

function showStep(step) {
    // Masquer toutes les sections
    document.querySelectorAll('.form-section').forEach(section => {
        section.classList.remove('active');
    });

    // Réinitialiser tous les indicateurs d'étapes
    document.querySelectorAll('.step').forEach(stepEl => {
        stepEl.classList.remove('active', 'completed');
    });

    // Afficher la section active
    document.getElementById(`section-${step}`).classList.add('active');

    // Mettre à jour les indicateurs d'étapes
    for (let i = 1; i <= 3; i++) {
        const stepEl = document.getElementById(`step-${i}`);
        if (i < step) {
            stepEl.classList.add('completed');
        } else if (i === step) {
            stepEl.classList.add('active');
        }
    }

    currentStep = step;
}

// Validation des étapes
function validateCurrentStep() {
    const section = document.getElementById(`section-${currentStep}`);
    const requiredFields = section.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validation spécifique pour l'étape 2 (dates)
    if (currentStep === 2) {
        isValid = isValid && validateDates();
    }

    return isValid;
}

// Validation des dates
function validateDates() {
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;

    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);

        if (fin <= debut) {
            alert('La date de fin doit être postérieure à la date de début.');
            document.getElementById('date_fin').classList.add('is-invalid');
            return false;
        }

        // Vérifier les conflits (simulation)
        checkConflicts();
    }

    return true;
}

// Vérification des conflits (simulation)
function checkConflicts() {
    // Simulation de vérification de conflits
    const conflictWarning = document.getElementById('conflictWarning');

    // Simulation: conflit si l'événement est un mardi
    const dateDebut = new Date(document.getElementById('date_debut').value);
    if (dateDebut.getDay() === 2) { // Mardi
        document.getElementById('conflictMessage').textContent =
            'Un autre événement est prévu le même jour pour certains participants.';
        conflictWarning.classList.remove('d-none');
    } else {
        conflictWarning.classList.add('d-none');
    }
}

// Sélection du type d'événement
function selectEventType(type) {
    document.querySelectorAll('.event-type-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.event-type-card').classList.add('selected');
    document.querySelector(`input[name="type"][value="${type}"]`).checked = true;
}

// Recherche de participants
document.getElementById('participantSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(item => {
        const userName = item.dataset.userName;
        if (userName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Gestion de la sélection des participants
document.addEventListener('change', function(e) {
    if (e.target.name === 'participants[]') {
        updateSelectedParticipants();
    }
});

function updateSelectedParticipants() {
    const selectedContainer = document.getElementById('selectedParticipants');
    const checkboxes = document.querySelectorAll('input[name="participants[]"]:checked');

    // Garder seulement l'organisateur
    selectedContainer.innerHTML = `
        <div class="participant-item">
            <div class="flex-grow-1">
                <strong>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</strong>
                <br>
                <small class="text-muted">{{ Auth::user()->email }} - Organisateur</small>
            </div>
            <span class="badge bg-primary">Organisateur</span>
        </div>
    `;

    // Ajouter les participants sélectionnés
    checkboxes.forEach(checkbox => {
        const userItem = checkbox.closest('.user-item');
        const label = userItem.querySelector('label');
        const userName = label.querySelector('strong').textContent;
        const userEmail = label.querySelector('small').textContent.split(' - ')[0];
        const userRole = label.querySelector('small').textContent.split(' - ')[1];

        const participantDiv = document.createElement('div');
        participantDiv.className = 'participant-item';
        participantDiv.innerHTML = `
            <div class="flex-grow-1">
                <strong>${userName}</strong>
                <br>
                <small class="text-muted">${userEmail} - ${userRole}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeParticipant('${checkbox.value}')">
                <i class="bi bi-x"></i>
            </button>
        `;

        selectedContainer.appendChild(participantDiv);
    });
}

// Supprimer un participant
function removeParticipant(userId) {
    const checkbox = document.querySelector(`input[value="${userId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedParticipants();
    }
}

// Sélectionner tous les techniciens
function selectAllTechnicians() {
    const technicianCheckboxes = document.querySelectorAll('.user-item input[type="checkbox"]');
    technicianCheckboxes.forEach(checkbox => {
        const userItem = checkbox.closest('.user-item');
        const roleText = userItem.querySelector('small').textContent.toLowerCase();
        if (roleText.includes('technicien')) {
            checkbox.checked = true;
        }
    });
    updateSelectedParticipants();
}

// Désélectionner tout
function clearSelection() {
    const checkboxes = document.querySelectorAll('input[name="participants[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedParticipants();
}

// Suggestions de lieux
document.getElementById('lieu').addEventListener('focus', function() {
    document.getElementById('locationSuggestions').style.display = 'block';
});

document.getElementById('lieu').addEventListener('blur', function() {
    setTimeout(() => {
        document.getElementById('locationSuggestions').style.display = 'none';
    }, 200);
});

document.querySelectorAll('.location-suggestion').forEach(suggestion => {
    suggestion.addEventListener('click', function() {
        document.getElementById('lieu').value = this.dataset.location;
        document.getElementById('locationSuggestions').style.display = 'none';
    });
});

// Validation en temps réel des dates
document.getElementById('date_debut').addEventListener('change', function() {
    const dateDebut = this.value;
    const dateFinInput = document.getElementById('date_fin');

    // Mettre à jour la date de fin minimale
    dateFinInput.min = dateDebut;

    // Si la date de fin est antérieure, l'ajuster automatiquement
    if (dateFinInput.value && new Date(dateFinInput.value) <= new Date(dateDebut)) {
        const newEndDate = new Date(dateDebut);
        newEndDate.setHours(newEndDate.getHours() + 1); // Ajouter 1 heure
        dateFinInput.value = newEndDate.toISOString().slice(0, 16);
    }
});

// Soumission du formulaire
document.getElementById('eventForm').addEventListener('submit', function(e) {
    if (!validateCurrentStep()) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
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
