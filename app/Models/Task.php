<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

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
        'progression' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'date_echeance',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Constantes pour les priorités
    const PRIORITE_BASSE = 'basse';
    const PRIORITE_MOYENNE = 'moyenne';
    const PRIORITE_HAUTE = 'haute';

    // Constantes pour les statuts
    const STATUT_A_FAIRE = 'a_faire';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';

    /**
     * Relation avec l'utilisateur assigné
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    /**
     * Relation avec le projet
     */
    public function projet()
    {
        return $this->belongsTo(Project::class, 'id_projet');
    }

    /**
     * Relation avec l'événement
     */
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    /**
     * Relation avec les rapports
     */
    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_tache');
    }

    /**
     * Vérifier si la tâche est en retard
     */
    public function isOverdue(): bool
    {
        return $this->date_echeance < Carbon::today() &&
               in_array($this->statut, [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Vérifier si la tâche est due aujourd'hui
     */
    public function isDueToday(): bool
    {
        return $this->date_echeance->isToday() &&
               in_array($this->statut, [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Vérifier si la tâche est due cette semaine
     */
    public function isDueThisWeek(): bool
    {
        return $this->date_echeance->isBetween(Carbon::now(), Carbon::now()->endOfWeek()) &&
               in_array($this->statut, [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Vérifier si la tâche est terminée
     */
    public function isCompleted(): bool
    {
        return $this->statut === self::STATUT_TERMINE;
    }

    /**
     * Vérifier si la tâche est active
     */
    public function isActive(): bool
    {
        return in_array($this->statut, [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Obtenir la couleur de la priorité
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priorite) {
            self::PRIORITE_HAUTE => 'danger',
            self::PRIORITE_MOYENNE => 'warning',
            self::PRIORITE_BASSE => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_A_FAIRE => 'secondary',
            self::STATUT_EN_COURS => 'primary',
            self::STATUT_TERMINE => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir le libellé formaté de la priorité
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priorite) {
            self::PRIORITE_HAUTE => 'Haute',
            self::PRIORITE_MOYENNE => 'Moyenne',
            self::PRIORITE_BASSE => 'Basse',
            default => ucfirst($this->priorite)
        };
    }

    /**
     * Obtenir le libellé formaté du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_A_FAIRE => 'À faire',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_TERMINE => 'Terminé',
            default => ucfirst(str_replace('_', ' ', $this->statut))
        };
    }

    /**
     * Obtenir le nombre de jours restants
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        return max(0, Carbon::today()->diffInDays($this->date_echeance, false));
    }

    /**
     * Obtenir le texte de l'échéance
     */
    public function getDueDateTextAttribute(): string
    {
        if ($this->isCompleted()) {
            return 'Terminé';
        }

        if ($this->isOverdue()) {
            $days = Carbon::today()->diffInDays($this->date_echeance);
            return "En retard de {$days} jour" . ($days > 1 ? 's' : '');
        }

        if ($this->isDueToday()) {
            return 'Échéance aujourd\'hui';
        }

        $days = $this->days_remaining;
        if ($days === 0) {
            return 'Échéance aujourd\'hui';
        } elseif ($days === 1) {
            return 'Échéance demain';
        } else {
            return "Échéance dans {$days} jours";
        }
    }

    /**
     * Scope pour les tâches en retard
     */
    public function scopeOverdue($query)
    {
        return $query->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Scope pour les tâches due aujourd'hui
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('date_echeance', Carbon::today())
                    ->whereIn('statut', [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Scope pour les tâches de cette semaine
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('date_echeance', [Carbon::now(), Carbon::now()->endOfWeek()])
                    ->whereIn('statut', [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Scope pour les tâches actives
     */
    public function scopeActive($query)
    {
        return $query->whereIn('statut', [self::STATUT_A_FAIRE, self::STATUT_EN_COURS]);
    }

    /**
     * Scope pour les tâches terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('statut', self::STATUT_TERMINE);
    }

    /**
     * Scope pour les tâches d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('id_utilisateur', $userId);
    }

    /**
     * Scope pour les tâches d'un projet
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('id_projet', $projectId);
    }

    /**
     * Scope par priorité
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priorite', $priority);
    }

    /**
     * Scope par statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    /**
     * Marquer la tâche comme terminée
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'statut' => self::STATUT_TERMINE,
            'progression' => 100
        ]);
    }

    /**
     * Marquer la tâche comme en cours
     */
    public function markAsInProgress(): bool
    {
        return $this->update([
            'statut' => self::STATUT_EN_COURS,
            'progression' => $this->progression > 0 ? $this->progression : 10
        ]);
    }

    /**
     * Réinitialiser la tâche
     */
    public function markAsTodo(): bool
    {
        return $this->update([
            'statut' => self::STATUT_A_FAIRE,
            'progression' => 0
        ]);
    }

    /**
     * Mettre à jour la progression
     */
    public function updateProgress(int $progress): bool
    {
        $progress = max(0, min(100, $progress));

        $newStatus = match(true) {
            $progress === 0 => self::STATUT_A_FAIRE,
            $progress === 100 => self::STATUT_TERMINE,
            default => self::STATUT_EN_COURS
        };

        return $this->update([
            'progression' => $progress,
            'statut' => $newStatus
        ]);
    }

    /**
     * Obtenir les tâches similaires
     */
    public function getSimilarTasks($limit = 5)
    {
        return self::where('id', '!=', $this->id)
                  ->where(function($query) {
                      if ($this->id_projet) {
                          $query->where('id_projet', $this->id_projet);
                      }

                      $query->orWhere('id_utilisateur', $this->id_utilisateur)
                            ->orWhere('priorite', $this->priorite);
                  })
                  ->limit($limit)
                  ->get();
    }

    /**
     * Vérifier si la tâche a des rapports
     */
    public function hasReports(): bool
    {
        return $this->rapports()->exists();
    }

    /**
     * Obtenir le rapport le plus récent
     */
    public function getLatestReport()
    {
        return $this->rapports()->latest()->first();
    }

    /**
     * Calculer le temps estimé restant basé sur la progression
     */
    public function getEstimatedTimeRemaining(): array
    {
        if ($this->isCompleted()) {
            return ['days' => 0, 'text' => 'Terminé'];
        }

        $totalDays = Carbon::now()->diffInDays($this->date_echeance);
        $remainingProgress = 100 - $this->progression;

        if ($this->progression > 0) {
            $estimatedDays = round(($remainingProgress / $this->progression) * $totalDays);
        } else {
            $estimatedDays = $totalDays;
        }

        return [
            'days' => max(0, $estimatedDays),
            'text' => $estimatedDays > 0 ? "Environ {$estimatedDays} jour(s)" : 'Bientôt terminé'
        ];
    }

    /**
     * Boot method pour les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($task) {
            // Ajuster automatiquement la progression selon le statut
            if ($task->isDirty('statut')) {
                switch ($task->statut) {
                    case self::STATUT_A_FAIRE:
                        $task->progression = 0;
                        break;
                    case self::STATUT_TERMINE:
                        $task->progression = 100;
                        break;
                }
            }
        });
    }
}
