<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantEvent extends Model
{
    use HasFactory;

    protected $table = 'participants_evenements';

    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence',
    ];

    // Constantes pour les statuts de présence
    public static $statusPresence = [
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
        return self::$statusPresence[$this->statut_presence] ?? $this->statut_presence;
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
            'confirme' => 'bi-check-circle',
            'decline' => 'bi-x-circle',
            'present' => 'bi-person-check',
            'absent' => 'bi-person-x',
            'invite' => 'bi-clock',
            default => 'bi-question-circle'
        };
    }

    // Méthodes utiles
    public function confirmer()
    {
        $this->update(['statut_presence' => 'confirme']);
    }

    public function decliner()
    {
        $this->update(['statut_presence' => 'decline']);
    }

    public function marquerPresent()
    {
        $this->update(['statut_presence' => 'present']);
    }

    public function marquerAbsent()
    {
        $this->update(['statut_presence' => 'absent']);
    }

    // Scopes
    public function scopeConfirmes($query)
    {
        return $query->where('statut_presence', 'confirme');
    }

    public function scopeDeclines($query)
    {
        return $query->where('statut_presence', 'decline');
    }

    public function scopePresents($query)
    {
        return $query->where('statut_presence', 'present');
    }

    public function scopeAbsents($query)
    {
        return $query->where('statut_presence', 'absent');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_presence', 'invite');
    }
}
