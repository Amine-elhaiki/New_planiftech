<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'role',
        'statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Constantes pour les rôles
    const ROLE_ADMIN = 'admin';
    const ROLE_TECHNICIEN = 'technicien';

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Vérifier si l'utilisateur est technicien
     */
    public function isTechnicien(): bool
    {
        return $this->role === self::ROLE_TECHNICIEN;
    }

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getFullNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Obtenir les initiales de l'utilisateur
     */
    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->prenom, 0, 1) . substr($this->nom, 0, 1));
    }

    /**
     * Formater le rôle pour l'affichage
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_TECHNICIEN => 'Technicien',
            default => ucfirst($this->role)
        };
    }

    /**
     * Relation avec les tâches assignées
     */
    public function taches()
    {
        return $this->hasMany(Task::class, 'id_utilisateur');
    }

    /**
     * Relation avec les tâches actives (non terminées)
     */
    public function tachesActives()
    {
        return $this->hasMany(Task::class, 'id_utilisateur')
                    ->whereIn('statut', ['a_faire', 'en_cours']);
    }

    /**
     * Relation avec les tâches en retard
     */
    public function tachesEnRetard()
    {
        return $this->hasMany(Task::class, 'id_utilisateur')
                    ->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours']);
    }

    /**
     * Relation avec les événements organisés
     */
    public function evenementsOrganises()
    {
        return $this->hasMany(Event::class, 'id_organisateur');
    }

    /**
     * Relation avec les participations aux événements
     */
    public function participationsEvenements()
    {
        return $this->hasMany(ParticipantEvent::class, 'id_utilisateur');
    }

    /**
     * Relation avec les événements où l'utilisateur participe
     */
    public function evenements()
    {
        return $this->belongsToMany(Event::class, 'participants_evenements', 'id_utilisateur', 'id_evenement')
                    ->withPivot('statut_presence')
                    ->withTimestamps();
    }

    /**
     * Relation avec les projets dont l'utilisateur est responsable
     */
    public function projetsResponsable()
    {
        return $this->hasMany(Project::class, 'id_responsable');
    }

    /**
     * Relation avec les rapports créés
     */
    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_utilisateur');
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    /**
     * Scope pour les administrateurs
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope pour les techniciens
     */
    public function scopeTechniciens($query)
    {
        return $query->where('role', self::ROLE_TECHNICIEN);
    }

    /**
     * Obtenir les statistiques de performance de l'utilisateur
     */
    public function getPerformanceStats()
    {
        $totalTasks = $this->taches()->count();
        $completedTasks = $this->taches()->where('statut', 'termine')->count();
        $overdueTasks = $this->tachesEnRetard()->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            'overdue_tasks' => $overdueTasks,
            'events_organized' => $this->evenementsOrganises()->count(),
            'reports_submitted' => $this->rapports()->count(),
            'projects_responsible' => $this->projetsResponsable()->count()
        ];
    }

    /**
     * Obtenir la charge de travail actuelle
     */
    public function getCurrentWorkload()
    {
        return [
            'active_tasks' => $this->tachesActives()->count(),
            'overdue_tasks' => $this->tachesEnRetard()->count(),
            'upcoming_events' => $this->evenements()
                                     ->where('date_debut', '>=', Carbon::now())
                                     ->where('date_debut', '<=', Carbon::now()->addDays(7))
                                     ->count(),
            'pending_reports' => $this->taches()
                                     ->where('statut', 'termine')
                                     ->whereDoesntHave('rapports')
                                     ->count()
        ];
    }

    /**
     * Vérifier si l'utilisateur peut modifier une ressource
     */
    public function canModify($resource): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Vérifications spécifiques selon le type de ressource
        if ($resource instanceof Task) {
            return $resource->id_utilisateur === $this->id;
        }

        if ($resource instanceof Event) {
            return $resource->id_organisateur === $this->id ||
                   $resource->participants->contains('id', $this->id);
        }

        if ($resource instanceof Project) {
            return $resource->id_responsable === $this->id;
        }

        if ($resource instanceof Report) {
            return $resource->id_utilisateur === $this->id;
        }

        return false;
    }

    /**
     * Obtenir la couleur associée au rôle
     */
    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'danger',
            self::ROLE_TECHNICIEN => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Obtenir l'avatar ou les initiales
     */
    public function getAvatarAttribute(): string
    {
        // Pour l'instant, on retourne les initiales
        // Dans le futur, on pourrait intégrer Gravatar ou un système d'upload
        return $this->initials;
    }

    /**
     * Obtenir les projets où l'utilisateur est impliqué
     */
    public function getAllProjects()
    {
        $responsibleProjects = $this->projetsResponsable()->pluck('id');

        $involvedProjects = Project::whereHas('taches', function($query) {
            $query->where('id_utilisateur', $this->id);
        })->pluck('id');

        $eventProjects = Project::whereHas('evenements.participants', function($query) {
            $query->where('id_utilisateur', $this->id);
        })->pluck('id');

        $allProjectIds = $responsibleProjects->merge($involvedProjects)
                                           ->merge($eventProjects)
                                           ->unique();

        return Project::whereIn('id', $allProjectIds)->get();
    }

    /**
     * Vérifier la disponibilité à une date donnée
     */
    public function isAvailableAt(Carbon $dateTime): bool
    {
        return !$this->evenements()
                    ->where('date_debut', '<=', $dateTime)
                    ->where('date_fin', '>=', $dateTime)
                    ->where('statut', '!=', 'annule')
                    ->exists();
    }

    /**
     * Obtenir les conflits d'emploi du temps
     */
    public function getScheduleConflicts(Carbon $startDate, Carbon $endDate)
    {
        return $this->evenements()
                    ->where(function($query) use ($startDate, $endDate) {
                        $query->whereBetween('date_debut', [$startDate, $endDate])
                              ->orWhereBetween('date_fin', [$startDate, $endDate])
                              ->orWhere(function($q) use ($startDate, $endDate) {
                                  $q->where('date_debut', '<=', $startDate)
                                    ->where('date_fin', '>=', $endDate);
                              });
                    })
                    ->where('statut', '!=', 'annule')
                    ->get();
    }
}
