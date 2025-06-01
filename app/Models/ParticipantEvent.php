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
        'statut_presence'
    ];

    public $timestamps = false;

    // Relations
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    // Scopes
    public function scopeInvites($query)
    {
        return $query->where('statut_presence', 'invite');
    }

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

    // Accessors
    public function getStatutColorAttribute()
    {
        return match($this->statut_presence) {
            'invite' => 'secondary',
            'confirme' => 'success',
            'decline' => 'danger',
            'present' => 'primary',
            'absent' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatutLibelleAttribute()
    {
        return match($this->statut_presence) {
            'invite' => 'Invité',
            'confirme' => 'Confirmé',
            'decline' => 'Décliné',
            'present' => 'Présent',
            'absent' => 'Absent',
            default => 'Inconnu'
        };
    }

    // Méthodes métier
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

    // Méthodes statiques
    public static function getStatutsDisponibles()
    {
        return [
            'invite' => 'Invité',
            'confirme' => 'Confirmé',
            'decline' => 'Décliné',
            'present' => 'Présent',
            'absent' => 'Absent'
        ];
    }
}
