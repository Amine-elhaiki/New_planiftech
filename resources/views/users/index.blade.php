{{-- filepath: c:\les_cours\Laravel\Projet-Amine\New_planiftech\resources\views\users\index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

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
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 20px rgba(79, 70, 229, 0.3);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .page-subtitle {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .stat-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-right: 1rem;
    }

    .stat-icon.users { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.active { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.inactive { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon.admin { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .stat-info h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--gray-900);
        margin: 0;
    }

    .content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        background: var(--gray-50);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.4);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px 0 rgba(79, 70, 229, 0.5);
        color: white;
        text-decoration: none;
    }

    .btn-outline {
        background: white;
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-outline:hover {
        background: var(--gray-50);
        border-color: var(--primary-color);
        color: var(--primary-color);
        text-decoration: none;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        color: white;
        text-decoration: none;
    }

    .btn-warning {
        background: var(--warning-color);
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
        color: white;
        text-decoration: none;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-secondary:hover {
        background: var(--gray-700);
        color: white;
        text-decoration: none;
    }

    .search-filters {
        padding: 1.5rem 2rem;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        margin: 0;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
    }

    .user-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .user-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .user-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .user-avatar {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--success-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
        margin-right: 1rem;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .user-info h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 0.25rem 0;
    }

    .user-info p {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0;
    }

    .user-details {
        margin-bottom: 1rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.8rem;
        color: var(--gray-600);
        font-weight: 500;
    }

    .detail-value {
        font-size: 0.875rem;
        color: var(--gray-900);
        font-weight: 600;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-actif { 
        background: #dcfce7; 
        color: #166534; 
    }
    
    .status-inactif { 
        background: #fee2e2; 
        color: #991b1b; 
    }

    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .role-admin { 
        background: #ede9fe; 
        color: #6b21a8; 
    }
    
    .role-chef-projet { 
        background: #fef3c7; 
        color: #92400e; 
    }
    
    .role-technicien { 
        background: #dbeafe; 
        color: #1e40af; 
    }

    .user-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        display: block;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
        display: block;
    }

    .user-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pagination-wrapper {
        padding: 2rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--gray-600);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--gray-400);
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gray-500);
        margin-bottom: 1.5rem;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        border-radius: 12px 12px 0 0;
        padding: 1.5rem 2rem;
    }

    .modal-title {
        font-weight: 700;
        color: var(--gray-900);
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        background: var(--gray-50);
        border-top: 1px solid var(--gray-200);
        border-radius: 0 0 12px 12px;
        padding: 1rem 2rem;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .filters-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .users-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }
        
        .header-actions {
            width: 100%;
            justify-content: space-between;
        }

        .card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .user-actions {
            justify-content: center;
        }

        .page-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="page-title">
                            <i class="fas fa-users me-3"></i>
                            Gestion des Utilisateurs
                        </h1>
                        <p class="page-subtitle">
                            @if(auth()->user()->role === 'admin')
                                Administration complète des comptes utilisateurs ORMVAT
                            @else
                                Consultation des membres de l'équipe ORMVAT
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Messages d'alerte -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Utilisateurs</h3>
                            <div class="stat-value">{{ $globalStats['total_users'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Utilisateurs Actifs</h3>
                            <div class="stat-value">{{ $globalStats['active_users'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->role === 'admin')
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon inactive">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Utilisateurs Inactifs</h3>
                            <div class="stat-value">{{ $globalStats['inactive_users'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-icon admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Administrateurs</h3>
                            <div class="stat-value">{{ $globalStats['admin_users'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="content-card">
                <!-- En-tête de la carte -->
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Liste des Utilisateurs ({{ $users->total() ?? $users->count() }})
                    </h2>
                    <div class="header-actions">
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Nouvel Utilisateur
                            </a>
                            <a href="{{ route('users.export', request()->query()) }}" class="btn btn-outline">
                                <i class="fas fa-file-export me-2"></i>
                                Exporter CSV
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline" onclick="toggleFilters()">
                            <i class="fas fa-filter me-2"></i>
                            Filtres
                        </button>
                    </div>
                </div>

                <!-- Filtres de recherche -->
                <div class="search-filters" id="searchFilters" style="display: none;">
                    <form method="GET" action="{{ route('users.index') }}">
                        <div class="filters-grid">
                            <div class="form-group">
                                <label for="search" class="form-label">Rechercher</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Nom, prénom, email...">
                            </div>

                            <div class="form-group">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">Tous les rôles</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>
                                        👑 Administrateur
                                    </option>
                                    <option value="chef_projet" {{ request('role') === 'chef_projet' ? 'selected' : '' }}>
                                        📊 Chef de Projet
                                    </option>
                                    <option value="technicien" {{ request('role') === 'technicien' ? 'selected' : '' }}>
                                        🔧 Technicien
                                    </option>
                                </select>
                            </div>

                            @if(auth()->user()->role === 'admin')
                            <div class="form-group">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="">Tous les statuts</option>
                                    <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>
                                        ✅ Actif
                                    </option>
                                    <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>
                                        ❌ Inactif
                                    </option>
                                </select>
                            </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Liste des utilisateurs -->
                @if($users->count() > 0)
                    <div class="users-grid">
                        @foreach($users as $user)
                            <div class="user-card">
                                <div class="user-header">
                                    <div class="user-avatar">
                                        {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
                                    </div>
                                    <div class="user-info">
                                        <h3>{{ $user->prenom }} {{ $user->nom }}</h3>
                                        <p>{{ $user->email }}</p>
                                    </div>
                                </div>

                                <div class="user-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Rôle</span>
                                        <span class="role-badge role-{{ str_replace('_', '-', $user->role) }}">
                                            {{ $user->role_libelle ?? ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>

                                    <div class="detail-row">
                                        <span class="detail-label">Statut</span>
                                        <span class="status-badge status-{{ $user->statut }}">
                                            {{ $user->statut === 'actif' ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>

                                    <div class="detail-row">
                                        <span class="detail-label">Téléphone</span>
                                        <span class="detail-value">{{ $user->telephone ?? 'Non renseigné' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <span class="detail-label">Date de creation</span>
                                        <span class="detail-value">
                                            {{ $user->date_creation ? $user->date_creation->format('d/m/Y H:i') : 'Jamais' }}
                                        </span>
                                    </div>
                                </div>

                                @if(auth()->user()->role === 'admin')
                                    <div class="user-stats">
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $user->taches->count() ?? 0 }}</span>
                                            <span class="stat-label">Tâches</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $user->rapports->count() ?? 0 }}</span>
                                            <span class="stat-label">Rapports</span>
                                        </div>
                                    </div>

                                    <div class="user-actions">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        @if($user->statut === 'actif')
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm" 
                                                        onclick="return confirm('Désactiver cet utilisateur ?')">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('users.toggle-status', $user) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Activer cet utilisateur ?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->prenom }} {{ $user->nom }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="user-actions">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline btn-sm">
                                            <i class="fas fa-eye"></i> Voir le profil
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($users, 'hasPages') && $users->hasPages())
                        <div class="pagination-wrapper">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>Aucun utilisateur trouvé</h3>
                        <p>Aucun utilisateur ne correspond à vos critères de recherche.</p>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Créer le premier utilisateur
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
                <p class="text-danger">
                    <small>
                        <i class="fas fa-warning me-1"></i>
                        Cette action est irréversible et supprimera définitivement toutes les données associées à cet utilisateur.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée pour les cartes
    const userCards = document.querySelectorAll('.user-card');
    userCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Auto-focus sur le champ de recherche quand les filtres s'ouvrent
    const searchInput = document.getElementById('search');
    if (searchInput && searchInput.value) {
        document.getElementById('searchFilters').style.display = 'block';
    }
});

function toggleFilters() {
    const filters = document.getElementById('searchFilters');
    const isVisible = filters.style.display !== 'none';
    
    filters.style.display = isVisible ? 'none' : 'block';
    
    if (!isVisible) {
        // Focus sur le champ de recherche quand on ouvre les filtres
        setTimeout(() => {
            document.getElementById('search').focus();
        }, 100);
    }
}

function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/users/${userId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-hide filters on mobile after search
if (window.innerWidth <= 768) {
    const form = document.querySelector('.search-filters form');
    if (form) {
        form.addEventListener('submit', function() {
            setTimeout(() => {
                document.getElementById('searchFilters').style.display = 'none';
            }, 100);
        });
    }
}

// Raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Ctrl+F ou Cmd+F pour ouvrir les filtres
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        toggleFilters();
        document.getElementById('search').focus();
    }
    
    // Escape pour fermer les filtres
    if (e.key === 'Escape') {
        document.getElementById('searchFilters').style.display = 'none';
    }
});

// Confirmation avant de quitter la page si des filtres sont appliqués
window.addEventListener('beforeunload', function(e) {
    const hasFilters = document.getElementById('search').value || 
                      document.getElementById('role').value || 
                      (document.getElementById('statut') && document.getElementById('statut').value);
    
    if (hasFilters && !confirm('Vous avez des filtres appliqués. Voulez-vous vraiment quitter cette page ?')) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Smooth scroll pour la pagination
document.querySelectorAll('.pagination a').forEach(link => {
    link.addEventListener('click', function(e) {
        // Scroll vers le haut de la liste des utilisateurs
        const usersGrid = document.querySelector('.users-grid');
        if (usersGrid) {
            setTimeout(() => {
                usersGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    });
});
</script>
@endpush