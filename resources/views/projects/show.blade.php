@extends('layouts.app')

@section('title', $project->nom)

@push('styles')
<style>
    .project-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }

    .info-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .info-card:hover {
        transform: translateY(-2px);
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        border-left: 4px solid #007bff;
    }

    .info-item i {
        font-size: 1.2rem;
        margin-right: 0.75rem;
        color: #007bff;
        width: 24px;
    }

    .progress-ring {
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .progress-ring circle {
        transition: stroke-dasharray 0.3s ease;
    }

    .task-item, .event-item {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .task-item:hover, .event-item:hover {
        background-color: #f8f9fa;
        border-color: #007bff;
        transform: translateX(5px);
    }

    .task-completed {
        background-color: #d4edda;
        border-color: #28a745;
    }

    .task-in-progress {
        background-color: #fff3cd;
        border-color: #ffc107;
    }

    .task-overdue {
        background-color: #f8d7da;
        border-color: #dc3545;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -15px;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #007bff;
    }

    .metric-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 10px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .metric-number {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
    }

    .priority-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .priority-haute { background-color: #ffc107; }
    .priority-moyenne { background-color: #007bff; }
    .priority-basse { background-color: #6c757d; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête du projet -->
    <div class="project-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center mb-3">
                    <h1 class="mb-0 me-3">{{ $project->nom }}</h1>
                    <span class="status-badge
                        @if($project->statut === 'termine') bg-success
                        @elseif($project->statut === 'en_cours') bg-primary
                        @elseif($project->statut === 'suspendu') bg-warning
                        @else bg-secondary @endif">
                        {{ $project->statut_nom }}
                    </span>
                </div>
                <p class="mb-3 opacity-90">{{ $project->description }}</p>
                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <i class="bi bi-person me-1"></i>
                        <strong>Responsable:</strong> {{ $project->responsable->prenom }} {{ $project->responsable->nom }}
                    </div>
                    <div>
                        <i class="bi bi-geo-alt me-1"></i>
                        <strong>Zone:</strong> {{ $project->zone_geographique }}
                    </div>
                    <div>
                        <i class="bi bi-calendar-range me-1"></i>
                        <strong>Durée:</strong> {{ $project->date_debut->format('d/m/Y') }} - {{ $project->date_fin->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            <div class="text-end">
                @if(Auth::user()->role === 'admin' || $project->id_responsable === Auth::id())
                    <div class="btn-group mb-3">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-light">
                            <i class="bi bi-pencil me-1"></i>
                            Modifier
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('projects.report', $project) }}">
                                    <i class="bi bi-file-text me-2"></i>Générer rapport
                                </a></li>
                                @if($project->statut !== 'termine')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateStatus('termine')">
                                        <i class="bi bi-check-circle me-2"></i>Marquer terminé
                                    </a></li>
                                    <li><a class="dropdown-item text-warning" href="#" onclick="updateStatus('suspendu')">
                                        <i class="bi bi-pause-circle me-2"></i>Suspendre
                                    </a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
                <a href="{{ route('projects.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Progression générale -->
            <div class="info-card card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Progression du Projet
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <div class="progress-ring">
                                <svg width="120" height="120">
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                    <circle cx="60" cy="60" r="54" fill="none" stroke="#007bff" stroke-width="8"
                                            stroke-linecap="round" stroke-dasharray="339.29"
                                            stroke-dashoffset="{{ 339.29 - (339.29 * $project->pourcentage_avancement / 100) }}"
                                            transform="rotate(-90 60 60)"/>
                                    <text x="60" y="60" text-anchor="middle" dy="0.3em" font-size="24" font-weight="bold" fill="#007bff">
                                        {{ $project->pourcentage_avancement }}%
                                    </text>
                                </svg>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-number text-primary">{{ $stats['total_taches'] }}</div>
                                        <div>Total Tâches</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-number text-success">{{ $stats['taches_terminees'] }}</div>
                                        <div>Terminées</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-number text-warning">{{ $stats['taches_en_cours'] }}</div>
                                        <div>En Cours</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-card">
                                        <div class="metric-number text-danger">{{ $stats['taches_en_retard'] }}</div>
                                        <div>En Retard</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($project->jours_restants > 0 && $project->statut !== 'termine')
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-clock me-2"></i>
                            <strong>{{ $project->jours_restants }} jours restants</strong>
                            pour terminer le projet avant l'échéance du {{ $project->date_fin->format('d/m/Y') }}.
                        </div>
                    @elseif($project->est_en_retard)
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Projet en retard !</strong>
                            L'échéance était fixée au {{ $project->date_fin->format('d/m/Y') }}.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tâches du projet -->
            <div class="info-card card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-task text-primary me-2"></i>
                        Tâches du Projet ({{ $project->taches->count() }})
                    </h5>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('tasks.create', ['projet' => $project->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus me-1"></i>
                            Ajouter tâche
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($project->taches->count() > 0)
                        @foreach($project->taches as $tache)
                            <div class="task-item
                                @if($tache->statut === 'termine') task-completed
                                @elseif($tache->statut === 'en_cours') task-in-progress
                                @elseif($tache->date_echeance < now() && $tache->statut !== 'termine') task-overdue
                                @endif">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="priority-indicator priority-{{ $tache->priorite }}"></span>
                                            {{ $tache->titre }}
                                        </h6>
                                        <p class="text-muted mb-2 small">{{ Str::limit($tache->description, 100) }}</p>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge
                                                @if($tache->statut === 'termine') bg-success
                                                @elseif($tache->statut === 'en_cours') bg-warning
                                                @else bg-secondary @endif">
                                                {{ ucfirst(str_replace('_', ' ', $tache->statut)) }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $tache->date_echeance->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $tache->utilisateur->prenom }} {{ $tache->utilisateur->nom }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <div class="progress mb-1" style="width: 80px; height: 6px;">
                                            <div class="progress-bar" style="width: {{ $tache->progression }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $tache->progression }}%</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-list-task text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-2">Aucune tâche créée</h6>
                            <p class="text-muted small">Les tâches apparaîtront ici une fois créées</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Événements du projet -->
            <div class="info-card card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event text-primary me-2"></i>
                        Événements Associés ({{ $project->evenements->count() }})
                    </h5>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('events.create', ['projet' => $project->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus me-1"></i>
                            Ajouter événement
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($project->evenements->count() > 0)
                        @foreach($project->evenements as $evenement)
                            <div class="event-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $evenement->titre }}</h6>
                                        <p class="text-muted mb-2 small">{{ Str::limit($evenement->description, 100) }}</p>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge
                                                @if($evenement->statut === 'termine') bg-success
                                                @elseif($evenement->statut === 'en_cours') bg-primary
                                                @elseif($evenement->statut === 'annule') bg-danger
                                                @else bg-secondary @endif">
                                                {{ $evenement->statut_nom }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $evenement->date_debut->format('d/m/Y H:i') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $evenement->lieu }}
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ route('events.show', $evenement) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-2">Aucun événement associé</h6>
                            <p class="text-muted small">Les événements liés au projet apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <!-- Détails du projet -->
            <div class="info-card card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Détails du Projet
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <i class="bi bi-calendar-date"></i>
                        <div>
                            <strong>Date de début</strong><br>
                            <span class="text-muted">{{ $project->date_debut->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <strong>Date de fin prévue</strong><br>
                            <span class="text-muted">{{ $project->date_fin->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-clock"></i>
                        <div>
                            <strong>Durée totale</strong><br>
                            <span class="text-muted">{{ $project->duree }} jours</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-person-badge"></i>
                        <div>
                            <strong>Responsable</strong><br>
                            <span class="text-muted">{{ $project->responsable->prenom }} {{ $project->responsable->nom }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <strong>Zone géographique</strong><br>
                            <span class="text-muted">{{ $project->zone_geographique }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline du projet -->
            <div class="info-card card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Historique
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Projet créé</h6>
                                    <p class="text-muted mb-0 small">
                                        Par {{ $project->responsable->prenom }} {{ $project->responsable->nom }}
                                    </p>
                                </div>
                                <small class="text-muted">{{ $project->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>

                        @if($project->updated_at != $project->created_at)
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Dernière modification</h6>
                                    <p class="text-muted mb-0 small">Informations mises à jour</p>
                                </div>
                                <small class="text-muted">{{ $project->updated_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($project->statut === 'en_cours')
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">Projet démarré</h6>
                                    <p class="text-muted mb-0 small">Statut changé en "En cours"</p>
                                </div>
                                <small class="text-muted">{{ $project->date_debut->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            @if(Auth::user()->role === 'admin' || $project->id_responsable === Auth::id())
            <div class="info-card card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.report', $project) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-text me-1"></i>
                            Générer rapport complet
                        </a>
                        @if($project->statut !== 'termine')
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="updateStatus('termine')">
                                <i class="bi bi-check-circle me-1"></i>
                                Marquer comme terminé
                            </button>
                        @endif
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil me-1"></i>
                                Modifier le projet
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Formulaire caché pour changer le statut -->
<form id="statusForm" method="POST" action="{{ route('projects.updateStatus', $project) }}" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" id="statutInput" name="statut">
</form>
@endsection

@push('scripts')
<script>
// Changer le statut du projet
function updateStatus(statut) {
    let message = '';
    switch(statut) {
        case 'termine':
            message = 'Êtes-vous sûr de vouloir marquer ce projet comme terminé ?';
            break;
        case 'suspendu':
            message = 'Êtes-vous sûr de vouloir suspendre ce projet ?';
            break;
        default:
            message = 'Êtes-vous sûr de vouloir changer le statut de ce projet ?';
    }

    if (confirm(message)) {
        document.getElementById('statutInput').value = statut;
        document.getElementById('statusForm').submit();
    }
}
</script>
@endpush
