{{--
==================================================
FICHIER : resources/views/users/index.blade.php
DESCRIPTION : Gestion des utilisateurs (Admin uniquement)
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@push('styles')
<style>
    body {
        background-color: #f7fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .app-layout {
        display: flex;
        min-height: 100vh;
    }

    /* ============================================
       SIDEBAR STYLES (Admin Theme)
       ============================================ */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
        color: white;
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .sidebar-logo i {
        font-size: 2.2rem;
        margin-right: 0.75rem;
        color: #fbbf24;
    }

    .sidebar-brand {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }

    .sidebar-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sidebar-user {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        margin-top: 1rem;
    }

    .user-avatar-sidebar {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        margin-right: 0.75rem;
        font-size: 1rem;
        border: 3px solid rgba(255, 255, 255, 0.2);
    }

    .sidebar-nav {
        flex: 1;
        padding: 1rem 0;
        overflow-y: auto;
    }

    .nav-section {
        margin-bottom: 1.5rem;
    }

    .nav-section-title {
        font-size: 0.7rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 1.5rem;
        margin-bottom: 0.75rem;
    }

    .nav-item {
        display: block;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        padding: 0.875rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.12);
        color: white;
        text-decoration: none;
    }

    .nav-item.active {
        background: rgba(255, 255, 255, 0.18);
        color: white;
        border-right: 4px solid #fbbf24;
    }

    .nav-item i {
        width: 1.5rem;
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    .sidebar-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        width: 100%;
        justify-content: center;
    }

    /* ============================================
       MAIN CONTENT STYLES
       ============================================ */
    .main-content {
        margin-left: 280px;
        padding: 1.5rem;
        background-color: #f7fafc;
        min-height: 100vh;
        width: calc(100% - 280px);
    }

    .page-header {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .page-subtitle {
        color: #6b7280;
        margin: 0;
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
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.4rem;
    }

    .stat-icon.admins { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }
    .stat-icon.techs { background: linear-gradient(135deg, #059669, #047857); color: white; }
    .stat-icon.active { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; }
    .stat-icon.inactive { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }

    .users-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .table-header {
        background: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .search-box {
        position: relative;
        width: 300px;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .table-content {
        overflow-x: auto;
    }

    .users-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th {
        text-align: left;
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .users-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #dc2626, #ef4444);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .role-admin { background-color: #fee2e2; color: #991b1b; }
    .role-technicien { background-color: #dcfce7; color: #166534; }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-actif { background-color: #dcfce7; color: #166534; }
    .status-inactif { background-color: #fee2e2; color: #991b1b; }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .btn-view { background-color: #dbeafe; color: #1e40af; }
    .btn-edit { background-color: #fef3c7; color: #92400e; }
    .btn-delete { background-color: #fee2e2; color: #991b1b; }

    .btn-action:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .mobile-toggle {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1.25rem;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
        }

        .mobile-toggle {
            display: block;
        }

        .search-box {
            width: 100%;
            margin-top: 1rem;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="app-layout">
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-shield-check"></i>
                <div>
                    <h1 class="sidebar-brand">Administration</h1>
                    <p class="sidebar-subtitle">ORMVAT</p>
                </div>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar-sidebar">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
                </div>
                <div>
                    <h4 style="color: white; margin: 0; font-size: 0.9rem;">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h4>
                    <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 0.75rem;">Administrateur</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Navigation</div>
                <a href="{{ route('dashboard') }}" class="nav-item">
                    <i class="bi bi-speedometer2"></i>
                    Tableau de bord
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="{{ route('users.index') }}" class="nav-item active">
                    <i class="bi bi-people"></i>
                    Gestion utilisateurs
                </a>
                <a href="{{ route('admin.logs') }}" class="nav-item">
                    <i class="bi bi-activity"></i>
                    Journaux système
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Modules</div>
                <a href="{{ route('tasks.index') }}" class="nav-item">
                    <i class="bi bi-list-check"></i>
                    Tâches
                </a>
                <a href="{{ route('events.index') }}" class="nav-item">
                    <i class="bi bi-calendar-event"></i>
                    Événements
                </a>
                <a href="{{ route('projects.index') }}" class="nav-item">
                    <i class="bi bi-folder"></i>
                    Projets
                </a>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    <i class="bi bi-file-text"></i>
                    Rapports
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">Gestion des Utilisateurs</h1>
                    <p class="page-subtitle">Administrez les comptes utilisateurs du système PlanifTech</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    Nouvel Utilisateur
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon admins">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin: 0; text-transform: uppercase;">Administrateurs</h3>
                        <div style="font-size: 2rem; font-weight: 800; color: #1f2937; margin: 0;">
                            {{ $stats['admins'] ?? 2 }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon techs">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin: 0; text-transform: uppercase;">Techniciens</h3>
                        <div style="font-size: 2rem; font-weight: 800; color: #1f2937; margin: 0;">
                            {{ $stats['techniciens'] ?? 8 }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon active">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin: 0; text-transform: uppercase;">Actifs</h3>
                        <div style="font-size: 2rem; font-weight: 800; color: #1f2937; margin: 0;">
                            {{ $stats['actifs'] ?? 9 }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon inactive">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; margin: 0; text-transform: uppercase;">Inactifs</h3>
                        <div style="font-size: 2rem; font-weight: 800; color: #1f2937; margin: 0;">
                            {{ $stats['inactifs'] ?? 1 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table">
            <div class="table-header">
                <h3 class="table-title">Liste des Utilisateurs</h3>
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Rechercher un utilisateur...">
                </div>
            </div>

            <div class="table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Dernière connexion</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 style="font-weight: 600; color: #1f2937; margin: 0; font-size: 0.875rem;">
                                            {{ $user->prenom }} {{ $user->nom }}
                                        </h4>
                                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ $user->role === 'admin' ? 'Administrateur' : 'Technicien' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $user->statut }}">
                                    {{ ucfirst($user->statut) }}
                                </span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('users.show', $user) }}" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <button onclick="confirmDelete({{ $user->id }})" class="btn-action btn-delete" style="border: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #6b7280;">
                                <i class="bi bi-people" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(isset($users) && $users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </main>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// Search functionality
document.querySelector('.search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Delete confirmation
function confirmDelete(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        const form = document.getElementById('deleteForm');
        form.action = `/users/${userId}`;
        form.submit();
    }
}

// Close sidebar on mobile when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.mobile-toggle');

    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});
</script>
@endpush
