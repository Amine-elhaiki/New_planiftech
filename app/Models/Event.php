<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

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
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
        'created_at',
        'updated_at'
    ];

    // ✅ PROPRIÉTÉS STATIQUES POUR LES OPTIONS
    public static $types = [
        'intervention' => 'Intervention Technique',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite de Terrain',
        'maintenance' => 'Maintenance',
        'inspection' => 'Inspection',
        'audit' => 'Audit'
    ];

    public static $statuts = [
        'planifie' => 'Planifié',
        'en_cours' => 'En Cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
        'reporte' => 'Reporté'
    ];

    public static $priorites = [
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente'
    ];

    // CONSTANTES POUR COMPATIBILITÉ
    const TYPES = [
        'intervention' => 'Intervention Technique',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite de Terrain',
        'maintenance' => 'Maintenance',
        'inspection' => 'Inspection',
        'audit' => 'Audit'
    ];

    const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En Cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
        'reporte' => 'Reporté'
    ];

    const PRIORITES = [
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente'
    ];

    // RELATIONS
    public function organisateur()
    {
        return $this->belongsTo(User::class, 'id_organisateur');
    }

    public function projet()
    {
        return $this->belongsTo(Project::class, 'id_projet');
    }

    public function participants()
    {
        return $this->hasMany(ParticipantEvent::class, 'id_evenement');
    }

    public function utilisateursParticipants()
    {
        return $this->belongsToMany(User::class, 'participant_events', 'id_evenement', 'id_utilisateur')
                    ->withPivot(['statut_presence', 'role_evenement', 'date_invitation', 'date_reponse', 'commentaire'])
                    ->withTimestamps();
    }

    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_evenement');
    }

    // ACCESSEURS
    public function getTypeLibelleAttribute()
    {
        return self::$types[$this->type] ?? ucfirst($this->type);
    }

    public function getStatutLibelleAttribute()
    {
        return self::$statuts[$this->statut] ?? ucfirst($this->statut);
    }

    public function getPrioriteLibelleAttribute()
    {
        return self::$priorites[$this->priorite] ?? ucfirst($this->priorite);
    }

    public function getDureeHeuresAttribute()
    {
        return $this->date_debut->diffInHours($this->date_fin);
    }


        /**
     * Vérifier si l'utilisateur peut confirmer sa participation
     */
    public function peutConfirmerParticipation($userId)
    {
        $participant = $this->participants()->where('id_utilisateur', $userId)->first();
        return $participant && $participant->statut_presence === 'invite';
    }

        /**
     * Vérifier si l'événement est aujourd'hui
     */
    public function getEstAujourdhuiAttribute()
    {
        return $this->date_debut->isToday();
    }


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

    public function getEstEnCoursAttribute()
    {
        $maintenant = now();
        return $maintenant->between($this->date_debut, $this->date_fin);
    }

    public function getEstPasseAttribute()
    {
        return $this->date_fin->isPast();
    }

    public function getEstAVenirAttribute()
    {
        return $this->date_debut->isFuture();
    }

    public function getNombreParticipantsConfirmesAttribute()
    {
        return $this->participants()->where('statut_presence', 'confirme')->count();
    }

    public function getNombreTotalParticipantsAttribute()
    {
        return $this->participants()->count();
    }

    // SCOPES
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeOfPriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    public function scopeOrganisePar($query, $userId)
    {
        return $query->where('id_organisateur', $userId);
    }

    public function scopeAvecParticipant($query, $userId)
    {
        return $query->whereHas('participants', function($q) use ($userId) {
            $q->where('id_utilisateur', $userId);
        });
    }

    public function scopeAVenir($query)
    {
        return $query->where('date_debut', '>', now());
    }

    public function scopePasses($query)
    {
        return $query->where('date_fin', '<', now());
    }

    public function scopeEnCours($query)
    {
        $maintenant = now();
        return $query->where('date_debut', '<=', $maintenant)
                     ->where('date_fin', '>=', $maintenant);
    }

    public function scopeDansPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_debut', [$dateDebut, $dateFin]);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_debut', today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_debut', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeCeMois($query)
    {
        return $query->whereBetween('date_debut', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // MÉTHODES MÉTIER
    public function peutEtreModifie()
    {
        return !in_array($this->statut, ['termine', 'annule']) && $this->date_debut->isFuture();
    }

    public function peutEtreSupprime()
    {
        return $this->statut === 'planifie' && $this->date_debut->isFuture();
    }

    public function demarrer()
    {
        if ($this->statut === 'planifie' && $this->date_debut->isPast()) {
            $this->update(['statut' => 'en_cours']);
        }
    }

    public function terminer()
    {
        if (in_array($this->statut, ['planifie', 'en_cours']) && $this->date_fin->isPast()) {
            $this->update(['statut' => 'termine']);
        }
    }

    public function annuler($raison = null)
    {
        $this->update([
            'statut' => 'annule',
            'commentaire_annulation' => $raison
        ]);
    }

    public function reporter($nouvelleDateDebut, $nouvelleDateFin, $raison = null)
    {
        $this->update([
            'statut' => 'reporte',
            'date_debut' => $nouvelleDateDebut,
            'date_fin' => $nouvelleDateFin,
            'commentaire_report' => $raison
        ]);
    }

    public function utilisateurParticipe($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    public function statutParticipationUtilisateur($userId)
    {
        $participant = $this->participants()->where('id_utilisateur', $userId)->first();
        return $participant ? $participant->statut_presence : null;
    }

    public function ajouterParticipant($userId, $statut = 'invite', $role = 'participant')
    {
        return $this->participants()->create([
            'id_utilisateur' => $userId,
            'statut_presence' => $statut,
            'role_evenement' => $role,
            'date_invitation' => now()
        ]);
    }

    public function supprimerParticipant($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->delete();
    }

    // MÉTHODES STATIQUES
    public static function urgents()
    {
        return self::where('priorite', 'urgente')
                  ->where('statut', '!=', 'termine')
                  ->where('date_debut', '>', now())
                  ->orderBy('date_debut')
                  ->get();
    }

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

    // MÉTHODES POUR OBTENIR LES OPTIONS
    public static function getTypesDisponibles()
    {
        return self::$types;
    }

    public static function getStatutsDisponibles()
    {
        return self::$statuts;
    }

    public static function getPrioritesDisponibles()
    {
        return self::$priorites;
    }
}