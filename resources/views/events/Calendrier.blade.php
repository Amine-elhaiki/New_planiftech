@extends('layouts.app')

@section('title', 'Calendrier des Événements')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .fc {
        max-width: 100%;
        margin: 0 auto;
    }

    .fc-toolbar {
        margin-bottom: 1.5rem !important;
    }

    .fc-toolbar-title {
        font-size: 1.75rem !important;
        font-weight: 600;
        color: #495057;
    }

    .fc-button {
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
        background: white !important;
        color: #495057 !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .fc-button:hover {
        background: #f8f9fa !important;
        border-color: #007bff !important;
        color: #007bff !important;
    }

    .fc-button-primary:not(:disabled).fc-button-active,
    .fc-button-primary:not(:disabled):active {
        background: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .fc-today-button {
        background: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }

    .fc-today-button:hover {
        background: #218838 !important;
        border-color: #1e7e34 !important;
    }

    .fc-daygrid-day {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }

    .fc-daygrid-day-top {
        padding: 8px;
    }

    .fc-event {
        border-radius: 4px;
        border: none !important;
        padding: 2px 6px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .fc-event-title {
        font-weight: 600;
    }

    .fc-more-link {
        color: #007bff;
        font-weight: 600;
        text-decoration: none;
    }

    .fc-more-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .calendar-legend {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
    }

    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid #dee2e6;
        text-align: center;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-intervention { color: #dc3545; }
    .stat-reunion { color: #007bff; }
    .stat-formation { color: #28a745; }
    .stat-visite { color: #fd7e14; }

    .mini-calendar {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .event-modal .modal-body {
        padding: 1.5rem;
    }

    .event-detail {
        margin-bottom: 1rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 6px;
        border-left: 4px solid #007bff;
    }

    .event-detail i {
        color: #007bff;
        width: 20px;
        margin-right: 0.5rem;
    }

    .upcoming-events {
        max-height: 400px;
        overflow-y: auto;
    }

    .event-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        border-left: 4px solid #007bff;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .event-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }

    .event-item.type-intervention { border-left-color: #dc3545; }
    .event-item.type-reunion { border-left-color: #007bff; }
    .event-item.type-formation { border-left-color: #28a745; }
    .event-item.type-visite { border-left-color: #fd7e14; }

    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
            gap: 1rem;
        }

        .calendar-legend {
            justify-content: center;
        }

        .legend-item {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                Calendrier des Événements
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble de tous vos événements planifiés</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Nouvel Événement
            </a>
            <a href="{{ route('events.index', ['view' => 'list']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i>
                Vue Liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Calendrier principal -->
        <div class="col-lg-9">
            <!-- Statistiques rapides -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-number stat-intervention">8</div>
                    <div>Interventions</div>
                    <small class="text-muted">Ce mois</small>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-reunion">12</div>
                    <div>Réunions</div>
                    <small class="text-muted">Ce mois</small>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-formation">5</div>
                    <div>Formations</div>
                    <small class="text-muted">Ce mois</small>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-visite">3</div>
                    <div>Visites</div>
                    <small class="text-muted">Ce mois</small>
                </div>
            </div>

            <!-- Légende des couleurs -->
            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #dc3545;"></div>
                    <span>Interventions</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #007bff;"></div>
                    <span>Réunions</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #28a745;"></div>
                    <span>Formations</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #fd7e14;"></div>
                    <span>Visites</span>
                </div>
            </div>

            <!-- Calendrier -->
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Mini calendrier de navigation -->
            <div class="mini-calendar">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-calendar-date text-primary me-2"></i>
                    Navigation Rapide
                </h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="goToToday()">
                        <i class="bi bi-calendar-check me-1"></i>
                        Aujourd'hui
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="goToWeek()">
                        <i class="bi bi-calendar-week me-1"></i>
                        Cette Semaine
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="goToMonth()">
                        <i class="bi bi-calendar-month me-1"></i>
                        Ce Mois
                    </button>
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
                <div class="card-body p-0">
                    <div class="upcoming-events">
                        <!-- Simulation d'événements à venir -->
                        <div class="event-item type-intervention" onclick="goToEvent(1)">
                            <div class="flex-grow-1">
                                <div class="fw-bold">Maintenance pompe A</div>
                                <small class="text-muted">Aujourd'hui 14:00</small>
                            </div>
                            <span class="badge bg-danger">Urgent</span>
                        </div>

                        <div class="event-item type-reunion" onclick="goToEvent(2)">
                            <div class="flex-grow-1">
                                <div class="fw-bold">Réunion équipe</div>
                                <small class="text-muted">Demain 09:00</small>
                            </div>
                            <span class="badge bg-primary">Réunion</span>
                        </div>

                        <div class="event-item type-formation" onclick="goToEvent(3)">
                            <div class="flex-grow-1">
                                <div class="fw-bold">Formation sécurité</div>
                                <small class="text-muted">Vendredi 10:00</small>
                            </div>
                            <span class="badge bg-success">Formation</span>
                        </div>

                        <div class="event-item type-visite" onclick="goToEvent(4)">
                            <div class="flex-grow-1">
                                <div class="fw-bold">Visite inspection</div>
                                <small class="text-muted">Lundi 08:00</small>
                            </div>
                            <span class="badge bg-warning">Visite</span>
                        </div>
                    </div>
                </div>
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
                        <button class="btn btn-outline-secondary btn-sm" onclick="exportCalendar()">
                            <i class="bi bi-download me-1"></i>
                            Exporter
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="printCalendar()">
                            <i class="bi bi-printer me-1"></i>
                            Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de détails d'événement -->
<div class="modal fade event-modal" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Détails de l'Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" class="btn btn-primary" id="eventModalEdit">Modifier</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création rapide -->
<div class="modal fade" id="quickCreateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Création Rapide d'Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickCreateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="quick_titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_type" class="form-label">Type</label>
                        <select class="form-select" id="quick_type" name="type" required>
                            <option value="intervention">Intervention</option>
                            <option value="reunion">Réunion</option>
                            <option value="formation">Formation</option>
                            <option value="visite">Visite</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_date" class="form-label">Date et heure</label>
                        <input type="datetime-local" class="form-control" id="quick_date" name="date_debut" required>
                    </div>
                    <input type="hidden" id="quick_date_clicked" name="date_clicked">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-outline-primary" onclick="goToFullForm()">Formulaire Complet</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Agenda'
        },
        views: {
            listWeek: {
                buttonText: 'Agenda'
            }
        },
        events: {
            url: '{{ route("events.calendar") }}',
            failure: function() {
                alert('Erreur lors du chargement des événements');
            }
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            showQuickCreate(info.dateStr);
        },
        eventMouseEnter: function(info) {
            // Tooltip au survol
            info.el.setAttribute('title',
                info.event.title + '\n' +
                info.event.extendedProps.lieu + '\n' +
                'Organisateur: ' + info.event.extendedProps.organisateur
            );
        },
        dayMaxEvents: 3, // Limiter le nombre d'événements affichés
        moreLinkClick: function(info) {
            calendar.changeView('listWeek', info.date);
        }
    });

    calendar.render();
});

// Afficher les détails d'un événement
function showEventDetails(event) {
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));

    document.getElementById('eventModalTitle').textContent = event.title;
    document.getElementById('eventModalEdit').href = `/events/${event.id}/edit`;

    const modalBody = document.getElementById('eventModalBody');
    modalBody.innerHTML = `
        <div class="event-detail">
            <i class="bi bi-calendar-date"></i>
            <strong>Date:</strong> ${event.start.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })}
        </div>

        <div class="event-detail">
            <i class="bi bi-clock"></i>
            <strong>Heure:</strong> ${event.start.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            })} - ${event.end.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            })}
        </div>

        <div class="event-detail">
            <i class="bi bi-geo-alt"></i>
            <strong>Lieu:</strong> ${event.extendedProps.lieu}
        </div>

        <div class="event-detail">
            <i class="bi bi-person"></i>
            <strong>Organisateur:</strong> ${event.extendedProps.organisateur}
        </div>

        <div class="event-detail">
            <i class="bi bi-tag"></i>
            <strong>Type:</strong> ${event.extendedProps.type}
        </div>

        <div class="event-detail">
            <i class="bi bi-flag"></i>
            <strong>Priorité:</strong> ${event.extendedProps.priorite}
        </div>

        <div class="event-detail">
            <i class="bi bi-info-circle"></i>
            <strong>Description:</strong> ${event.extendedProps.description}
        </div>

        <div class="mt-3">
            <a href="/events/${event.id}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i>
                Voir Détails Complets
            </a>
        </div>
    `;

    modal.show();
}

// Afficher le modal de création rapide
function showQuickCreate(dateStr) {
    const modal = new bootstrap.Modal(document.getElementById('quickCreateModal'));
    const dateInput = document.getElementById('quick_date');
    const dateClickedInput = document.getElementById('quick_date_clicked');

    // Définir la date cliquée
    dateClickedInput.value = dateStr;

    // Définir la date et heure par défaut (8h00 du jour cliqué)
    const date = new Date(dateStr + 'T08:00');
    dateInput.value = date.toISOString().slice(0, 16);

    modal.show();
}

// Aller au formulaire complet
function goToFullForm() {
    const dateClicked = document.getElementById('quick_date_clicked').value;
    window.location.href = `/events/create?date=${dateClicked}`;
}

// Navigation rapide
function goToToday() {
    calendar.today();
}

function goToWeek() {
    calendar.changeView('timeGridWeek');
}

function goToMonth() {
    calendar.changeView('dayGridMonth');
}

// Aller à un événement spécifique
function goToEvent(eventId) {
    window.location.href = `/events/${eventId}`;
}

// Exporter le calendrier
function exportCalendar() {
    window.location.href = '{{ route("events.export") }}';
}

// Imprimer le calendrier
function printCalendar() {
    window.print();
}

// Gestion du formulaire de création rapide
document.getElementById('quickCreateForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // Ajouter les champs requis
    data.description = 'Créé via création rapide';
    data.lieu = 'À définir';
    data.priorite = 'normale';
    data.date_fin = new Date(new Date(data.date_debut).getTime() + 60*60*1000).toISOString().slice(0, 16);

    // Simuler la création (dans un vrai projet, faire un appel AJAX)
    alert('Fonctionnalité de création rapide à implémenter avec AJAX');

    // Fermer le modal
    bootstrap.Modal.getInstance(document.getElementById('quickCreateModal')).hide();

    // Recharger le calendrier
    calendar.refetchEvents();
});

// Gestion responsive du calendrier
window.addEventListener('resize', function() {
    if (window.innerWidth < 768) {
        calendar.changeView('listWeek');
    } else {
        calendar.changeView('dayGridMonth');
    }
});
</script>
@endpush
