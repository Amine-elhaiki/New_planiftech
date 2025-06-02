// REMPLACER LA SECTION @push('scripts') PAR CECI :

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
            events: '/events/calendar',  // ✅ URL STATIQUE - LIGNE 263 CORRIGÉE
            eventClick: function(info) {
                window.location.href = '/events/' + info.event.id;
            },
            dateClick: function(info) {
                window.location.href = '/events/create?date=' + info.dateStr;
            }
        });

        calendar.render();
    }

    // Gestion des onglets avec URL
    var tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
    for (var i = 0; i < tabElements.length; i++) {
        tabElements[i].addEventListener('shown.bs.tab', function(e) {
            var view = e.target.id === 'calendar-tab' ? 'calendar' : 'list';
            var url = new URL(window.location);
            url.searchParams.set('view', view);
            window.history.replaceState({}, '', url);
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
</script>
@endpush
