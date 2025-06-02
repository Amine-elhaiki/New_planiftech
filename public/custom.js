/**
 * ============================================
 * PLANIFTECH ORMVAT - SCRIPTS PERSONNALISÉS
 * ============================================
 */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {

    // Initialiser les fonctionnalités
    initSidebar();
    initAnimations();
    initTooltips();
    initFormValidation();
    initTableFeatures();

});

/**
 * ============================================
 * GESTION DE LA SIDEBAR
 * ============================================
 */

function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.mobile-toggle');

    if (!sidebar || !toggleBtn) return;

    // Toggle sidebar sur mobile
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
    });

    // Fermer la sidebar en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('open');
        }
    });

    // Marquer l'élément actif dans la navigation
    markActiveNavItem();
}

function markActiveNavItem() {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.split('/').pop())) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

/**
 * ============================================
 * ANIMATIONS
 * ============================================
 */

function initAnimations() {
    // Animation des cartes de statistiques
    animateStatsCards();

    // Animation des tableaux
    animateTables();

    // Animation au scroll
    initScrollAnimations();
}

function animateStatsCards() {
    const statCards = document.querySelectorAll('.stat-card');

    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function animateTables() {
    setTimeout(() => {
        const tables = document.querySelectorAll('.data-table, .table-responsive');

        tables.forEach((table, index) => {
            table.style.opacity = '0';
            table.style.transform = 'translateY(20px)';
            table.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                table.style.opacity = '1';
                table.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    }, 400);
}

function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);

    // Observer les éléments animables
    document.querySelectorAll('.card, .alert, .table-responsive').forEach(el => {
        observer.observe(el);
    });
}

/**
 * ============================================
 * TOOLTIPS ET POPOVERS
 * ============================================
 */

function initTooltips() {
    // Initialiser les tooltips Bootstrap
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialiser les popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
}

/**
 * ============================================
 * VALIDATION DES FORMULAIRES
 * ============================================
 */

function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                // Focuser sur le premier champ invalide
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }

            form.classList.add('was-validated');
        });

        // Validation en temps réel
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }
            });
        });
    });
}

/**
 * ============================================
 * FONCTIONNALITÉS DES TABLEAUX
 * ============================================
 */

function initTableFeatures() {
    // Ajouter la fonctionnalité de tri
    addTableSorting();

    // Ajouter la recherche dans les tableaux
    addTableSearch();

    // Responsive tables
    makeTablesResponsive();
}

function addTableSorting() {
    const sortableHeaders = document.querySelectorAll('th[data-sortable]');

    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.innerHTML += ' <i class="bi bi-arrow-down-up ms-1"></i>';

        header.addEventListener('click', function() {
            sortTable(header);
        });
    });
}

function sortTable(header) {
    const table = header.closest('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const currentSort = header.dataset.sort || 'asc';
    const newSort = currentSort === 'asc' ? 'desc' : 'asc';

    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();

        if (newSort === 'asc') {
            return aValue.localeCompare(bValue, 'fr', { numeric: true });
        } else {
            return bValue.localeCompare(aValue, 'fr', { numeric: true });
        }
    });

    // Réorganiser les lignes
    rows.forEach(row => tbody.appendChild(row));

    // Mettre à jour l'attribut de tri
    header.dataset.sort = newSort;

    // Mettre à jour l'icône
    const icon = header.querySelector('i');
    if (icon) {
        icon.className = newSort === 'asc' ? 'bi bi-arrow-up ms-1' : 'bi bi-arrow-down ms-1';
    }
}

function addTableSearch() {
    const searchInputs = document.querySelectorAll('[data-table-search]');

    searchInputs.forEach(input => {
        const tableId = input.dataset.tableSearch;
        const table = document.getElementById(tableId);

        if (table) {
            input.addEventListener('input', function() {
                filterTable(table, input.value);
            });
        }
    });
}

function filterTable(table, searchTerm) {
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matches = text.includes(searchTerm.toLowerCase());
        row.style.display = matches ? '' : 'none';
    });
}

function makeTablesResponsive() {
    const tables = document.querySelectorAll('table:not(.table-responsive table)');

    tables.forEach(table => {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

/**
 * ============================================
 * UTILITAIRES
 * ============================================
 */

// Formater les dates
function formatDate(date, format = 'dd/mm/yyyy') {
    if (!(date instanceof Date)) {
        date = new Date(date);
    }

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year);
}

// Afficher des notifications toast
function showToast(message, type = 'info') {
    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Ajouter au container
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }

    container.appendChild(toast);

    // Afficher le toast
    if (typeof bootstrap !== 'undefined') {
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Supprimer après affichage
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
}

// Confirmer une action
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Copier du texte dans le presse-papiers
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Texte copié dans le presse-papiers', 'success');
    } catch (err) {
        console.error('Erreur lors de la copie:', err);
        showToast('Erreur lors de la copie', 'danger');
    }
}

/**
 * ============================================
 * EXPORTS GLOBAUX
 * ============================================
 */

// Rendre les fonctions disponibles globalement
window.PlanifTech = {
    showToast,
    confirmAction,
    copyToClipboard,
    formatDate,
    toggleSidebar: function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    }
};
