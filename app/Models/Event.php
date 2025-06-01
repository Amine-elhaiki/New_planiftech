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
        'id_projet'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime'
    ];

    const UPDATED_AT = 'date_modification';
    const CREATED_AT = 'date_creation';

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

    public function scopeAVenir($query)
    {
        return $query->where('date_debut', '>', now());
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_debut', today());
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors & Mutators
    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'planifie' => 'primary',
            'en_cours' => 'warning',
            'termine' => 'success',
            'annule' => 'danger',
            'reporte' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPrioriteColorAttribute()
    {
        return match($this->priorite) {
            'normale' => 'success',
            'haute' => 'warning',
            'urgente' => 'danger',
            default => 'secondary'
        };
    }

    public function getDureeAttribute()
    {
        if ($this->date_debut && $this->date_fin) {
            return $this->date_debut->diffInMinutes($this->date_fin);
        }
        return 0;
    }

    public function getDureeFormatteeAttribute()
    {
        $duree = $this->duree;

        if ($duree >= 60) {
            $heures = intval($duree / 60);
            $minutes = $duree % 60;
            return $heures . 'h' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
        }

        return $duree . 'min';
    }

    // Méthodes métier
    public function ajouterParticipant($userId, $statut = 'invite')
    {
        return ParticipantEvent::updateOrCreate(
            [
                'id_evenement' => $this->id,
                'id_utilisateur' => $userId
            ],
            [
                'statut_presence' => $statut
            ]
        );
    }

    public function retirerParticipant($userId)
    {
        return ParticipantEvent::where('id_evenement', $this->id)
                              ->where('id_utilisateur', $userId)
                              ->delete();
    }

    public function getNombreParticipantsAttribute()
    {
        return $this->participants()->count();
    }

    public function getNombreParticipantsConfirmesAttribute()
    {
        return $this->participants()->where('statut_presence', 'confirme')->count();
    }

    public function estOrganisateurOuParticipant($userId)
    {
        return $this->id_organisateur == $userId ||
               $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    public function peutEtreModifiePar($user)
    {
        return $user->isAdmin() || $this->id_organisateur == $user->id;
    }

    public function estEnRetard()
    {
        return $this->date_debut < now() && !in_array($this->statut, ['termine', 'annule']);
    }

    public function estAujourdhui()
    {
        return $this->date_debut->isToday();
    }

    public function estDemain()
    {
        return $this->date_debut->isTomorrow();
    }

    public function estCetteSemaine()
    {
        return $this->date_debut->isCurrentWeek();
    }

    public function marquerCommeTermine()
    {
        $this->update(['statut' => 'termine']);

        // Marquer automatiquement les participants comme présents s'ils étaient confirmés
        $this->participants()
             ->where('statut_presence', 'confirme')
             ->update(['statut_presence' => 'present']);
    }

    public function annuler()
    {
        $this->update(['statut' => 'annule']);
    }

    public function reporter($nouvelleDateDebut, $nouvelleDateFin)
    {
        $this->update([
            'date_debut' => $nouvelleDateDebut,
            'date_fin' => $nouvelleDateFin,
            'statut' => 'reporte'
        ]);
    }

    // Méthodes statiques
    public static function getTypesDisponibles()
    {
        return [
            'intervention' => 'Intervention technique',
            'reunion' => 'Réunion',
            'formation' => 'Formation',
            'visite' => 'Visite'
        ];
    }

    public static function getStatutsDisponibles()
    {
        return [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            'reporte' => 'Reporté'
        ];
    }

    public static function getPrioritesDisponibles()
    {
        return [
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
    }

    public static function getEvenementsDuJour()
    {
        return static::whereDate('date_debut', today())
                    ->orderBy('date_debut')
                    ->get();
    }

    public static function getEvenementsDeLaSemaine()
    {
        return static::whereBetween('date_debut', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->orderBy('date_debut')
                    ->get();
    }

    public static function getEvenementsAVenir($limit = 10)
    {
        return static::where('date_debut', '>', now())
                    ->orderBy('date_debut')
                    ->limit($limit)
                    ->get();
    }
}
