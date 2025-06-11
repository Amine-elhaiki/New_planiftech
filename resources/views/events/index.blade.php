@extends('layouts.app')

@section('title', 'Gestion des Événements')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .event-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .event-card.type-intervention { border-left-color: #dc3545; }
    .event-card.type-reunion { border-left-color: #007bff; }
    .event-card.type-formation { border-left-color: #28a745; }
    .event-card.type-visite { border-left-color: #fd7e14; }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .priority-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .priority-normale { background-color: #6c757d; }
    .priority-haute { background-color: #ffc107; }
    .priority-urgente { background-color: #dc3545; }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .filter-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .calendar-container {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .fc-toolbar {
        margin-bottom: 1rem !important;
    }

    .fc-button {
        border-radius: 6px !important;
        font-size: 0.875rem !important;
    }

    .event-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
    }

    .icon-intervention { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .icon-reunion { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .icon-formation { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }
    .icon-visite { background-color: rgba(253, 126, 20, 0.1); color: #fd7e14; }

    .participant-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        margin-right: 0.25rem;
    }

    .view-toggle {
        background-color: white;
        border-radius: 10px;
        padding: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .view-toggle .nav-link {
        border-radius: 6px;
        color: #6c757d;
        border: none;
        padding: 0.75rem 1.5rem;
    }

    .view-toggle .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                Gestion des Événements
            </h1>
            <p class="text-muted mb-0">Planifiez et suivez vos événements et interventions</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nouvel Événement
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-gear me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('events.export') }}">
                        <i class="bi bi-download me-2"></i>Exporter CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('events.calendar') }}">
                        <i class="bi bi-calendar3 me-2"></i>Vue Calendrier
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-card">
        <div class="row text-center">
            <div class="col-md-3">
                <h3 class="mb-1">{{ $stats['total'] ?? 0 }}</h3>
                <small>Total Événements</small>
            </div>
            <div class="col-md-3">
                <h3 class="mb-1">{{ $stats['aujourd_hui'] ?? 0 }}</h3>
                <small>Aujourd'hui</small>
            </div>
            <div class="col-md-3">
                <h3 class="mb-1">{{ $stats['cette_semaine'] ?? 0 }}</h3>
                <small>Cette Semaine</small>
            </div>
            <div class="col-md-3">
                <h3 class="mb-1">{{ $stats['urgents'] ?? 0 }}</h3>
                <small>Urgents</small>
            </div>
        </div>
    </div>

    <!-- Sélecteur de vue -->
    <div class="view-toggle">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $view === 'calendar' ? 'active' : '' }}"
                   id="calendar-tab" data-bs-toggle="tab" href="#calendar-view" role="tab">
                    <i class="bi bi-calendar3 me-1"></i>
                    Calendrier
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $view === 'list' ? 'active' : '' }}"
                   id="list-tab" data-bs-toggle="tab" href="#list-view" role="tab">
                    <i class="bi bi-list-ul me-1"></i>
                    Liste
                </a>
            </li>
        </ul>
    </div>

    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Vue Calendrier -->
                <div class="tab-pane fade {{ $view === 'calendar' ? 'show active' : '' }}"
                     id="calendar-view" role="tabpanel">
                    <div class="calendar-container">
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- Vue Liste -->
                <div class="tab-pane fade {{ $view === 'list' ? 'show active' : '' }}"
                     id="list-view" role="tabpanel">
                    @if(isset($events) && $events->count() > 0)
                        @foreach($events as $event)
                            <div class="event-card card type-{{ $event->type }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <!-- Icône du type d'événement -->
                                        <div class="event-type-icon icon-{{ $event->type }}">
                                            <i class="bi bi-{{ $event->type === 'intervention' ? 'tools' : ($event->type === 'reunion' ? 'people' : ($event->type === 'formation' ? 'book' : 'geo-alt')) }}"></i>
                                        </div>

                                        <!-- Contenu principal -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $event->titre }}</h5>
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <span class="priority-indicator priority-{{ $event->priorite }}"></span>
                                                        <span class="badge status-badge
                                                            @if($event->statut === 'termine') bg-success
                                                            @elseif($event->statut === 'en_cours') bg-primary
                                                            @elseif($event->statut === 'annule') bg-danger
                                                            @elseif($event->statut === 'reporte') bg-warning
                                                            @else bg-secondary @endif">
                                                            {{ $event->statut_nom }}
                                                        </span>
                                                        <span class="badge bg-light text-dark">{{ $event->type_nom }}</span>
                                                        @if($event->priorite === 'urgente')
                                                            <span class="badge bg-danger">{{ $event->priorite_nom }}</span>
                                                        @elseif($event->priorite === 'haute')
                                                            <span class="badge bg-warning">{{ $event->priorite_nom }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('events.show', $event) }}"
                                                       class="btn btn-outline-primary" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                                                        <a href="{{ route('events.edit', $event) }}"
                                                           class="btn btn-outline-secondary" title="Modifier">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger"
                                                                title="Supprimer" onclick="confirmDelete({{ $event->id }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            <p class="card-text text-muted mb-2">
                                                {{ Str::limit($event->description, 100) }}
                                            </p>

                                            <!-- Informations détaillées -->
                                            <div class="row text-sm">
                                                <div class="col-md-4">
                                                    <i class="bi bi-calendar-date text-primary me-1"></i>
                                                    <strong>Date:</strong><br>
                                                    <small class="text-muted">{{ $event->date_debut->format('d/m/Y à H:i') }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                                    <strong>Lieu:</strong><br>
                                                    <small class="text-muted">{{ $event->lieu }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <i class="bi bi-person text-primary me-1"></i>
                                                    <strong>Organisateur:</strong><br>
                                                    <small class="text-muted">{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</small>
                                                </div>
                                            </div>

                                            <!-- Participants -->
                                            @if($event->participants->count() > 0)
                                                <div class="mt-3">
                                                    <small class="text-muted">Participants ({{ $event->participants->count() }}):</small>
                                                    <div class="d-flex flex-wrap mt-1">
                                                        @foreach($event->participants->take(5) as $participation)
                                                            <div class="participant-avatar"
                                                                 title="{{ $participation->utilisateur->prenom }} {{ $participation->utilisateur->nom }}">
                                                                {{ substr($participation->utilisateur->prenom, 0, 1) }}{{ substr($participation->utilisateur->nom, 0, 1) }}
                                                            </div>
                                                        @endforeach
                                                        @if($event->participants->count() > 5)
                                                            <div class="participant-avatar" style="background-color: #6c757d;">
                                                                +{{ $event->participants->count() - 5 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        @if(isset($events) && method_exists($events, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $events->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Aucun événement trouvé</h4>
                            <p class="text-muted">Commencez par créer votre premier événement.</p>
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>
                                Créer un Événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar avec filtres -->
        <div class="col-lg-3">
            <!-- Filtres -->
            <div class="filter-card">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-funnel text-primary me-2"></i>
                    Filtres
                </h6>

                <form method="GET" action="{{ route('events.index') }}" id="filterForm">
                    <input type="hidden" name="view" value="{{ $view }}">

                    <div class="mb-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control form-control-sm"
                               id="search" name="search" value="{{ request('search') }}"
                               placeholder="Titre, description, lieu...">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select form-select-sm" id="type" name="type">
                            <option value="">Tous les types</option>
                            @foreach(App\Models\Event::$types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select form-select-sm" id="statut" name="statut">
                            <option value="">Tous les statuts</option>
                            @foreach(App\Models\Event::$statuts as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="priorite" class="form-label">Priorité</label>
                        <select class="form-select form-select-sm" id="priorite" name="priorite">
                            <option value="">Toutes les priorités</option>
                            @foreach(App\Models\Event::$priorites as $key => $label)
                                <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label for="date_debut" class="form-label">Du</label>
                            <input type="date" class="form-control form-control-sm"
                                   id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-6">
                            <label for="date_fin" class="form-label">Au</label>
                            <input type="date" class="form-control form-control-sm"
                                   id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('events.index', ['view' => $view]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning text-primary me-2"></i>
                        Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus me-1"></i>
                            Créer Événement
                        </a>
                        <a href="{{ route('events.index', ['view' => 'calendar']) }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-calendar3 me-1"></i>
                            Vue Calendrier
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>
                            Imprimer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Événements à venir -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-clock text-primary me-2"></i>
                        Prochains Événements
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Simulation d'événements à venir -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <div class="priority-indicator priority-urgente"></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small">Maintenance urgente</div>
                                <small class="text-muted">Aujourd'hui 14:00</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <div class="priority-indicator priority-normale"></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small">Réunion équipe</div>
                                <small class="text-muted">Demain 09:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de suppression (caché) -->
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
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
                url: '/events/calendar/data',
                method: 'GET',
                failure: function() {
                    alert('Erreur lors du chargement des événements');
                }
            },
            eventClick: function(info) {
                window.location.href = '/events/' + info.event.id;
            },
            dateClick: function(info) {
                window.location.href = '/events/create?date=' + info.dateStr;
            },
            eventDidMount: function(info) {
                // Ajouter un tooltip
                info.el.setAttribute('title', info.event.title + ' - ' + (info.event.extendedProps.lieu || ''));
            }
        });

        calendar.render();
    }

    // Gestion des onglets avec URL
    var tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabElements.forEach(function(tabElement) {
        tabElement.addEventListener('shown.bs.tab', function(e) {
            var view = e.target.id === 'calendar-tab' ? 'calendar' : 'list';
            var url = new URL(window.location);
            url.searchParams.set('view', view);
            window.history.replaceState({}, '', url);
        });
    });

    // Auto-soumission des filtres
    var filterSelects = document.querySelectorAll('#filterForm select');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Recherche avec délai
    var searchInput = document.getElementById('search');
    if (searchInput) {
        var searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }
});

// Fonction de confirmation de suppression
function confirmDelete(eventId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
        var form = document.getElementById('deleteForm');
        form.action = '/events/' + eventId;
        form.submit();
    }
}

// Fonction pour aller à la vue calendrier
function goToCalendarView() {
    var url = new URL(window.location);
    url.searchParams.set('view', 'calendar');
    window.location.href = url.toString();
}

// Fonction pour aller à la vue liste
function goToListView() {
    var url = new URL(window.location);
    url.searchParams.set('view', 'list');
    window.location.href = url.toString();
}

// Fonction pour exporter
function exportEvents() {
    var params = new URLSearchParams(window.location.search);
    window.location.href = '/events/export/csv?' + params.toString();
}

// Fonction pour imprimer
function printEvents() {
    window.print();
}
</script>
@endpush
