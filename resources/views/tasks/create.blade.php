{{--
==================================================
FICHIER : resources/views/tasks/create.blade.php
DESCRIPTION : Formulaire de création d'une nouvelle tâche
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Créer une tâche')

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

    .card-conseil {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-left: 4px solid #3b82f6;
    }

    .conseil-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        border-radius: 0.375rem;
        border-left: 3px solid #10b981;
    }

    .conseil-title {
        font-weight: 600;
        color: #059669;
        font-size: 0.875rem;
    }

    .conseil-text {
        font-size: 0.8rem;
        color: #6b7280;
        margin: 0;
    }
</style>
@endpush

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
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Informations de la tâche
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tasks.store') }}" id="taskForm">
                        @csrf

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
                                       value="{{ old('titre') }}"
                                       placeholder="Ex: Inspection du canal principal secteur B4"
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
                                    <option value="basse" {{ old('priorite') === 'basse' ? 'selected' : '' }}>
                                        🟢 Basse
                                    </option>
                                    <option value="moyenne" {{ old('priorite') === 'moyenne' ? 'selected' : '' }}>
                                        🟡 Moyenne
                                    </option>
                                    <option value="haute" {{ old('priorite') === 'haute' ? 'selected' : '' }}>
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
                                Description détaillée <span class="required-field">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Décrivez en détail les actions à effectuer, le matériel nécessaire, les procédures à suivre..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attribution et Échéance -->
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                                                    {{ old('id_utilisateur') == $user->id ? 'selected' : '' }}>
                                                👤 {{ $user->prenom }} {{ $user->nom }}
                                                ({{ ucfirst($user->role) }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Aucun utilisateur disponible</option>
                                    @endif
                                </select>
                                @error('id_utilisateur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_echeance" class="form-label">
                                    Date d'échéance <span class="required-field">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('date_echeance') is-invalid @enderror"
                                       id="date_echeance"
                                       name="date_echeance"
                                       value="{{ old('date_echeance', now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Associations optionnelles -->
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
                                                    {{ old('id_projet') == $project->id ? 'selected' : '' }}>
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
                                                    {{ old('id_evenement') == $event->id ? 'selected' : '' }}>
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

        <!-- Sidebar avec conseils -->
        <div class="col-lg-4">
            <div class="card card-conseil">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Conseils pour créer une tâche efficace
                    </h6>
                </div>
                <div class="card-body">
                    <div class="conseil-item">
                        <div class="conseil-title">📝 Titre clair</div>
                        <p class="conseil-text">
                            Utilisez un titre précis qui décrit l'action à effectuer et la localisation.
                        </p>
                    </div>

                    <div class="conseil-item">
                        <div class="conseil-title">📋 Description détaillée</div>
                        <p class="conseil-text">
                            Incluez : localisation exacte, équipements nécessaires, procédures, résultats attendus.
                        </p>
                    </div>

                    <div class="conseil-item">
                        <div class="conseil-title">⚡ Priorité appropriée</div>
                        <p class="conseil-text">
                            <span class="badge bg-danger">Haute</span> Urgences et pannes<br>
                            <span class="badge bg-warning">Moyenne</span> Tâches planifiées<br>
                            <span class="badge bg-success">Basse</span> Maintenance préventive
                        </p>
                    </div>

                    <div class="conseil-item">
                        <div class="conseil-title">👥 Attribution réfléchie</div>
                        <p class="conseil-text">
                            Assignez la tâche au technicien ayant les compétences et la disponibilité appropriées.
                        </p>
                    </div>

                    <div class="conseil-item">
                        <div class="conseil-title">📅 Échéance réaliste</div>
                        <p class="conseil-text">
                            Tenez compte de la complexité, des conditions terrain et de la charge de travail.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if(isset($stats))
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Aperçu des tâches
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <div class="text-warning">
                                <i class="bi bi-clock fs-4"></i>
                            </div>
                            <strong>{{ $stats['a_faire'] ?? 0 }}</strong>
                            <br><small class="text-muted">À faire</small>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-info">
                                <i class="bi bi-arrow-repeat fs-4"></i>
                            </div>
                            <strong>{{ $stats['en_cours'] ?? 0 }}</strong>
                            <br><small class="text-muted">En cours</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des dates minimum (aujourd'hui)
    const dateInput = document.getElementById('date_echeance');
    if (dateInput) {
        const now = new Date();
        const minDate = now.toISOString().slice(0, 16);
        dateInput.setAttribute('min', minDate);
    }

    // Validation du formulaire
    const form = document.getElementById('taskForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();
            const utilisateur = document.getElementById('id_utilisateur').value;
            const priorite = document.getElementById('priorite').value;
            const dateEcheance = document.getElementById('date_echeance').value;

            let errors = [];

            if (!titre) errors.push('Le titre est obligatoire');
            if (!description) errors.push('La description est obligatoire');
            if (!utilisateur) errors.push('Vous devez assigner la tâche à un technicien');
            if (!priorite) errors.push('La priorité est obligatoire');
            if (!dateEcheance) errors.push('La date d\'échéance est obligatoire');

            if (errors.length > 0) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs suivantes :\n- ' + errors.join('\n- '));
                return false;
            }

            // Vérification de la date d'échéance
            const selectedDate = new Date(dateEcheance);
            const now = new Date();
            if (selectedDate <= now) {
                e.preventDefault();
                alert('La date d\'échéance doit être dans le futur');
                return false;
            }

            return true;
        });
    }

    // Auto-suggestions pour le titre selon la priorité
    const prioriteSelect = document.getElementById('priorite');
    const titreInput = document.getElementById('titre');

    if (prioriteSelect && titreInput) {
        prioriteSelect.addEventListener('change', function() {
            if (titreInput.value.trim() === '') {
                const suggestions = {
                    'haute': [
                        'Réparation urgente',
                        'Panne système',
                        'Intervention d\'urgence'
                    ],
                    'moyenne': [
                        'Inspection programmée',
                        'Maintenance planifiée',
                        'Contrôle qualité'
                    ],
                    'basse': [
                        'Maintenance préventive',
                        'Nettoyage équipement',
                        'Relevé de données'
                    ]
                };

                const priority = this.value;
                if (suggestions[priority]) {
                    titreInput.setAttribute('placeholder',
                        'Ex: ' + suggestions[priority][Math.floor(Math.random() * suggestions[priority].length)]
                    );
                }
            }
        });
    }
});
</script>
@endpush
