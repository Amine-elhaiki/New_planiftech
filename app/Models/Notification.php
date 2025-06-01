<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'titre',
        'message',
        'type',
        'lue',
        'destinataire_id',
        'date_lecture'
    ];

    protected $casts = [
        'lue' => 'boolean',
        'date_creation' => 'datetime',
        'date_lecture' => 'datetime'
    ];

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null;

    // Relations
    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Scopes
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    public function scopeLues($query)
    {
        return $query->where('lue', true);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParDestinataire($query, $userId)
    {
        return $query->where('destinataire_id', $userId);
    }

    public function scopeRecentes($query, $jours = 7)
    {
        return $query->where('date_creation', '>=', now()->subDays($jours));
    }

    public function scopeUrgentes($query)
    {
        return $query->whereIn('type', ['tache', 'evenement'])
                    ->nonLues()
                    ->where('date_creation', '>=', now()->subHours(24));
    }

    // Accessors
    public function getDateCreationFormatteeAttribute()
    {
        return $this->date_creation->format('d/m/Y H:i');
    }

    public function getDateLectureFormatteeAttribute()
    {
        return $this->date_lecture ? $this->date_lecture->format('d/m/Y H:i') : null;
    }

    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            'tache' => 'Tâche',
            'evenement' => 'Événement',
            'projet' => 'Projet',
            'systeme' => 'Système',
            default => ucfirst($this->type)
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'tache' => 'primary',
            'evenement' => 'success',
            'projet' => 'warning',
            'systeme' => 'info',
            default => 'secondary'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'tache' => 'bi-list-check',
            'evenement' => 'bi-calendar-event',
            'projet' => 'bi-folder',
            'systeme' => 'bi-gear',
            default => 'bi-bell'
        };
    }

    public function getTempsEcouleAttribute()
    {
        $maintenant = now();
        $creation = $this->date_creation;

        $diff = $creation->diffInMinutes($maintenant);

        if ($diff < 1) {
            return 'À l\'instant';
        } elseif ($diff < 60) {
            return "Il y a {$diff} minute(s)";
        } elseif ($diff < 1440) { // 24 heures
            $heures = intval($diff / 60);
            return "Il y a {$heures} heure(s)";
        } elseif ($diff < 10080) { // 7 jours
            $jours = intval($diff / 1440);
            return "Il y a {$jours} jour(s)";
        } else {
            return $this->date_creation_formattee;
        }
    }

    public function getPrioriteAttribute()
    {
        // Calculer la priorité basée sur le type et l'âge
        $priorite = 1;

        // Priorité par type
        switch ($this->type) {
            case 'tache':
                $priorite += 3;
                break;
            case 'evenement':
                $priorite += 2;
                break;
            case 'projet':
                $priorite += 1;
                break;
        }

        // Augmenter la priorité si récente
        if ($this->date_creation->diffInHours(now()) <= 1) {
            $priorite += 2;
        } elseif ($this->date_creation->diffInHours(now()) <= 6) {
            $priorite += 1;
        }

        return $priorite;
    }

    // Méthodes métier
    public function marquerCommeLue()
    {
        if (!$this->lue) {
            $this->update([
                'lue' => true,
                'date_lecture' => now()
            ]);
        }
    }

    public function marquerCommeNonLue()
    {
        $this->update([
            'lue' => false,
            'date_lecture' => null
        ]);
    }

    public function estRecente($heures = 24)
    {
        return $this->date_creation->diffInHours(now()) <= $heures;
    }

    public function estUrgente()
    {
        return in_array($this->type, ['tache', 'evenement']) &&
               !$this->lue &&
               $this->estRecente();
    }

    public function obtenirLienAction()
    {
        return match($this->type) {
            'tache' => route('tasks.index'),
            'evenement' => route('events.index'),
            'projet' => route('projects.index'),
            default => route('dashboard')
        };
    }

    // Méthodes statiques
    public static function creerPourUtilisateur($userId, $titre, $message, $type = 'systeme')
    {
        return static::create([
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'destinataire_id' => $userId,
            'lue' => false,
            'date_creation' => now()
        ]);
    }

    public static function diffuserATous($titre, $message, $type = 'systeme', $saufUserId = null)
    {
        $utilisateurs = User::where('statut', 'actif');

        if ($saufUserId) {
            $utilisateurs->where('id', '!=', $saufUserId);
        }

        $notifications = [];
        foreach ($utilisateurs->get() as $utilisateur) {
            $notifications[] = [
                'titre' => $titre,
                'message' => $message,
                'type' => $type,
                'destinataire_id' => $utilisateur->id,
                'lue' => false,
                'date_creation' => now()
            ];
        }

        return static::insert($notifications);
    }

    public static function diffuserAuxTechniciens($titre, $message, $type = 'systeme')
    {
        $techniciens = User::where('role', 'technicien')
                          ->where('statut', 'actif')
                          ->get();

        $notifications = [];
        foreach ($techniciens as $technicien) {
            $notifications[] = [
                'titre' => $titre,
                'message' => $message,
                'type' => $type,
                'destinataire_id' => $technicien->id,
                'lue' => false,
                'date_creation' => now()
            ];
        }

        return static::insert($notifications);
    }

    public static function diffuserAuxAdmins($titre, $message, $type = 'systeme')
    {
        $admins = User::where('role', 'admin')
                     ->where('statut', 'actif')
                     ->get();

        $notifications = [];
        foreach ($admins as $admin) {
            $notifications[] = [
                'titre' => $titre,
                'message' => $message,
                'type' => $type,
                'destinataire_id' => $admin->id,
                'lue' => false,
                'date_creation' => now()
            ];
        }

        return static::insert($notifications);
    }

    public static function notifierTacheEnRetard($tache)
    {
        return static::creerPourUtilisateur(
            $tache->id_utilisateur,
            'Tâche en retard',
            "La tâche \"{$tache->titre}\" est en retard depuis le {$tache->date_echeance->format('d/m/Y')}.",
            'tache'
        );
    }

    public static function notifierEvenementProchain($evenement, $userId)
    {
        return static::creerPourUtilisateur(
            $userId,
            'Événement à venir',
            "L'événement \"{$evenement->titre}\" aura lieu le {$evenement->date_debut->format('d/m/Y à H:i')}.",
            'evenement'
        );
    }

    public static function notifierProjetModifie($projet, $userId)
    {
        return static::creerPourUtilisateur(
            $userId,
            'Projet modifié',
            "Le projet \"{$projet->nom}\" a été modifié.",
            'projet'
        );
    }

    // Nettoyage
    public static function nettoyerAnciennes($jours = 30)
    {
        return static::where('date_creation', '<', now()->subDays($jours))->delete();
    }

    public static function nettoyerLues($jours = 7)
    {
        return static::where('lue', true)
                    ->where('date_lecture', '<', now()->subDays($jours))
                    ->delete();
    }

    // Statistiques
    public static function getStatistiquesParType($userId = null)
    {
        $query = static::query();

        if ($userId) {
            $query->where('destinataire_id', $userId);
        }

        return $query->selectRaw('type, COUNT(*) as total, SUM(CASE WHEN lue = 0 THEN 1 ELSE 0 END) as non_lues')
                    ->groupBy('type')
                    ->get()
                    ->pluck(['total', 'non_lues'], 'type')
                    ->toArray();
    }

    public static function getTypesDisponibles()
    {
        return [
            'tache' => 'Tâche',
            'evenement' => 'Événement',
            'projet' => 'Projet',
            'systeme' => 'Système'
        ];
    }
}
