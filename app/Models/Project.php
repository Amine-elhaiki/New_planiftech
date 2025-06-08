<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'date_fin',
        'zone_geographique',
        'statut',
        'id_responsable',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relations
    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    public function taches()
    {
        return $this->hasMany(Task::class, 'id_projet');
    }

    public function evenements()
    {
        return $this->hasMany(Event::class, 'id_projet');
    }

    // Méthodes utilitaires
    public function getPourcentageAvancementAttribute()
    {
        $totalTaches = $this->taches()->count();
        if ($totalTaches === 0) return 0;

        $tachesTerminees = $this->taches()->where('statut', 'termine')->count();
        return round(($tachesTerminees / $totalTaches) * 100, 2);
    }
}
