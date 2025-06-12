<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanifTech ORMVAT - Gestion Améliorée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        :root {
            /* Palette de couleurs ORMVAT - Thème Agricole/Hydraulique */
            --primary-water: #2E86AB;        /* Bleu eau principale */
            --primary-water-light: #A8DCEF;  /* Bleu eau clair */
            --primary-water-dark: #1B5E7C;   /* Bleu eau foncé */

            --secondary-earth: #8B4513;      /* Terre/sol */
            --secondary-earth-light: #D4A574; /* Terre claire */

            --accent-agriculture: #4A7C59;   /* Vert agriculture */
            --accent-agriculture-light: #7FB285; /* Vert clair */
            --accent-agriculture-dark: #2F5233;  /* Vert foncé */

            --success-harvest: #22C55E;      /* Vert réussite */
            --warning-sun: #F59E0B;          /* Orange soleil */
            --danger-drought: #EF4444;       /* Rouge sécheresse */
            --info-sky: #06B6D4;            /* Bleu ciel */

            --neutral-light: #F8FAFC;        /* Gris très clair */
            --neutral: #64748B;              /* Gris moyen */
            --neutral-dark: #1E293B;         /* Gris foncé */

            --gradient-water: linear-gradient(135deg, var(--primary-water) 0%, var(--info-sky) 100%);
            --gradient-earth: linear-gradient(135deg, var(--secondary-earth) 0%, var(--secondary-earth-light) 100%);
            --gradient-agriculture: linear-gradient(135deg, var(--accent-agriculture) 0%, var(--accent-agriculture-light) 100%);

            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-strong: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

            --border-radius-sm: 6px;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --border-radius-xl: 24px;
        }

        body {
            background: linear-gradient(135deg, #F0F9FF 0%, #F8FAFC 50%, #F0FDF4 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }

        /* Header ORMVAT */
        .ormvat-header {
            background: var(--gradient-water);
            color: white;
            padding: 2rem 0;
            border-radius: 0 0 var(--border-radius-xl) var(--border-radius-xl);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .ormvat-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .ormvat-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .ormvat-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .ormvat-header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
        }

        /* Navigation Tabs Améliorées */
        .nav-tabs-ormvat {
            border: none;
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 0.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
        }

        .nav-tabs-ormvat .nav-link {
            border: none;
            border-radius: var(--border-radius);
            color: var(--neutral);
            font-weight: 500;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-tabs-ormvat .nav-link:hover {
            background: var(--primary-water-light);
            color: var(--primary-water-dark);
            transform: translateY(-2px);
        }

        .nav-tabs-ormvat .nav-link.active {
            background: var(--gradient-water);
            color: white;
            box-shadow: var(--shadow-medium);
        }

        /* Cards de Tâches Améliorées */
        .task-card-enhanced {
            background: white;
            border-radius: var(--border-radius-lg);
            border: none;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .task-card-enhanced:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-strong);
        }

        .task-card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-agriculture);
        }

        .task-card-enhanced.priority-haute::before {
            background: linear-gradient(180deg, var(--danger-drought) 0%, var(--warning-sun) 100%);
        }

        .task-card-enhanced.priority-moyenne::before {
            background: linear-gradient(180deg, var(--warning-sun) 0%, var(--secondary-earth-light) 100%);
        }

        .task-card-enhanced.priority-basse::before {
            background: linear-gradient(180deg, var(--accent-agriculture) 0%, var(--success-harvest) 100%);
        }

        .task-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }

        .task-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--neutral-dark);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .task-meta {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .task-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .task-badge.status-a_faire {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-sun);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .task-badge.status-en_cours {
            background: rgba(46, 134, 171, 0.1);
            color: var(--primary-water);
            border: 1px solid rgba(46, 134, 171, 0.2);
        }

        .task-badge.status-termine {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success-harvest);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .task-progress {
            padding: 0 1.5rem 1.5rem 1.5rem;
        }

        .progress-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .progress-bar-custom {
            height: 8px;
            border-radius: 10px;
            background: var(--neutral-light);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-agriculture);
            border-radius: 10px;
            transition: width 0.5s ease;
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .task-assignee {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 1.5rem 1.5rem 1.5rem;
        }

        .avatar-enhanced {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-water);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-soft);
        }

        /* Calendrier Amélioré */
        .calendar-container-enhanced {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-medium);
            margin-bottom: 2rem;
        }

        .fc-toolbar-title {
            color: var(--neutral-dark) !important;
            font-weight: 700 !important;
            font-size: 1.8rem !important;
        }

        .fc-button-primary {
            background: var(--gradient-water) !important;
            border: none !important;
            border-radius: var(--border-radius-sm) !important;
            padding: 0.5rem 1rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .fc-button-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: var(--shadow-medium) !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active {
            background: var(--primary-water-dark) !important;
        }

        .fc-today-button {
            background: var(--gradient-agriculture) !important;
        }

        .fc-daygrid-day {
            transition: background-color 0.2s ease;
        }

        .fc-daygrid-day:hover {
            background-color: var(--primary-water-light) !important;
        }

        .fc-event {
            border: none !important;
            border-radius: var(--border-radius-sm) !important;
            padding: 0.25rem 0.5rem !important;
            font-weight: 500 !important;
            box-shadow: var(--shadow-soft) !important;
            transition: all 0.2s ease !important;
        }

        .fc-event:hover {
            transform: translateY(-1px) !important;
            box-shadow: var(--shadow-medium) !important;
        }

        .fc-event.intervention {
            background: linear-gradient(135deg, var(--danger-drought) 0%, #FF6B6B 100%) !important;
        }

        .fc-event.reunion {
            background: var(--gradient-water) !important;
        }

        .fc-event.formation {
            background: var(--gradient-agriculture) !important;
        }

        .fc-event.visite {
            background: var(--gradient-earth) !important;
        }

        /* Événements Cards */
        .event-card-enhanced {
            background: white;
            border-radius: var(--border-radius-lg);
            border: none;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .event-card-enhanced:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-strong);
        }

        .event-type-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .event-type-indicator.intervention {
            background: linear-gradient(90deg, var(--danger-drought) 0%, #FF6B6B 100%);
        }

        .event-type-indicator.reunion {
            background: var(--gradient-water);
        }

        .event-type-indicator.formation {
            background: var(--gradient-agriculture);
        }

        .event-type-indicator.visite {
            background: var(--gradient-earth);
        }

        .event-icon-enhanced {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 1rem;
            box-shadow: var(--shadow-soft);
        }

        .event-icon-enhanced.intervention {
            background: linear-gradient(135deg, var(--danger-drought) 0%, #FF6B6B 100%);
        }

        .event-icon-enhanced.reunion {
            background: var(--gradient-water);
        }

        .event-icon-enhanced.formation {
            background: var(--gradient-agriculture);
        }

        .event-icon-enhanced.visite {
            background: var(--gradient-earth);
        }

        /* Statistiques Dashboard */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card-enhanced {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-enhanced:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-strong);
        }

        .stat-card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-water);
        }

        .stat-card-enhanced.tasks::before {
            background: var(--gradient-agriculture);
        }

        .stat-card-enhanced.events::before {
            background: var(--gradient-water);
        }

        .stat-card-enhanced.projects::before {
            background: var(--gradient-earth);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--neutral-dark);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--neutral);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .stat-trend.positive {
            color: var(--success-harvest);
        }

        .stat-trend.negative {
            color: var(--danger-drought);
        }

        /* Filtres Améliorés */
        .filters-enhanced {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-control-enhanced, .form-select-enhanced {
            border: 2px solid transparent;
            background: var(--neutral-light);
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control-enhanced:focus, .form-select-enhanced:focus {
            border-color: var(--primary-water);
            background: white;
            box-shadow: 0 0 0 3px rgba(46, 134, 171, 0.1);
            outline: none;
        }

        .btn-filter {
            background: var(--gradient-water);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }

        /* Actions rapides */
        .quick-actions {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }

        .fab-enhanced {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-agriculture);
            color: white;
            border: none;
            font-size: 1.5rem;
            box-shadow: var(--shadow-strong);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fab-enhanced:hover {
            transform: scale(1.1);
            box-shadow: 0 20px 30px -5px rgba(0, 0, 0, 0.2);
        }

        .fab-enhanced.secondary {
            background: var(--gradient-water);
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ormvat-header h1 {
                font-size: 2rem;
            }

            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .quick-actions {
                bottom: 1rem;
                right: 1rem;
            }

            .task-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Thème sombre pour sections spéciales */
        .dark-section {
            background: var(--neutral-dark);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin: 2rem 0;
        }

        .dark-section h3 {
            color: var(--primary-water-light);
        }
    </style>
</head>
<body>
    <!-- Header ORMVAT -->
    <div class="ormvat-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-water me-3"></i>PlanifTech ORMVAT</h1>
                    <p class="subtitle">Gestion Intelligente des Interventions Hydrauliques et Agricoles</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-light"><i class="bi bi-person me-2"></i>Mon Profil</button>
                        <button class="btn btn-outline-light"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <!-- Navigation Améliorée -->
        <ul class="nav nav-tabs nav-tabs-ormvat" id="mainTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#dashboard-tab">
                    <i class="bi bi-speedometer2 me-2"></i>Tableau de Bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tasks-tab">
                    <i class="bi bi-check2-square me-2"></i>Tâches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#events-tab">
                    <i class="bi bi-calendar3 me-2"></i>Événements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#calendar-tab">
                    <i class="bi bi-calendar-month me-2"></i>Calendrier
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard-tab">
                <!-- Statistiques -->
                <div class="stats-grid animate-fade-in">
                    <div class="stat-card-enhanced tasks">
                        <div class="stat-number">24</div>
                        <div class="stat-label">Tâches Actives</div>
                        <div class="stat-trend positive">
                            <i class="bi bi-arrow-up"></i>+12% cette semaine
                        </div>
                    </div>
                    <div class="stat-card-enhanced events">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Événements Prévus</div>
                        <div class="stat-trend positive">
                            <i class="bi bi-arrow-up"></i>+3 nouveaux
                        </div>
                    </div>
                    <div class="stat-card-enhanced projects">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Projets en Cours</div>
                        <div class="stat-trend negative">
                            <i class="bi bi-arrow-down"></i>-1 complété
                        </div>
                    </div>
                    <div class="stat-card-enhanced">
                        <div class="stat-number">87%</div>
                        <div class="stat-label">Taux de Réussite</div>
                        <div class="stat-trend positive">
                            <i class="bi bi-arrow-up"></i>+5% ce mois
                        </div>
                    </div>
                </div>

                <!-- Aperçu Rapide -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="task-card-enhanced priority-haute animate-fade-in">
                            <div class="task-header">
                                <h4 class="task-title">Réparation Urgente - Canal Principal B4</h4>
                                <div class="task-meta">
                                    <span class="task-badge status-en_cours">En Cours</span>
                                    <span class="badge bg-danger">Haute Priorité</span>
                                    <span class="badge bg-warning">En Retard</span>
                                </div>
                                <p class="text-muted mb-0">Fuite importante détectée sur le canal principal du secteur B4. Intervention immédiate requise pour éviter la perte d'eau.</p>
                            </div>
                            <div class="task-progress">
                                <div class="progress-container">
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 65%"></div>
                                    </div>
                                    <small class="text-muted">65% terminé</small>
                                </div>
                            </div>
                            <div class="task-assignee">
                                <div class="avatar-enhanced">MB</div>
                                <div>
                                    <div class="fw-bold">Mohamed Benali</div>
                                    <small class="text-muted">Technicien Hydraulique</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="event-card-enhanced animate-fade-in" style="animation-delay: 0.1s">
                            <div class="event-type-indicator intervention"></div>
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="event-icon-enhanced intervention">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Maintenance Préventive - Station P12</h5>
                                        <p class="card-text text-muted">Contrôle et entretien de la station de pompage P12 selon le planning annuel.</p>
                                        <div class="d-flex gap-2 mb-2">
                                            <span class="badge bg-primary">Intervention</span>
                                            <span class="badge bg-info">Planifié</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <span>Demain 08:00 - 12:00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Tab -->
            <div class="tab-pane fade" id="tasks-tab">
                <!-- Filtres Tâches -->
                <div class="filters-enhanced">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filtrer les Tâches</h6>
                    <div class="filter-group">
                        <div>
                            <label class="form-label">Recherche</label>
                            <input type="text" class="form-control-enhanced" placeholder="Rechercher une tâche...">
                        </div>
                        <div>
                            <label class="form-label">Statut</label>
                            <select class="form-select-enhanced">
                                <option>Tous les statuts</option>
                                <option>À faire</option>
                                <option>En cours</option>
                                <option>Terminé</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Priorité</label>
                            <select class="form-select-enhanced">
                                <option>Toutes priorités</option>
                                <option>Haute</option>
                                <option>Moyenne</option>
                                <option>Basse</option>
                            </select>
                        </div>
                        <div>
                            <button class="btn btn-filter">
                                <i class="bi bi-search me-2"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste des Tâches -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="task-card-enhanced priority-haute animate-fade-in">
                            <div class="task-header">
                                <h4 class="task-title">Inspection Canal Secteur A3</h4>
                                <div class="task-meta">
                                    <span class="task-badge status-a_faire">À Faire</span>
                                    <span class="badge bg-danger">Haute</span>
                                </div>
                                <p class="text-muted mb-0">Vérification complète de l'état du canal d'irrigation du secteur A3 suite aux récentes précipitations.</p>
                            </div>
                            <div class="task-progress">
                                <div class="progress-container">
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">0% terminé</small>
                                </div>
                            </div>
                            <div class="task-assignee">
                                <div class="avatar-enhanced">AH</div>
                                <div>
                                    <div class="fw-bold">Ahmed Hassan</div>
                                    <small class="text-muted">Technicien Terrain</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="task-card-enhanced priority-moyenne animate-fade-in" style="animation-delay: 0.1s">
                            <div class="task-header">
                                <h4 class="task-title">Relevé Compteurs Zone Est</h4>
                                <div class="task-meta">
                                    <span class="task-badge status-en_cours">En Cours</span>
                                    <span class="badge bg-warning">Moyenne</span>
                                </div>
                                <p class="text-muted mb-0">Collecte mensuelle des données de consommation d'eau des exploitations agricoles de la zone Est.</p>
                            </div>
                            <div class="task-progress">
                                <div class="progress-container">
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" style="width: 45%"></div>
                                    </div>
                                    <small class="text-muted">45% terminé</small>
                                </div>
                            </div>
                            <div class="task-assignee">
                                <div class="avatar-enhanced">FK</div>
                                <div>
                                    <div class="fw-bold">Fatima Khemir</div>
                                    <small class="text-muted">Technicienne Mesures</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Tab -->
            <div class="tab-pane fade" id="events-tab">
                <!-- Filtres Événements -->
                <div class="filters-enhanced">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filtrer les Événements</h6>
                    <div class="filter-group">
                        <div>
                            <label class="form-label">Type</label>
                            <select class="form-select-enhanced">
                                <option>Tous les types</option>
                                <option>Intervention</option>
                                <option>Réunion</option>
                                <option>Formation</option>
                                <option>Visite</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Période</label>
                            <select class="form-select-enhanced">
                                <option>Cette semaine</option>
                                <option>Ce mois</option>
                                <option>Prochains 3 mois</option>
                            </select>
                        </div>
                        <div>
                            <button class="btn btn-filter">
                                <i class="bi bi-search me-2"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste des Événements -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="event-card-enhanced animate-fade-in">
                            <div class="event-type-indicator reunion"></div>
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="event-icon-enhanced reunion">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Réunion Équipe Technique</h5>
                                        <p class="card-text text-muted">Point mensuel sur l'avancement des projets et planification des interventions.</p>
                                        <div class="d-flex gap-2 mb-2">
                                            <span class="badge bg-primary">Réunion</span>
                                            <span class="badge bg-success">Confirmé</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted mb-2">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <span>Vendredi 15 Mars, 09:00 - 11:00</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-geo-alt me-2"></i>
                                            <span>Salle de réunion principale</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="event-card-enhanced animate-fade-in" style="animation-delay: 0.1s">
                            <div class="event-type-indicator formation"></div>
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="event-icon-enhanced formation">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title">Formation Sécurité</h5>
                                        <p class="card-text text-muted">Session de formation sur les nouvelles normes de sécurité pour les interventions terrain.</p>
                                        <div class="d-flex gap-2 mb-2">
                                            <span class="badge bg-success">Formation</span>
                                            <span class="badge bg-warning">Planifié</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted mb-2">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <span>Lundi 18 Mars, 14:00 - 17:00</span>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-geo-alt me-2"></i>
                                            <span>Centre de formation</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Tab -->
            <div class="tab-pane fade" id="calendar-tab">
                <div class="calendar-container-enhanced">
                    <div id="calendar-enhanced"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides Flottantes -->
    <div class="quick-actions">
        <button class="fab-enhanced secondary" title="Nouveau Rapport">
            <i class="bi bi-file-earmark-plus"></i>
        </button>
        <button class="fab-enhanced secondary" title="Nouvel Événement">
            <i class="bi bi-calendar-plus"></i>
        </button>
        <button class="fab-enhanced" title="Nouvelle Tâche">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <!-- Section Informative -->
    <div class="container-fluid px-4 mt-5">
        <div class="dark-section">
            <div class="row">
                <div class="col-md-8">
                    <h3><i class="bi bi-info-circle me-2"></i>Système PlanifTech ORMVAT</h3>
                    <p>Application de gestion intelligente des interventions hydrauliques et agricoles développée spécifiquement pour les besoins de l'Office Régional de Mise en Valeur Agricole du Tadla.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check2 me-2 text-success"></i>Gestion centralisée des tâches et événements</li>
                        <li><i class="bi bi-check2 me-2 text-success"></i>Suivi en temps réel des interventions terrain</li>
                        <li><i class="bi bi-check2 me-2 text-success"></i>Interface adaptée aux besoins spécifiques ORMVAT</li>
                        <li><i class="bi bi-check2 me-2 text-success"></i>Optimisation des ressources hydrauliques et agricoles</li>
                    </ul>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4">
                        <i class="bi bi-water" style="font-size: 4rem; color: var(--primary-water-light);"></i>
                        <h4 class="mt-3">Innovation Hydraulique</h4>
                        <p>Au service de l'agriculture marocaine</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation du calendrier amélioré
            const calendarEl = document.getElementById('calendar-enhanced');

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
                    events: [
                        {
                            title: 'Maintenance Station P12',
                            start: '2024-03-15T08:00:00',
                            end: '2024-03-15T12:00:00',
                            className: 'intervention',
                            description: 'Maintenance préventive'
                        },
                        {
                            title: 'Réunion Équipe',
                            start: '2024-03-15T09:00:00',
                            end: '2024-03-15T11:00:00',
                            className: 'reunion',
                            description: 'Point mensuel'
                        },
                        {
                            title: 'Formation Sécurité',
                            start: '2024-03-18T14:00:00',
                            end: '2024-03-18T17:00:00',
                            className: 'formation',
                            description: 'Nouvelles normes'
                        },
                        {
                            title: 'Visite Inspection Zone A',
                            start: '2024-03-20T10:00:00',
                            end: '2024-03-20T16:00:00',
                            className: 'visite',
                            description: 'Contrôle annuel'
                        }
                    ],
                    eventClick: function(info) {
                        alert('Événement: ' + info.event.title + '\n' + info.event.extendedProps.description);
                    },
                    dateClick: function(info) {
                        alert('Date sélectionnée: ' + info.dateStr);
                    },
                    eventDidMount: function(info) {
                        info.el.setAttribute('title', info.event.title + ' - ' + (info.event.extendedProps.description || ''));
                    }
                });

                calendar.render();
            }

            // Animation des cartes au scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.task-card-enhanced, .event-card-enhanced, .stat-card-enhanced').forEach(card => {
                observer.observe(card);
            });

            // Gestion des actions rapides
            document.querySelectorAll('.fab-enhanced').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('title');
                    alert('Action: ' + action);
                });
            });

            // Filtres interactifs
            document.querySelectorAll('.form-select-enhanced').forEach(select => {
                select.addEventListener('change', function() {
                    console.log('Filtre changé:', this.value);
                    // Ici vous ajouteriez la logique de filtrage
                });
            });

            // Recherche en temps réel
            document.querySelectorAll('.form-control-enhanced[placeholder*="Rechercher"]').forEach(input => {
                let searchTimeout;
                input.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        console.log('Recherche:', this.value);
                        // Ici vous ajouteriez la logique de recherche
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>
