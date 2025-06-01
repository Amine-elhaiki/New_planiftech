<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory, SoftDeletes;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Constantes pour les statuts
    const STATUT_PLANIFIE = 'planifie';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_SUSPENDU = 'suspendu';

    /**
     * Relation avec le responsable du projet
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    /**
     * Relation avec les tâches du projet
     */
    public function taches()
    {
        return $this->hasMany(Task::class, 'id_projet');
    }

    /**
     * Relation avec les événements du projet
     */
    public function evenements()
    {
        return $this->hasMany(Event::class, 'id_projet');
    }

    /**
     * Relation avec les rapports via les tâches
     */
    public function rapports()
    {
        return $this->hasManyThrough(Report::class, Task::class, 'id_projet', 'id_tache');
    }

    /**
     * Obtenir tous les utilisateurs impliqués dans le projet
     */
    public function utilisateursImpliques()
    {
        $userIds = collect();

        // Responsable du projet
        $userIds->push($this->id_responsable);

        // Utilisateurs assignés aux tâches
        $taskUserIds = $this->taches()->pluck('id_utilisateur');
        $userIds = $userIds->merge($taskUserIds);

        // Organisateurs d'événements
        $eventOrganizerIds = $this->evenements()->pluck('id_organisateur');
        $userIds = $userIds->merge($eventOrganizerIds);

        // Participants aux événements
        $eventParticipantIds = ParticipantEvent::whereIn('id_evenement', $this->evenements()->pluck('id'))
                                              ->pluck('id_utilisateur');
        $userIds = $userIds->merge($eventParticipantIds);

        return User::whereIn('id', $userIds->unique())->get();
    }

    /**
     * Calculer l'avancement du projet basé sur les tâches
     */
    public function getAvancementAttribute(): float
    {
        $totalTasks = $this->taches()->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->taches()->where('statut', Task::STATUT_TERMINE)->count();

        return round(($completedTasks / $totalTasks) * 100, 1);
    }

    /**
     * Calculer l'avancement basé sur la progression des tâches
     */
    public function getAvancementDetailleAttribute(): float
    {
        $totalTasks = $this->taches()->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $totalProgress = $this->taches()->sum('progression');

        return round($totalProgress / $totalTasks, 1);
    }

    /**
     * Vérifier si le projet est en retard
     */
    public function isOverdue(): bool
    {
        return $this->date_fin < Carbon::today() &&
               $this->statut !== self::STATUT_TERMINE;
    }

    /**
     * Vérifier si le projet se termine bientôt
     */
    public function isDueSoon(int $days = 7): bool
    {
        return $this->date_fin->diffInDays(Carbon::today()) <= $days &&
               $this->statut !== self::STATUT_TERMINE;
    }

    /**
     * Vérifier si le projet est actif
     */
    public function isActive(): bool
    {
        return in_array($this->statut, [self::STATUT_PLANIFIE, self::STATUT_EN_COURS]);
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_PLANIFIE => 'info',
            self::STATUT_EN_COURS => 'primary',
            self::STATUT_TERMINE => 'success',
            self::STATUT_SUSPENDU => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Obtenir le libellé formaté du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_PLANIFIE => 'Planifié',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_SUSPENDU => 'Suspendu',
            default => ucfirst($this->statut)
        };
    }

    /**
     * Obtenir la durée du projet en jours
     */
    public function getDurationInDaysAttribute(): int
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }

    /**
     * Obtenir le nombre de jours restants
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->statut === self::STATUT_TERMINE) {
            return 0;
        }

        return max(0, Carbon::today()->diffInDays($this->date_fin, false));
    }

    /**
     * Obtenir le pourcentage de temps écoulé
     */
    public function getTimeElapsedPercentageAttribute(): float
    {
        $totalDays = $this->duration_in_days;

        if ($totalDays === 0) {
            return 100;
        }

        $elapsedDays = $this->date_debut->diffInDays(Carbon::today());

        return min(100, round(($elapsedDays / $totalDays) * 100, 1));
    }

    /**
     * Obtenir le texte de l'échéance
     */
    public function getDueDateTextAttribute(): string
    {
        if ($this->statut === self::STATUT_TERMINE) {
            return 'Terminé';
        }

        if ($this->isOverdue()) {
            $days = Carbon::today()->diffInDays($this->date_fin);
            return "En retard de {$days} jour" . ($days > 1 ? 's' : '');
        }

        $days = $this->days_remaining;

        if ($days === 0) {
            return 'Se termine aujourd\'hui';
        } elseif ($days === 1) {
            return 'Se termine demain';
        } else {
            return "Se termine dans {$days} jours";
        }
    }

    /**
     * Scope pour les projets actifs
     */
    public function scopeActive($query)
    {
        return $query->whereIn('statut', [self::STATUT_PLANIFIE, self::STATUT_EN_COURS]);
    }

    /**
     * Scope pour les projets terminés
     */
    public function scopeCompleted($query)
    {
        return $query->where('statut', self::STATUT_TERMINE);
    }

    /**
     * Scope pour les projets en retard
     */
    public function scopeOverdue($query)
    {
        return $query->where('date_fin', '<', Carbon::today())
                    ->where('statut', '!=', self::STATUT_TERMINE);
    }

    /**
     * Scope pour les projets qui se terminent bientôt
     */
    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->whereBetween('date_fin', [Carbon::today(), Carbon::today()->addDays($days)])
                    ->where('statut', '!=', self::STATUT_TERMINE);
    }

    /**
     * Scope pour les projets d'un responsable
     */
    public function scopeForResponsible($query, $userId)
    {
        return $query->where('id_responsable', $userId);
    }

    /**
     * Scope par statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    /**
     * Scope par zone géographique
     */
    public function scopeInZone($query, $zone)
    {
        return $query->where('zone_geographique', 'like', "%{$zone}%");
    }

    /**
     * Obtenir les statistiques des tâches
     */
    public function getTaskStatistics(): array
    {
        return [
            'total' => $this->taches()->count(),
            'a_faire' => $this->taches()->where('statut', Task::STATUT_A_FAIRE)->count(),
            'en_cours' => $this->taches()->where('statut', Task::STATUT_EN_COURS)->count(),
            'termine' => $this->taches()->where('statut', Task::STATUT_TERMINE)->count(),
            'en_retard' => $this->taches()->overdue()->count(),
        ];
    }

    /**
     * Obtenir les statistiques des événements
     */
    public function getEventStatistics(): array
    {
        return [
            'total' => $this->evenements()->count(),
            'planifie' => $this->evenements()->where('statut', Event::STATUT_PLANIFIE)->count(),
            'en_cours' => $this->evenements()->where('statut', Event::STATUT_EN_COURS)->count(),
            'termine' => $this->evenements()->where('statut', Event::STATUT_TERMINE)->count(),
            'annule' => $this->evenements()->where('statut', Event::STATUT_ANNULE)->count(),
        ];
    }

    /**
     * Obtenir les tâches critiques (priorité haute et en retard)
     */
    public function getCriticalTasks()
    {
        return $this->taches()
                   ->where('priorite', Task::PRIORITE_HAUTE)
                   ->where(function($query) {
                       $query->overdue()
                             ->orWhere('date_echeance', '<=', Carbon::today()->addDays(3));
                   })
                   ->with('utilisateur')
                   ->get();
    }

    /**
     * Obtenir les prochains événements
     */
    public function getUpcomingEvents($limit = 5)
    {
        return $this->evenements()
                   ->upcoming()
                   ->orderBy('date_debut')
                   ->with('organisateur')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Calculer la charge de travail par utilisateur
     */
    public function getWorkloadByUser(): array
    {
        $workload = [];

        foreach ($this->utilisateursImpliques() as $user) {
            $activeTasks = $this->taches()
                               ->where('id_utilisateur', $user->id)
                               ->active()
                               ->count();

            $completedTasks = $this->taches()
                                  ->where('id_utilisateur', $user->id)
                                  ->completed()
                                  ->count();

            $workload[] = [
                'user' => $user,
                'active_tasks' => $activeTasks,
                'completed_tasks' => $completedTasks,
                'total_tasks' => $activeTasks + $completedTasks,
                'completion_rate' => $activeTasks + $completedTasks > 0
                    ? round(($completedTasks / ($activeTasks + $completedTasks)) * 100, 1)
                    : 0
            ];
        }

        return $workload;
    }

    /**
     * Marquer le projet comme terminé
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['statut' => self::STATUT_TERMINE]);
    }

    /**
     * Suspendre le projet
     */
    public function suspend(): bool
    {
        return $this->update(['statut' => self::STATUT_SUSPENDU]);
    }

    /**
     * Reprendre le projet suspendu
     */
    public function resume(): bool
    {
        return $this->update(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Démarrer le projet
     */
    public function start(): bool
    {
        return $this->update(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Obtenir la santé du projet (basée sur l'avancement vs temps écoulé)
     */
    public function getHealthStatus(): array
    {
        $avancement = $this->avancement;
        $tempsEcoule = $this->time_elapsed_percentage;

        if ($avancement >= $tempsEcoule) {
            $status = 'good';
            $message = 'Le projet est dans les temps';
        } elseif ($avancement >= $tempsEcoule - 20) {
            $status = 'warning';
            $message = 'Le projet présente un léger retard';
        } else {
            $status = 'danger';
            $message = 'Le projet est en retard significatif';
        }

        return [
            'status' => $status,
            'message' => $message,
            'progress' => $avancement,
            'time_elapsed' => $tempsEcoule,
            'difference' => $avancement - $tempsEcoule
        ];
    }

    /**
     * Estimer la date de fin basée sur l'avancement actuel
     */
    public function getEstimatedEndDate(): Carbon
    {
        $avancement = $this->avancement_detaille;

        if ($avancement >= 100) {
            return Carbon::today();
        }

        if ($avancement === 0) {
            return $this->date_fin;
        }

        $joursEcoules = $this->date_debut->diffInDays(Carbon::today());
        $joursEstimesTotal = ($joursEcoules * 100) / $avancement;

        return $this->date_debut->addDays($joursEstimesTotal);
    }

    /**
     * Obtenir les jalons du projet
     */
    public function getMilestones(): array
    {
        $milestones = [];

        // Jalon de début
        $milestones[] = [
            'date' => $this->date_debut,
            'title' => 'Début du projet',
            'type' => 'start',
            'completed' => true
        ];

        // Jalons des événements importants
        foreach ($this->evenements()->orderBy('date_debut')->get() as $event) {
            $milestones[] = [
                'date' => $event->date_debut,
                'title' => $event->titre,
                'type' => 'event',
                'completed' => $event->statut === Event::STATUT_TERMINE
            ];
        }

        // Jalon de fin
        $milestones[] = [
            'date' => $this->date_fin,
            'title' => 'Fin prévue du projet',
            'type' => 'end',
            'completed' => $this->statut === self::STATUT_TERMINE
        ];

        return collect($milestones)->sortBy('date')->values()->toArray();
    }

    /**
     * Boot method pour les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            // Définir le statut initial selon la date
            if ($project->date_debut > Carbon::today()) {
                $project->statut = self::STATUT_PLANIFIE;
            } else {
                $project->statut = self::STATUT_EN_COURS;
            }
        });

        static::updating(function ($project) {
            // Mettre à jour automatiquement le statut selon la progression
            if ($project->isDirty('avancement') && $project->avancement >= 100) {
                $project->statut = self::STATUT_TERMINE;
            }
        });
    }
}
