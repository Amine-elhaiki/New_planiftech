{{-- Corrections pour projects/index.blade.php --}}

@push('styles')
<style>
    /* CORRECTION CSS - Ligne 445 approximative */
    .project-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* CORRIGÉ: valeur correcte */
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .project-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .project-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .status-active {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-completed {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    /* Autres styles... */
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CORRECTION JavaScript - Ligne 476 approximative

    // Configuration des modals
    var modals = {
        create: null,
        edit: null,
        delete: null
    };

    // Initialisation des modals Bootstrap
    if (typeof bootstrap !== 'undefined') {
        var createModalEl = document.getElementById('createProjectModal');
        var editModalEl = document.getElementById('editProjectModal');
        var deleteModalEl = document.getElementById('deleteProjectModal');

        if (createModalEl) modals.create = new bootstrap.Modal(createModalEl);
        if (editModalEl) modals.edit = new bootstrap.Modal(editModalEl);
        if (deleteModalEl) modals.delete = new bootstrap.Modal(deleteModalEl);
    }

    // Gestion des événements
    document.addEventListener('click', function(e) {
        var target = e.target.closest('[data-action]');
        if (!target) return;

        var action = target.getAttribute('data-action');
        var projectId = target.getAttribute('data-project-id');

        // CORRIGÉ: switch case avec syntaxe correcte
        switch (action) {
            case 'edit':
                if (projectId && modals.edit) {
                    openEditModal(projectId);
                }
                break;
            case 'delete':
                if (projectId) {
                    confirmDelete(projectId);
                }
                break;
            case 'view':
                if (projectId) {
                    window.location.href = '/projects/' + projectId;
                }
                break;
            default:
                console.log('Action non reconnue:', action);
        }
    });

    // Recherche avec délai
    var searchInput = document.querySelector('[name="search"]');
    if (searchInput) {
        var searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            var form = this.form;
            searchTimeout = setTimeout(function() {
                if (form) {
                    form.submit();
                }
            }, 500);
        });
    }
});

// CORRIGÉ: Fonction avec paramètres correctement définis
function openEditModal(projectId) {
    console.log('Ouverture du modal d\'édition pour le projet:', projectId);
    // Logique pour charger les données du projet
}

function confirmDelete(projectId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
        // CORRIGÉ: Création correcte du formulaire
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/projects/' + projectId;

        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        var methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function showNotification(message, type) {
    type = type || 'success';
    var notification = document.createElement('div');
    notification.className = 'alert alert-' + type + ' alert-dismissible fade show';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.innerHTML =
        '<i class="bi bi-check-circle me-2"></i>' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    document.body.appendChild(notification);

    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush
