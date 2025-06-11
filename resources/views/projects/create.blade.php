@extends('layouts.app')

@section('title', 'Créer un Projet')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #dee2e6;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }

    .required-field::after {
        content: '*';
        color: #dc3545;
        margin-left: 2px;
    }

    .form-help {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .progress-indicator {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .step {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-weight: bold;
    }

    .zone-suggestion {
        cursor: pointer;
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s;
    }

    .zone-suggestion:hover {
        background-color: #f8f9fa;
    }

    .date-info {
        background-color: #e7f1ff;
        border: 1px solid #b6d7ff;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Créer un Nouveau Projet
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projets</a></li>
                    <li class="breadcrumb-item active">Créer</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Retour
        </a>
    </div>

    <div class="row">
        <!-- Formulaire principal -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('projects.store') }}" id="projectForm">
                @csrf

                <!-- Section Informations générales -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informations Générales
                    </h4>

                    <div class="mb-3">
                        <label for="nom" class="form-label required-field">Nom du projet</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror"
                               id="nom" name="nom" value="{{ old('nom') }}" required
                               placeholder="Ex: Modernisation réseau irrigation Tadla-Nord">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-help">Donnez un nom descriptif et unique à votre projet</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label required-field">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4" required
                                  placeholder="Décrivez les objectifs, enjeux et portée du projet...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-help">Détaillez les objectifs et la portée du projet</div>
                    </div>

                    <div class="mb-3">
                        <label for="zone_geographique" class="form-label required-field">Zone géographique</label>
                        <input type="text" class="form-control @error('zone_geographique') is-invalid @enderror"
                               id="zone_geographique" name="zone_geographique" value="{{ old('zone_geographique') }}" required
                               placeholder="Ex: Tadla-Nord, Fkih Ben Salah...">

                        <!-- Suggestions de zones -->
                        <div class="dropdown-menu w-100" id="zoneSuggestions" style="display: none;">
                            <div class="zone-suggestion" data-zone="Tadla-Nord">Tadla-Nord</div>
                            <div class="zone-suggestion" data-zone="Tadla-Sud">Tadla-Sud</div>
                            <div class="zone-suggestion" data-zone="Fkih Ben Salah">Fkih Ben Salah</div>
                            <div class="zone-suggestion" data-zone="Beni Mellal">Beni Mellal</div>
                            <div class="zone-suggestion" data-zone="Kasba Tadla">Kasba Tadla</div>
                            <div class="zone-suggestion" data-zone="Ensemble région ORMVAT">Ensemble région ORMVAT</div>
                        </div>

                        @error('zone_geographique')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-help">Spécifiez la zone d'intervention du projet</div>
                    </div>
                </div>

                <!-- Section Planification -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="bi bi-calendar-range text-primary me-2"></i>
                        Planification
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label required-field">Date de début</label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                                       id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="form-label required-field">Date de fin prévue</label>
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror"
                                       id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="date-info" id="dateInfo" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            <div>
                                <strong>Durée du projet :</strong> <span id="dureeProjet"></span>
                                <br>
                                <small class="text-muted">Assurez-vous que cette durée est réaliste pour atteindre vos objectifs</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Responsabilité -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="bi bi-person-badge text-primary me-2"></i>
                        Responsabilité
                    </h4>

                    <div class="mb-3">
                        <label for="id_responsable" class="form-label required-field">Responsable du projet</label>
                        <select class="form-select @error('id_responsable') is-invalid @enderror"
                                id="id_responsable" name="id_responsable" required>
                            <option value="">Sélectionner un responsable</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                        {{ old('id_responsable') == $user->id ? 'selected' : '' }}
                                        data-role="{{ $user->role }}">
                                    {{ $user->prenom }} {{ $user->nom }} - {{ ucfirst($user->role) }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_responsable')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-help">Le responsable coordonnera l'ensemble du projet</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Conseil :</strong> Choisissez un responsable ayant l'expérience et les compétences
                        nécessaires pour mener à bien ce type de projet.
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        <i class="bi bi-arrow-left me-1"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Créer le Projet
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar avec aide -->
        <div class="col-lg-4">
            <!-- Guide de création -->
            <div class="progress-indicator">
                <h6 class="mb-3">
                    <i class="bi bi-compass me-2"></i>
                    Guide de Création
                </h6>

                <div class="step">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Informations générales</strong>
                        <br><small>Définissez le nom et la description</small>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div>
                        <strong>Planification</strong>
                        <br><small>Fixez les dates de début et fin</small>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div>
                        <strong>Responsabilité</strong>
                        <br><small>Assignez un responsable</small>
                    </div>
                </div>
            </div>

            <!-- Conseils -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        Conseils
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Utilisez un nom de projet descriptif
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Définissez des objectifs clairs
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Prévoyez une marge dans les délais
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Choisissez un responsable expérimenté
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Exemples de projets -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-bookmark text-info me-2"></i>
                        Exemples de Projets ORMVAT
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Maintenance préventive</strong>
                        <p class="small text-muted mb-2">
                            Programme annuel de maintenance des équipements hydrauliques
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>Modernisation infrastructure</strong>
                        <p class="small text-muted mb-2">
                            Upgrade des systèmes d'irrigation existants
                        </p>
                    </div>
                    <div class="mb-0">
                        <strong>Extension réseau</strong>
                        <p class="small text-muted mb-0">
                            Développement de nouvelles zones irrigables
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des suggestions de zones
    const zoneInput = document.getElementById('zone_geographique');
    const zoneSuggestions = document.getElementById('zoneSuggestions');

    zoneInput.addEventListener('focus', function() {
        zoneSuggestions.style.display = 'block';
    });

    zoneInput.addEventListener('blur', function() {
        setTimeout(() => {
            zoneSuggestions.style.display = 'none';
        }, 200);
    });

    document.querySelectorAll('.zone-suggestion').forEach(suggestion => {
        suggestion.addEventListener('click', function() {
            zoneInput.value = this.dataset.zone;
            zoneSuggestions.style.display = 'none';
        });
    });

    // Calcul de la durée du projet
    function updateDuree() {
        const dateDebut = document.getElementById('date_debut').value;
        const dateFin = document.getElementById('date_fin').value;
        const dateInfo = document.getElementById('dateInfo');
        const dureeElement = document.getElementById('dureeProjet');

        if (dateDebut && dateFin) {
            const debut = new Date(dateDebut);
            const fin = new Date(dateFin);
            const diffTime = fin - debut;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 0) {
                const mois = Math.floor(diffDays / 30);
                const jours = diffDays % 30;

                let dureeText = '';
                if (mois > 0) {
                    dureeText += mois + ' mois';
                    if (jours > 0) dureeText += ' et ';
                }
                if (jours > 0 || mois === 0) {
                    dureeText += jours + ' jours';
                }

                dureeElement.textContent = dureeText;
                dateInfo.style.display = 'block';
            } else {
                dateInfo.style.display = 'none';
            }
        } else {
            dateInfo.style.display = 'none';
        }
    }

    // Validation des dates
    document.getElementById('date_debut').addEventListener('change', function() {
        const dateDebut = this.value;
        const dateFin = document.getElementById('date_fin');

        // Mettre à jour la date minimum pour la date de fin
        dateFin.min = dateDebut;

        // Si la date de fin est antérieure, la réinitialiser
        if (dateFin.value && dateFin.value <= dateDebut) {
            dateFin.value = '';
        }

        updateDuree();
    });

    document.getElementById('date_fin').addEventListener('change', updateDuree);

    // Validation du formulaire
    document.getElementById('projectForm').addEventListener('submit', function(e) {
        const dateDebut = new Date(document.getElementById('date_debut').value);
        const dateFin = new Date(document.getElementById('date_fin').value);

        if (dateFin <= dateDebut) {
            e.preventDefault();
            alert('La date de fin doit être postérieure à la date de début.');
            return false;
        }

        // Confirmation
        if (!confirm('Êtes-vous sûr de vouloir créer ce projet ?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
