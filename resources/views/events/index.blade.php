@extends('layouts.app')

@section('title', 'Événements - Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);

        --shadow-sm: 0 2px 4px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --shadow-xl: 0 25px 50px rgba(0,0,0,0.15);

        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 24px;

        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header Hero Section */
    .hero-section {
        background: var(--primary-gradient);
        border-radius: var(--radius-xl);
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        color: white;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite reverse;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1.5rem;
    }

    .hero-stat {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        padding: 1.5rem 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: var(--transition);
    }

    .hero-stat:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .hero-stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: block;
    }

    .hero-stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Navigation Tabs */
    .custom-tabs {
        background: white;
        border-radius: var(--radius-lg);
        padding: 0.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        display: flex;
        gap: 0.5rem;
    }

    .custom-tab {
        flex: 1;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        border: none;
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .custom-tab.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: var(--shadow-md);
        transform: scale(1.02);
    }

    .custom-tab:hover:not(.active) {
        background: #f3f4f6;
        color: #374151;
    }

    /* Main Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 2rem;
    }

    /* Event Cards Grid */
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
    }

    .event-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border: 1px solid #e5e7eb;
        position: relative;
    }

    .event-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
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
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .type-intervention {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }

    .type-reunion {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #2563eb;
    }

    .type-formation {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }

    .type-visite {
        background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        color: #ea580c;
    }

    .event-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        margin-right: 5rem;
    }

    .event-description {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .event-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .priority-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .priority-normale { background: #6b7280; }
    .priority-haute { background: #f59e0b; }
    .priority-urgente { background: #ef4444; }

    .status-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
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
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        padding: 1.5rem;
        background: #f9fafb;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .info-icon {
        width: 16px;
        height: 16px;
        color: #6b7280;
        flex-shrink: 0;
    }

    .event-footer {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .participants-preview {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }

    .participant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: -8px;
        border: 2px solid white;
        position: relative;
        z-index: 1;
    }

    .participant-avatar:first-child {
        margin-left: 0;
    }

    .participants-count {
        font-size: 0.875rem;
        color: #6b7280;
        margin-left: 0.5rem;
    }

    .event-actions {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        border: none;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #374151;
        color: white;
        transform: scale(1.1);
    }

    .action-btn.primary {
        background: var(--primary-gradient);
        color: white;
    }

    .action-btn.primary:hover {
        transform: scale(1.1);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    /* Sidebar */
    .sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid #e5e7eb;
    }

    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-title i {
        color: #667eea;
    }

    /* Filters */
    .filter-group {
        margin-bottom: 1rem;
    }

    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        transition: var(--transition);
        background: white;
    }

    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .filter-btn {
        flex: 1;
        padding: 0.75rem;
        border: none;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        text-align: center;
        display: inline-block;
    }

    .filter-btn.primary {
        background: var(--primary-gradient);
        color: white;
    }

    .filter-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .filter-btn.secondary {
        background: #f3f4f6;
        color: #6b7280;
    }

    .filter-btn.secondary:hover {
        background: #e5e7eb;
        color: #374151;
        text-decoration: none;
    }

    /* Quick Actions */
    .quick-action {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: var(--radius-md);
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        text-decoration: none;
        transition: var(--transition);
        margin-bottom: 0.75rem;
    }

    .quick-action:hover {
        background: #f9fafb;
        color: #1f2937;
        transform: translateX(5px);
        border-color: #667eea;
        text-decoration: none;
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quick-action-content h4 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .quick-action-content p {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
    }

    /* Calendar Container */
    .calendar-container {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid #e5e7eb;
        min-height: 600px;
    }

    /* Calendar Customization */
    .fc {
        font-family: inherit;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #1f2937;
    }

    .fc-button {
        background: var(--primary-gradient) !important;
        border: none !important;
        border-radius: var(--radius-md) !important;
        padding: 0.5rem 1rem !important;
        font-weight: 500 !important;
        transition: var(--transition) !important;
    }

    .fc-button:hover {
        transform: translateY(-2px) !important;
        box-shadow: var(--shadow-lg) !important;
    }

    .fc-button:not(:disabled).fc-button-active {
        background: #374151 !important;
    }

    .fc-event {
        border: none !important;
        border-radius: var(--radius-sm) !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid #e5e7eb;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        color: #6b7280;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .empty-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: var(--radius-lg);
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }

    .empty-action:hover {
        color: white;
        transform: translateY(-3px);
        box-shadow: var(--shadow-xl);
        text-decoration: none;
    }

    /* Floating Action Button */
    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        background: var(--primary-gradient);
        border: none;
        border-radius: 50%;
        box-shadow: var(--shadow-xl);
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: var(--transition);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fab:hover {
        transform: scale(1.1);
        box-shadow: 0 25px 50px rgba(102, 126, 234, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .events-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 2rem 1.5rem;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .custom-tabs {
            flex-direction: column;
            gap: 0.25rem;
        }

        .events-grid {
            grid-template-columns: 1fr;
        }

        .event-info {
            grid-template-columns: 1fr;
        }

        .fab {
            bottom: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }

    /* Animations */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
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

    .slide-up {
        animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Loading States */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Hero Section -->
    <div class="hero-section fade-in">
        <div class="hero-content">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="hero-title">
                        <i class="bi bi-calendar-event me-3"></i>
                        Tableau de Bord Événements
                    </h1>
                    <p class="hero-subtitle">
                        Gérez efficacement vos interventions, réunions et formations ORMVAT
                    </p>
                </div>
                <a href="{{ route('events.create') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nouvel Événement
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['total'] ?? 0 }}</span>
                    <span class="hero-stat-label">Total</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['aujourd_hui'] ?? 0 }}</span>
                    <span class="hero-stat-label">Aujourd'hui</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['cette_semaine'] ?? 0 }}</span>
                    <span class="hero-stat-label">Cette Semaine</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['urgents'] ?? 0 }}</span>
                    <span class="hero-stat-label">Urgents</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ $stats['ce_mois'] ?? 0 }}</span>
                    <span class="hero-stat-label">Ce Mois</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="custom-tabs slide-up">
        <button class="custom-tab {{ $view === 'cards' ? 'active' : '' }}"
                onclick="switchView('cards')">
            <i class="bi bi-grid-3x3-gap"></i>
            Vue Cartes
        </button>
        <button class="custom-tab {{ $view === 'calendar' ? 'active' : '' }}"
                onclick="switchView('calendar')">
            <i class="bi bi-calendar3"></i>
            Calendrier
        </button>
        <button class="custom-tab {{ $view === 'timeline' ? 'active' : '' }}"
                onclick="switchView('timeline')">
            <i class="bi bi-list-ul"></i>
            Timeline
        </button>
    </div>

    <!-- Main Content -->
    <div class="content-grid">
        <!-- Events Content -->
        <div class="main-content">
            <!-- Cards View -->
            <div id="cards-view" class="{{ $view === 'cards' ? '' : 'd-none' }}">
                @if(isset($events) && $events->count() > 0)
                    <div class="events-grid">
                        @foreach($events as $event)
                            <div class="event-card fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                                <div class="event-card-header">
                                    <div class="event-type-badge type-{{ $event->type }}">
                                        {{ $event->type_nom }}
                                    </div>

                                    <h3 class="event-title">{{ $event->titre }}</h3>
                                    <p class="event-description">
                                        {{ Str::limit($event->description, 120) }}
                                    </p>

                                    <div class="event-meta">
                                        <div class="priority-dot priority-{{ $event->priorite }}"
                                             title="{{ $event->priorite_nom }}"></div>
                                        <span class="status-pill status-{{ $event->statut }}">
                                            {{ $event->statut_nom }}
                                        </span>
                                        @if($event->date_debut < now() && $event->statut !== 'termine')
                                            <span class="status-pill status-annule">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                En retard
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="event-info">
                                    <div class="info-item">
                                        <i class="bi bi-calendar-date info-icon"></i>
                                        <span>{{ $event->date_debut->format('d/m/Y à H:i') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-clock info-icon"></i>
                                        <span>{{ $event->duree }} min</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-geo-alt info-icon"></i>
                                        <span>{{ Str::limit($event->lieu, 20) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-person info-icon"></i>
                                        <span>{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</span>
                                    </div>
                                </div>

                                <div class="event-footer">
                                    <div class="participants-preview">
                                        @if($event->participants->count() > 0)
                                            @foreach($event->participants->take(3) as $participant)
                                                <div class="participant-avatar"
                                                     title="{{ $participant->utilisateur->prenom }} {{ $participant->utilisateur->nom }}">
                                                    {{ substr($participant->utilisateur->prenom, 0, 1) }}{{ substr($participant->utilisateur->nom, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($event->participants->count() > 3)
                                                <span class="participants-count">
                                                    +{{ $event->participants->count() - 3 }} autres
                                                </span>
                                            @endif
                                        @else
                                            <span class="participants-count text-muted">Aucun participant</span>
                                        @endif
                                    </div>

                                    <div class="event-actions">
                                        <a href="{{ route('events.show', $event) }}"
                                           class="action-btn primary" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(Auth::user()->role === 'admin' || $event->id_organisateur === Auth::id())
                                            <a href="{{ route('events.edit', $event) }}"
                                               class="action-btn" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="action-btn"
                                                    onclick="confirmDelete({{ $event->id }})"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if(isset($events) && method_exists($events, 'links'))
                        <div class="d-flex justify-content-center mt-4">
                            {{ $events->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h3 class="empty-title">Aucun événement trouvé</h3>
                        <p class="empty-description">
                            Commencez par créer votre premier événement pour organiser vos activités ORMVAT.
                        </p>
                        <a href="{{ route('events.create') }}" class="empty-action">
                            <i class="bi bi-plus-lg"></i>
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

            <!-- Timeline View -->
            <div id="timeline-view" class="{{ $view === 'timeline' ? '' : 'd-none' }}">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-list-timeline"></i>
                    </div>
                    <h3 class="empty-title">Vue Timeline</h3>
                    <p class="empty-description">
                        Cette fonctionnalité sera bientôt disponible.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Filters -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <i class="bi bi-funnel"></i>
                    Filtres Avancés
                </h3>

                <form method="GET" action="{{ route('events.index') }}" id="filterForm">
                    <input type="hidden" name="view" value="{{ $view }}" id="viewInput">

                    <div class="filter-group">
                        <label class="filter-label">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="filter-input" placeholder="Titre, description, lieu...">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Type d'événement</label>
                        <select name="type" class="filter-input">
                            <option value="">Tous les types</option>
                            @foreach(App\Models\Event::$types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Statut</label>
                        <select name="statut" class="filter-input">
                            <option value="">Tous les statuts</option>
                            @foreach(App\Models\Event::$statuts as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Priorité</label>
                        <select name="priorite" class="filter-input">
                            <option value="">Toutes</option>
                            @foreach(App\Models\Event::$priorites as $key => $label)
                                <option value="{{ $key }}" {{ request('priorite') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Période</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                                   class="filter-input" style="flex: 1;">
                            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                                   class="filter-input" style="flex: 1;">
                        </div>
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="filter-btn primary">
                            <i class="bi bi-search me-1"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('events.index', ['view' => $view]) }}"
                           class="filter-btn secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <i class="bi bi-lightning"></i>
                    Actions Rapides
                </h3>

                <a href="{{ route('events.create') }}" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>Créer Événement</h4>
                        <p>Nouveau rendez-vous ou intervention</p>
                    </div>
                </a>

                <a href="{{ route('events.export') }}" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="bi bi-download"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>Exporter Données</h4>
                        <p>Télécharger au format CSV</p>
                    </div>
                </a>

                <a href="#" onclick="window.print()" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="bi bi-printer"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>Imprimer Planning</h4>
                        <p>Version imprimable</p>
                    </div>
                </a>

                <a href="{{ route('events.calendar') }}" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>Vue Calendrier Complet</h4>
                        <p>Page dédiée calendrier</p>
                    </div>
                </a>
            </div>

            <!-- Upcoming Events -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <i class="bi bi-clock"></i>
                    Prochains Événements
                </h3>

                <div class="d-flex flex-column gap-3">
                    <!-- Example upcoming events -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="priority-dot priority-urgente"></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-sm">Maintenance urgente pompe A</div>
                            <div class="text-xs text-muted">Aujourd'hui 14:00</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="priority-dot priority-normale"></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-sm">Réunion équipe technique</div>
                            <div class="text-xs text-muted">Demain 09:00</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="priority-dot priority-haute"></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold text-sm">Formation sécurité</div>
                            <div class="text-xs text-muted">Vendredi 10:00</div>
                        </div>
                    </div>
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
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            events: '/events/calendar/data',
            eventClick: function(info) {
                window.location.href = '/events/' + info.event.id;
            },
            dateClick: function(info) {
                window.location.href = '/events/create?date=' + info.dateStr;
            },
            eventDidMount: function(info) {
                var title = info.event.title;
                var lieu = info.event.extendedProps.lieu || '';
                var organisateur = info.event.extendedProps.organisateur || '';
                info.el.setAttribute('title', title + '\n' + lieu + '\nOrganisateur: ' + organisateur);
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

        // Initialize calendar if calendar view is selected
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

    // Search with delay
    var searchInput = document.querySelector('[name="search"]');
    if (searchInput) {
        var searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }

    // Initialize calendar if calendar view is active
    var calendarView = document.getElementById('calendar-view');
    if (calendarView && !calendarView.classList.contains('d-none')) {
        initCalendar();
    }

    // Smooth animations on scroll
    var observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in, .slide-up').forEach(function(el) {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
});

// Export function
function exportEvents() {
    var params = new URLSearchParams(window.location.search);
    window.location.href = '/events/export/csv?' + params.toString();
}
</script>
@endpush
