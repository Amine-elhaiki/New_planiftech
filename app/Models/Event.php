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
        'intervention' => 'Intervention technique',
        'reunion' => 'Réunion',
        'formation' => 'Formation',
        'visite' => 'Visite terrain'
    ];

    public static $statuts = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
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

    public function utilisateursParticipants()
    {
        return $this->belongsToMany(User::class, 'participant_events', 'id_evenement', 'id_utilisateur')
                    ->withPivot('statut_presence')
                    ->withTimestamps();
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

    // Méthodes utilitaires
    public function utilisateurParticipe($userId)
    {
        return $this->participants()->where('id_utilisateur', $userId)->exists();
    }

    public function estOrganisateurOuAdmin($userId)
    {
        $user = User::find($userId);
        return $this->id_organisateur === $userId || ($user && $user->role === 'admin');
    }

    public function peutEtreModifie()
    {
        return !in_array($this->statut, ['termine', 'annule']);
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

    public function scopePourUtilisateur($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('id_organisateur', $userId)
              ->orWhereHas('participants', function($participantQuery) use ($userId) {
                  $participantQuery->where('id_utilisateur', $userId);
              });
        });
    }
}
