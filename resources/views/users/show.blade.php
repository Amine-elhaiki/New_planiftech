{{-- filepath: c:\les_cours\Laravel\Projet-Amine\New_planiftech\resources\views\users\show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil utilisateur - ' . $user->prenom . ' ' . $user->nom)

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #3730a3;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    body {
        background-color: var(--gray-50);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        width: calc(100% - 280px);
        transition: all 0.3s ease;
        padding: 2rem;
    }

    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 20px;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(79, 70, 229, 0.3);
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: rotate(45deg);
    }

    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .profile-info {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-right: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 4px solid rgba(255, 255, 255, 0.2);
    }

    .profile-details h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .profile-details .user-email {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .profile-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.15);
        padding: 0.75rem 1.25rem;
        border-radius: 25px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .meta-item i {
        font-size: 1.1rem;
    }

    .back-button {
        position: absolute;
        top: 2rem;
        right: 2rem;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        z-index: 3;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px -5px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .card-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .card-icon.personal { background: linear-gradient(135deg, #667eea, #764ba2); }
    .card-icon.role { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .card-icon.activity { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .card-icon.stats { background: linear-gradient(135deg, #43e97b, #38f9d7); }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--gray-600);
        font-size: 0.9rem;
    }

    .info-value {
        font-weight: 700;
        color: var(--gray-900);
        font-size: 1rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-actif { 
        background: linear-gradient(135deg, #dcfce7, #bbf7d0); 
        color: #166534; 
        border: 2px solid #22c55e;
    }
    
    .status-inactif { 
        background: linear-gradient(135deg, #fee2e2, #fecaca); 
        color: #991b1b; 
        border: 2px solid #ef4444;
    }

    .role-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .role-admin { 
        background: linear-gradient(135deg, #ede9fe, #ddd6fe); 
        color: #6b21a8; 
        border: 2px solid #8b5cf6;
    }
    
    .role-chef-projet { 
        background: linear-gradient(135deg, #fef3c7, #fde68a); 
        color: #92400e; 
        border: 2px solid #f59e0b;
    }
    
    .role-technicien { 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe); 
        color: #1e40af; 
        border: 2px solid #3b82f6;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, var(--gray-50), white);
        border-radius: 12px;
        border: 2px solid var(--gray-100);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-color);
        display: block;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--gray-600);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .activity-timeline {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        grid-column: 1 / -1;
        position: relative;
        overflow: hidden;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--warning-color), var(--success-color));
    }

    .timeline-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .timeline-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #ff9a9e, #fecfef);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-left: 3px solid var(--gray-200);
        margin-left: 1rem;
        padding-left: 2rem;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--primary-color);
    }

    .timeline-item:last-child {
        border-left-color: transparent;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .timeline-date {
        font-size: 0.8rem;
        color: var(--gray-500);
    }

    .admin-actions {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        grid-column: 1 / -1;
        margin-top: 2rem;
    }

    .actions-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .actions-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        gap: 0.75rem;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning-color), #d97706);
        color: white;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
        color: white;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(75, 85, 99, 0.4);
        color: white;
        text-decoration: none;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-info {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        border-color: #3b82f6;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border-color: #f59e0b;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
        }

        .profile-header {
            padding: 2rem 1.5rem;
        }

        .profile-info {
            flex-direction: column;
            text-align: center;
        }

        .profile-avatar {
            margin-right: 0;
            margin-bottom: 1.5rem;
        }

        .profile-details h1 {
            font-size: 2rem;
        }

        .profile-meta {
            justify-content: center;
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .back-button {
            position: static;
            margin-top: 1rem;
            align-self: center;
        }

        .actions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="profile-container">
        <!-- En-tête du profil -->
        <div class="profile-header">
            <a href="{{ route('users.index') }}" class="back-button">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            
            <div class="profile-info">
                <div class="profile-avatar">
                    {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
                </div>
                
                <div class="profile-details">
                    <h1>{{ $user->prenom }} {{ $user->nom }}</h1>
                    <div class="user-email">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $user->email }}
                    </div>
                    
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-user-tag"></i>
                            <span class="role-badge role-{{ str_replace('_', '-', $user->role) }}">
                                @if($user->role === 'admin')
                                    <i class="fas fa-crown"></i> Administrateur
                                @elseif($user->role === 'chef_projet')
                                    <i class="fas fa-project-diagram"></i> Chef de Projet
                                @else
                                    <i class="fas fa-tools"></i> Technicien
                                @endif
                            </span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-circle"></i>
                            <span class="status-badge status-{{ $user->statut }}">
                                {{ $user->statut === 'actif' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Membre depuis {{ $user->date_creation->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($user->statut === 'inactif')
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Compte inactif :</strong> Cet utilisateur ne peut pas se connecter au système.
            </div>
        @endif

        <!-- Grille de contenu -->
        <div class="content-grid">
            <!-- Informations personnelles -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon personal">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="card-title">Informations Personnelles</h3>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Nom complet</span>
                    <span class="info-value">{{ $user->prenom }} {{ $user->nom }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Adresse e-mail</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $user->telephone ?? 'Non renseigné' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Date d'inscription</span>
                    <span class="info-value">{{ $user->date_creation->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            <!-- Rôle et permissions -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon role">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="card-title">Rôle et Permissions</h3>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Rôle actuel</span>
                    <span class="role-badge role-{{ str_replace('_', '-', $user->role) }}">
                        @if($user->role === 'admin')
                            <i class="fas fa-crown"></i> Administrateur
                        @elseif($user->role === 'chef_projet')
                            <i class="fas fa-project-diagram"></i> Chef de Projet
                        @else
                            <i class="fas fa-tools"></i> Technicien
                        @endif
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Statut du compte</span>
                    <span class="status-badge status-{{ $user->statut }}">
                        {{ $user->statut === 'actif' ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Dernière mise à jour</span>
                    <span class="info-value">{{ $user->date_modification->format('d/m/Y à H:i') }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Permissions</span>
                    <span class="info-value">
                        @if($user->role === 'admin')
                            Accès complet au système
                        @elseif($user->role === 'chef_projet')
                            Gestion de projets et équipes
                        @else
                            Exécution de tâches
                        @endif
                    </span>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon activity">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="card-title">Activité Récente</h3>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Dernière connexion</span>
                    <span class="info-value">
                        {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y à H:i') : 'Jamais connecté' }}
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Connexions ce mois</span>
                    <span class="info-value">{{ $userStats['connexions_mois'] ?? 0 }} fois</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Adresse IP</span>
                    <span class="info-value">{{ $user->derniere_ip ?? 'Non disponible' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Navigateur</span>
                    <span class="info-value">{{ $user->user_agent ?? 'Non disponible' }}</span>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon stats">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="card-title">Statistiques</h3>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">{{ $userStats['taches_total'] ?? 0 }}</span>
                        <span class="stat-label">Tâches Total</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number">{{ $userStats['taches_terminees'] ?? 0 }}</span>
                        <span class="stat-label">Tâches Terminées</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number">{{ $userStats['rapports_total'] ?? 0 }}</span>
                        <span class="stat-label">Rapports</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number">{{ $userStats['projets_total'] ?? 0 }}</span>
                        <span class="stat-label">Projets</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline d'activité -->
        <div class="activity-timeline">
            <div class="timeline-header">
                <div class="timeline-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">Historique d'Activité</h3>
            </div>
            
            @if(isset($recentActivities) && $recentActivities->count() > 0)
                @foreach($recentActivities as $activity)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $activity->description ?? 'Activité' }}</div>
                            <div class="timeline-date">{{ $activity->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-title">Création du compte</div>
                        <div class="timeline-date">{{ $user->date_creation->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
                
                @if($user->derniere_connexion)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-title">Dernière connexion</div>
                            <div class="timeline-date">{{ $user->derniere_connexion->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Actions administrateur -->
        @if(auth()->user()->role === 'admin' && $user->id !== auth()->id())
            <div class="admin-actions">
                <div class="actions-header">
                    <div class="actions-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="card-title">Actions Administrateur</h3>
                </div>
                
                <div class="actions-grid">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Modifier l'utilisateur
                    </a>
                    
                    @if($user->statut === 'actif')
                        <form method="POST" action="{{ route('users.deactivate', $user) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning" 
                                    onclick="return confirm('Désactiver cet utilisateur ?')">
                                <i class="fas fa-user-slash"></i>
                                Désactiver le compte
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('users.toggle-status', $user) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary" 
                                    onclick="return confirm('Réactiver cet utilisateur ?')">
                                <i class="fas fa-user-check"></i>
                                Réactiver le compte
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-secondary" 
                            onclick="resetPassword({{ $user->id }}, '{{ $user->prenom }} {{ $user->nom }}')">
                        <i class="fas fa-key"></i>
                        Réinitialiser mot de passe
                    </button>
                    
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmDelete({{ $user->id }}, '{{ $user->prenom }} {{ $user->nom }}')">
                        <i class="fas fa-trash"></i>
                        Supprimer le compte
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et supprimera toutes les données associées.
                </div>
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

<!-- Modal de réinitialisation mot de passe -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>
                    Réinitialiser le mot de passe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Réinitialiser le mot de passe de <strong id="resetUserName"></strong> ?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Un nouveau mot de passe temporaire sera généré et envoyé par email.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="resetPasswordForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">Réinitialiser</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/users/${userId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function resetPassword(userId, userName) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetPasswordForm').action = `/users/${userId}/reset-password`;
    
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

// Animation d'entrée
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.info-card, .activity-timeline, .admin-actions');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
});
</script>
@endpush