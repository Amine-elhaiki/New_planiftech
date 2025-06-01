<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'statut',
        'telephone',
        'date_creation',
        'derniere_connexion'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_creation' => 'datetime',
        'derniere_connexion' => 'datetime'
    ];

    // Relations
    public function taches()
    {
        return $this->hasMany(Task::class, 'id_utilisateur');
    }

    public function evenementsOrganises()
    {
        return $this->hasMany(Event::class, 'id_organisateur');
    }

    public function participationsEvenements()
    {
        return $this->hasMany(ParticipantEvent::class, 'id_utilisateur');
    }

    public function projetsResponsable()
    {
        return $this->hasMany(Project::class, 'id_responsable');
    }

    public function rapports()
    {
        return $this->hasMany(Report::class, 'id_utilisateur');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'destinataire_id');
    }

    public function journaux()
    {
        return $this->hasMany(Journal::class, 'utilisateur_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeInactive($query)
    {
        return $query->where('statut', 'inactif');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeTechniciens($query)
    {
        return $query->where('role', 'technicien');
    }

    // Accessors & Mutators
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getInitialesAttribute()
    {
        return strtoupper(substr($this->prenom, 0, 1) . substr($this->nom, 0, 1));
    }

    public function getRoleLibelleAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrateur',
            'technicien' => 'Technicien',
            default => 'Utilisateur'
        };
    }

    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'actif' => 'success',
            'inactif' => 'danger',
            default => 'secondary'
        };
    }

    // Méthodes de vérification des rôles
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTechnicien()
    {
        return $this->role === 'technicien';
    }

    public function isActive()
    {
        return $this->statut === 'actif';
    }

    // Méthodes métier
    public function enregistrerConnexion()
    {
        $this->update([
            'derniere_connexion' => now()
        ]);

        Journal::enregistrerConnexion($this->id);
    }

    public function creerNotification($titre, $message, $type = 'systeme')
    {
        return $this->notifications()->create([
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'lue' => false,
            'date_creation' => now()
        ]);
    }

    public function getNotificationsNonLuesAttribute()
    {
        return $this->notifications()->where('lue', false)->orderBy('date_creation', 'desc');
    }

    public function getNombreNotificationsNonLuesAttribute()
    {
        return $this->getNotificationsNonLuesAttribute()->count();
    }

    public function marquerNotificationsCommeLues()
    {
        return $this->notifications()
                    ->where('lue', false)
                    ->update([
                        'lue' => true,
                        'date_lecture' => now()
                    ]);
    }

    // Statistiques des tâches
    public function getNombreTachesParStatutAttribute()
    {
        return [
            'a_faire' => $this->taches()->where('statut', 'a_faire')->count(),
            'en_cours' => $this->taches()->where('statut', 'en_cours')->count(),
            'termine' => $this->taches()->where('statut', 'termine')->count()
        ];
    }

    public function getTauxCompletionTaches()
    {
        $totalTaches = $this->taches()->count();
        if ($totalTaches === 0) {
            return 0;
        }

        $tachesTerminees = $this->taches()->where('statut', 'termine')->count();
        return round(($tachesTerminees / $totalTaches) * 100, 1);
    }

    public function getTachesEnRetard()
    {
        return $this->taches()
                    ->where('date_echeance', '<', Carbon::today())
                    ->whereIn('statut', ['a_faire', 'en_cours']);
    }

    public function getNombreTachesEnRetardAttribute()
    {
        return $this->getTachesEnRetard()->count();
    }

    // Statistiques des projets
    public function getNombreProjetsParStatutAttribute()
    {
        return [
            'planifie' => $this->projetsResponsable()->where('statut', 'planifie')->count(),
            'en_cours' => $this->projetsResponsable()->where('statut', 'en_cours')->count(),
            'termine' => $this->projetsResponsable()->where('statut', 'termine')->count(),
            'suspendu' => $this->projetsResponsable()->where('statut', 'suspendu')->count()
        ];
    }

    public function getProjetsActifs()
    {
        return $this->projetsResponsable()
                    ->whereIn('statut', ['planifie', 'en_cours'])
                    ->orderBy('date_debut');
    }

    // Événements
    public function getEvenementsAVenir()
    {
        return $this->participationsEvenements()
                    ->whereHas('evenement', function($query) {
                        $query->where('date_debut', '>', now())
                              ->whereIn('statut', ['planifie', 'en_cours']);
                    })
                    ->with('evenement')
                    ->orderBy(function($query) {
                        $query->select('date_debut')
                              ->from('evenements')
                              ->whereColumn('evenements.id', 'participants_evenements.id_evenement');
                    });
    }

    public function getEvenementsDuJour()
    {
        return $this->participationsEvenements()
                    ->whereHas('evenement', function($query) {
                        $query->whereDate('date_debut', today());
                    })
                    ->with('evenement');
    }

    // Rapports
    public function getRapportsDuMois()
    {
        return $this->rapports()
                    ->whereMonth('date_creation', now()->month)
                    ->whereYear('date_creation', now()->year);
    }

    public function getNombreRapportsDuMoisAttribute()
    {
        return $this->getRapportsDuMois()->count();
    }

    // Méthodes de performance
    public function getStatistiquesPerformance()
    {
        return [
            'taches_completees_mois' => $this->taches()
                                            ->where('statut', 'termine')
                                            ->whereMonth('date_modification', now()->month)
                                            ->count(),
            'taux_completion' => $this->getTauxCompletionTaches(),
            'taches_en_retard' => $this->nombre_taches_en_retard,
            'rapports_soumis_mois' => $this->nombre_rapports_du_mois,
            'projets_actifs' => $this->getProjetsActifs()->count()
        ];
    }

    // Méthodes statiques
    public static function getUtilisateursActifs()
    {
        return static::where('statut', 'actif')->orderBy('nom');
    }

    public static function getTechniciens()
    {
        return static::where('role', 'technicien')
                    ->where('statut', 'actif')
                    ->orderBy('nom');
    }

    public static function getAdministrateurs()
    {
        return static::where('role', 'admin')
                    ->where('statut', 'actif')
                    ->orderBy('nom');
    }
}
