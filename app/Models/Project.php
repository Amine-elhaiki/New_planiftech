<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projets';

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
    public function calculateProgress()
    {
        $totalTasks = $this->taches->count();
        if ($totalTasks === 0) {
            return 0;
        }
        $completedTasks = $this->taches->where('statut', 'termine')->count();
        return round(($completedTasks / $totalTasks) * 100, 1);
    }
}
