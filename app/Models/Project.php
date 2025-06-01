<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projets';

    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'date_fin',
        'zone_geographique',
        'statut',
        'id_responsable'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime'
    ];

    const UPDATED_AT = 'date_modification';
    const CREATED_AT = 'date_creation';

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
        return $this->hasManyThrough(
            Report::class,
            Task::class,
            'id_projet',
            'id_tache',
            'id',
            'id'
        );
    }

    // Scopes
    public function scopePlanifies($query)
    {
        return $query->where('statut', 'planifie');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeTermines($query)
    {
        return $query->where('statut', 'termine');
    }

    public function scopeSuspendus($query)
    {
        return $query->where('statut', 'suspendu');
    }

    public function scopeActifs($query)
    {
        return $query->whereIn('statut', ['planifie', 'en_cours']);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('date_fin', '<', Carbon::today())
                    ->whereNotIn('statut', ['termine', 'suspendu']);
    }

    public function scopeParResponsable($query, $userId)
    {
        return $query->where('id_responsable', $userId);
    }

    public function scopeParZone($query, $zone)
    {
        return $query->where('zone_geographique', 'like', "%{$zone}%");
    }

    // Accessors
    public function getStatutColorAttribute()
    {
        if ($this->estEnRetard()) {
            return 'danger';
        }

        return match($this->statut) {
            'planifie' => 'info',
            'en_cours' => 'warning',
            'termine' => 'success',
            'suspendu' => 'secondary',
            default => 'secondary'
        };
    }

    public function getDureeAttribute()
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }

    public function getDureeFormatteeAttribute()
    {
        $duree = $this->duree;

        if ($duree >= 365) {
            $annees = intval($duree / 365);
            $jours = $duree % 365;
            return $annees . ' an(s)' . ($jours > 0 ? ' ' . $jours . ' jour(s)' : '');
        } elseif ($duree >= 30) {
            $mois = intval($duree / 30);
            $jours = $duree % 30;
            return $mois . ' mois' . ($jours > 0 ? ' ' . $jours . ' jour(s)' : '');
        } else {
            return $duree . ' jour(s)';
        }
    }

    public function getJoursRestantsAttribute()
    {
        if ($this->statut === 'termine') {
            return 0;
        }

        return max(0, Carbon::today()->diffInDays($this->date_fin, false));
    }

    public function getJoursEcoulesAttribute()
    {
        return max(0, $this->date_debut->diffInDays(Carbon::today()));
    }

    public function getPourcentageTempsEcouleAttribute()
    {
        if ($this->duree <= 0) {
            return 0;
        }

        return min(100, round(($this->jours_ecoules / $this->duree) * 100, 1));
    }

    public function getAvancementAttribute()
    {
        $totalTaches = $this->taches()->count();

        if ($totalTaches === 0) {
            return 0;
        }

        $tachesTerminees = $this->taches()->where('statut', 'termine')->count();
        return round(($tachesTerminees / $totalTaches) * 100, 1);
    }

    // Méthodes métier
    public function estEnRetard()
    {
        return $this->date_fin < Carbon::today() &&
               !in_array($this->statut, ['termine', 'suspendu']);
    }

    public function estEnCours()
    {
        return $this->statut === 'en_cours';
    }

    public function estTermine()
    {
        return $this->statut === 'termine';
    }

    public function peutEtreModifiePar($user)
    {
        return $user->isAdmin() || $this->id_responsable == $user->id;
    }

    public function demarrer()
    {
        if ($this->statut === 'planifie') {
            $this->update(['statut' => 'en_cours']);
        }
    }

    public function terminer()
    {
        $this->update(['statut' => 'termine']);

        // Marquer toutes les tâches non terminées comme terminées
        $this->taches()
             ->whereIn('statut', ['a_faire', 'en_cours'])
             ->update(['statut' => 'termine', 'progression' => 100]);
    }

    public function suspendre()
    {
        $this->update(['statut' => 'suspendu']);
    }

    public function reprendre()
    {
        if ($this->statut === 'suspendu') {
            $this->update(['statut' => 'en_cours']);
        }
    }

    public function changerResponsable($userId)
    {
        $this->update(['id_responsable' => $userId]);
    }

    public function ajouterTache($tacheData)
    {
        $tacheData['id_projet'] = $this->id;
        return Task::create($tacheData);
    }

    public function ajouterEvenement($evenementData)
    {
        $evenementData['id_projet'] = $this->id;
        return Event::create($evenementData);
    }

    // Statistiques
    public function getStatistiquesTaches()
    {
        return [
            'total' => $this->taches()->count(),
            'a_faire' => $this->taches()->where('statut', 'a_faire')->count(),
            'en_cours' => $this->taches()->where('statut', 'en_cours')->count(),
            'terminees' => $this->taches()->where('statut', 'termine')->count(),
            'en_retard' => $this->taches()->where('date_echeance', '<', Carbon::today())
                                        ->whereIn('statut', ['a_faire', 'en_cours'])
                                        ->count()
        ];
    }

    public function getStatistiquesEvenements()
    {
        return [
            'total' => $this->evenements()->count(),
            'planifies' => $this->evenements()->where('statut', 'planifie')->count(),
            'en_cours' => $this->evenements()->where('statut', 'en_cours')->count(),
            'termines' => $this->evenements()->where('statut', 'termine')->count(),
            'annules' => $this->evenements()->where('statut', 'annule')->count()
        ];
    }

    public function getEquipe()
    {
        $membres = collect();

        // Responsable du projet
        $membres->push($this->responsable);

        // Utilisateurs assignés aux tâches
        $this->taches->each(function($tache) use ($membres) {
            if ($tache->utilisateur && !$membres->contains('id', $tache->utilisateur->id)) {
                $membres->push($tache->utilisateur);
            }
        });

        // Organisateurs d'événements
        $this->evenements->each(function($evenement) use ($membres) {
            if ($evenement->organisateur && !$membres->contains('id', $evenement->organisateur->id)) {
                $membres->push($evenement->organisateur);
            }
        });

        return $membres->unique('id');
    }

    public function getTachesEnRetard()
    {
        return $this->taches()
                    ->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours'])
                    ->with('utilisateur')
                    ->orderBy('date_echeance');
    }

    public function getTachesUrgentes()
    {
        return $this->taches()
                    ->where('priorite', 'haute')
                    ->whereIn('statut', ['a_faire', 'en_cours'])
                    ->with('utilisateur')
                    ->orderBy('date_echeance');
    }

    // Méthodes statiques
    public static function getStatutsDisponibles()
    {
        return [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu'
        ];
    }

    public static function getProjetsEnRetard()
    {
        return static::enRetard()->with('responsable')->get();
    }

    public static function getProjetsActifs()
    {
        return static::actifs()->with('responsable')->orderBy('date_debut')->get();
    }

    public static function getStatistiquesGlobales()
    {
        return [
            'total' => static::count(),
            'planifies' => static::where('statut', 'planifie')->count(),
            'en_cours' => static::where('statut', 'en_cours')->count(),
            'termines' => static::where('statut', 'termine')->count(),
            'suspendus' => static::where('statut', 'suspendu')->count(),
            'en_retard' => static::enRetard()->count()
        ];
    }
}
