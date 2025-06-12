{{--
==================================================
FICHIER : resources/views/users/create.blade.php
DESCRIPTION : Création d'un nouvel utilisateur
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

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
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .form-body {
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .required::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background-color: white;
        transition: all 0.2s ease;
    }

    .form-text {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .role-option {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .role-option:hover {
        border-color: #2563eb;
        background-color: #f8fafc;
    }

    .role-option.selected {
        border-color: #2563eb;
        background-color: #eff6ff;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .role-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid;
    }

    .alert-info {
        background-color: #eff6ff;
        border-color: #bfdbfe;
        color: #1e40af;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
        text-decoration: none;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
                        Créer un Utilisateur
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        Ajoutez un nouveau membre à l'équipe PlanifTech ORMVAT
                    </p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="bi bi-person-plus text-primary me-2"></i>
                    Informations du Nouvel Utilisateur
                </h2>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <!-- Informations personnelles -->
                    <div class="form-section">
                        <h3 class="section-title">Informations Personnelles</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label required">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                       id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label required">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label required">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            <div class="form-text">L'email servira d'identifiant de connexion</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                   id="telephone" name="telephone" value="{{ old('telephone') }}"
                                   placeholder="+212 6XX XXX XXX">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôle et Permissions -->
                    <div class="form-section">
                        <h3 class="section-title">Rôle et Permissions</h3>

                        <div class="mb-3">
                            <label class="form-label required">Sélectionner le rôle</label>

                            <div class="role-option" onclick="selectRole('admin')">
                                <input type="radio" name="role" value="admin" id="role_admin"
                                       {{ old('role') === 'admin' ? 'checked' : '' }}>
                                <div class="role-title">🛡️ Administrateur</div>
                                <p class="role-description">
                                    Accès complet au système : gestion des utilisateurs, supervision de tous les projets,
                                    administration des paramètres système.
                                </p>
                            </div>

                            <div class="role-option" onclick="selectRole('technicien')">
                                <input type="radio" name="role" value="technicien" id="role_technicien"
                                       {{ old('role') === 'technicien' || old('role') === null ? 'checked' : '' }}>
                                <div class="role-title">🔧 Technicien</div>
                                <p class="role-description">
                                    Accès aux tâches assignées, création de rapports d'intervention,
                                    consultation du planning et des projets.
                                </p>
                            </div>

                            @error('role')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="form-section">
                        <h3 class="section-title">Mot de Passe</h3>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Mot de passe temporaire :</strong> Un mot de passe temporaire sera automatiquement
                            généré et envoyé à l'utilisateur par email. Il devra le changer lors de sa première connexion.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label required">Mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <div class="form-text">Minimum 8 caractères</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label required">Confirmer le mot de passe</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="form-section">
                        <h3 class="section-title">Statut du Compte</h3>

                        <div class="mb-3">
                            <label for="statut" class="form-label required">Statut initial</label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                <option value="actif" {{ old('statut') === 'actif' || old('statut') === null ? 'selected' : '' }}>
                                    ✅ Actif - L'utilisateur peut se connecter immédiatement
                                </option>
                                <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>
                                    ❌ Inactif - L'utilisateur ne peut pas se connecter
                                </option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="btn-group">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }

    password.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('input', validatePasswords);
});
</script>
@endpush

{{--
==================================================
FICHIER : resources/views/users/edit.blade.php
DESCRIPTION : Édition d'un utilisateur existant
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@push('styles')
<style>
    /* Réutiliser les mêmes styles que create.blade.php */
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
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .form-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .info-card {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .danger-zone {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .danger-zone h4 {
        color: #dc2626;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    /* Reprendre les autres styles de create.blade.php */
    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }

    .required::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-group {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
                        Modifier l'Utilisateur
                    </h1>
                    <p style="color: #6b7280; margin: 0;">
                        {{ $user->prenom }} {{ $user->nom }} - {{ $user->email }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>
                        Voir
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="bi bi-pencil-square text-warning me-2"></i>
                    Modification des Informations
                </h2>
            </div>

            <div class="form-body">
                <!-- Informations sur le compte -->
                <div class="info-card">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-blue-600 me-2"></i>
                        <div>
                            <strong>Compte créé le :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}<br>
                            <strong>Dernière modification :</strong> {{ $user->updated_at->format('d/m/Y à H:i') }}<br>
                            @if($user->derniere_connexion)
                                <strong>Dernière connexion :</strong> {{ $user->derniere_connexion->format('d/m/Y à H:i') }}
                            @else
                                <strong>Dernière connexion :</strong> Jamais connecté
                            @endif
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Informations personnelles -->
                    <div class="form-section">
                        <h3 class="section-title">Informations Personnelles</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label required">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                       id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label required">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label required">Adresse email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                   id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                                   placeholder="+212 6XX XXX XXX">
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôle et Statut -->
                    <div class="form-section">
                        <h3 class="section-title">Rôle et Statut</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label required">Rôle</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        🛡️ Administrateur
                                    </option>
                                    <option value="technicien" {{ old('role', $user->role) === 'technicien' ? 'selected' : '' }}>
                                        🔧 Technicien
                                    </option>
                                </select>
                                @if($user->id === auth()->id())
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Vous ne pouvez pas modifier votre propre rôle
                                    </div>
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                @endif
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label required">Statut</label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="actif" {{ old('statut', $user->statut) === 'actif' ? 'selected' : '' }}>
                                        ✅ Actif
                                    </option>
                                    <option value="inactif" {{ old('statut', $user->statut) === 'inactif' ? 'selected' : '' }}>
                                        ❌ Inactif
                                    </option>
                                </select>
                                @if($user->id === auth()->id())
                                    <div class="form-text text-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Vous ne pouvez pas modifier votre propre statut
                                    </div>
                                    <input type="hidden" name="statut" value="{{ $user->statut }}">
                                @endif
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="btn-group">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>
                            Mettre à Jour
                        </button>
                    </div>
                </form>

                <!-- Zone de danger -->
                @if($user->id !== auth()->id())
                <div class="danger-zone">
                    <h4>
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Zone de Danger
                    </h4>
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        Ces actions sont irréversibles. Procédez avec précaution.
                    </p>

                    <div class="d-flex gap-2">
                        <button onclick="resetPassword()" class="btn btn-outline-warning">
                            <i class="bi bi-key me-2"></i>
                            Réinitialiser le mot de passe
                        </button>

                        <button onclick="confirmDelete()" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>
                            Supprimer l'utilisateur
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulaires cachés -->
<form id="resetPasswordForm" method="POST" action="{{ route('users.resetPassword', $user) }}" style="display: none;">
    @csrf
</form>

<form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function resetPassword() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?\n\nUn nouveau mot de passe temporaire sera envoyé par email.')) {
        document.getElementById('resetPasswordForm').submit();
    }
}

function confirmDelete() {
    const userName = '{{ $user->prenom }} {{ $user->nom }}';
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?\n\nCette action est irréversible et supprimera :\n- Le compte utilisateur\n- Toutes ses tâches assignées\n- Tous ses rapports\n- Son historique d'activité`)) {
        if (confirm('Dernière confirmation : Cette action est définitive !')) {
            document.getElementById('deleteForm').submit();
        }
    }
}
</script>
@endpush
