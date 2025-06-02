<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'evenements';

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
        'date_creation',
        'date_modification'
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'date_creation',
        'date_modification',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    // Types d'événements autorisés
    public static $types = [
        'intervention' => 'Intervention Technique',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite'
    ];

    // Statuts autorisés
    public static $statuts = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
        'reporte' => 'Reporté'
    ];

    // Priorités autorisées
    public static $priorites = [
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente'
    ];

    /**
     * Relation avec l'organisateur (User)
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
     * Relation avec les participants (Many-to-Many)
     */
    public function participants()
    {
        return $this->hasMany(ParticipantEvent::class, 'id_evenement');
    }

    /**
     * Relation avec les utilisateurs participants
     */
    public function utilisateursParticipants()
    {
        return $this->belongsToMany(User::class, 'participant_events', 'id_evenement', 'id_utilisateur')
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
     * Mutateur pour la date de modification
     */
    public function setDateModificationAttribute($value)
    {
        $this->attributes['date_modification'] = now();
    }

    /**
     * Accesseur pour le nom du type
     */
    public function getTypeNomAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }

    /**
     * Accesseur pour le nom du statut
     */
    public function getStatutNomAttribute()
    {
        return self::$statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Accesseur pour le nom de la priorité
     */
    public function getPrioriteNomAttribute()
    {
        return self::$priorites[$this->priorite] ?? $this->priorite;
    }

    /**
     * Accesseur pour la durée de l'événement
     */
    public function getDureeAttribute()
    {
        if ($this->date_debut && $this->date_fin) {
            return $this->date_debut->diffInMinutes($this->date_fin);
        }
        return 0;
    }

    /**
     * Accesseur pour savoir si l'événement est passé
     */
    public function getEstPasseAttribute()
    {
        return $this->date_fin < now();
    }

    /**
     * Accesseur pour savoir si l'événement est en cours
     */
    public function getEstEnCoursAttribute()
    {
        return $this->date_debut <= now() && $this->date_fin >= now();
    }

    /**
     * Accesseur pour savoir si l'événement est futur
     */
    public function getEstFuturAttribute()
    {
        return $this->date_debut > now();
    }

    /**
     * Accesseur pour la couleur selon le type
     */
    public function getCouleurAttribute()
    {
        $couleurs = [
            'intervention' => '#dc3545',
            'reunion' => '#007bff',
            'formation' => '#28a745',
            'visite' => '#fd7e14'
        ];

        return $couleurs[$this->type] ?? '#6c757d';
    }

    /**
     * Accesseur pour la classe CSS de priorité
     */
    public function getClassePrioriteAttribute()
    {
        $classes = [
            'normale' => 'border-secondary',
            'haute' => 'border-warning',
            'urgente' => 'border-danger'
        ];

        return $classes[$this->priorite] ?? 'border-secondary';
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeParType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, $statut)
    {
        if ($statut) {
            return $query->where('statut', $statut);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par priorité
     */
    public function scopeParPriorite($query, $priorite)
    {
        if ($priorite) {
            return $query->where('priorite', $priorite);
        }
        return $query;
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        if ($dateDebut && $dateFin) {
            return $query->whereBetween('date_debut', [$dateDebut, $dateFin]);
        }
        return $query;
    }

    /**
     * Scope pour les événements d'aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_debut', today());
    }

    /**
     * Scope pour les événements de cette semaine
     */
    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_debut', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour les événements urgents
     */
    public function scopeUrgents($query)
    {
        return $query->where('priorite', 'urgente');
    }

    /**
     * Obtenir le nombre de participants confirmés
     */
    public function getNombreParticipantsConfirmesAttribute()
    {
        return $this->participants()->where('statut_presence', 'confirme')->count();
    }

    /**
     * Vérifier si un utilisateur participe à l'événement
     */
    public function utilisateurParticipe($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    /**
     * Ajouter un participant
     */
    public function ajouterParticipant($userId, $statutPresence = 'invite')
    {
        return $this->participants()->updateOrCreate(
            ['id_utilisateur' => $userId],
            ['statut_presence' => $statutPresence]
        );
    }

    /**
     * Supprimer un participant
     */
    public function supprimerParticipant($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->delete();
    }
}
