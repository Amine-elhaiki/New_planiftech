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

    .time-input {
        max-width: 120px;
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
                                        <label for="type" class="form-label required-field">Type d'événement</label>
                                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">Sélectionner un type</option>
                                            @foreach(App\Models\Event::$types as $key => $label)
                                                <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
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

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priorite" class="form-label required-field">Priorité</label>
                                        <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
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

                                <div class="col-md-6">
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
                                </div>
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

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_debut" class="form-label required-field">Date de début</label>
                                        <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                                               id="date_debut" name="date_debut" value="{{ old('date_debut', request('date', date('Y-m-d'))) }}"
                                               min="{{ date('Y-m-d') }}" required>
                                        @error('date_debut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="heure_debut" class="form-label required-field">Heure de début</label>
                                        <input type="time" class="form-control time-input @error('date_debut') is-invalid @enderror"
                                               id="heure_debut" name="heure_debut" value="{{ old('heure_debut', '08:00') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="heure_fin" class="form-label required-field">Heure de fin</label>
                                        <input type="time" class="form-control time-input @error('date_fin') is-invalid @enderror"
                                               id="heure_fin" name="heure_fin" value="{{ old('heure_fin', '17:00') }}" required>
                                    </div>
                                </div>
                            </div>

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

                        <!-- Champs cachés pour les dates complètes -->
                        <input type="hidden" id="date_debut_complete" name="date_debut">
                        <input type="hidden" id="date_fin_complete" name="date_fin">
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
    const heureDebut = document.getElementById('heure_debut').value;
    const heureFin = document.getElementById('heure_fin').value;

    if (dateDebut && heureDebut && heureFin) {
        const debut = new Date(`${dateDebut}T${heureDebut}`);
        const fin = new Date(`${dateDebut}T${heureFin}`);

        if (fin <= debut) {
            alert('L\'heure de fin doit être postérieure à l\'heure de début.');
            return false;
        }

        // Mettre à jour les champs cachés
        updateDateTimeFields();

        // Vérifier les conflits (simulation)
        checkConflicts();
    }

    return true;
}

// Mettre à jour les champs de date-heure complets
function updateDateTimeFields() {
    const dateDebut = document.getElementById('date_debut').value;
    const heureDebut = document.getElementById('heure_debut').value;
    const heureFin = document.getElementById('heure_fin').value;

    if (dateDebut && heureDebut && heureFin) {
        document.getElementById('date_debut_complete').value = `${dateDebut} ${heureDebut}:00`;
        document.getElementById('date_fin_complete').value = `${dateDebut} ${heureFin}:00`;
    }
}

// Vérification des conflits (simulation)
function checkConflicts() {
    // Simulation de vérification de conflits
    // Dans un vrai projet, ceci ferait un appel AJAX au serveur
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

// Soumission du formulaire
document.getElementById('eventForm').addEventListener('submit', function(e) {
    updateDateTimeFields();

    if (!validateCurrentStep()) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel des dates
    document.getElementById('date_debut').addEventListener('change', updateDateTimeFields);
    document.getElementById('heure_debut').addEventListener('change', updateDateTimeFields);
    document.getElementById('heure_fin').addEventListener('change', updateDateTimeFields);
});
</script>
@endpush
