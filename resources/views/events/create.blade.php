@extends('layouts.app')

@section('title', 'Créer un Événement')

@push('styles')
<style>
    .create-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .event-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .event-type-card {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .event-type-card:hover {
        border-color: #667eea;
        background: #f8faff;
    }

    .event-type-card.selected {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    .event-type-card i {
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
        border-color: #667eea;
    }

    .priority-option.selected {
        border-color: #667eea;
        background: #667eea;
        color: white;
    }

    .priority-normale.selected {
        background: #6b7280;
        border-color: #6b7280;
    }

    .priority-haute.selected {
        background: #f59e0b;
        border-color: #f59e0b;
    }

    .priority-urgente.selected {
        background: #ef4444;
        border-color: #ef4444;
    }

    .participants-section {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        background: #f9fafb;
    }

    .participant-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 1px solid #e5e7eb;
    }

    .participant-item:last-child {
        margin-bottom: 0;
    }

    .participant-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .participant-info {
        flex-grow: 1;
    }

    .participant-name {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .participant-role {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-outline-secondary {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .datetime-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .datetime-row {
            grid-template-columns: 1fr;
        }

        .event-type-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .priority-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="create-container">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
                        <li class="breadcrumb-item active">Créer</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>
        </div>

        <!-- Formulaire -->
        <div class="form-card">
            <div class="form-header">
                <h1 class="h3 mb-2">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Créer un Nouvel Événement
                </h1>
                <p class="mb-0">Planifiez vos interventions, réunions et formations ORMVAT</p>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('events.store') }}" id="eventForm">
                    @csrf

                    <!-- Informations générales -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="titre" class="form-label required">Titre de l'événement</label>
                                <input type="text" class="form-control @error('titre') is-invalid @enderror"
                                       id="titre" name="titre" value="{{ old('titre') }}"
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

                    <!-- Type d'événement -->
                    <div class="form-group">
                        <label class="form-label required">Type d'événement</label>
                        <div class="event-type-grid">
                            @foreach(App\Models\Event::$types as $key => $label)
                                <div class="event-type-card {{ old('type') === $key ? 'selected' : '' }}"
                                     onclick="selectEventType('{{ $key }}')">
                                    <i class="bi bi-{{ $key === 'intervention' ? 'tools' : ($key === 'reunion' ? 'people' : ($key === 'formation' ? 'book' : 'geo-alt')) }}"></i>
                                    <div class="fw-bold">{{ $label }}</div>
                                    <input type="radio" name="type" value="{{ $key }}"
                                           {{ old('type') === $key ? 'checked' : '' }} style="display: none;">
                                </div>
                            @endforeach
                        </div>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div class="form-group">
                        <label class="form-label required">Priorité</label>
                        <div class="priority-grid">
                            @foreach(App\Models\Event::$priorites as $key => $label)
                                <div class="priority-option priority-{{ $key }} {{ old('priorite', 'normale') === $key ? 'selected' : '' }}"
                                     onclick="selectPriority('{{ $key }}')">
                                    <div class="fw-bold">{{ $label }}</div>
                                    <input type="radio" name="priorite" value="{{ $key }}"
                                           {{ old('priorite', 'normale') === $key ? 'checked' : '' }} style="display: none;">
                                </div>
                            @endforeach
                        </div>
                        @error('priorite')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label required">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4" required
                                  placeholder="Décrivez en détail l'objet de cet événement...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates et heures -->
                    <div class="datetime-row">
                        <div class="form-group">
                            <label for="date_debut" class="form-label required">Date et heure de début</label>
                            <input type="datetime-local"
                                   class="form-control @error('date_debut') is-invalid @enderror"
                                   id="date_debut"
                                   name="date_debut"
                                   value="{{ old('date_debut', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_fin" class="form-label required">Date et heure de fin</label>
                            <input type="datetime-local"
                                   class="form-control @error('date_fin') is-invalid @enderror"
                                   id="date_fin"
                                   name="date_fin"
                                   value="{{ old('date_fin', now()->addHour()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Lieu -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="lieu" class="form-label required">Lieu</label>
                                <input type="text" class="form-control @error('lieu') is-invalid @enderror"
                                       id="lieu" name="lieu" value="{{ old('lieu') }}"
                                       placeholder="Ex: Station de pompage A, Bureau principal..." required>
                                @error('lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="coordonnees_gps" class="form-label">Coordonnées GPS</label>
                                <input type="text" class="form-control @error('coordonnees_gps') is-invalid @enderror"
                                       id="coordonnees_gps" name="coordonnees_gps" value="{{ old('coordonnees_gps') }}"
                                       placeholder="Ex: 32.4816, -6.7929">
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
                            <div class="mb-3">
                                <input type="text" class="form-control" id="participantSearch"
                                       placeholder="Rechercher un utilisateur...">
                            </div>

                            <div style="max-height: 300px; overflow-y: auto;">
                                <!-- Organisateur (automatique) -->
                                <div class="participant-item mb-3" style="background: #e7f1ff; border-color: #667eea;">
                                    <div class="participant-avatar">
                                        {{ substr(Auth::user()->prenom, 0, 1) }}{{ substr(Auth::user()->nom, 0, 1) }}
                                    </div>
                                    <div class="participant-info">
                                        <div class="participant-name">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                                        <div class="participant-role">{{ Auth::user()->email }} - Organisateur</div>
                                    </div>
                                    <span class="badge bg-primary">Organisateur</span>
                                </div>

                                <!-- Autres utilisateurs -->
                                @foreach($users as $user)
                                    @if($user->id !== Auth::id())
                                        <div class="participant-item user-item" data-user-name="{{ strtolower($user->prenom . ' ' . $user->nom . ' ' . $user->email) }}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="participants[]"
                                                       value="{{ $user->id }}" id="user{{ $user->id }}">
                                            </div>
                                            <div class="participant-avatar ms-2">
                                                {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
                                            </div>
                                            <div class="participant-info">
                                                <div class="participant-name">{{ $user->prenom }} {{ $user->nom }}</div>
                                                <div class="participant-role">{{ $user->email }} - {{ ucfirst($user->role) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllTechnicians()">
                                    <i class="bi bi-check-all me-1"></i>
                                    Sélectionner tous les techniciens
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="clearSelection()">
                                    <i class="bi bi-x-lg me-1"></i>
                                    Désélectionner tout
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Créer l'Événement
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

// Recherche de participants
document.getElementById('participantSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');

    userItems.forEach(item => {
        const userName = item.dataset.userName;
        if (userName.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Sélectionner tous les techniciens
function selectAllTechnicians() {
    const checkboxes = document.querySelectorAll('.user-item input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        const userItem = checkbox.closest('.user-item');
        const roleText = userItem.querySelector('.participant-role').textContent.toLowerCase();
        if (roleText.includes('technicien')) {
            checkbox.checked = true;
        }
    });
}

// Désélectionner tout
function clearSelection() {
    const checkboxes = document.querySelectorAll('input[name="participants[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Validation des dates
document.getElementById('date_debut').addEventListener('change', function() {
    const dateDebut = this.value;
    const dateFinInput = document.getElementById('date_fin');

    // Mettre à jour la date de fin minimale
    dateFinInput.min = dateDebut;

    // Si la date de fin est antérieure, l'ajuster automatiquement
    if (dateFinInput.value && new Date(dateFinInput.value) <= new Date(dateDebut)) {
        const newEndDate = new Date(dateDebut);
        newEndDate.setHours(newEndDate.getHours() + 1);
        dateFinInput.value = newEndDate.toISOString().slice(0, 16);
    }
});

// Validation du formulaire
document.getElementById('eventForm').addEventListener('submit', function(e) {
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;

    if (dateDebut && dateFin && new Date(dateFin) <= new Date(dateDebut)) {
        e.preventDefault();
        alert('La date de fin doit être postérieure à la date de début.');
        return false;
    }

    const type = document.querySelector('input[name="type"]:checked');
    if (!type) {
        e.preventDefault();
        alert('Veuillez sélectionner un type d\'événement.');
        return false;
    }

    const priorite = document.querySelector('input[name="priorite"]:checked');
    if (!priorite) {
        e.preventDefault();
        alert('Veuillez sélectionner une priorité.');
        return false;
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner automatiquement le type si déjà choisi
    const selectedType = document.querySelector('input[name="type"]:checked');
    if (selectedType) {
        selectedType.closest('.event-type-card').classList.add('selected');
    }

    // Sélectionner automatiquement la priorité si déjà choisie
    const selectedPriority = document.querySelector('input[name="priorite"]:checked');
    if (selectedPriority) {
        selectedPriority.closest('.priority-option').classList.add('selected');
    }
});
</script>
@endpush
