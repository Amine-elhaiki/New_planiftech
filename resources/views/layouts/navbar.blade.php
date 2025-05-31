<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Logo et titre -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="bi bi-water me-2"></i>
            <span>PlanifTech ORMVAT</span>
        </a>

        <!-- Toggle pour mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu principal -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door me-1"></i>Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tasks.index') }}">
                        <i class="bi bi-list-check me-1"></i>Tâches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('events.index') }}">
                        <i class="bi bi-calendar-event me-1"></i>Événements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('projects.index') }}">
                        <i class="bi bi-folder me-1"></i>Projets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-text me-1"></i>Rapports
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear me-1"></i>Administration
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">Gestion utilisateurs</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.logs') }}">Journaux</a></li>
                    </ul>
                </li>
                @endif
            </ul>

            <!-- Menu utilisateur -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->prenom }} {{ auth()->user()->nom }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
