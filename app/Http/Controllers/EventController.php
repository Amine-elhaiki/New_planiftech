<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'id_projet',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    // Constantes pour les énumérations
    public static $types = [
        'intervention' => 'Intervention Technique',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite de Terrain'
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

    // Relations
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

    public function taches()
    {
        return $this->hasMany(Task::class, 'id_evenement');
    }

    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_evenement');
    }

    // Accesseurs
    public function getTypeNomAttribute()
    {
        return self::$types[$this->type] ?? $this->type;
    }

    public function getStatutNomAttribute()
    {
        return self::$statuts[$this->statut] ?? $this->statut;
    }

    public function getPrioriteNomAttribute()
    {
        return self::$priorites[$this->priorite] ?? $this->priorite;
    }

    public function getDureeAttribute()
    {
        if ($this->date_debut && $this->date_fin) {
            return $this->date_debut->diffInMinutes($this->date_fin);
        }
        return 0;
    }

    public function getNombreParticipantsConfirmesAttribute()
    {
        return $this->participants()->where('statut_presence', 'confirme')->count();
    }

    public function getIsTermineAttribute()
    {
        return $this->statut === 'termine';
    }

    public function getIsAnnuleAttribute()
    {
        return $this->statut === 'annule';
    }

    public function getIsPlanifieAttribute()
    {
        return $this->statut === 'planifie';
    }

    public function getIsEnCoursAttribute()
    {
        return $this->statut === 'en_cours';
    }

    public function getIsReporteAttribute()
    {
        return $this->statut === 'reporte';
    }

    // Méthodes utiles
    public function utilisateurParticipe($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    public function peutEtreModifiePar($user)
    {
        return $user->role === 'admin' || $this->id_organisateur === $user->id;
    }

    public function peutEtreSupprimePar($user)
    {
        return $user->role === 'admin' || $this->id_organisateur === $user->id;
    }

    public function estDansLeFutur()
    {
        return $this->date_debut > now();
    }

    public function estEnCours()
    {
        return now()->between($this->date_debut, $this->date_fin);
    }

    public function estPasse()
    {
        return $this->date_fin < now();
    }

    // Scopes
    public function scopeAVenir($query)
    {
        return $query->where('date_debut', '>', now());
    }

    public function scopeEnCours($query)
    {
        return $query->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now());
    }

    public function scopePasses($query)
    {
        return $query->where('date_fin', '<', now());
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeParPriorite($query, $priorite)
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

    public function scopePourUtilisateur($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('id_organisateur', $userId)
              ->orWhereHas('participants', function($participantQuery) use ($userId) {
                  $participantQuery->where('id_utilisateur', $userId);
              });
        });
    }

    // Méthodes de modification d'état
    public function marquerTermine()
    {
        $this->update(['statut' => 'termine']);
    }

    public function annuler()
    {
        $this->update(['statut' => 'annule']);
    }

    public function reporter($nouvelleDate, $nouvelleDateFin = null)
    {
        $this->update([
            'statut' => 'reporte',
            'date_debut' => $nouvelleDate,
            'date_fin' => $nouvelleDateFin ?? Carbon::parse($nouvelleDate)->addHour()
        ]);
    }

    public function demarrer()
    {
        $this->update(['statut' => 'en_cours']);
    }

    // Méthodes de formatage
    public function formatDateDebut($format = 'd/m/Y à H:i')
    {
        return $this->date_debut ? $this->date_debut->format($format) : '';
    }

    public function formatDateFin($format = 'd/m/Y à H:i')
    {
        return $this->date_fin ? $this->date_fin->format($format) : '';
    }

    public function formatDureeHumaine()
    {
        $duree = $this->duree;
        if ($duree < 60) {
            return $duree . ' minutes';
        } elseif ($duree < 1440) {
            $heures = floor($duree / 60);
            $minutes = $duree % 60;
            return $heures . 'h' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
        } else {
            $jours = floor($duree / 1440);
            $heures = floor(($duree % 1440) / 60);
            return $jours . ' jour' . ($jours > 1 ? 's' : '') .
                   ($heures > 0 ? ' ' . $heures . 'h' : '');
        }
    }
}
