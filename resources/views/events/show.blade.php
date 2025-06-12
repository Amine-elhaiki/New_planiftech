@extends('layouts.app')

@section('title', 'Événement - ' . $event->titre)

@push('styles')
<style>
    .event-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .event-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1.25rem;
        font-weight: 600;
    }

    .card-body {
        padding: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .info-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        color: #667eea;
        margin-top: 0.25rem;
    }

    .info-content h6 {
        margin-bottom: 0.25rem;
        color: #374151;
        font-weight: 600;
    }

    .info-content p {
        margin: 0;
        color: #6b7280;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-planifie { background: #dbeafe; color: #1e40af; }
    .status-en_cours { background: #fef3c7; color: #d97706; }
    .status-termine { background: #d1fae5; color: #065f46; }
    .status-annule { background: #fee2e2; color: #dc2626; }
    .status-reporte { background: #f3e8ff; color: #7c3aed; }

    .priority-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-normale { background: #d1fae5; color: #065f46; }
    .priority-haute { background: #fef3c7; color: #d97706; }
    .priority-urgente { background: #fee2e2; color: #dc2626; }

    .participants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .participant-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .participant-card:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .participant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 0.75rem;
        font-size: 0.875rem;
    }

    .participant-info h6 {
        margin-bottom: 0.25rem;
        color: #374151;
        font-size: 0.875rem;
    }

    .participant-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
        margin-top: 0.25rem;
        display: inline-block;
    }

    .presence-confirme { background: #d1fae5; color: #065f46; }
    .presence-invite { background: #fef3c7; color: #d97706; }
    .presence-decline { background: #fee2e2; color: #dc2626; }
    .presence-present { background: #dcfce7; color: #166534; }
    .presence-absent { background: #fecaca; color: #b91c1c; }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-success {
        background: #22c55e;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background: #16a34a;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #ef4444;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-warning {
        background: #f59e0b;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #6b7280;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .participation-actions {
        background: #f0f4ff;
        border: 2px solid #667eea;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 1rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #667eea;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border: none;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #22c55e;
    }

    .alert-warning {
        background: #fef3c7;
        color: #d97706;
        border-left: 4px solid #f59e0b;
    }

    .alert-danger {
        background: #fee2e2;
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-left: 4px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<!-- En-tête de l'événement -->
<div class="event-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <h1 class="h3 mb-0 me-3">{{ $event->titre }}</h1>
                    <span class="status-badge status-{{ $event->statut }}">
                        {{ $event->statut_nom }}
                    </span>
                    <span class="priority-badge priority-{{ $event->priorite }} ms-2">
                        {{ $event->priorite_nom }}
                    </span>
                </div>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-calendar me-2"></i>
                    {{ $event->date_debut->format('d/m/Y à H:i') }} -
                    {{ $event->date_fin->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                @if($event->canEditEvent(Auth::id()))
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-light me-2">
                        <i class="bi bi-pencil me-2"></i>Modifier
                    </a>
                @endif
                <a href="{{ route('events.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Erreur :</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- Détails de l'événement -->
            <div class="event-card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>
                    Détails de l'événement
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                @switch($event->type)
                                    @case('intervention')
                                        <i class="bi bi-tools"></i>
                                        @break
                                    @case('reunion')
                                        <i class="bi bi-people"></i>
                                        @break
                                    @case('formation')
                                        <i class="bi bi-book"></i>
                                        @break
                                    @case('visite')
                                        <i class="bi bi-building"></i>
                                        @break
                                @endswitch
                            </div>
                            <div class="info-content">
                                <h6>Type d'événement</h6>
                                <p>{{ $event->type_nom }}</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="info-content">
                                <h6>Lieu</h6>
                                <p>{{ $event->lieu }}</p>
                                @if($event->coordonnees_gps)
                                    <small class="text-muted">GPS: {{ $event->coordonnees_gps }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="info-content">
                                <h6>Organisateur</h6>
                                <p>{{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}</p>
                                <small class="text-muted">{{ $event->organisateur->email }}</small>
                            </div>
                        </div>

                        @if($event->projet)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-folder"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Projet associé</h6>
                                    <p>{{ $event->projet->nom }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="info-content">
                                <h6>Durée</h6>
                                <p>{{ $event->duree }} minutes</p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="info-content">
                                <h6>Participants</h6>
                                <p>{{ $event->participants->count() }} personne(s)</p>
                                <small class="text-muted">{{ $event->nombre_participants_confirmes }} confirmé(s)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="bi bi-text-paragraph me-2"></i>Description</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $event->description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants -->
            <div class="event-card">
                <div class="card-header">
                    <i class="bi bi-people me-2"></i>
                    Participants ({{ $event->participants->count() }})
                </div>
                <div class="card-body">
                    @if($event->participants->count() > 0)
                        <div class="participants-grid">
                            @foreach($event->participants as $participant)
                                <div class="participant-card">
                                    <div class="participant-avatar">
                                        {{ strtoupper(substr($participant->utilisateur->prenom, 0, 1) . substr($participant->utilisateur->nom, 0, 1)) }}
                                    </div>
                                    <div class="participant-info">
                                        <h6>{{ $participant->utilisateur->prenom }} {{ $participant->utilisateur->nom }}</h6>
                                        <div class="d-flex align-items-center">
                                            <span class="participant-status presence-{{ $participant->statut_presence }}">
                                                <i class="{{ $participant->icone_statut }} me-1"></i>
                                                {{ $participant->statut_presence_nom }}
                                            </span>
                                            @if($participant->role_evenement !== 'participant')
                                                <span class="badge bg-primary ms-2">{{ $participant->role_evenement_nom }}</span>
                                            @endif
                                        </div>
                                        @if($participant->commentaire)
                                            <small class="text-muted mt-1 d-block">{{ $participant->commentaire }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-people display-4 mb-3"></i>
                            <p>Aucun participant pour cet événement</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions de participation -->
            @php
                $userParticipation = $event->participants->where('id_utilisateur', Auth::id())->first();
            @endphp

            @if($userParticipation && $userParticipation->statut_presence === 'invite')
                <div class="participation-actions">
                    <h6 class="mb-3">
                        <i class="bi bi-hand-index me-2"></i>
                        Répondre à l'invitation
                    </h6>
                    <p class="mb-3">Vous êtes invité à cet événement. Souhaitez-vous y participer ?</p>

                    <form method="POST" action="{{ route('events.confirmer', $event) }}" class="d-inline">
                        @csrf
                        <div class="mb-3">
                            <textarea name="commentaire" class="form-control" rows="2"
                                      placeholder="Commentaire (optionnel)..."></textarea>
                        </div>
                        <button type="submit" class="btn-success me-2">
                            <i class="bi bi-check-circle me-2"></i>Confirmer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('events.decliner', $event) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-danger">
                            <i class="bi bi-x-circle me-2"></i>Décliner
                        </button>
                    </form>
                </div>
            @elseif($userParticipation)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Votre statut :</strong> {{ $userParticipation->statut_presence_nom }}
                    @if($userParticipation->commentaire)
                        <br><small>{{ $userParticipation->commentaire }}</small>
                    @endif
                </div>
            @endif

            <!-- Actions de gestion -->
            @if($event->canEditEvent(Auth::id()))
                <div class="event-card">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>
                        Actions de gestion
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('events.edit', $event) }}" class="btn-primary">
                                <i class="bi bi-pencil me-2"></i>Modifier l'événement
                            </a>

                            @if($event->statut === 'planifie')
                                <form method="POST" action="{{ route('events.update-status', $event) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="en_cours">
                                    <button type="submit" class="btn-warning w-100">
                                        <i class="bi bi-play me-2"></i>Démarrer l'événement
                                    </button>
                                </form>
                            @endif

                            @if($event->statut === 'en_cours')
                                <form method="POST" action="{{ route('events.update-status', $event) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="statut" value="termine">
                                    <button type="submit" class="btn-success w-100">
                                        <i class="bi bi-check-circle me-2"></i>Terminer l'événement
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('events.duplicate', $event) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-secondary w-100">
                                    <i class="bi bi-files me-2"></i>Dupliquer
                                </button>
                            </form>

                            <hr>

                            <form method="POST" action="{{ route('events.destroy', $event) }}"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-100">
                                    <i class="bi bi-trash me-2"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informations complémentaires -->
            <div class="event-card">
                <div class="card-header">
                    <i class="bi bi-info-square me-2"></i>
                    Informations
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <h6 class="mb-1">Événement créé</h6>
                            <small class="text-muted">
                                {{ $event->created_at->format('d/m/Y à H:i') }}
                                par {{ $event->organisateur->prenom }} {{ $event->organisateur->nom }}
                            </small>
                        </div>

                        @if($event->updated_at != $event->created_at)
                            <div class="timeline-item">
                                <h6 class="mb-1">Dernière modification</h6>
                                <small class="text-muted">
                                    {{ $event->updated_at->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                        @endif

                        @if($event->date_debut->isPast())
                            <div class="timeline-item">
                                <h6 class="mb-1">Événement passé</h6>
                                <small class="text-muted">
                                    Il y a {{ $event->date_debut->diffForHumans() }}
                                </small>
                            </div>
                        @elseif($event->date_debut->isFuture())
                            <div class="timeline-item">
                                <h6 class="mb-1">Événement à venir</h6>
                                <small class="text-muted">
                                    {{ $event->date_debut->diffForHumans() }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-masquer les alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
@endpush
