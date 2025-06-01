<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $table = 'taches';

    protected $fillable = [
        'titre',
        'description',
        'date_echeance',
        'priorite',
        'statut',
        'progression',
        'id_utilisateur',
        'id_projet',
        'id_evenement'
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
        'progression' => 'integer'
    ];

    const UPDATED_AT = 'date_modification';
    const CREATED_AT = 'date_creation';

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    public function projet()
    {
        return $this->belongsTo(Project::class, 'id_projet');
    }

    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_tache');
    }

    // Scopes
    public function scopeAFaire($query)
    {
        return $query->where('statut', 'a_faire');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTerminees($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours']);
    }

    public function scopePrioritaires($query)
    {
        return $query->where('priorite', 'haute');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('priorite', 'haute')
                    ->where('date_echeance', '<=', Carbon::today()->addDays(3));
    }

    public function scopeParUtilisateur($query, $userId)
    {
        return $query->where('id_utilisateur', $userId);
    }

    public function scopeParProjet($query, $projetId)
    {
        return $query->where('id_projet', $projetId);
    }

    // Accessors
    public function getStatutColorAttribute()
    {
        if ($this->estEnRetard()) {
            return 'danger';
        }

        return match($this->statut) {
            'a_faire' => 'secondary',
            'en_cours' => 'warning',
            'termine' => 'success',
            default => 'secondary'
        };
    }

    public function getPrioriteColorAttribute()
    {
        return match($this->priorite) {
            'basse' => 'success',
            'moyenne' => 'warning',
            'haute' => 'danger',
            default => 'secondary'
        };
    }

    public function getProgressionBarColorAttribute()
    {
        if ($this->progression >= 100) {
            return 'success';
        } elseif ($this->progression >= 50) {
            return 'warning';
        } else {
            return 'info';
        }
    }

    public function getJoursRestantsAttribute()
    {
        if ($this->statut === 'termine') {
            return 0;
        }

        $jours = Carbon::today()->diffInDays($this->date_echeance, false);
        return max($jours, 0);
    }

    public function getJoursRestantsTexteAttribute()
    {
        $jours = $this->jours_restants;

        if ($this->statut === 'termine') {
            return 'Terminée';
        }

        if ($jours < 0) {
            return 'En retard de ' . abs($jours) . ' jour(s)';
        } elseif ($jours == 0) {
            return 'Aujourd\'hui';
        } elseif ($jours == 1) {
            return 'Demain';
        } else {
            return $jours . ' jour(s) restant(s)';
        }
    }

    // Méthodes métier
    public function estEnRetard()
    {
        return $this->date_echeance < Carbon::today() &&
               !in_array($this->statut, ['termine']);
    }

    public function estUrgente()
    {
        return $this->priorite === 'haute' ||
               ($this->date_echeance <= Carbon::today()->addDays(3) &&
                !in_array($this->statut, ['termine']));
    }

    public function estAujourdhui()
    {
        return $this->date_echeance->isToday();
    }

    public function estDemain()
    {
        return $this->date_echeance->isTomorrow();
    }

    public function estCetteSemaine()
    {
        return $this->date_echeance->isCurrentWeek();
    }

    public function peutEtreModifieePar($user)
    {
        return $user->isAdmin() || $this->id_utilisateur == $user->id;
    }

    public function marquerCommeTerminee()
    {
        $this->update([
            'statut' => 'termine',
            'progression' => 100
        ]);
    }

    public function commencer()
    {
        if ($this->statut === 'a_faire') {
            $this->update([
                'statut' => 'en_cours',
                'progression' => max($this->progression, 10)
            ]);
        }
    }

    public function mettreAJourProgression($progression)
    {
        $progression = max(0, min(100, $progression));

        $statut = $this->statut;
        if ($progression >= 100) {
            $statut = 'termine';
        } elseif ($progression > 0 && $this->statut === 'a_faire') {
            $statut = 'en_cours';
        }

        $this->update([
            'progression' => $progression,
            'statut' => $statut
        ]);
    }

    public function attribuer($userId)
    {
        $this->update(['id_utilisateur' => $userId]);
    }

    public function lierAuProjet($projetId)
    {
        $this->update(['id_projet' => $projetId]);
    }

    public function lierAEvenement($evenementId)
    {
        $this->update(['id_evenement' => $evenementId]);
    }

    // Méthodes statiques
    public static function getPrioritesDisponibles()
    {
        return [
            'basse' => 'Basse',
            'moyenne' => 'Moyenne',
            'haute' => 'Haute'
        ];
    }

    public static function getStatutsDisponibles()
    {
        return [
            'a_faire' => 'À faire',
            'en_cours' => 'En cours',
            'termine' => 'Terminé'
        ];
    }

    public static function getTachesEnRetard()
    {
        return static::enRetard()->with(['utilisateur', 'projet'])->get();
    }

    public static function getTachesDuJour()
    {
        return static::whereDate('date_echeance', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours'])
                    ->with(['utilisateur', 'projet'])
                    ->orderBy('priorite', 'desc')
                    ->get();
    }

    public static function getTachesUrgentes()
    {
        return static::urgentes()
                    ->with(['utilisateur', 'projet'])
                    ->orderBy('date_echeance')
                    ->get();
    }

    public static function getStatistiquesGlobales()
    {
        return [
            'total' => static::count(),
            'a_faire' => static::where('statut', 'a_faire')->count(),
            'en_cours' => static::where('statut', 'en_cours')->count(),
            'terminees' => static::where('statut', 'termine')->count(),
            'en_retard' => static::enRetard()->count(),
            'urgentes' => static::urgentes()->count()
        ];
    }
}
