<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantEvent extends Model
{
    use HasFactory;

    protected $table = 'participant_events';

    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence',
    ];

    // Constantes pour les statuts de présence
    public static $statutsPresence = [
        'invite' => 'Invité',
        'confirme' => 'Confirmé',
        'decline' => 'Décliné',
        'present' => 'Présent',
        'absent' => 'Absent'
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

    public function getClasseStatutAttribute()
    {
        return match($this->statut_presence) {
            'confirme', 'present' => 'bg-success',
            'decline', 'absent' => 'bg-danger',
            'invite' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    public function getIconeStatutAttribute()
    {
        return match($this->statut_presence) {
            'confirme', 'present' => 'bi-check-circle',
            'decline', 'absent' => 'bi-x-circle',
            'invite' => 'bi-clock',
            default => 'bi-question-circle'
        };
    }

    // Scopes
    public function scopeConfirmes($query)
    {
        return $query->where('statut_presence', 'confirme');
    }

    public function scopePresents($query)
    {
        return $query->where('statut_presence', 'present');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_presence', 'invite');
    }
}
