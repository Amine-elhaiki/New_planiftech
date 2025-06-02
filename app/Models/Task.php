<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

   protected $table = 'taches';

    protected $fillable = [
        'titre',
        'description',
        'date_echeance',
        'priorite',
        'statut',
        'progression',
        'id_utilisateur',
        'id_projet',
        'id_evenement',
    ];

    protected $casts = [
        'date_echeance' => 'date',
    ];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    public function projet()
    {
        return $this->belongsTo(Project::class, 'id_projet');
    }

    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_tache');
    }

    // Scopes
    public function scopeEnRetard($query)
    {
        return $query->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours']);
    }

    public function scopeParPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    // Accesseurs
    public function getIsEnRetardAttribute()
    {
        return $this->date_echeance < Carbon::today() &&
               in_array($this->statut, ['a_faire', 'en_cours']);
    }
}
