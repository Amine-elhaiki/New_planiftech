<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipantEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'participant_events';

    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence',
        'role_evenement',
        'date_invitation',
        'date_reponse',
        'commentaire',
        'notification_envoyee',
        'rappel_envoye',
        'preferences'
    ];

    protected $casts = [
        'date_invitation' => 'datetime',
        'date_reponse' => 'datetime',
        'notification_envoyee' => 'boolean',
        'rappel_envoye' => 'boolean',
        'preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'date_invitation',
        'date_reponse',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Statuts de présence possibles
    const STATUTS_PRESENCE = [
        'invite' => 'Invité',
        'confirme' => 'Confirmé',
        'decline' => 'Décliné',
        'excuse' => 'Excusé',
        'absent' => 'Absent',
        'present' => 'Présent'
    ];

    // Rôles dans l'événement
    const ROLES_EVENEMENT = [
        'organisateur' => 'Organisateur',
        'participant' => 'Participant',
        'intervenant' => 'Intervenant',
        'observateur' => 'Observateur'
    ];

    // RELATIONS

    /**
     * Relation avec l'événement
     */
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    /**
     * Relation avec l'utilisateur participant
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    // ACCESSEURS

    /**
     * Obtenir le libellé du statut de présence
     */
    public function getStatutPresenceLibelleAttribute()
    {
        return self::STATUTS_PRESENCE[$this->statut_presence] ?? $this->statut_presence;
    }

    /**
     * Obtenir le libellé du rôle dans l'événement
     */
    public function getRoleEvenementLibelleAttribute()
    {
        return self::ROLES_EVENEMENT[$this->role_evenement] ?? $this->role_evenement;
    }

    /**
     * Vérifier si la participation est confirmée
     */
    public function getEstConfirmeAttribute()
    {
        return $this->statut_presence === 'confirme';
    }

    /**
     * Vérifier si la participation est déclinée
     */
    public function getEstDeclineAttribute()
    {
        return $this->statut_presence === 'decline';
    }

    /**
     * Vérifier si c'est un organisateur
     */
    public function getEstOrganisateurAttribute()
    {
        return $this->role_evenement === 'organisateur';
    }

    /**
     * Obtenir les préférences par défaut
     */
    public function getPreferencesParDefautAttribute()
    {
        return [
            'rappel_email' => true,
            'rappel_sms' => false,
            'langue_preferee' => 'fr'
        ];
    }

    // MUTATEURS

    /**
     * Définir les préférences avec valeurs par défaut
     */
    public function setPreferencesAttribute($value)
    {
        $defaultPreferences = $this->preferences_par_defaut;
        $this->attributes['preferences'] = json_encode(array_merge($defaultPreferences, $value ?? []));
    }

    // MÉTHODES MÉTIER

    /**
     * Confirmer la participation
     */
    public function confirmerParticipation($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'confirme',
            'date_reponse' => now(),
            'commentaire' => $commentaire
        ]);

        // Créer une notification pour l'organisateur
        if ($this->evenement && $this->evenement->organisateur) {
            Notification::create([
                'titre' => 'Participation confirmée',
                'message' => "{$this->utilisateur->nom_complet} a confirmé sa participation à l'événement '{$this->evenement->titre}'.",
                'type' => 'evenement',
                'destinataire_id' => $this->evenement->id_organisateur,
                'lue' => false
            ]);
        }
    }

    /**
     * Décliner la participation
     */
    public function declinerInvitation($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'decline',
            'date_reponse' => now(),
            'commentaire' => $commentaire
        ]);

        // Créer une notification pour l'organisateur
        if ($this->evenement && $this->evenement->organisateur) {
            Notification::create([
                'titre' => 'Participation déclinée',
                'message' => "{$this->utilisateur->nom_complet} a décliné l'invitation à l'événement '{$this->evenement->titre}'.",
                'type' => 'evenement',
                'destinataire_id' => $this->evenement->id_organisateur,
                'lue' => false
            ]);
        }
    }

    /**
     * Marquer comme absent
     */
    public function marquerAbsent($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'absent',
            'commentaire' => $commentaire
        ]);
    }

    /**
     * Marquer comme présent
     */
    public function marquerPresent($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'present',
            'commentaire' => $commentaire
        ]);
    }

    /**
     * S'excuser pour l'événement
     */
    public function sexcuser($commentaire = null)
    {
        $this->update([
            'statut_presence' => 'excuse',
            'date_reponse' => now(),
            'commentaire' => $commentaire
        ]);
    }

    /**
     * Envoyer une notification d'invitation
     */
    public function envoyerNotificationInvitation()
    {
        if (!$this->notification_envoyee && $this->utilisateur) {
            Notification::create([
                'titre' => 'Invitation à un événement',
                'message' => "Vous êtes invité à l'événement '{$this->evenement->titre}' prévu le " .
                           $this->evenement->date_debut->format('d/m/Y à H:i') . " à {$this->evenement->lieu}.",
                'type' => 'evenement',
                'destinataire_id' => $this->id_utilisateur,
                'lue' => false
            ]);

            $this->update(['notification_envoyee' => true]);
        }
    }

    /**
     * Envoyer un rappel
     */
    public function envoyerRappel()
    {
        if (!$this->rappel_envoye && $this->statut_presence === 'confirme' && $this->utilisateur) {
            $heuresAvant = $this->evenement->date_debut->diffInHours(now());

            if ($heuresAvant <= 24 && $heuresAvant > 0) {
                Notification::create([
                    'titre' => 'Rappel d\'événement',
                    'message' => "Rappel : L'événement '{$this->evenement->titre}' aura lieu dans " .
                               $heuresAvant . " heure(s) à {$this->evenement->lieu}.",
                    'type' => 'evenement',
                    'destinataire_id' => $this->id_utilisateur,
                    'lue' => false
                ]);

                $this->update(['rappel_envoye' => true]);
            }
        }
    }

    /**
     * Obtenir les préférences de notification
     */
    public function getPreferenceNotification($cle)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$cle] ?? $this->preferences_par_defaut[$cle] ?? false;
    }

    /**
     * Définir une préférence de notification
     */
    public function setPreferenceNotification($cle, $valeur)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$cle] = $valeur;
        $this->update(['preferences' => $preferences]);
    }

    // SCOPES

    /**
     * Scope pour les participants confirmés
     */
    public function scopeConfirmes($query)
    {
        return $query->where('statut_presence', 'confirme');
    }

    /**
     * Scope pour les participants invités (en attente)
     */
    public function scopeInvites($query)
    {
        return $query->where('statut_presence', 'invite');
    }

    /**
     * Scope pour les participants qui ont décliné
     */
    public function scopeDeclinees($query)
    {
        return $query->where('statut_presence', 'decline');
    }

    /**
     * Scope pour les organisateurs
     */
    public function scopeOrganisateurs($query)
    {
        return $query->where('role_evenement', 'organisateur');
    }

    /**
     * Scope pour les participants d'un événement donné
     */
    public function scopeDeEvenement($query, $eventId)
    {
        return $query->where('id_evenement', $eventId);
    }

    /**
     * Scope pour les participations d'un utilisateur donné
     */
    public function scopeDeUtilisateur($query, $userId)
    {
        return $query->where('id_utilisateur', $userId);
    }

    /**
     * Scope pour les participations nécessitant un rappel
     */
    public function scopeNecessitantRappel($query)
    {
        return $query->where('statut_presence', 'confirme')
                     ->where('rappel_envoye', false)
                     ->whereHas('evenement', function($q) {
                         $q->where('date_debut', '>', now())
                           ->where('date_debut', '<=', now()->addHours(24));
                     });
    }

    // MÉTHODES STATIQUES

    /**
     * Obtenir les statistiques des participations
     */
    public static function statistiques()
    {
        return [
            'total' => self::count(),
            'confirmees' => self::where('statut_presence', 'confirme')->count(),
            'en_attente' => self::where('statut_presence', 'invite')->count(),
            'declinees' => self::where('statut_presence', 'decline')->count(),
            'organisateurs' => self::where('role_evenement', 'organisateur')->count()
        ];
    }

    /**
     * Envoyer les rappels automatiques
     */
    public static function envoyerRappelsAutomatiques()
    {
        $participationsARappeler = self::necessitantRappel()->get();

        foreach ($participationsARappeler as $participation) {
            $participation->envoyerRappel();
        }

        return $participationsARappeler->count();
    }
}
