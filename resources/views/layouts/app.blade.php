<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PlanifTech ORMVAT - Système de gestion des interventions techniques">
    <meta name="author" content="ORMVAT">

    <title>@yield('title', 'PlanifTech') - ORMVAT</title>

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Styles personnalisés globaux -->
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
            --light-bg: #f8fafc;
            --dark-text: #1f2937;
            --muted-text: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --sidebar-width: 280px;
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            font-size: 14px;
            overflow-x: hidden;
        }

        /* Amélioration de la lisibilité */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.3;
            color: var(--dark-text);
        }

        p {
            margin-bottom: 1rem;
            color: var(--muted-text);
        }

        /* Boutons améliorés */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all var(--transition-fast);
            border: none;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-success:hover {
            box-shadow: var(--shadow-lg);
            color: white;
        }

        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Cards améliorées */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
            background: white;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Tables améliorées */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--muted-text);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
            background-color: #f9fafb;
        }

        .table td {
            border-top: 1px solid #f3f4f6;
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Alertes améliorées */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #166534;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border-left: 4px solid var(--warning-color);
        }

        .alert-info {
            background: linear-gradient(135deg, #cffafe, #a7f3d0);
            color: #0c4a6e;
            border-left: 4px solid var(--info-color);
        }

        /* Forms améliorées */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            transition: all var(--transition-fast);
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        /* Badges améliorés */
        .badge {
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            letter-spacing: 0.25px;
        }

        /* Animations globales */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        .scale-in {
            animation: scaleIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Loading states */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Utilities */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shadow-smooth {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .border-gradient {
            border: 1px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, var(--primary-color), #8b5cf6) border-box;
        }

        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            body {
                font-size: 13px;
            }

            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            .card-body {
                padding: 1rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        /* Print styles */
        @media print {
            .sidebar,
            .mobile-toggle,
            .btn,
            .alert {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }

            * {
                box-shadow: none !important;
            }
        }

        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .focus-visible:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Dark mode support (optionnel) */
        @media (prefers-color-scheme: dark) {
            :root {
                --light-bg: #1f2937;
                --dark-text: #f9fafb;
                --muted-text: #d1d5db;
                --border-color: #374151;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php
        // Préparation sécurisée de la configuration pour JavaScript
        $jsConfig = [
            'baseUrl' => url('/'),
            'csrfToken' => csrf_token(),
            'locale' => app()->getLocale(),
            'user' => null
        ];

        // Vérification multiple pour s'assurer que l'utilisateur existe
        if (auth()->check()) {
            $currentUser = auth()->user();
            if ($currentUser && isset($currentUser->prenom) && isset($currentUser->nom) && isset($currentUser->role)) {
                $jsConfig['user'] = [
                    'id' => $currentUser->id ?? 0,
                    'name' => ($currentUser->prenom ?? 'Utilisateur') . ' ' . ($currentUser->nom ?? ''),
                    'role' => $currentUser->role ?? 'user'
                ];
            }
        }
    @endphp

    <div id="app">
        <!-- Contenu principal -->
        @yield('content')

        <!-- Messages Flash -->
        @if(session('success'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-white bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-white bg-warning border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-white bg-info border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ session('info') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        @if($errors && $errors->any())
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show align-items-center text-white bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Erreurs détectées :</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100" style="background: rgba(255,255,255,0.9); z-index: 9998;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="text-muted">Chargement en cours...</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Configuration JavaScript (injectée en JSON sécurisé) -->
    <script id="app-config" type="application/json">
        {!! json_encode($jsConfig) !!}
    </script>
    <script>
        // Configuration globale (100% JavaScript valide)
        window.PlanifTech = window.PlanifTech || {};

        // Lecture de la configuration depuis le script JSON
        var configScript = document.getElementById('app-config');
        if (configScript) {
            try {
                window.PlanifTech.config = JSON.parse(configScript.textContent);
            } catch (e) {
                console.error('Erreur lors du parsing de la configuration:', e);
                window.PlanifTech.config = {
                    baseUrl: window.location.origin,
                    csrfToken: '',
                    locale: 'fr',
                    user: null
                };
            }
        } else {
            // Configuration par défaut si le script n'est pas trouvé
            window.PlanifTech.config = {
                baseUrl: window.location.origin,
                csrfToken: '',
                locale: 'fr',
                user: null
            };
        }

        // Utilitaires JavaScript
        window.PlanifTech.utils = {
            // Fonction pour afficher un toast
            showToast: function(message, type) {
                type = type || 'success';
                var toastContainer = document.querySelector('.toast-container') || this.createToastContainer();
                var toast = this.createToast(message, type);
                toastContainer.appendChild(toast);

                var bsToast = new bootstrap.Toast(toast);
                bsToast.show();

                // Supprimer le toast après qu'il soit caché
                toast.addEventListener('hidden.bs.toast', function() {
                    toast.remove();
                });
            },

            createToastContainer: function() {
                var container = document.createElement('div');
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
                return container;
            },

            createToast: function(message, type) {
                var icons = {
                    success: 'check-circle',
                    error: 'exclamation-triangle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                var colors = {
                    success: 'bg-success',
                    error: 'bg-danger',
                    warning: 'bg-warning',
                    info: 'bg-info'
                };

                var toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white ' + colors[type] + ' border-0';
                toast.setAttribute('role', 'alert');
                toast.innerHTML =
                    '<div class="d-flex">' +
                        '<div class="toast-body">' +
                            '<i class="bi bi-' + icons[type] + ' me-2"></i>' +
                            message +
                        '</div>' +
                        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                    '</div>';
                return toast;
            },

            // Fonction pour afficher le loading
            showLoading: function() {
                var overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.remove('d-none');
                }
            },

            hideLoading: function() {
                var overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.add('d-none');
                }
            },

            // Fonction pour formater les dates
            formatDate: function(date, options) {
                options = options || {};
                var defaultOptions = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                // Merge objects manually for compatibility
                var finalOptions = {};
                for (var key in defaultOptions) {
                    finalOptions[key] = defaultOptions[key];
                }
                for (var key in options) {
                    finalOptions[key] = options[key];
                }
                return new Date(date).toLocaleDateString('fr-FR', finalOptions);
            },

            // Fonction pour formater les nombres
            formatNumber: function(number, decimals) {
                decimals = decimals || 0;
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }).format(number);
            },

            // Fonction pour débouncer les appels
            debounce: function(func, wait, immediate) {
                var timeout;
                return function executedFunction() {
                    var context = this;
                    var args = arguments;
                    var later = function() {
                        timeout = null;
                        if (!immediate) func.apply(context, args);
                    };
                    var callNow = immediate && !timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                    if (callNow) func.apply(context, args);
                };
            },

            // Fonction pour confirmer une action
            confirm: function(message, callback) {
                if (confirm(message)) {
                    callback();
                }
            },

            // Fonction pour copier dans le presse-papiers
            copyToClipboard: function(text) {
                var self = this;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(function() {
                        self.showToast('Copié dans le presse-papiers !', 'success');
                    }).catch(function() {
                        self.showToast('Erreur lors de la copie', 'error');
                    });
                } else {
                    // Fallback pour les navigateurs plus anciens
                    var textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        self.showToast('Copié dans le presse-papiers !', 'success');
                    } catch (err) {
                        self.showToast('Erreur lors de la copie', 'error');
                    }
                    document.body.removeChild(textArea);
                }
            }
        };

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Masquer automatiquement les toasts après 5 secondes
            var toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(toast) {
                setTimeout(function() {
                    var bsToast = bootstrap.Toast.getInstance(toast);
                    if (bsToast) {
                        bsToast.hide();
                    }
                }, 5000);
            });

            // Ajouter des animations aux éléments qui apparaissent
            var observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observer les cartes et sections
            document.querySelectorAll('.card, .stat-card, section').forEach(function(el) {
                observer.observe(el);
            });

            // Masquer le loading initial
            setTimeout(function() {
                window.PlanifTech.utils.hideLoading();
            }, 500);
        });

        // Gestion des erreurs globales
        window.addEventListener('error', function(e) {
            console.error('Erreur JavaScript:', e.error);
            // En production, vous pourriez envoyer cette erreur à un service de monitoring
        });

        // Performance monitoring (optionnel)
        window.addEventListener('load', function() {
            if ('performance' in window && 'getEntriesByType' in performance) {
                var navigation = performance.getEntriesByType('navigation')[0];
                if (navigation) {
                    console.log('Temps de chargement:', Math.round(navigation.loadEventEnd - navigation.fetchStart), 'ms');
                }
            }
        });

        // Service Worker pour le cache (optionnel)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // navigator.registerServiceWorker('/sw.js');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
