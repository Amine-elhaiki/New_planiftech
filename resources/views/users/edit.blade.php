{{-- filepath: c:\les_cours\Laravel\Projet-Amine\New_planiftech\resources\views\users\edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un utilisateur')

@push('styles')
<style>
    body {
        background-color: #f7fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .main-content {
        margin-left: 280px;
        padding: 1.5rem;
        background-color: #f7fafc;
        min-height: 100vh;
        width: calc(100% - 280px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-container {
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        text-align: center;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        padding: 2rem;
        color: white;
        text-align: center;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .form-body {
        padding: 3rem;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
        text-align: center;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.9rem;
    }

    .required::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-control:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        background: white;
    }

    .form-select {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        background: white;
    }

    .form-text {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.5rem;
        text-align: center;
    }

    .role-options {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    .role-option {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        background: #f9fafb;
    }

    .role-option:hover {
        border-color: #f59e0b;
        background-color: #fef3c7;
        transform: translateY(-2px);
    }

    .role-option.selected {
        border-color: #f59e0b;
        background-color: #fef3c7;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .role-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .role-icon.admin { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .role-icon.chef-projet { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .role-icon.technicien { background: linear-gradient(135deg, #10b981, #059669); }

    .role-title {
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        font-size: 1.1rem;
    }

    .role-description {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 0;
        line-height: 1.5;
    }

    .alert {
        padding: 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: 1px solid;
        text-align: center;
    }

    .alert-warning {
        background-color: #fef3c7;
        border-color: #fde68a;
        color: #92400e;
    }

    .alert-danger {
        background-color: #fee2e2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .btn {
        padding: 1rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 140px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
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
        transform: translateY(-2px);
        text-decoration: none;
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        text-decoration: none;
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        text-decoration: none;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .invalid-feedback {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        text-align: center;
    }

    .is-invalid {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    .user-info-card {
        background: linear-gradient(135deg, #fef3c7, #fed7aa);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #f59e0b;
    }

    .user-info-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .user-avatar {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        margin-right: 1rem;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .user-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #92400e;
        margin: 0 0 0.25rem 0;
    }

    .user-info p {
        font-size: 1rem;
        color: #a16207;
        margin: 0;
    }

    .danger-zone {
        background: #fef2f2;
        border: 2px solid #fecaca;
        border-radius: 12px;
        padding: 2rem;
        margin-top: 2rem;
        text-align: center;
    }

    .danger-zone h4 {
        color: #dc2626;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .danger-zone p {
        color: #991b1b;
        margin-bottom: 1.5rem;
    }

    .modal-lg {
        max-width: 800px;
    }

    .action-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        background: white;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-card.danger {
        border-color: #fecaca;
        background: #fef2f2;
    }

    .action-card.warning {
        border-color: #fed7aa;
        background: #fef3c7;
    }

    .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .card-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .card-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .card-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
            align-items: flex-start;
        }

        .form-body {
            padding: 2rem;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .user-info-header {
            flex-direction: column;
            text-align: center;
        }

        .user-avatar {
            margin-right: 0;
            margin-bottom: 1rem;
        }

        .modal-lg {
            max-width: 95%;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin: 0 0 0.5rem 0;">
                    <i class="bi bi-person-gear text-warning me-3"></i>
                    Modifier l'Utilisateur
                </h1>
                <p style="color: #6b7280; margin: 0; font-size: 1.1rem;">
                    Modifiez les informations du membre de l'équipe PlanifTech ORMVAT
                </p>
            </div>
        </div>

        <!-- Informations utilisateur actuel -->
        <div class="user-info-card">
            <div class="user-info-header">
                <div class="user-avatar">
                    {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
                </div>
                <div class="user-info">
                    <h3>{{ $user->prenom }} {{ $user->nom }}</h3>
                    <p>{{ $user->email }} • {{ $user->role_libelle ?? ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                </div>
            </div>
        </div>

        <!-- Messages d'erreur -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Erreurs de validation :</strong>
                <ul class="mb-0 mt-2" style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Avertissement pour modification de son propre compte -->
        @if($user->id === auth()->id())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Attention :</strong> Vous modifiez votre propre compte. Vous ne pouvez pas changer votre rôle ou votre statut.
            </div>
        @endif

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="bi bi-person-gear"></i>
                    Modification des Informations
                </h2>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Informations personnelles -->
                    <div class="form-section">
                        <h3 class="section-title">👤 Informations Personnelles</h3>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="prenom" class="form-label required">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                       id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="nom" class="form-label required">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label required">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            <div class="form-text">L'email sert d'identifiant de connexion</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                   id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                                   placeholder="+212 6XX XXX XXX">
                            <div class="form-text">Format recommandé : +212 6XX XXX XXX</div>
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôle et Permissions (seulement si pas son propre compte) -->
                    @if($user->id !== auth()->id())
                    <div class="form-section">
                        <h3 class="section-title">🔑 Rôle et Permissions</h3>

                        <div class="mb-4">
                            <label class="form-label required">Rôle de l'utilisateur</label>
                            
                            <div class="role-options">
                                <div class="role-option {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}" 
                                     onclick="selectRole('admin')">
                                    <input type="radio" name="role" value="admin" id="role_admin" 
                                           {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon admin">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <div class="role-title">Administrateur</div>
                                    </div>
                                    <p class="role-description">
                                        Accès complet au système : gestion des utilisateurs, projets, tâches, événements et rapports.
                                    </p>
                                </div>

                                <div class="role-option {{ old('role', $user->role) === 'chef_projet' ? 'selected' : '' }}" 
                                     onclick="selectRole('chef_projet')">
                                    <input type="radio" name="role" value="chef_projet" id="role_chef_projet" 
                                           {{ old('role', $user->role) === 'chef_projet' ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon chef-projet">
                                            <i class="bi bi-diagram-2"></i>
                                        </div>
                                        <div class="role-title">Chef de Projet</div>
                                    </div>
                                    <p class="role-description">
                                        Gestion des projets assignés : planification, suivi des tâches, coordination des équipes.
                                    </p>
                                </div>

                                <div class="role-option {{ old('role', $user->role) === 'technicien' ? 'selected' : '' }}" 
                                     onclick="selectRole('technicien')">
                                    <input type="radio" name="role" value="technicien" id="role_technicien" 
                                           {{ old('role', $user->role) === 'technicien' ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon technicien">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                        <div class="role-title">Technicien</div>
                                    </div>
                                    <p class="role-description">
                                        Exécution des tâches assignées : réalisation des interventions, création de rapports.
                                    </p>
                                </div>
                            </div>

                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Statut du compte -->
                    <div class="form-section">
                        <h3 class="section-title">⚡ Statut du Compte</h3>

                        <div class="mb-4">
                            <label for="statut" class="form-label required">Statut du compte</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="actif" {{ old('statut', $user->statut) === 'actif' ? 'selected' : '' }}>
                                    ✅ Actif - L'utilisateur peut se connecter et utiliser le système
                                </option>
                                <option value="inactif" {{ old('statut', $user->statut) === 'inactif' ? 'selected' : '' }}>
                                    ❌ Inactif - L'utilisateur ne peut pas se connecter
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif

                    <!-- Boutons -->
                    <div class="btn-group">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Mettre à Jour
                        </button>
                    </div>
                </form>

                <!-- Zone dangereuse pour suppression/désactivation -->
                @if($user->id !== auth()->id())
                <div class="danger-zone">
                    <h4>
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Actions Avancées
                    </h4>
                    <p>
                        Actions irréversibles ou critiques pour cet utilisateur. Procédez avec prudence.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="action-card warning">
                                <div class="card-header">
                                    <div class="card-icon warning">
                                        <i class="bi bi-person-slash"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Désactiver le Compte</h6>
                                        <small class="text-muted">Recommandé - Préserve les données</small>
                                    </div>
                                </div>
                                <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    Désactive l'utilisateur sans supprimer ses données. Il ne pourra plus se connecter.
                                </p>
                                <button type="button" class="btn btn-warning btn-sm" 
                                        onclick="confirmDeactivation({{ $user->id }}, '{{ $user->prenom }} {{ $user->nom }}')">
                                    <i class="bi bi-person-slash me-2"></i>
                                    Désactiver
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="action-card danger">
                                <div class="card-header">
                                    <div class="card-icon danger">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Supprimer le Compte</h6>
                                        <small class="text-muted">Irréversible - Supprime toutes les données</small>
                                    </div>
                                </div>
                                <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    Supprime définitivement l'utilisateur et toutes ses données associées.
                                </p>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="confirmDeletion({{ $user->id }}, '{{ $user->prenom }} {{ $user->nom }}')">
                                    <i class="bi bi-trash me-2"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de désactivation -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="deactivateModalLabel">
                    <i class="bi bi-person-slash me-2"></i>
                    Confirmer la Désactivation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-person-slash" style="font-size: 3rem; color: #f59e0b;"></i>
                </div>
                <h6 class="text-center mb-3">Désactiver <strong id="userNameToDeactivate"></strong> ?</h6>
                <p class="text-center">
                    L'utilisateur ne pourra plus se connecter au système, mais ses données seront préservées.
                </p>
                <div class="alert alert-warning">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Cette action peut être annulée</strong> en réactivant le compte plus tard.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>
                    Annuler
                </button>
                <form id="deactivateForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-person-slash me-2"></i>
                        Confirmer la Désactivation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirmer la Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-person-x" style="font-size: 3rem; color: #dc2626;"></i>
                </div>
                <h6 class="text-center mb-3">Suppression de <strong id="userNameToDelete"></strong></h6>
                
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Attention !</strong> Cette action est définitive et irréversible.
                </div>

                <p class="text-center">
                    Toutes les données associées à cet utilisateur seront définitivement perdues :
                </p>
                <ul class="text-start">
                    <li>Tâches assignées</li>
                    <li>Rapports créés</li>
                    <li>Événements organisés</li>
                    <li>Historique d'activité</li>
                    <li>Notifications et messages</li>
                </ul>

                <hr>
                <p class="text-center mb-2">
                    <small class="text-muted">
                        Pour confirmer, tapez le nom complet de l'utilisateur ci-dessous :
                    </small>
                </p>
                <input type="text" class="form-control" id="deleteConfirmationInput" 
                       placeholder="Tapez le nom complet pour confirmer">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>
                    Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="bi bi-trash me-2"></i>
                        Supprimer Définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectRole(role) {
    // Désélectionner toutes les options
    document.querySelectorAll('.role-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Sélectionner l'option cliquée
    event.currentTarget.classList.add('selected');

    // Cocher le radio button correspondant
    document.getElementById('role_' + role).checked = true;
}

function confirmDeactivation(userId, userName) {
    document.getElementById('userNameToDeactivate').textContent = userName;
    document.getElementById('deactivateForm').action = `/users/${userId}/deactivate`;
    
    const modal = new bootstrap.Modal(document.getElementById('deactivateModal'));
    modal.show();
}

function confirmDeletion(userId, userName) {
    document.getElementById('userNameToDelete').textContent = userName;
    document.getElementById('deleteForm').action = `/users/${userId}`;
    document.getElementById('deleteConfirmationInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Initialiser la sélection au chargement
document.addEventListener('DOMContentLoaded', function() {
    const checkedRole = document.querySelector('input[name="role"]:checked');
    if (checkedRole) {
        const roleOption = checkedRole.closest('.role-option');
        if (roleOption) {
            roleOption.classList.add('selected');
        }
    }

    // Validation de confirmation de suppression
    const deleteInput = document.getElementById('deleteConfirmationInput');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const expectedName = '{{ $user->prenom }} {{ $user->nom }}';

    if (deleteInput && confirmBtn) {
        deleteInput.addEventListener('input', function() {
            const inputValue = this.value.trim();
            const isMatch = inputValue === expectedName;
            
            confirmBtn.disabled = !isMatch;
            
            if (isMatch) {
                confirmBtn.classList.remove('btn-secondary');
                confirmBtn.classList.add('btn-danger');
            } else {
                confirmBtn.classList.remove('btn-danger');
                confirmBtn.classList.add('btn-secondary');
            }
        });
    }

    // Animation des sections au chargement
    const formSections = document.querySelectorAll('.form-section');
    formSections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            section.style.transition = 'all 0.6s ease';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 200);
    });
});

// Prévenir la soumission accidentelle du formulaire de suppression
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    const input = document.getElementById('deleteConfirmationInput').value.trim();
    const expected = '{{ $user->prenom }} {{ $user->nom }}';
    
    if (input !== expected) {
        e.preventDefault();
        alert('Le nom saisi ne correspond pas. Suppression annulée.');
        return false;
    }
    
    // Double confirmation
    if (!confirm('DERNIÈRE CONFIRMATION : Êtes-vous absolument certain de vouloir supprimer cet utilisateur ?')) {
        e.preventDefault();
        return false;
    }
});

// Confirmation pour la désactivation
document.getElementById('deactivateForm').addEventListener('submit', function(e) {
    if (!confirm('Confirmer la désactivation de cet utilisateur ?')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush