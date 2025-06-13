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

    .event-card-header {
        padding: 2rem;
        background: linear-gradient(135deg, #f8faff 0%, #e8f2ff 100%);
        border-bottom: 1px solid #e5e7eb;
    }

    .event-card-body {
        padding: 2rem;
    }

    .participation-actions {
        background: #f8faff;
        border: 2px solid #667eea;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .status-planifie { background: #dbeafe; color: #1e40af; }
    .status-en_cours { background: #fef3c7; color: #92400e; }
    .status-termine { background: #d1fae5; color: #065f46; }
    .status-annule { background: #fee2e2; color: #dc2626; }

    .priority-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .priority-normale { background: #d1fae5; color: #065f46; }
    .priority-haute { background: #fef3c7; color: #92400e; }
    .priority-urgente { background: #fee2e2; color: #dc2626; }

    .participant-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .participant-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .participant-info {
        flex: 1;
    }

    .participant-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .participant-role, .participant-status {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .presence-invite { background: #fef3c7; color: #92400e; }
    .presence-confirme { background: #d1fae5; color: #065f46; }
    .presence-decline { background: #fee2e2; color: #dc2626; }
    .presence-present { background: #d1fae5; color: #065f46; }
    .presence-absent { background: #fee2e2; color: #dc2626; }
    .presence-excuse { background: #fef3c7; color: #92400e; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 10px;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .event-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }

    .btn-participation {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-confirmer {
        background: #10b981;
        color: white;
        border: none;
    }

    .btn-confirmer:hover {
        background: #059669;
        color: white;
        transform: translateY(-1px);
    }

    .btn-decliner {
        background: #ef4444;
        color: white;
        border: none;
    }

    .btn-decliner:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête de l'événement -->
    <div class="event-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="display-6 mb-2">{{ $event->titre }}</h1>
                    <p class="lead mb-3">{{ $event->description }}</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="status-badge status-{{ $event->statut }}">
                            {{ $event->statut_libelle }}
                        </span>
                        <span class="priority-badge priority-{{ $event->priorite }}">
                            {{ $event->priorite_libelle }}
                        </span>
                        <span class="badge bg-light text-dark">
                            {{ $event->type_libelle }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $userParticipation = $event->participants->where('id_utilisateur', auth()->id())->first();
                        $isOrganizer = $event->id_organisateur === auth()->id();
                        $canManage = auth()->user()->role === 'admin' || $isOrganizer;
                    @endphp
                    
                    @if($canManage)
                        <span class="badge bg-warning text-dark fs-6">
                            <i class="bi bi-star me-1"></i>
                            {{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Organisateur' }}
                        </span>
                    @elseif($userParticipation)
                        <span class="badge bg-info text-dark fs-6">
                            <i class="bi bi-person me-1"></i>
                            Participant
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informations principales -->
            <div class="event-card">
                <div class="event-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Détails de l'événement
                    </h3>
                </div>
                <div class="event-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <strong>Date de début</strong><br>
                                <span>{{ $event->date_debut->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <strong>Date de fin</strong><br>
                                <span>{{ $event->date_fin->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <strong>Lieu</strong><br>
                                <span>{{ $event->lieu }}</span>
                                @if($event->coordonnees_gps)
                                    <br><small class="text-muted">{{ $event->coordonnees_gps }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <strong>Durée</strong><br>
                                <span>{{ $event->duree_formattee }}</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <strong>Organisateur</strong><br>
                                <span>{{ $event->organisateur->nom_complet ?? 'Inconnu' }}</span>
                            </div>
                        </div>

                        @if($event->projet)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-folder"></i>
                                </div>
                                <div>
                                    <strong>Projet associé</strong><br>
                                    <a href="{{ route('projects.show', $event->projet) }}">{{ $event->projet->nom }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions de participation -->
            @if($userParticipation && $userParticipation->statut_presence === 'invite' && !$isOrganizer)
                <div class="participation-actions">
                    <h5 class="mb-3">
                        <i class="bi bi-person-check me-2"></i>
                        Répondre à l'invitation
                    </h5>
                    <p class="text-muted mb-3">
                        Vous êtes invité à participer à cet événement. 
                        Merci de confirmer ou décliner votre participation.
                    </p>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn-participation btn-confirmer" 
                                onclick="showParticipationModal('confirmer', {{ $event->id }})">
                            <i class="bi bi-check-lg"></i>
                            Confirmer ma participation
                        </button>
                        <button type="button" class="btn-participation btn-decliner" 
                                onclick="showParticipationModal('decliner', {{ $event->id }})">
                            <i class="bi bi-x-lg"></i>
                            Décliner l'invitation
                        </button>
                    </div>
                </div>
            @elseif($userParticipation && $userParticipation->statut_presence !== 'invite' && !$isOrganizer)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Votre statut :</strong> 
                    <span class="badge presence-{{ $userParticipation->statut_presence }} ms-1">
                        {{ match($userParticipation->statut_presence) {
                            'confirme' => 'Participation confirmée',
                            'decline' => 'Participation déclinée',
                            'present' => 'Présent(e)',
                            'absent' => 'Absent(e)',
                            'excuse' => 'Excusé(e)',
                            default => ucfirst($userParticipation->statut_presence)
                        } }}
                    </span>
                    @if($userParticipation->date_reponse)
                        <br><small>Réponse donnée le {{ $userParticipation->date_reponse->format('d/m/Y à H:i') }}</small>
                    @endif
                    @if($userParticipation->commentaire)
                        <br><small><em>"{{ $userParticipation->commentaire }}"</em></small>
                    @endif
                </div>
            @elseif($isOrganizer)
                <div class="alert alert-success">
                    <i class="bi bi-star me-2"></i>
                    <strong>Vous êtes l'organisateur</strong> de cet événement.
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Participants -->
            <div class="event-card">
                <div class="event-card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Participants ({{ $event->participants->count() }})
                    </h5>
                </div>
                <div class="event-card-body">
                    @if($event->participants->count() > 0)
                        @foreach($event->participants as $participant)
                            <div class="participant-card">
                                <div class="participant-avatar">
                                    {{ substr($participant->utilisateur->prenom ?? 'U', 0, 1) }}{{ substr($participant->utilisateur->nom ?? 'U', 0, 1) }}
                                </div>
                                <div class="participant-info">
                                    <div class="participant-name">
                                        {{ $participant->utilisateur->nom_complet ?? 'Utilisateur inconnu' }}
                                    </div>
                                    <div class="participant-role">
                                        {{ ucfirst($participant->role_evenement) }}
                                    </div>
                                    <span class="badge presence-{{ $participant->statut_presence }} mt-1">
                                        {{ match($participant->statut_presence) {
                                            'invite' => 'En attente',
                                            'confirme' => 'Confirmé',
                                            'decline' => 'Décliné',
                                            'present' => 'Présent',
                                            'absent' => 'Absent',
                                            'excuse' => 'Excusé',
                                            default => ucfirst($participant->statut_presence)
                                        } }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">Aucun participant</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="event-card">
                <div class="event-card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="event-card-body">
                    <div class="d-grid gap-2">
                        @if($canManage)
                            @if($event->peutEtreModifie())
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>
                                    Modifier
                                </a>
                            @endif
                            
                            @if($event->peutEtreSupprime())
                                <button type="button" class="btn btn-danger" onclick="confirmerSuppression()">
                                    <i class="bi bi-trash me-2"></i>
                                    Supprimer
                                </button>
                            @endif
                        @endif

                        <button type="button" class="btn btn-outline-primary" onclick="dupliquerEvenement()">
                            <i class="bi bi-files me-2"></i>
                            Dupliquer
                        </button>

                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de participation -->
<div class="modal fade" id="participationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participationModalTitle">Confirmer la participation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="participationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Événement</label>
                        <div class="bg-light p-3 rounded">
                            <h6>{{ $event->titre }}</h6>
                            <small class="text-muted">
                                {{ $event->date_debut->format('d/m/Y à H:i') }} - {{ $event->lieu }}
                            </small>
                        </div>
                    </div>
                    
                    <p id="participationQuestion"></p>
                    
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                  placeholder="Ajoutez un commentaire si vous le souhaitez..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="participationConfirmBtn">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form de suppression caché -->
<form id="deleteForm" method="POST" action="{{ route('events.destroy', $event) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Form de duplication caché -->
<form id="duplicateForm" method="POST" action="{{ route('events.duplicate', $event) }}" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
function showParticipationModal(action, eventId) {
    const modal = new bootstrap.Modal(document.getElementById('participationModal'));
    const form = document.getElementById('participationForm');
    const title = document.getElementById('participationModalTitle');
    const question = document.getElementById('participationQuestion');
    const btn = document.getElementById('participationConfirmBtn');

    if (action === 'confirmer') {
        title.textContent = 'Confirmer votre participation';
        question.textContent = 'Voulez-vous confirmer votre participation à cet événement ?';
        btn.textContent = 'Confirmer ma participation';
        btn.className = 'btn btn-success';
        form.action = `/events/${eventId}/confirmer`;
    } else {
        title.textContent = 'Décliner l\'invitation';
        question.textContent = 'Voulez-vous décliner votre participation à cet événement ?';
        btn.textContent = 'Décliner l\'invitation';
        btn.className = 'btn btn-danger';
        form.action = `/events/${eventId}/decliner`;
    }

    document.getElementById('commentaire').value = '';
    modal.show();
}

function confirmerSuppression() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
        document.getElementById('deleteForm').submit();
    }
}

function dupliquerEvenement() {
    if (confirm('Voulez-vous dupliquer cet événement ? Une copie sera créée avec les mêmes paramètres.')) {
        document.getElementById('duplicateForm').submit();
    }
}
</script>
@endpush