<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // Constantes pour les statuts
    public static $statuts = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'suspendu' => 'Suspendu'
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

    public function rapports()
    {
        return $this->hasManyThrough(Report::class, Task::class, 'id_projet', 'id_tache');
    }

    // Accesseurs
    public function getStatutNomAttribute()
    {
        return self::$statuts[$this->statut] ?? $this->statut;
    }

    public function getPourcentageAvancementAttribute()
    {
        $totalTaches = $this->taches()->count();
        if ($totalTaches === 0) return 0;

        $tachesTerminees = $this->taches()->where('statut', 'termine')->count();
        return round(($tachesTerminees / $totalTaches) * 100, 2);
    }

    public function getDureeAttribute()
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }

    public function getJoursRestantsAttribute()
    {
        if ($this->statut === 'termine') return 0;
        return max(0, now()->diffInDays($this->date_fin, false));
    }

    public function getEstEnRetardAttribute()
    {
        return now() > $this->date_fin && $this->statut !== 'termine';
    }

    public function getClasseStatutAttribute()
    {
        return match($this->statut) {
            'termine' => 'bg-success',
            'en_cours' => 'bg-primary',
            'suspendu' => 'bg-warning',
            'planifie' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->whereIn('statut', ['planifie', 'en_cours']);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeParResponsable($query, $userId)
    {
        return $query->where('id_responsable', $userId);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('date_fin', '<', now())
                    ->where('statut', '!=', 'termine');
    }
}
