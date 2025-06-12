@extends('layouts.app')

@section('title', 'Détails de l\'événement')

@push('styles')
<style>
    .event-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .event-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .event-content {
        position: relative;
        z-index: 2;
    }

    .event-type-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .type-intervention {
        background: rgba(220, 38, 38, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(220, 38, 38, 0.3);
    }

    .type-reunion {
        background: rgba(37, 99, 235, 0.2);
        color: #93c5fd;
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .type-formation {
        background: rgba(22, 163, 74, 0.2);
        color: #86efac;
        border: 1px solid rgba(22, 163, 74, 0.3);
    }

    .type-visite {
        background: rgba(234, 88, 12, 0.2);
        color: #fdba74;
        border: 1px solid rgba(234, 88, 12, 0.3);
    }

    .priority-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-left: 1rem;
    }

    .priority-normale {
        background: rgba(107, 114, 128, 0.2);
        color: #d1d5db;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }

    .priority-haute {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .priority-urgente {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .priority-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .priority-dot.normale { background: #d1d5db; }
    .priority-dot.haute { background: #fbbf24; }
    .priority-dot.urgente { background: #f87171; }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: 1rem;
    }

    .status-planifie {
        background: rgba(75, 85, 99, 0.2);
        color: #d1d5db;
        border: 1px solid rgba(75, 85, 99, 0.3);
    }

    .status-en_cours {
        background: rgba(37, 99, 235, 0.2);
        color: #93c5fd;
        border: 1px solid rgba(37, 99, 235, 0.3);
    }

    .status-termine {
        background: rgba(22, 163, 74, 0.2);
        color: #86efac;
        border: 1px solid rgba(22, 163, 74, 0.3);
    }

    .status-annule {
        background: rgba(220, 38, 38, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(220, 38, 38, 0.3);
    }

    .status-reporte {
        background: rgba(217, 119, 6, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(217, 119, 6, 0.3);
    }

    .info-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }

    .info-card h5 {
        color: #374151;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card h5 i {
        color: #667eea;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 10px;
        border: 1px solid #f3f4f6;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-content {
        flex-grow: 1;
    }

    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 2px;
    }

    .info-value {
        font-weight: 600;
        color: #374151;
    }

    .participant-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }

    .participant-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 10px;
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .participant-item:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
    }

    .participant-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .participant-info {
        flex-grow: 1;
    }

    .participant-name {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .participant-email {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .participant-status {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-confirme {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-invite {
        background: #fef3c7;
        color: #d97706;
    }

    .status-decline {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-present {
        background: #dbeafe;
        color: #2563eb;
    }

    .status-absent {
        background: #f3f4f6;
        color: #6b7280;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-action {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        color: white;
        box-shadow: 0 8px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(22, 163, 74, 0.3);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);
    }

    .btn-outline-secondary {
        background: white;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        color: #374151;
        border-color: #d1d5db;
    }

    .description-content {
        line-height: 1.6;
        color: #4b5563;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .map-container {
        height: 300px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .event-header {
            padding: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .participant-list {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Événements</a></li>
            <li class="breadcrumb-item active">{{ $event->titre }}</li>
        </ol>
    </nav>

    <!-- En-tête de l'événement -->
    <div class="event-header">
        <div class="event-content">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="event-type-badge type-{{ $event->type }}">
                        {{ $event->type_nom }}
                    </div>
                    <div class="priority-indicator priority-{{ $event->priorite }}">
                        <div class="priority-dot {{ $event->priorite }}"></div>
                        {{ $event->priorite_nom }}
                    </div>
                    <div class="status-badge status-{{ $event->statut }}">
                        {{ $event->statut_nom }}
                    </div>
                </div>
                <div class="action-buttons">
                    @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                        <a href="{{ route('events.edit', $event) }}" class="btn-action btn-primary">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                    @endif
                    <a href="{{ route('events.index') }}" class="btn-action btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Retour
                    </a>
                </div>
            </div>

            <h1 class="display-6 fw-bold mb-3">{{ $event->titre }}</h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center text-white-50 mb-2">
                        <i class="bi bi-person me-2"></i>
                        Organisé par {{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}
                    </div>
                    @if($event->projet)
                        <div class="d-flex align-items-center text-white-50">
                            <i class="bi bi-folder me-2"></i>
                            Projet : {{ $event->projet->nom }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="text-white-50">
                        <i class="bi bi-people me-1"></i>
                        {{ $event->participants->count() }} participant(s)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="info-card">
                <h5>
                    <i class="bi bi-file-text"></i>
                    Description
                </h5>
                <div class="description-content">
                    {{ $event->description }}
                </div>
            </div>

            <!-- Détails de l'événement -->
            <div class="info-card">
                <h5>
                    <i class="bi bi-info-circle"></i>
                    Détails de l'événement
                </h5>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-date"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Date de début</div>
                            <div class="info-value">{{ $event->date_debut->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Date de fin</div>
                            <div class="info-value">{{ $event->date_fin->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Durée</div>
                            <div class="info-value">
                                @if($event->duree >= 60)
                                    {{ intval($event->duree / 60) }}h {{ $event->duree % 60 }}min
                                @else
                                    {{ $event->duree }} minutes
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Lieu</div>
                            <div class="info-value">{{ $event->lieu }}</div>
                        </div>
                    </div>

                    @if($event->coordonnees_gps)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-geo"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Coordonnées GPS</div>
                                <div class="info-value">{{ $event->coordonnees_gps }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Créé le</div>
                            <div class="info-value">{{ $event->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants -->
            <div class="info-card">
                <h5>
                    <i class="bi bi-people"></i>
                    Participants ({{ $event->participants->count() }})
                </h5>

                @if($event->participants->count() > 0)
                    <div class="participant-list">
                        @foreach($event->participants as $participant)
                            <div class="participant-item">
                                <div class="participant-avatar">
                                    {{ substr($participant->utilisateur->prenom, 0, 1) }}{{ substr($participant->utilisateur->nom, 0, 1) }}
                                </div>
                                <div class="participant-info">
                                    <div class="participant-name">
                                        {{ $participant->utilisateur->prenom }} {{ $participant->utilisateur->nom }}
                                        @if($participant->id_utilisateur === $event->id_organisateur)
                                            <span class="badge bg-primary ms-2">Organisateur</span>
                                        @endif
                                    </div>
                                    <div class="participant-email">{{ $participant->utilisateur->email }}</div>
                                </div>
                                <div class="participant-status status-{{ $participant->statut_presence }}">
                                    {{ ucfirst(str_replace('_', ' ', $participant->statut_presence)) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-people display-4 mb-3"></i>
                        <p>Aucun participant pour cet événement</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions rapides -->
            @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                <div class="info-card">
                    <h5>
                        <i class="bi bi-lightning"></i>
                        Actions rapides
                    </h5>

                    <div class="d-grid gap-2">
                        @if($event->statut === 'planifie')
                            <button class="btn-action btn-success" onclick="updateStatus('en_cours')">
                                <i class="bi bi-play"></i>
                                Commencer l'événement
                            </button>
                        @endif

                        @if(in_array($event->statut, ['planifie', 'en_cours']))
                            <button class="btn-action btn-success" onclick="updateStatus('termine')">
                                <i class="bi bi-check-lg"></i>
                                Marquer comme terminé
                            </button>

                            <button class="btn-action btn-warning" onclick="showPostponeModal()">
                                <i class="bi bi-calendar-week"></i>
                                Reporter l'événement
                            </button>

                            <button class="btn-action btn-danger" onclick="updateStatus('annule')">
                                <i class="bi bi-x-lg"></i>
                                Annuler l'événement
                            </button>
                        @endif

                        <a href="{{ route('events.edit', $event) }}" class="btn-action btn-primary">
                            <i class="bi bi-pencil"></i>
                            Modifier l'événement
                        </a>

                        <button class="btn-action btn-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash"></i>
                            Supprimer l'événement
                        </button>
                    </div>
                </div>
            @endif

            <!-- Carte/Localisation -->
            @if($event->coordonnees_gps)
                <div class="info-card">
                    <h5>
                        <i class="bi bi-map"></i>
                        Localisation
                    </h5>

                    <div class="map-container">
                        <div class="text-center">
                            <i class="bi bi-geo-alt display-4 mb-2"></i>
                            <p class="mb-1">{{ $event->lieu }}</p>
                            <small>{{ $event->coordonnees_gps }}</small>
                            <br>
                            <a href="https://www.google.com/maps?q={{ $event->coordonnees_gps }}"
                               target="_blank" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-map me-1"></i>
                                Voir sur Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informations supplémentaires -->
            <div class="info-card">
                <h5>
                    <i class="bi bi-graph-up"></i>
                    Statistiques
                </h5>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-3">
                            <div class="h4 mb-1 text-primary">{{ $event->participants->where('statut_presence', 'confirme')->count() }}</div>
                            <small class="text-muted">Confirmés</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3">
                            <div class="h4 mb-1 text-warning">{{ $event->participants->where('statut_presence', 'invite')->count() }}</div>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>

                @if($event->date_debut < now())
                    <div class="alert alert-info">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Événement passé</strong><br>
                        Il y a {{ $event->date_debut->diffForHumans() }}
                    </div>
                @elseif($event->date_debut->isToday())
                    <div class="alert alert-warning">
                        <i class="bi bi-calendar-day me-2"></i>
                        <strong>Événement aujourd'hui</strong><br>
                        À {{ $event->date_debut->format('H:i') }}
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="bi bi-calendar-plus me-2"></i>
                        <strong>Événement à venir</strong><br>
                        {{ $event->date_debut->diffForHumans() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulaires cachés -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="statut" id="statusInput">
</form>

<form id="deleteForm" method="POST" action="{{ route('events.destroy', $event) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
// Mettre à jour le statut de l'événement
function updateStatus(newStatus) {
    const messages = {
        'en_cours': 'Voulez-vous commencer cet événement ?',
        'termine': 'Voulez-vous marquer cet événement comme terminé ?',
        'annule': 'Voulez-vous annuler cet événement ?'
    };

    if (confirm(messages[newStatus])) {
        document.getElementById('statusInput').value = newStatus;
        document.getElementById('statusForm').action = '/events/{{ $event->id }}/status';
        document.getElementById('statusForm').submit();
    }
}

// Confirmer la suppression
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Afficher le modal de report (simulation)
function showPostponeModal() {
    const newDate = prompt('Nouvelle date de début (YYYY-MM-DD HH:MM):');
    if (newDate) {
        const newEndDate = prompt('Nouvelle date de fin (YYYY-MM-DD HH:MM):');
        if (newEndDate) {
            // Créer un formulaire dynamique pour le report
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/events/{{ $event->id }}/postpone';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PATCH';

            const dateDebut = document.createElement('input');
            dateDebut.type = 'hidden';
            dateDebut.name = 'date_debut';
            dateDebut.value = newDate;

            const dateFin = document.createElement('input');
            dateFin.type = 'hidden';
            dateFin.name = 'date_fin';
            dateFin.value = newEndDate;

            form.appendChild(csrf);
            form.appendChild(method);
            form.appendChild(dateDebut);
            form.appendChild(dateFin);

            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush
