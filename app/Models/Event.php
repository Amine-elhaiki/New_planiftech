<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'titre',
        'description',
        'type',
        'date_debut',
        'date_fin',
        'lieu',
        'coordonnees_gps',
        'statut',
        'priorite',
        'id_organisateur',
        'id_projet',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
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

    // Constantes pour les types
    const TYPE_INTERVENTION = 'intervention';
    const TYPE_REUNION = 'reunion';
    const TYPE_FORMATION = 'formation';
    const TYPE_VISITE = 'visite';

    // Constantes pour les statuts
    const STATUT_PLANIFIE = 'planifie';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';
    const STATUT_REPORTE = 'reporte';

    // Constantes pour les priorités
    const PRIORITE_NORMALE = 'normale';
    const PRIORITE_HAUTE = 'haute';
    const PRIORITE_URGENTE = 'urgente';

    /**
     * Relation avec l'organisateur
     */
    public function organisateur()
    {
        return $this->belongsTo(User::class, 'id_organisateur');
    }

    /**
     * Relation avec le projet
     */
    public function projet()
    {
        return $this->belongsTo(Project::class, 'id_projet');
    }

    /**
     * Relation avec les participants
     */
    public function participants()
    {
        return $this->hasMany(ParticipantEvent::class, 'id_evenement');
    }

    /**
     * Relation many-to-many avec les utilisateurs participants
     */
    public function utilisateursParticipants()
    {
        return $this->belongsToMany(User::class, 'participants_evenements', 'id_evenement', 'id_utilisateur')
                    ->withPivot('statut_presence')
                    ->withTimestamps();
    }

    /**
     * Relation avec les tâches associées
     */
    public function taches()
    {
        return $this->hasMany(Task::class, 'id_evenement');
    }

    /**
     * Relation avec les rapports
     */
    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_evenement');
    }

    /**
     * Vérifier si l'événement est en cours
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $this->date_debut <= $now && $this->date_fin >= $now;
    }

    /**
     * Vérifier si l'événement est passé
     */
    public function isPast(): bool
    {
        return $this->date_fin < Carbon::now();
    }

    /**
     * Vérifier si l'événement est futur
     */
    public function isFuture(): bool
    {
        return $this->date_debut > Carbon::now();
    }

    /**
     * Vérifier si l'événement est aujourd'hui
     */
    public function isToday(): bool
    {
        return $this->date_debut->isToday() || $this->date_fin->isToday() || $this->isOngoing();
    }

    /**
     * Vérifier si l'événement est cette semaine
     */
    public function isThisWeek(): bool
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->date_debut->between($startOfWeek, $endOfWeek) ||
               $this->date_fin->between($startOfWeek, $endOfWeek) ||
               ($this->date_debut < $startOfWeek && $this->date_fin > $endOfWeek);
    }

    /**
     * Obtenir la durée de l'événement
     */
    public function getDurationAttribute(): \DateInterval
    {
        return $this->date_debut->diff($this->date_fin);
    }

    /**
     * Obtenir la durée en minutes
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->date_debut->diffInMinutes($this->date_fin);
    }

    /**
     * Obtenir la durée formatée
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_in_minutes;

        if ($minutes < 60) {
            return "{$minutes} min";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours}h";
        }

        return "{$hours}h{$remainingMinutes}";
    }

    /**
     * Obtenir la couleur du type
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INTERVENTION => 'danger',
            self::TYPE_REUNION => 'primary',
            self::TYPE_FORMATION => 'success',
            self::TYPE_VISITE => 'warning',
            default => 'secondary'
        };
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
            self::STATUT_ANNULE => 'danger',
            self::STATUT_REPORTE => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Obtenir la couleur de la priorité
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priorite) {
            self::PRIORITE_URGENTE => 'danger',
            self::PRIORITE_HAUTE => 'warning',
            self::PRIORITE_NORMALE => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir le libellé formaté du type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INTERVENTION => 'Intervention',
            self::TYPE_REUNION => 'Réunion',
            self::TYPE_FORMATION => 'Formation',
            self::TYPE_VISITE => 'Visite',
            default => ucfirst($this->type)
        ];
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
            self::STATUT_ANNULE => 'Annulé',
            self::STATUT_REPORTE => 'Reporté',
            default => ucfirst($this->statut)
        ];
    }

    /**
     * Obtenir le libellé formaté de la priorité
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priorite) {
            self::PRIORITE_URGENTE => 'Urgente',
            self::PRIORITE_HAUTE => 'Haute',
            self::PRIORITE_NORMALE => 'Normale',
            default => ucfirst($this->priorite)
        ];
    }

    /**
     * Obtenir le texte du timing
     */
    public function getTimingTextAttribute(): string
    {
        if ($this->isOngoing()) {
            return 'En cours';
        }

        if ($this->isPast()) {
            return 'Terminé';
        }

        $now = Carbon::now();
        $diffInDays = $now->diffInDays($this->date_debut);

        if ($this->date_debut->isToday()) {
            return 'Aujourd\'hui à ' . $this->date_debut->format('H:i');
        }

        if ($this->date_debut->isTomorrow()) {
            return 'Demain à ' . $this->date_debut->format('H:i');
        }

        if ($diffInDays <= 7) {
            return 'Dans ' . $diffInDays . ' jour' . ($diffInDays > 1 ? 's' : '');
        }

        return $this->date_debut->format('d/m/Y à H:i');
    }

    /**
     * Scope pour les événements d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_debut', Carbon::today())
                    ->orWhereDate('date_fin', Carbon::today())
                    ->orWhere(function($q) {
                        $q->where('date_debut', '<', Carbon::today())
                          ->where('date_fin', '>', Carbon::today()->endOfDay());
                    });
    }

    /**
     * Scope pour les événements de cette semaine
     */
    public function scopeThisWeek($query)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $query->where(function($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('date_debut', [$startOfWeek, $endOfWeek])
              ->orWhereBetween('date_fin', [$startOfWeek, $endOfWeek])
              ->orWhere(function($subQuery) use ($startOfWeek, $endOfWeek) {
                  $subQuery->where('date_debut', '<', $startOfWeek)
                           ->where('date_fin', '>', $endOfWeek);
              });
        });
    }

    /**
     * Scope pour les événements en cours
     */
    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('date_debut', '<=', $now)
                    ->where('date_fin', '>=', $now);
    }

    /**
     * Scope pour les événements futurs
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_debut', '>', Carbon::now());
    }

    /**
     * Scope pour les événements passés
     */
    public function scopePast($query)
    {
        return $query->where('date_fin', '<', Carbon::now());
    }

    /**
     * Scope pour les événements d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('id_organisateur', $userId)
                    ->orWhereHas('participants', function($q) use ($userId) {
                        $q->where('id_utilisateur', $userId);
                    });
    }

    /**
     * Scope par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope par statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    /**
     * Ajouter un participant
     */
    public function addParticipant(User $user, string $statut = 'invite'): ParticipantEvent
    {
        return ParticipantEvent::firstOrCreate(
            [
                'id_evenement' => $this->id,
                'id_utilisateur' => $user->id
            ],
            [
                'statut_presence' => $statut
            ]
        );
    }

    /**
     * Supprimer un participant
     */
    public function removeParticipant(User $user): bool
    {
        return ParticipantEvent::where('id_evenement', $this->id)
                               ->where('id_utilisateur', $user->id)
                               ->delete();
    }

    /**
     * Vérifier si un utilisateur participe
     */
    public function hasParticipant(User $user): bool
    {
        return $this->participants()
                   ->where('id_utilisateur', $user->id)
                   ->exists();
    }

    /**
     * Obtenir le statut de participation d'un utilisateur
     */
    public function getParticipantStatus(User $user): ?string
    {
        $participant = $this->participants()
                           ->where('id_utilisateur', $user->id)
                           ->first();

        return $participant ? $participant->statut_presence : null;
    }

    /**
     * Marquer comme terminé
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['statut' => self::STATUT_TERMINE]);
    }

    /**
     * Annuler l'événement
     */
    public function cancel(): bool
    {
        return $this->update(['statut' => self::STATUT_ANNULE]);
    }

    /**
     * Reporter l'événement
     */
    public function postpone(Carbon $newStartDate, Carbon $newEndDate): bool
    {
        return $this->update([
            'date_debut' => $newStartDate,
            'date_fin' => $newEndDate,
            'statut' => self::STATUT_REPORTE
        ]);
    }

    /**
     * Obtenir les participants confirmés
     */
    public function getConfirmedParticipants()
    {
        return $this->participants()
                   ->where('statut_presence', 'confirme')
                   ->with('utilisateur')
                   ->get();
    }

    /**
     * Obtenir les participants présents
     */
    public function getPresentParticipants()
    {
        return $this->participants()
                   ->where('statut_presence', 'present')
                   ->with('utilisateur')
                   ->get();
    }

    /**
     * Calculer le taux de participation
     */
    public function getAttendanceRate(): float
    {
        $totalParticipants = $this->participants()->count();

        if ($totalParticipants === 0) {
            return 0;
        }

        $presentParticipants = $this->participants()
                                   ->where('statut_presence', 'present')
                                   ->count();

        return round(($presentParticipants / $totalParticipants) * 100, 1);
    }

    /**
     * Vérifier s'il y a conflit avec d'autres événements
     */
    public function hasConflictWith(Event $otherEvent): bool
    {
        return $this->date_debut < $otherEvent->date_fin &&
               $this->date_fin > $otherEvent->date_debut;
    }

    /**
     * Obtenir les événements en conflit
     */
    public function getConflictingEvents()
    {
        return self::where('id', '!=', $this->id)
                  ->where('statut', '!=', self::STATUT_ANNULE)
                  ->where(function($query) {
                      $query->where(function($q) {
                          $q->where('date_debut', '<', $this->date_fin)
                            ->where('date_fin', '>', $this->date_debut);
                      });
                  })
                  ->get();
    }

    /**
     * Boot method pour les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            // Ajuster automatiquement le statut selon la date
            if ($event->date_debut > Carbon::now()) {
                $event->statut = self::STATUT_PLANIFIE;
            }
        });

        static::updating(function ($event) {
            // Mettre à jour automatiquement le statut selon la date
            if ($event->isDirty(['date_debut', 'date_fin'])) {
                $now = Carbon::now();

                if ($event->date_debut <= $now && $event->date_fin >= $now) {
                    $event->statut = self::STATUT_EN_COURS;
                } elseif ($event->date_fin < $now && $event->statut !== self::STATUT_ANNULE) {
                    $event->statut = self::STATUT_TERMINE;
                }
            }
        });
    }
}
