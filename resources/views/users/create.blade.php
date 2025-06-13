{{-- filepath: c:\les_cours\Laravel\Projet-Amine\New_planiftech\resources\views\users\create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un utilisateur')

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
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
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
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
        border-color: #667eea;
        background-color: #f0f4ff;
        transform: translateY(-2px);
    }

    .role-option.selected {
        border-color: #667eea;
        background-color: #eff6ff;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
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

    .role-icon.admin { background: linear-gradient(135deg, #667eea, #764ba2); }
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

    .alert-info {
        background-color: #eff6ff;
        border-color: #bfdbfe;
        color: #1e40af;
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
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
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
                    <i class="bi bi-person-plus text-primary me-3"></i>
                    Créer un Utilisateur
                </h1>
                <p style="color: #6b7280; margin: 0; font-size: 1.1rem;">
                    Ajoutez un nouveau membre à l'équipe PlanifTech ORMVAT
                </p>
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

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="bi bi-person-plus"></i>
                    Informations du Nouvel Utilisateur
                </h2>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <!-- Informations personnelles -->
                    <div class="form-section">
                        <h3 class="section-title">👤 Informations Personnelles</h3>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="prenom" class="form-label required">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                       id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="nom" class="form-label required">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label required">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            <div class="form-text">L'email servira d'identifiant de connexion</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                   id="telephone" name="telephone" value="{{ old('telephone') }}"
                                   placeholder="+212 6XX XXX XXX">
                            <div class="form-text">Optionnel - Format recommandé : +212 6XX XXX XXX</div>
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôle et Permissions -->
                    <div class="form-section">
                        <h3 class="section-title">🔑 Rôle et Permissions</h3>

                        <div class="mb-4">
                            <label class="form-label required">Sélectionnez le rôle</label>
                            
                            <div class="role-options">
                                <div class="role-option" onclick="selectRole('admin')" {{ old('role') === 'admin' ? 'class=selected' : '' }}>
                                    <input type="radio" name="role" value="admin" id="role_admin" {{ old('role') === 'admin' ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon admin">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <div class="role-title">Administrateur</div>
                                    </div>
                                    <p class="role-description">
                                        Accès complet au système : gestion des utilisateurs, projets, tâches, événements et rapports. 
                                        Peut créer, modifier et supprimer tous les éléments.
                                    </p>
                                </div>

                                <div class="role-option" onclick="selectRole('chef_projet')" {{ old('role') === 'chef_projet' ? 'class=selected' : '' }}>
                                    <input type="radio" name="role" value="chef_projet" id="role_chef_projet" {{ old('role') === 'chef_projet' ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon chef-projet">
                                            <i class="bi bi-diagram-2"></i>
                                        </div>
                                        <div class="role-title">Chef de Projet</div>
                                    </div>
                                    <p class="role-description">
                                        Gestion des projets assignés : planification, suivi des tâches, coordination des équipes, 
                                        création d'événements et supervision des rapports de son équipe.
                                    </p>
                                </div>

                                <div class="role-option" onclick="selectRole('technicien')" {{ old('role') === 'technicien' || old('role') === null ? 'class=selected' : '' }}>
                                    <input type="radio" name="role" value="technicien" id="role_technicien" {{ old('role') === 'technicien' || old('role') === null ? 'checked' : '' }}>
                                    <div class="role-header">
                                        <div class="role-icon technicien">
                                            <i class="bi bi-tools"></i>
                                        </div>
                                        <div class="role-title">Technicien</div>
                                    </div>
                                    <p class="role-description">
                                        Exécution des tâches assignées : réalisation des interventions, création de rapports, 
                                        participation aux événements. Accès en lecture seule aux autres utilisateurs.
                                    </p>
                                </div>
                            </div>

                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="form-section">
                        <h3 class="section-title">🔒 Mot de Passe</h3>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Sécurité :</strong> Le mot de passe doit contenir au moins 8 caractères.
                            L'utilisateur pourra le modifier lors de sa première connexion.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label required">Mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <div class="form-text">Minimum 8 caractères</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label required">Confirmer le mot de passe</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       id="password_confirmation" name="password_confirmation" required>
                                <div class="form-text">Retapez le même mot de passe</div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="form-section">
                        <h3 class="section-title">⚡ Statut du Compte</h3>

                        <div class="mb-4">
                            <label for="statut" class="form-label required">Statut initial</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>
                                    ✅ Actif - L'utilisateur peut se connecter immédiatement
                                </option>
                                <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>
                                    ❌ Inactif - L'utilisateur ne peut pas se connecter
                                </option>
                            </select>
                            <div class="form-text">
                                Vous pouvez modifier ce statut à tout moment depuis la liste des utilisateurs.
                            </div>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="btn-group">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Créer l'Utilisateur
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

// Initialiser la sélection au chargement
document.addEventListener('DOMContentLoaded', function() {
    const checkedRole = document.querySelector('input[name="role"]:checked');
    if (checkedRole) {
        const roleOption = checkedRole.closest('.role-option');
        if (roleOption) {
            roleOption.classList.add('selected');
        }
    }

    // Validation en temps réel du mot de passe
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
            passwordConfirm.classList.add('is-invalid');
        } else {
            passwordConfirm.setCustomValidity('');
            passwordConfirm.classList.remove('is-invalid');
        }
    }

    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);

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
</script>
@endpush