@extends('layouts.app')

@section('title', 'Événements - Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }

    .hero-stat {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 0.5rem;
    }

    .hero-stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .custom-tabs {
        background: white;
        border-radius: 15px;
        padding: 0.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: flex;
        gap: 0.5rem;
    }

    .custom-tab {
        flex: 1;
        padding: 1rem;
        border-radius: 10px;
        border: none;
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-tab.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: scale(1.02);
    }

    .custom-tab:hover:not(.active) {
        background: #f3f4f6;
    }

    .event-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .event-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
    }

    .event-type-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-intervention {
        background: #fee2e2;
        color: #dc2626;
    }

    .type-reunion {
        background: #dbeafe;
        color: #2563eb;
    }

    .type-formation {
        background: #dcfce7;
        color: #16a34a;
    }

    .type-visite {
        background: #fed7aa;
        color: #ea580c;
    }

    .event-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        margin-right: 6rem;
    }

    .event-description {
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .priority-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .priority-normale { background: #6b7280; }
    .priority-haute { background: #f59e0b; }
    .priority-urgente { background: #ef4444; }

    .status-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-planifie {
        background: #f3f4f6;
        color: #4b5563;
    }

    .status-en_cours {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-termine {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-annule {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-reporte {
        background: #fef3c7;
        color: #d97706;
    }

    .event-info {
        padding: 1.5rem;
        background: #f9fafb;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .event-footer {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .participants-count {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: #f3f4f6;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        margin-left: 0.5rem;
    }

    .action-btn:hover {
        background: #374151;
        color: white;
        transform: scale(1.1);
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .action-btn.primary:hover {
        color: white;
    }

    .sidebar-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .calendar-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-height: 600px;
    }

    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50%;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .fab:hover {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 1.5rem;
        }

        .custom-tabs {
            flex-direction: column;
        }

        .event-info {
            grid-template-columns: 1fr;
        }

        .event-footer {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="display-5 fw-bold mb-2">
                    <i class="bi bi-calendar-event me-3"></i>
                    Tableau de Bord Événements
                </h1>
                <p class="lead mb-0">
                    Gérez efficacement vos interventions, réunions et formations ORMVAT
                </p>
            </div>
            <a href="{{ route('events.create') }}" class="btn btn-light btn-lg">
                <i class="bi bi-plus-lg me-2"></i>
                Nouvel Événement
            </a>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['total'] ?? 0 }}</span>
                    <span>Total</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['aujourd_hui'] ?? 0 }}</span>
                    <span>Aujourd'hui</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['cette_semaine'] ?? 0 }}</span>
                    <span>Cette Semaine</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['ce_mois'] ?? 0 }}</span>
                    <span>Ce Mois</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['urgents'] ?? 0 }}</span>
                    <span>Urgents</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="custom-tabs">
        <button class="custom-tab {{ $view === 'cards' ? 'active' : '' }}" onclick="switchView('cards')">
            <i class="bi bi-grid-3x3-gap me-2"></i>
            Vue Cartes
        </button>
        <button class="custom-tab {{ $view === 'calendar' ? 'active' : '' }}" onclick="switchView('calendar')">
            <i class="bi bi-calendar3 me-2"></i>
            Calendrier
        </button>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Cards View -->
            <div id="cards-view" class="{{ $view === 'cards' ? '' : 'd-none' }}">
                @if(isset($events) && $events->count() > 0)
                    <div class="row">
                        @foreach($events as $event)
                            <div class="col-lg-6 col-xl-4">
                                <div class="event-card">
                                    <div class="event-card-header">
                                        <div class="event-type-badge type-{{ $event->type }}">
                                            {{ $event->type_nom }}
                                        </div>

                                        <h3 class="event-title">{{ $event->titre }}</h3>
                                        <p class="event-description">
                                            {{ Str::limit($event->description, 100) }}
                                        </p>

                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="priority-dot priority-{{ $event->priorite }}"
                                                 title="{{ $event->priorite_nom }}"></div>
                                            <span class="status-pill status-{{ $event->statut }}">
                                                {{ $event->statut_nom }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="event-info">
                                        <div class="info-item">
                                            <i class="bi bi-calendar-date"></i>
                                            <span>{{ $event->date_debut->format('d/m/Y à H:i') }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-clock"></i>
                                            <span>{{ $event->duree }} min</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span>{{ Str::limit($event->lieu, 25) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-person"></i>
                                            <span>{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</span>
                                        </div>
                                    </div>

                                    <div class="event-footer">
                                        <div class="participants-count">
                                            <i class="bi bi-people me-1"></i>
                                            {{ $event->participants->count() }} participant(s)
                                        </div>

                                        <div>
                                            <a href="{{ route('events.show', $event) }}" class="action-btn primary" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                                                <a href="{{ route('events.edit', $event) }}" class="action-btn" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="action-btn" onclick="confirmDelete({{ $event->id }})" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h3>Aucun événement trouvé</h3>
                        <p class="text-muted mb-4">
                            Commencez par créer votre premier événement pour organiser vos activités ORMVAT.
                        </p>
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-lg me-2"></i>
                            Créer un Événement
                        </a>
                    </div>
                @endif
            </div>

            <!-- Calendar View -->
            <div id="calendar-view" class="{{ $view === 'calendar' ? '' : 'd-none' }}">
                <div class="calendar-container">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Filters -->
            <div class="sidebar-card">
                <h5 class="mb-3">
                    <i class="bi bi-funnel me-2"></i>
                    Filtres
                </h5>

                <form method="GET" action="{{ route('events.index') }}" id="filterForm">
                    <input type="hidden" name="view" value="{{ $view }}" id="viewInput">

                    <input type="text" name="search" value="{{ request('search') }}"
                           class="filter-input" placeholder="Rechercher...">

                    <select name="type" class="filter-input">
                        <option value="">Tous les types</option>
                        @foreach(App\Models\Event::$types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <select name="statut" class="filter-input">
                        <option value="">Tous les statuts</option>
                        @foreach(App\Models\Event::$statuts as $key => $label)
                            <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <select name="priorite" class="filter-input">
                        <option value="">Toutes les priorités</option>
                        @foreach(App\Models\Event::$priorites as $key => $label)
                            <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('events.index', ['view' => $view]) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-card">
                <h5 class="mb-3">
                    <i class="bi bi-lightning me-2"></i>
                    Actions Rapides
                </h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>
                        Créer Événement
                    </a>
                    <a href="{{ route('events.export') }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i>
                        Exporter CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="fab" onclick="window.location.href='{{ route('events.create') }}'" title="Créer un événement">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;

    // Initialize Calendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine'
            },
            events: '/events/calendar/data',
            eventClick: function(info) {
                window.location.href = '/events/' + info.event.id;
            },
            dateClick: function(info) {
                window.location.href = '/events/create?date=' + info.dateStr;
            }
        });

        calendar.render();
    }

    // View Switching
    window.switchView = function(view) {
        // Hide all views
        document.querySelectorAll('[id$="-view"]').forEach(function(el) {
            el.classList.add('d-none');
        });

        // Show selected view
        document.getElementById(view + '-view').classList.remove('d-none');

        // Update active tab
        document.querySelectorAll('.custom-tab').forEach(function(tab) {
            tab.classList.remove('active');
        });
        document.querySelector('[onclick="switchView(\'' + view + '\')"]').classList.add('active');

        // Update URL
        var url = new URL(window.location);
        url.searchParams.set('view', view);
        window.history.replaceState({}, '', url);

        // Update hidden input
        document.getElementById('viewInput').value = view;

        // Initialize calendar if needed
        if (view === 'calendar' && !calendar) {
            setTimeout(initCalendar, 100);
        }
    };

    // Delete Confirmation
    window.confirmDelete = function(eventId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
            var form = document.getElementById('deleteForm');
            form.action = '/events/' + eventId;
            form.submit();
        }
    };

    // Auto-submit filters on change
    document.querySelectorAll('#filterForm select').forEach(function(select) {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Initialize calendar if calendar view is active
    if (!document.getElementById('calendar-view').classList.contains('d-none')) {
        initCalendar();
    }
});
</script>
@endpush
