<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipantEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'participant_events';

    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence',
        'role_evenement',
        'date_invitation',
        'date_reponse',
        'commentaire',
        'notes_organisateur',
        'notification_envoyee',
        'rappel_envoye'
    ];

    protected $casts = [
        'date_invitation' => 'datetime',
        'date_reponse' => 'datetime',
        'notification_envoyee' => 'boolean',
        'rappel_envoye' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes pour les énumérations
    public static $statutsPresence = [
        'invite' => 'Invité',
        'confirme' => 'Confirmé',
        'decline' => 'Décliné',
        'present' => 'Présent',
        'absent' => 'Absent',
        'excuse' => 'Excusé'
    ];

    public static $rolesEvenement = [
        'organisateur' => 'Organisateur',
        'participant' => 'Participant',
        'intervenant' => 'Intervenant',
        'observateur' => 'Observateur'
    ];

    // Relations
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    // Accesseurs
    public function getStatutPresenceNomAttribute()
    {
        return self::$statutsPresence[$this->statut_presence] ?? $this->statut_presence;
    }

    public function getRoleEvenementNomAttribute()
    {
        return self::$rolesEvenement[$this->role_evenement] ?? $this->role_evenement;
    }

    public function getClasseStatutAttribute()
    {
        return match($this->statut_presence) {
            'confirme', 'present' => 'bg-success text-white',
            'decline', 'absent' => 'bg-danger text-white',
            'invite' => 'bg-warning text-dark',
            'excuse' => 'bg-info text-white',
            default => 'bg-secondary text-white'
        };
    }

    public function getIconeStatutAttribute()
    {
        return match($this->statut_presence) {
            'confirme' => 'bi-check-circle',
            'present' => 'bi-check-circle-fill',
            'decline' => 'bi-x-circle',
            'absent' => 'bi-x-circle-fill',
            'invite' => 'bi-clock',
            'excuse' => 'bi-exclamation-circle',
            default => 'bi-question-circle'
        };
    }

    // Scopes
    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut_presence', $statut);
    }

    public function scopeParRole($query, $role)
    {
        return $query->where('role_evenement', $role);
    }

    public function scopeConfirmes($query)
    {
        return $query->whereIn('statut_presence', ['confirme', 'present']);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_presence', 'invite');
    }

    public function scopeNotificationNonEnvoyee($query)
    {
        return $query->where('notification_envoyee', false);
    }

    public function scopeRappelNonEnvoye($query)
    {
        return $query->where('rappel_envoye', false);
    }

    // Méthodes utilitaires
    public function marquerCommePresent()
    {
        $this->update([
            'statut_presence' => 'present',
            'date_reponse' => now()
        ]);
    }

    public function marquerCommeAbsent()
    {
        $this->update([
            'statut_presence' => 'absent',
            'date_reponse' => now()
        ]);
    }

    public function confirmerPresence($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'confirme',
            'date_reponse' => now(),
            'commentaire' => $commentaire
        ]);
    }

    public function declinerInvitation($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'decline',
            'date_reponse' => now(),
            'commentaire' => $commentaire
        ]);
    }

    public function marquerNotificationEnvoyee()
    {
        $this->update(['notification_envoyee' => true]);
    }

    public function marquerRappelEnvoye()
    {
        $this->update(['rappel_envoye' => true]);
    }
}
