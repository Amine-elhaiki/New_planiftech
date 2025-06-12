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
        'id_projet'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Types d'événements possibles
    const TYPES = [
        'intervention' => 'Intervention',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite'
    ];

    // Statuts possibles
    const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
        'reporte' => 'Reporté'
    ];

    // Priorités possibles
    const PRIORITES = [
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente'
    ];

    // RELATIONS

    /**
     * Relation avec l'organisateur (User)
     */
    public function organisateur()
    {
        return $this->belongsTo(User::class, 'id_organisateur');
    }

    /**
     * Relation avec le projet associé
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
     * Relation avec les utilisateurs participants via la table pivot
     */
    public function utilisateursParticipants()
    {
        return $this->belongsToMany(User::class, 'participant_events', 'id_evenement', 'id_utilisateur')
                    ->withPivot(['statut_presence', 'role_evenement', 'date_invitation', 'date_reponse', 'commentaire'])
                    ->withTimestamps();
    }

    /**
     * Relation avec les rapports d'intervention
     */
    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_evenement');
    }

    // ACCESSEURS

    /**
     * Obtenir le nom complet du type
     */
    public function getTypeLibelleAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir le nom complet du statut
     */
    public function getStatutLibelleAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Obtenir le nom complet de la priorité
     */
    public function getPrioriteLibelleAttribute()
    {
        return self::PRIORITES[$this->priorite] ?? $this->priorite;
    }

    /**
     * Obtenir la durée de l'événement en heures
     */
    public function getDureeHeuresAttribute()
    {
        return $this->date_debut->diffInHours($this->date_fin);
    }

    /**
     * Obtenir la durée formatée
     */
    public function getDureeFormatteeAttribute()
    {
        $duree = $this->date_debut->diff($this->date_fin);

        if ($duree->days > 0) {
            return $duree->days . ' jour(s) ' . $duree->h . 'h' . $duree->i . 'm';
        } elseif ($duree->h > 0) {
            return $duree->h . 'h' . $duree->i . 'm';
        } else {
            return $duree->i . ' minute(s)';
        }
    }

    /**
     * Vérifier si l'événement est en cours
     */
    public function getEstEnCoursAttribute()
    {
        $maintenant = now();
        return $maintenant->between($this->date_debut, $this->date_fin);
    }

    /**
     * Vérifier si l'événement est passé
     */
    public function getEstPasseAttribute()
    {
        return $this->date_fin->isPast();
    }

    /**
     * Vérifier si l'événement est à venir
     */
    public function getEstAVenirAttribute()
    {
        return $this->date_debut->isFuture();
    }

    /**
     * Nombre de participants confirmés
     */
    public function getNombreParticipantsConfirmesAttribute()
    {
        return $this->participants()->where('statut_presence', 'confirme')->count();
    }

    /**
     * Nombre total de participants invités
     */
    public function getNombreTotalParticipantsAttribute()
    {
        return $this->participants()->count();
    }

    // MÉTHODES MÉTIER

    /**
     * Vérifier si l'événement peut être modifié
     */
    public function peutEtreModifie()
    {
        return !in_array($this->statut, ['termine', 'annule']) && $this->date_debut->isFuture();
    }

    /**
     * Vérifier si l'événement peut être supprimé
     */
    public function peutEtreSupprime()
    {
        return $this->statut === 'planifie' && $this->date_debut->isFuture();
    }

    /**
     * Démarrer l'événement
     */
    public function demarrer()
    {
        if ($this->statut === 'planifie' && $this->date_debut->isPast()) {
            $this->update(['statut' => 'en_cours']);
        }
    }

    /**
     * Terminer l'événement
     */
    public function terminer()
    {
        if (in_array($this->statut, ['planifie', 'en_cours']) && $this->date_fin->isPast()) {
            $this->update(['statut' => 'termine']);
        }
    }

    /**
     * Annuler l'événement
     */
    public function annuler($raison = null)
    {
        $this->update([
            'statut' => 'annule',
            'commentaire_annulation' => $raison
        ]);
    }

    /**
     * Reporter l'événement
     */
    public function reporter($nouvelleDateDebut, $nouvelleDateFin, $raison = null)
    {
        $this->update([
            'statut' => 'reporte',
            'date_debut' => $nouvelleDateDebut,
            'date_fin' => $nouvelleDateFin,
            'commentaire_report' => $raison
        ]);
    }

    /**
     * Vérifier si un utilisateur participe à l'événement
     */
    public function utilisateurParticipe($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    /**
     * Obtenir le statut de participation d'un utilisateur
     */
    public function statutParticipationUtilisateur($userId)
    {
        $participant = $this->participants()->where('id_utilisateur', $userId)->first();
        return $participant ? $participant->statut_presence : null;
    }

    /**
     * Ajouter un participant
     */
    public function ajouterParticipant($userId, $statut = 'invite', $role = 'participant')
    {
        return $this->participants()->create([
            'id_utilisateur' => $userId,
            'statut_presence' => $statut,
            'role_evenement' => $role,
            'date_invitation' => now()
        ]);
    }

    /**
     * Supprimer un participant
     */
    public function supprimerParticipant($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->delete();
    }

    // SCOPES

    /**
     * Scope pour les événements d'un type donné
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les événements d'un statut donné
     */
    public function scopeOfStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les événements d'une priorité donnée
     */
    public function scopeOfPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    /**
     * Scope pour les événements organisés par un utilisateur
     */
    public function scopeOrganisePar($query, $userId)
    {
        return $query->where('id_organisateur', $userId);
    }

    /**
     * Scope pour les événements où un utilisateur participe
     */
    public function scopeAvecParticipant($query, $userId)
    {
        return $query->whereHas('participants', function($q) use ($userId) {
            $q->where('id_utilisateur', $userId);
        });
    }

    /**
     * Scope pour les événements à venir
     */
    public function scopeAVenir($query)
    {
        return $query->where('date_debut', '>', now());
    }

    /**
     * Scope pour les événements passés
     */
    public function scopePasses($query)
    {
        return $query->where('date_fin', '<', now());
    }

    /**
     * Scope pour les événements en cours
     */
    public function scopeEnCours($query)
    {
        $maintenant = now();
        return $query->where('date_debut', '<=', $maintenant)
                     ->where('date_fin', '>=', $maintenant);
    }

    /**
     * Scope pour les événements d'une période donnée
     */
    public function scopeDansPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_debut', [$dateDebut, $dateFin]);
    }

    /**
     * Scope pour les événements du jour
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_debut', today());
    }

    /**
     * Scope pour les événements de la semaine
     */
    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_debut', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope pour les événements du mois
     */
    public function scopeCeMois($query)
    {
        return $query->whereBetween('date_debut', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // MÉTHODES STATIQUES

    /**
     * Obtenir les événements urgents
     */
    public static function urgents()
    {
        return self::where('priorite', 'urgente')
                  ->where('statut', '!=', 'termine')
                  ->where('date_debut', '>', now())
                  ->orderBy('date_debut')
                  ->get();
    }

    /**
     * Obtenir les statistiques des événements
     */
    public static function statistiques()
    {
        return [
            'total' => self::count(),
            'planifies' => self::where('statut', 'planifie')->count(),
            'en_cours' => self::where('statut', 'en_cours')->count(),
            'termines' => self::where('statut', 'termine')->count(),
            'annules' => self::where('statut', 'annule')->count(),
            'ce_mois' => self::ceMois()->count(),
            'cette_semaine' => self::cetteSemaine()->count(),
            'aujourd_hui' => self::aujourdhui()->count()
        ];
    }

    /**
     * Mettre à jour automatiquement les statuts des événements
     */
    public static function mettreAJourStatuts()
    {
        // Démarrer les événements planifiés dont la date de début est passée
        self::where('statut', 'planifie')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>', now())
            ->update(['statut' => 'en_cours']);

        // Terminer les événements en cours dont la date de fin est passée
        self::where('statut', 'en_cours')
            ->where('date_fin', '<=', now())
            ->update(['statut' => 'termine']);
    }
}
