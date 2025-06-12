@extends('layouts.app')

@section('title', 'Créer un Événement')

@push('styles')
<style>
    .create-container {
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
        border-color: #667eea;
        background: #f8faff;
    }

    .event-type-card.selected {
        border-color: #667eea;
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
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
        border-color: #667eea;
        background: #667eea;
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

    .participants-section {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        background: #f9fafb;
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
        background: linear-gradient(135deg, #667eea, #764ba2);
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

    .participant-role {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        background: #e5e7eb;
        color: #374151;
    }

    .btn-action {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #6b7280;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #4b5563;
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

    .selection-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-sm:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .error-alert {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .success-alert {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="create-container">

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

        <!-- Formulaire principal -->
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

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description" class="form-label required">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4" required
                                  placeholder="Décrivez l'objectif et les détails de cet événement...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type d'événement -->
                    <div class="form-group">
                        <label class="form-label required">Type d'événement</label>
                        <div class="event-type-grid">
                            @foreach(App\Models\Event::$types as $key => $label)
                                <div class="event-type-card {{ old('type') === $key ? 'selected' : '' }}"
                                     onclick="selectEventType('{{ $key }}')">
                                    <input type="radio" name="type" value="{{ $key }}"
                                           {{ old('type') === $key ? 'checked' : '' }}>
                                    <span class="event-type-icon">
                                        @switch($key)
                                            @case('intervention')
                                                🔧
                                                @break
                                            @case('reunion')
                                                👥
                                                @break
                                            @case('formation')
                                                📚
                                                @break
                                            @case('visite')
                                                🏗️
                                                @break
                                        @endswitch
                                    </span>
                                    <div class="fw-bold">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div class="form-group">
                        <label class="form-label required">Priorité</label>
                        <div class="priority-grid">
                            @foreach(App\Models\Event::$priorites as $key => $label)
                                <div class="priority-option priority-{{ $key }} {{ old('priorite') === $key ? 'selected' : '' }}"
                                     onclick="selectPriority('{{ $key }}')">
                                    <input type="radio" name="priorite" value="{{ $key }}"
                                           {{ old('priorite') === $key ? 'checked' : '' }}>
                                    <div class="fw-bold">{{ $label }}</div>
                                    <div class="small">
                                        @switch($key)
                                            @case('normale')
                                                Standard
                                                @break
                                            @case('haute')
                                                Important
                                                @break
                                            @case('urgente')
                                                Critique
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('priorite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dates et heures -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_debut" class="form-label required">Date et heure de début</label>
                                <input type="datetime-local"
                                       class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut"
                                       value="{{ old('date_debut', now()->addHour()->format('Y-m-d\TH:i')) }}"
                                       min="{{ now()->format('Y-m-d\TH:i') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_fin" class="form-label required">Date et heure de fin</label>
                                <input type="datetime-local"
                                       class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin"
                                       value="{{ old('date_fin', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
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
                            <div class="participants-header">
                                <h6 class="mb-0">Sélectionner les participants</h6>
                                <div class="selection-actions">
                                    <button type="button" class="btn-sm" onclick="selectAllTechnicians()">
                                        Tous les techniciens
                                    </button>
                                    <button type="button" class="btn-sm" onclick="clearSelection()">
                                        Désélectionner tout
                                    </button>
                                </div>
                            </div>

                            <div class="participants-search">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" id="participantSearch"
                                       placeholder="Rechercher un participant...">
                            </div>

                            <div class="participants-list" id="participantsList">
                                @foreach($users as $user)
                                    <div class="user-item" data-name="{{ strtolower($user->nom . ' ' . $user->prenom) }}"
                                         data-role="{{ strtolower($user->role) }}">
                                        <input type="checkbox" name="participants[]" value="{{ $user->id }}"
                                               {{ in_array($user->id, old('participants', [])) ? 'checked' : '' }}>

                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                        </div>

                                        <div class="user-info">
                                            <div class="user-name">{{ $user->prenom }} {{ $user->nom }}</div>
                                            <div class="user-role">{{ $user->email }}</div>
                                        </div>

                                        <span class="participant-role">{{ ucfirst($user->role) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('participants.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Boutons d'action -->
                    <div class="action-buttons">
                        <a href="{{ route('events.index') }}" class="btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn-action">
                            <i class="bi bi-calendar-plus me-2"></i>
                            Créer l'événement
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
    // Désélectionner tous les cards
    document.querySelectorAll('.event-type-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Sélectionner le card cliqué
    event.target.closest('.event-type-card').classList.add('selected');

    // Cocher le radio button
    document.querySelector(`input[name="type"][value="${type}"]`).checked = true;
}

// Sélection de la priorité
function selectPriority(priority) {
    // Désélectionner toutes les options
    document.querySelectorAll('.priority-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Sélectionner l'option cliquée
    event.target.closest('.priority-option').classList.add('selected');

    // Cocher le radio button
    document.querySelector(`input[name="priorite"][value="${priority}"]`).checked = true;
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

    if (dateDebut) {
        // Mettre à jour la date de fin minimale
        dateFinInput.min = dateDebut;

        // Si la date de fin est antérieure, l'ajuster automatiquement
        if (dateFinInput.value && new Date(dateFinInput.value) <= new Date(dateDebut)) {
            const newEndDate = new Date(dateDebut);
            newEndDate.setHours(newEndDate.getHours() + 1);
            dateFinInput.value = newEndDate.toISOString().slice(0, 16);
        }
    }
});

// Validation du formulaire
document.getElementById('eventForm').addEventListener('submit', function(e) {
    let errors = [];

    // Vérifier les champs obligatoires
    const titre = document.getElementById('titre').value.trim();
    const description = document.getElementById('description').value.trim();
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;
    const lieu = document.getElementById('lieu').value.trim();
    const type = document.querySelector('input[name="type"]:checked');
    const priorite = document.querySelector('input[name="priorite"]:checked');

    if (!titre) errors.push('Le titre est obligatoire');
    if (!description) errors.push('La description est obligatoire');
    if (!dateDebut) errors.push('La date de début est obligatoire');
    if (!dateFin) errors.push('La date de fin est obligatoire');
    if (!lieu) errors.push('Le lieu est obligatoire');
    if (!type) errors.push('Le type d\'événement est obligatoire');
    if (!priorite) errors.push('La priorité est obligatoire');

    // Vérifier les dates
    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        const maintenant = new Date();

        if (debut <= maintenant) {
            errors.push('La date de début doit être dans le futur');
        }

        if (fin <= debut) {
            errors.push('La date de fin doit être postérieure à la date de début');
        }

        // Vérifier que l'événement ne dure pas plus de 7 jours
        const diffDays = (fin - debut) / (1000 * 60 * 60 * 24);
        if (diffDays > 7) {
            errors.push('Un événement ne peut pas durer plus de 7 jours');
        }
    }

    // Afficher les erreurs si il y en a
    if (errors.length > 0) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs suivantes :\n\n• ' + errors.join('\n• '));
        return false;
    }

    // Confirmer la création
    const participants = document.querySelectorAll('input[name="participants[]"]:checked');
    const message = `Voulez-vous créer cet événement ?\n\n` +
                   `Titre: ${titre}\n` +
                   `Date: ${new Date(dateDebut).toLocaleDateString('fr-FR')} à ${new Date(dateDebut).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'})}\n` +
                   `Participants: ${participants.length} personne(s)`;

    if (!confirm(message)) {
        e.preventDefault();
        return false;
    }

    // Désactiver le bouton pour éviter les doubles soumissions
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Création en cours...';
    }

    return true;
});

// Initialisation au chargement de la page
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

    // Configurer les dates minimum
    const now = new Date();
    const minDateTime = now.toISOString().slice(0, 16);
    document.getElementById('date_debut').setAttribute('min', minDateTime);
});
</script>
@endpush
