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
        'statut_presence'
    ];

    // Statuts de présence autorisés
    public static $statutsPresence = [
        'invite' => 'Invité',
        'confirme' => 'Confirmé',
        'decline' => 'Décliné',
        'present' => 'Présent',
        'absent' => 'Absent'
    ];

    /**
     * Relation avec l'événement
     */
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    /**
     * Accesseur pour le nom du statut de présence
     */
    public function getStatutPresenceNomAttribute()
    {
        return self::$statutsPresence[$this->statut_presence] ?? $this->statut_presence;
    }

    /**
     * Accesseur pour la classe CSS du statut
     */
    public function getClasseStatutAttribute()
    {
        $classes = [
            'invite' => 'text-info',
            'confirme' => 'text-success',
            'decline' => 'text-danger',
            'present' => 'text-success',
            'absent' => 'text-muted'
        ];

        return $classes[$this->statut_presence] ?? 'text-secondary';
    }

    /**
     * Accesseur pour l'icône du statut
     */
    public function getIconeStatutAttribute()
    {
        $icones = [
            'invite' => 'bi-envelope',
            'confirme' => 'bi-check-circle',
            'decline' => 'bi-x-circle',
            'present' => 'bi-person-check',
            'absent' => 'bi-person-x'
        ];

        return $icones[$this->statut_presence] ?? 'bi-question-circle';
    }

    /**
     * Scope pour filtrer par statut de présence
     */
    public function scopeParStatut($query, $statut)
    {
        if ($statut) {
            return $query->where('statut_presence', $statut);
        }
        return $query;
    }

    /**
     * Scope pour les participants confirmés
     */
    public function scopeConfirmes($query)
    {
        return $query->where('statut_presence', 'confirme');
    }

    /**
     * Scope pour les participants présents
     */
    public function scopePresents($query)
    {
        return $query->where('statut_presence', 'present');
    }
}
