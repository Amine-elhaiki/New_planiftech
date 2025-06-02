@extends('layouts.app')

@section('title', $event->titre)

@push('styles')
<style>
    .event-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }

    .priority-indicator {
        width: 4px;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
        border-radius: 4px 0 0 4px;
    }

    .priority-haute { background-color: #ffc107; }
    .priority-urgente { background-color: #dc3545; }
    .priority-normale { background-color: #6c757d; }

    .participant-card {
        transition: transform 0.2s ease;
        position: relative;
        border-left: 4px solid #dee2e6;
    }

    .participant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .participant-card.organizer {
        border-left-color: #007bff;
        background-color: #f8f9fa;
    }

    .participant-card.confirmed {
        border-left-color: #28a745;
    }

    .participant-card.declined {
        border-left-color: #dc3545;
    }

    .participant-card.pending {
        border-left-color: #ffc107;
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

    .task-item {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .task-item:hover {
        background-color: #f8f9fa;
        border-color: #007bff;
    }

    .task-completed {
        background-color: #d4edda;
        border-color: #28a745;
    }

    .task-in-progress {
        background-color: #fff3cd;
        border-color: #ffc107;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 12px;
        height: 12px;
        background-color: #007bff;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #007bff;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 1.2rem;
        width: 2px;
        height: calc(100% - 0.7rem);
        background-color: #dee2e6;
    }

    .timeline-item:last-child::after {
        display: none;
    }

    .event-type-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .type-intervention { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .type-reunion { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .type-formation { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }
    .type-visite { background-color: rgba(253, 126, 20, 0.1); color: #fd7e14; }

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

    .participation-chart {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête de l'événement -->
    <div class="event-header">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <div class="event-type-icon type-{{ $event->type }}">
                    <i class="bi bi-{{ $event->type === 'intervention' ? 'tools' : ($event->type === 'reunion' ? 'people' : ($event->type === 'formation' ? 'book' : 'geo-alt')) }}"></i>
                </div>
                <div>
                    <h1 class="mb-2">{{ $event->titre }}</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge status-badge
                            @if($event->statut === 'termine') bg-success
                            @elseif($event->statut === 'en_cours') bg-primary
                            @elseif($event->statut === 'annule') bg-danger
                            @elseif($event->statut === 'reporte') bg-warning
                            @else bg-secondary @endif">
                            {{ $event->statut_nom }}
                        </span>
                        <span class="badge bg-white text-dark">{{ $event->type_nom }}</span>
                        <span class="badge
                            @if($event->priorite === 'urgente') bg-danger
                            @elseif($event->priorite === 'haute') bg-warning
                            @else bg-info @endif">
                            {{ $event->priorite_nom }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <div class="btn-group">
                    @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-light">
                            <i class="bi bi-pencil me-1"></i>
                            Modifier
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('events.duplicate', $event) }}">
                                    <i class="bi bi-files me-2"></i>Dupliquer
                                </a></li>
                                @if($event->statut !== 'termine' && $event->statut !== 'annule')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="markCompleted()">
                                        <i class="bi bi-check-circle me-2"></i>Marquer terminé
                                    </a></li>
                                    <li><a class="dropdown-item text-warning" href="#" onclick="postponeEvent()">
                                        <i class="bi bi-calendar-x me-2"></i>Reporter
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="cancelEvent()">
                                        <i class="bi bi-x-circle me-2"></i>Annuler
                                    </a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    <a href="{{ route('events.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-text text-primary me-2"></i>
                        Description
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $event->description }}</p>
                </div>
            </div>

            <!-- Participants -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people text-primary me-2"></i>
                        Participants ({{ $event->participants->count() }})
                    </h5>
                    <div>
                        <span class="badge bg-success">{{ $event->participants->where('statut_presence', 'confirme')->count() }} Confirmés</span>
                        <span class="badge bg-warning">{{ $event->participants->where('statut_presence', 'invite')->count() }} En attente</span>
                        <span class="badge bg-danger">{{ $event->participants->where('statut_presence', 'decline')->count() }} Déclinés</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($event->participants as $participation)
                            <div class="col-md-6 mb-3">
                                <div class="participant-card card h-100
                                    @if($participation->id_utilisateur === $event->id_organisateur) organizer
                                    @elseif($participation->statut_presence === 'confirme') confirmed
                                    @elseif($participation->statut_presence === 'decline') declined
                                    @else pending @endif">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title mb-1">
                                                    {{ $participation->utilisateur->prenom }} {{ $participation->utilisateur->nom }}
                                                    @if($participation->id_utilisateur === $event->id_organisateur)
                                                        <span class="badge bg-primary ms-2">Organisateur</span>
                                                    @endif
                                                </h6>
                                                <p class="card-text text-muted small mb-2">
                                                    {{ $participation->utilisateur->email }}
                                                </p>
                                                <span class="badge {{ $participation->classe_statut }}">
                                                    <i class="bi {{ $participation->icone_statut }} me-1"></i>
                                                    {{ $participation->statut_presence_nom }}
                                                </span>
                                            </div>
                                            @if($participation->id_utilisateur === Auth::id() && $participation->id_utilisateur !== $event->id_organisateur)
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-success"
                                                            onclick="updateParticipation('confirme')" title="Confirmer">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="updateParticipation('decline')" title="Décliner">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tâches associées -->
            @if($event->taches->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check text-primary me-2"></i>
                        Tâches Associées ({{ $event->taches->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($event->taches as $tache)
                        <div class="task-item
                            @if($tache->statut === 'termine') task-completed
                            @elseif($tache->statut === 'en_cours') task-in-progress @endif">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $tache->titre }}</h6>
                                    <p class="text-muted mb-2 small">{{ Str::limit($tache->description, 100) }}</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge
                                            @if($tache->statut === 'termine') bg-success
                                            @elseif($tache->statut === 'en_cours') bg-warning
                                            @else bg-secondary @endif">
                                            {{ ucfirst(str_replace('_', ' ', $tache->statut)) }}
                                        </span>
                                        <small class="text-muted">
                                            Échéance: {{ $tache->date_echeance ? $tache->date_echeance->format('d/m/Y') : 'Non définie' }}
                                        </small>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Historique (simulation) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Historique des Modifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">Événement créé</h6>
                                <p class="text-muted mb-0">Par {{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</p>
                            </div>
                            <small class="text-muted">{{ $event->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>

                    @if($event->created_at != $event->updated_at)
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">Dernière modification</h6>
                                <p class="text-muted mb-0">Informations mises à jour</p>
                            </div>
                            <small class="text-muted">{{ $event->updated_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4">
            <!-- Informations générales -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Détails de l'Événement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <i class="bi bi-calendar-date"></i>
                        <div>
                            <strong>Date de début</strong><br>
                            <span class="text-muted">{{ $event->date_debut->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <strong>Date de fin</strong><br>
                            <span class="text-muted">{{ $event->date_fin->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-clock"></i>
                        <div>
                            <strong>Durée</strong><br>
                            <span class="text-muted">{{ $event->duree }} minutes</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <strong>Lieu</strong><br>
                            <span class="text-muted">{{ $event->lieu }}</span>
                            @if($event->coordonnees_gps)
                                <br><small class="text-info">GPS: {{ $event->coordonnees_gps }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="bi bi-person-badge"></i>
                        <div>
                            <strong>Organisateur</strong><br>
                            <span class="text-muted">{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</span>
                        </div>
                    </div>

                    @if($event->projet)
                    <div class="info-item">
                        <i class="bi bi-folder"></i>
                        <div>
                            <strong>Projet associé</strong><br>
                            <span class="text-muted">{{ $event->projet->nom }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Métriques de participation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-pie-chart text-primary me-2"></i>
                        Statistiques de Participation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-number text-success">{{ $event->participants->where('statut_presence', 'confirme')->count() }}</div>
                                <small class="text-muted">Confirmés</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-number text-warning">{{ $event->participants->where('statut_presence', 'invite')->count() }}</div>
                                <small class="text-muted">En attente</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-number text-danger">{{ $event->participants->where('statut_presence', 'decline')->count() }}</div>
                                <small class="text-muted">Déclinés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions contextuelles -->
            @if($event->statut !== 'termine' && $event->statut !== 'annule')
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                            <button type="button" class="btn btn-success" onclick="markCompleted()">
                                <i class="bi bi-check-circle me-2"></i>
                                Marquer comme terminé
                            </button>
                            <button type="button" class="btn btn-warning" onclick="postponeEvent()">
                                <i class="bi bi-calendar-x me-2"></i>
                                Reporter l'événement
                            </button>
                        @endif

                        @if($event->utilisateurParticipe(Auth::id()) && Auth::id() !== $event->id_organisateur)
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success" onclick="updateParticipation('confirme')">
                                    <i class="bi bi-check me-1"></i>
                                    Confirmer
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="updateParticipation('decline')">
                                    <i class="bi bi-x me-1"></i>
                                    Décliner
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de report -->
<div class="modal fade" id="postponeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporter l'Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('events.postpone', $event) }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="new_date_debut" class="form-label">Nouvelle date de début</label>
                            <input type="datetime-local" class="form-control" id="new_date_debut" name="date_debut" required>
                        </div>
                        <div class="col-md-6">
                            <label for="new_date_fin" class="form-label">Nouvelle date de fin</label>
                            <input type="datetime-local" class="form-control" id="new_date_fin" name="date_fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Reporter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Marquer comme terminé
function markCompleted() {
    if (confirm('Êtes-vous sûr de vouloir marquer cet événement comme terminé ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("events.markCompleted", $event) }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

// Reporter l'événement
function postponeEvent() {
    const modal = new bootstrap.Modal(document.getElementById('postponeModal'));
    modal.show();
}

// Annuler l'événement
function cancelEvent() {
    if (confirm('Êtes-vous sûr de vouloir annuler cet événement ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("events.cancel", $event) }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

// Mettre à jour la participation
function updateParticipation(statut) {
    fetch('{{ route("events.updateParticipation", $event) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            statut_presence: statut
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour voir les changements
        } else {
            alert('Erreur lors de la mise à jour de la participation');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour de la participation');
    });
}

// Validation des dates dans le modal de report
document.getElementById('new_date_debut').addEventListener('change', function() {
    const dateDebut = new Date(this.value);
    const dateFin = document.getElementById('new_date_fin');

    // Mettre à jour la date de fin minimale
    dateFin.min = this.value;

    // Si la date de fin est antérieure, l'ajuster
    if (dateFin.value && new Date(dateFin.value) <= dateDebut) {
        const newEndDate = new Date(dateDebut.getTime() + 60 * 60 * 1000); // +1 heure
        dateFin.value = newEndDate.toISOString().slice(0, 16);
    }
});
</script>
@endpush solid white;
        box-shadow: 0 0 0 2px #007bff;
    }

    .timeline-item::after {
        content: '';
        position: absolute
