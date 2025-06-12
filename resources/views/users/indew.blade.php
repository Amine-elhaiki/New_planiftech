{{-- Corrections pour users/index.blade.php --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration globale
    var config = {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        baseUrl: window.location.origin
    };

    // Initialisation des modals
    var modals = {};

    // CORRECTION: Initialisation correcte des modals Bootstrap
    var modalElements = ['createUserModal', 'editUserModal', 'deleteUserModal'];
    modalElements.forEach(function(modalId) {
        var modalEl = document.getElementById(modalId);
        if (modalEl && typeof bootstrap !== 'undefined') {
            modals[modalId] = new bootstrap.Modal(modalEl);
        }
    });

    // Gestion des événements de clic
    document.addEventListener('click', function(e) {
        var target = e.target.closest('[data-action]');
        if (!target) return;

        var action = target.getAttribute('data-action');
        var userId = target.getAttribute('data-user-id');

        // CORRIGÉ: Gestion correcte des actions avec paramètres
        handleUserAction(action, userId);
    });

    // Recherche avec délai
    initializeSearch();

    // Filtres automatiques
    initializeFilters();
});

// CORRIGÉ: Fonction avec paramètres correctement définis
function handleUserAction(action, userId) {
    switch (action) {
        case 'edit':
            if (userId) {
                openEditModal(userId);
            }
            break;
        case 'delete':
            if (userId) {
                confirmDeleteUser(userId);
            }
            break;
        case 'toggle-status':
            if (userId) {
                toggleUserStatus(userId);
            }
            break;
        case 'reset-password':
            if (userId) {
                resetUserPassword(userId);
            }
            break;
        default:
            console.log('Action non reconnue:', action);
    }
}

function openEditModal(userId) {
    console.log('Ouverture du modal d\'édition pour l\'utilisateur:', userId);

    // Charger les données de l'utilisateur
    fetch('/admin/users/' + userId + '/edit', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            populateEditForm(data.user);
            var editModal = getModal('editUserModal');
            if (editModal) {
                editModal.show();
            }
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement des données', 'error');
    });
}

// CORRIGÉ: Fonction de confirmation avec gestion d'erreurs
function confirmDeleteUser(userId) {
    if (!userId) {
        console.error('ID utilisateur manquant');
        return;
    }

    var confirmMessage = 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?\n\nCette action est irréversible.';

    if (confirm(confirmMessage)) {
        deleteUser(userId);
    }
}

function deleteUser(userId) {
    fetch('/admin/users/' + userId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showNotification('Utilisateur supprimé avec succès', 'success');
            // Supprimer la ligne du tableau
            var userRow = document.querySelector('[data-user-id="' + userId + '"]').closest('tr');
            if (userRow) {
                userRow.remove();
            }
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression', 'error');
    });
}

function toggleUserStatus(userId) {
    fetch('/admin/users/' + userId + '/toggle-status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showNotification('Statut mis à jour avec succès', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour', 'error');
    });
}

function resetUserPassword(userId) {
    if (confirm('Réinitialiser le mot de passe de cet utilisateur ?')) {
        fetch('/admin/users/' + userId + '/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showNotification('Mot de passe réinitialisé', 'success');
            } else {
                showNotification('Erreur lors de la réinitialisation', 'error');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la réinitialisation', 'error');
        });
    }
}

// Fonctions utilitaires
function getCSRFToken() {
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? tokenMeta.getAttribute('content') : '';
}

function getModal(modalId) {
    var modalEl = document.getElementById(modalId);
    return modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
}

function populateEditForm(user) {
    if (!user) return;

    var form = document.getElementById('editUserForm');
    if (!form) return;

    // Remplir les champs du formulaire
    var fields = ['prenom', 'nom', 'email', 'telephone', 'role', 'statut'];
    fields.forEach(function(field) {
        var input = form.querySelector('[name="' + field + '"]');
        if (input && user[field] !== undefined) {
            input.value = user[field];
        }
    });

    // Mettre à jour l'action du formulaire
    form.action = '/admin/users/' + user.id;
}

function initializeSearch() {
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
}

function initializeFilters() {
    var filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            var form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
}

function showNotification(message, type) {
    type = type || 'success';
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';

    var notification = document.createElement('div');
    notification.className = 'alert ' + alertClass + ' alert-dismissible fade show';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';

    notification.innerHTML =
        '<i class="bi ' + iconClass + ' me-2"></i>' +
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
