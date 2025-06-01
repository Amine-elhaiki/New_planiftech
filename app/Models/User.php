<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'statut',
        'telephone',
        'date_creation',
        'derniere_connexion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_creation' => 'datetime',
        'derniere_connexion' => 'datetime',
    ];

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeTechnicien($query)
    {
        return $query->where('role', 'technicien');
    }

    // Relations
    public function taches()
    {
        return $this->hasMany(Task::class, 'id_utilisateur');
    }

    public function projetsResponsable()
    {
        return $this->hasMany(Project::class, 'id_responsable');
    }

    public function evenementsOrganises()
    {
        return $this->hasMany(Event::class, 'id_organisateur');
    }

    public function participationsEvenements()
    {
        return $this->belongsToMany(Event::class, 'participants_evenements', 'id_utilisateur', 'id_evenement')
                    ->withPivot('statut_presence');
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

    // Accessors
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getNotificationsNonLuesAttribute()
    {
        return $this->notifications()->where('lue', false)->get();
    }

    public function getNombreTachesParStatutAttribute()
    {
        return [
            'a_faire' => $this->taches()->where('statut', 'a_faire')->count(),
            'en_cours' => $this->taches()->where('statut', 'en_cours')->count(),
            'termine' => $this->taches()->where('statut', 'termine')->count(),
        ];
    }

    public function getNombreProjetsParStatutAttribute()
    {
        return [
            'planifie' => $this->projetsResponsable()->where('statut', 'planifie')->count(),
            'en_cours' => $this->projetsResponsable()->where('statut', 'en_cours')->count(),
            'termine' => $this->projetsResponsable()->where('statut', 'termine')->count(),
            'suspendu' => $this->projetsResponsable()->where('statut', 'suspendu')->count(),
        ];
    }

    // Méthodes métier principales

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive()
    {
        return $this->statut === 'actif';
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est technicien
     */
    public function isTechnicien()
    {
        return $this->role === 'technicien';
    }

    /**
     * Mettre à jour les informations de profil
     */
    public function updateProfile(array $data)
    {
        return $this->update($data);
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(string $newPassword)
    {
        return $this->update(['password' => Hash::make($newPassword)]);
    }

    /**
     * Enregistrer une connexion
     */
    public function enregistrerConnexion()
    {
        $this->update(['derniere_connexion' => now()]);

        // Enregistrer dans le journal si la classe existe
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerAction('connexion', 'Connexion de : ' . $this->nom_complet, $this->id);
        }

        return $this;
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus()
    {
        $nouveauStatut = $this->statut === 'actif' ? 'inactif' : 'actif';
        $this->update(['statut' => $nouveauStatut]);

        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerModification('utilisateur', "Statut changé vers : {$nouveauStatut}", $this->id);
        }

        return $this;
    }

    /**
     * Créer une notification pour cet utilisateur
     */
    public function creerNotification($titre, $message, $type = 'systeme')
    {
        if (class_exists('App\Models\Notification')) {
            return \App\Models\Notification::create([
                'titre' => $titre,
                'message' => $message,
                'type' => $type,
                'destinataire_id' => $this->id,
                'lue' => false,
            ]);
        }
        return null;
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function marquerNotificationsLues()
    {
        return $this->notifications()->where('lue', false)->update(['lue' => true]);
    }

    /**
     * Obtenir les événements à venir pour cet utilisateur
     */
    public function getEvenementsAVenir($limite = 10)
    {
        $evenementsOrganises = $this->evenementsOrganises()
                                   ->where('date_debut', '>=', now())
                                   ->orderBy('date_debut')
                                   ->limit($limite)
                                   ->get();

        $evenementsParticipes = $this->participationsEvenements()
                                    ->where('date_debut', '>=', now())
                                    ->orderBy('date_debut')
                                    ->limit($limite)
                                    ->get();

        return $evenementsOrganises->concat($evenementsParticipes)
                                  ->sortBy('date_debut')
                                  ->take($limite);
    }

    /**
     * Obtenir les projets actifs pour cet utilisateur
     */
    public function getProjetsActifs($limite = 10)
    {
        // Projets dont il est responsable
        $projetsResponsable = $this->projetsResponsable()
                                  ->whereIn('statut', ['planifie', 'en_cours'])
                                  ->limit($limite)
                                  ->get();

        // Projets où il a des tâches
        if (class_exists('App\Models\Project')) {
            $projetsAvecTaches = \App\Models\Project::whereHas('taches', function($query) {
                                            $query->where('id_utilisateur', $this->id);
                                        })
                                        ->whereIn('statut', ['planifie', 'en_cours'])
                                        ->limit($limite)
                                        ->get();

            return $projetsResponsable->concat($projetsAvecTaches)
                                      ->unique('id')
                                      ->take($limite);
        }

        return $projetsResponsable->take($limite);
    }

    /**
     * Calculer le taux de completion des tâches
     */
    public function getTauxCompletionTaches()
    {
        $totalTaches = $this->taches()->count();

        if ($totalTaches === 0) {
            return 0;
        }

        $tachesCompletes = $this->taches()->where('statut', 'termine')->count();

        return round(($tachesCompletes / $totalTaches) * 100, 1);
    }

    /**
     * Obtenir les statistiques de performance
     */
    public function getStatistiquesPerformance($periode = 30)
    {
        $dateDebut = Carbon::now()->subDays($periode);

        return [
            'taches_completees' => $this->taches()
                                       ->where('statut', 'termine')
                                       ->where('date_modification', '>=', $dateDebut)
                                       ->count(),
            'rapports_soumis' => $this->rapports()
                                     ->where('date_creation', '>=', $dateDebut)
                                     ->count(),
            'evenements_organises' => $this->evenementsOrganises()
                                          ->where('date_creation', '>=', $dateDebut)
                                          ->count(),
            'taux_completion' => $this->getTauxCompletionTaches(),
        ];
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword($nouveauMotDePasse = null)
    {
        if (!$nouveauMotDePasse) {
            $nouveauMotDePasse = 'password123'; // Mot de passe temporaire
        }

        $this->update(['password' => Hash::make($nouveauMotDePasse)]);

        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerModification('utilisateur', 'Mot de passe réinitialisé', $this->id);
        }

        return $nouveauMotDePasse;
    }

    /**
     * Vérifier si l'utilisateur peut être supprimé
     */
    public function peutEtreSuprime()
    {
        return $this->taches()->count() === 0 &&
               $this->projetsResponsable()->count() === 0 &&
               $this->evenementsOrganises()->count() === 0 &&
               $this->rapports()->count() === 0;
    }

    /**
     * Obtenir les permissions de l'utilisateur
     */
    public function getPermissions()
    {
        if ($this->isAdmin()) {
            return [
                'gerer_utilisateurs',
                'gerer_projets',
                'gerer_taches',
                'gerer_evenements',
                'voir_tous_rapports',
                'exporter_donnees',
                'configuration_systeme',
            ];
        }

        return [
            'gerer_mes_taches',
            'creer_evenements',
            'soumettre_rapports',
            'voir_mes_rapports',
        ];
    }

    /**
     * Vérifier une permission spécifique
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * Mutator pour le mot de passe (hachage automatique)
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
