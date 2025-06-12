<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantEvent extends Model
{
    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence'
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }
}
