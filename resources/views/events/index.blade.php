@extends('layouts.app')

@section('title', 'Gestion des Événements')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .event-card {
        transition: all 0.3s ease;
        border-left: 4px solid #dee2e6;
    }
    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .event-intervention { border-left-color: #dc3545; }
    .event-reunion { border-left-color: #007bff; }
    .event-formation { border-left-color: #28a745; }
    .event-visite { border-left-color: #fd7e14; }

    .priority-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600;
    }

    .fc-button-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .fc-button-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête avec titre et actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-calendar-event text-primary me-2"></i>
                Gestion des Événements
            </h1>
            <p class="text-muted mb-0">Planifiez et suivez vos interventions, réunions et formations</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nouvel Événement
            </a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-download me-1"></i>
                Exporter
            </button>
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if(Auth::user()->role === 'admin')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-number">{{ $stats['total'] }}</div>
                <div>Total Événements</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-number">{{ $stats['aujourd_hui'] }}</div>
                <div>Aujourd'hui</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stats-number">{{ $stats['cette_semaine'] }}</div>
                <div>Cette Semaine</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stats-number">{{ $stats['urgents'] }}</div>
                <div>Urgents</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Onglets pour les vues -->
    <ul class="nav nav-tabs mb-4" id="eventTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $view === 'calendar' ? 'active' : '' }}" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-pane" type="button" role="tab">
                <i class="bi bi-calendar3 me-1"></i>
                Vue Calendrier
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $view === 'list' ? 'active' : '' }}" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-pane" type="button" role="tab">
                <i class="bi bi-list-ul me-1"></i>
                Vue Liste
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="eventTabsContent">
        <!-- Vue Calendrier -->
        <div class="tab-pane fade {{ $view === 'calendar' ? 'show active' : '' }}" id="calendar-pane" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Vue Liste -->
        <div class="tab-pane fade {{ $view === 'list' ? 'show active' : '' }}" id="list-pane" role="tabpanel">
            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                        <input type="hidden" name="view" value="list">

                        <div class="col-md-3">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Titre, description, lieu...">
                        </div>

                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous les types</option>
                                @foreach(App\Models\Event::$types as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="">Tous les statuts</option>
                                @foreach(App\Models\Event::$statuts as $key => $label)
                                    <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="priorite" class="form-label">Priorité</label>
                            <select class="form-select" id="priorite" name="priorite">
                                <option value="">Toutes les priorités</option>
                                @foreach(App\Models\Event::$priorites as $key => $label)
                                    <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">Période</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="date_debut" name="date_debut"
                                       value="{{ request('date_debut') }}">
                                <input type="date" class="form-control" id="date_fin" name="date_fin"
                                       value="{{ request('date_fin') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('events.index', ['view' => 'list']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des événements -->
            @if($view === 'list' && $events->count() > 0)
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-12 mb-3">
                            <div class="card event-card event-{{ $event->type }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="card-title mb-1">
                                                <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                                    {{ $event->titre }}
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted small mb-2">{{ Str::limit($event->description, 100) }}</p>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-secondary">{{ $event->type_nom }}</span>
                                                <span class="badge priority-badge
                                                    @if($event->priorite === 'urgente') bg-danger
                                                    @elseif($event->priorite === 'haute') bg-warning
                                                    @else bg-info @endif">
                                                    {{ $event->priorite_nom }}
                                                </span>
                                                <span class="badge
                                                    @if($event->statut === 'termine') bg-success
                                                    @elseif($event->statut === 'en_cours') bg-primary
                                                    @elseif($event->statut === 'annule') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ $event->statut_nom }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="text-muted small">
                                                <div><i class="bi bi-calendar-date me-1"></i>{{ $event->date_debut->format('d/m/Y') }}</div>
                                                <div><i class="bi bi-clock me-1"></i>{{ $event->date_debut->format('H:i') }} - {{ $event->date_fin->format('H:i') }}</div>
                                                <div><i class="bi bi-geo-alt me-1"></i>{{ $event->lieu }}</div>
                                                <div><i class="bi bi-person me-1"></i>{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary" title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary" title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Supprimer"
                                                            onclick="confirmDelete({{ $event->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                {{ $events->links() }}
            @elseif($view === 'list')
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="mt-3">Aucun événement trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou créez un nouvel événement.</p>
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Créer un événement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter les événements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('events.export') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_type" class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="">Tous les types</option>
                            @foreach(App\Models\Event::$types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="export_statut" class="form-label">Statut</label>
                        <select class="form-select" name="statut">
                            <option value="">Tous les statuts</option>
                            @foreach(App\Models\Event::$statuts as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="export_date_debut" class="form-label">Date début</label>
                            <input type="date" class="form-control" name="date_debut">
                        </div>
                        <div class="col-6">
                            <label for="export_date_fin" class="form-label">Date fin</label>
                            <input type="date" class="form-control" name="date_fin">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>
                        Exporter CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du calendrier
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            events: {
                url: '{{ route("events.calendar") }}',
                failure: function() {
                    alert('Erreur lors du chargement des événements');
                }
            },
            eventClick: function(info) {
                window.location.href = `/events/${info.event.id}`;
            },
            dateClick: function(info) {
                window.location.href = `/events/create?date=${info.dateStr}`;
            }
        });

        calendar.render();
    }
});

// Fonction de confirmation de suppression
function confirmDelete(eventId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
        const form = document.getElementById('deleteForm');
        form.action = "/events/" + eventId;
        form.submit();
    }
}

// Gestion des onglets avec URL
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function(e) {
        const view = e.target.id === 'calendar-tab' ? 'calendar' : 'list';
        const url = new URL(window.location);
        url.searchParams.set('view', view);
        window.history.replaceState({}, '', url);
    });
});
</script>
@endpush
