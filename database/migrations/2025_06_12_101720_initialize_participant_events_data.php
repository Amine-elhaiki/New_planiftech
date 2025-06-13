<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // S'assurer que la table participant_events existe
        if (!Schema::hasTable('participant_events')) {
            throw new \Exception('La table participant_events doit exister avant d\'exécuter cette migration de données.');
        }

        // Initialiser les données manquantes pour les enregistrements existants
        $this->initializeExistingRecords();

        // Créer les participations des organisateurs manquantes
        $this->createMissingOrganizerParticipations();

        // Nettoyer les données incohérentes
        $this->cleanInconsistentData();
    }

    /**
     * Initialiser les colonnes pour les enregistrements existants
     */
    private function initializeExistingRecords(): void
    {
        // Mettre à jour les enregistrements qui n'ont pas de date_invitation
        DB::table('participant_events')
            ->whereNull('date_invitation')
            ->update([
                'date_invitation' => DB::raw('COALESCE(created_at, NOW())'),
                'notification_envoyee' => false,
                'rappel_envoye' => false
            ]);

        // Définir le rôle des organisateurs
        DB::statement("
            UPDATE participant_events pe
            INNER JOIN events e ON pe.id_evenement = e.id
            SET pe.role_evenement = 'organisateur',
                pe.statut_presence = 'confirme'
            WHERE pe.id_utilisateur = e.id_organisateur
            AND (pe.role_evenement IS NULL OR pe.role_evenement = 'participant')
        ");

        // Mettre à jour date_reponse pour les participants qui ont déjà répondu
        DB::statement("
            UPDATE participant_events
            SET date_reponse = COALESCE(updated_at, created_at)
            WHERE statut_presence IN ('confirme', 'decline', 'excuse')
            AND date_reponse IS NULL
        ");

        // Initialiser les préférences par défaut
        DB::table('participant_events')
            ->whereNull('preferences')
            ->update([
                'preferences' => json_encode([
                    'rappel_email' => true,
                    'rappel_sms' => false,
                    'langue_preferee' => 'fr'
                ])
            ]);
    }

    /**
     * Créer les participations manquantes pour les organisateurs
     */
    private function createMissingOrganizerParticipations(): void
    {
        // Trouver les événements où l'organisateur n'est pas dans la liste des participants
        $missingParticipations = DB::select("
            SELECT DISTINCT e.id as event_id, e.id_organisateur as user_id, e.created_at
            FROM events e
            LEFT JOIN participant_events pe ON (
                e.id = pe.id_evenement
                AND e.id_organisateur = pe.id_utilisateur
                AND pe.deleted_at IS NULL
            )
            WHERE pe.id IS NULL
            AND e.id_organisateur IS NOT NULL
        ");

        foreach ($missingParticipations as $participation) {
            try {
                DB::table('participant_events')->insert([
                    'id_evenement' => $participation->event_id,
                    'id_utilisateur' => $participation->user_id,
                    'statut_presence' => 'confirme',
                    'role_evenement' => 'organisateur',
                    'date_invitation' => $participation->created_at,
                    'date_reponse' => $participation->created_at,
                    'notification_envoyee' => true,
                    'rappel_envoye' => false,
                    'commentaire' => 'Organisateur de l\'événement',
                    'preferences' => json_encode([
                        'rappel_email' => true,
                        'rappel_sms' => false,
                        'langue_preferee' => 'fr'
                    ]),
                    'created_at' => $participation->created_at,
                    'updated_at' => $participation->created_at
                ]);
            } catch (\Exception $e) {
                Log::warning("Erreur lors de la création de la participation organisateur: " . $e->getMessage());
            }
        }
    }

    /**
     * Nettoyer les données incohérentes
     */
    private function cleanInconsistentData(): void
    {
        // Supprimer les doublons (garder le plus récent)
        DB::statement("
            DELETE pe1 FROM participant_events pe1
            INNER JOIN participant_events pe2
            WHERE pe1.id_evenement = pe2.id_evenement
            AND pe1.id_utilisateur = pe2.id_utilisateur
            AND pe1.id < pe2.id
            AND pe1.deleted_at IS NULL
            AND pe2.deleted_at IS NULL
        ");

        // Corriger les statuts incohérents
        DB::statement("
            UPDATE participant_events
            SET statut_presence = 'decline',
                date_reponse = COALESCE(date_reponse, updated_at, created_at)
            WHERE statut_presence = 'absent'
            AND date_reponse IS NULL
        ");

        // S'assurer que les organisateurs ont le bon rôle et statut
        DB::statement("
            UPDATE participant_events pe
            INNER JOIN events e ON pe.id_evenement = e.id
            SET pe.role_evenement = 'organisateur',
                pe.statut_presence = CASE
                    WHEN pe.statut_presence = 'invite' THEN 'confirme'
                    ELSE pe.statut_presence
                END
            WHERE pe.id_utilisateur = e.id_organisateur
        ");

        // Mettre à jour les notifications pour les anciens enregistrements
        DB::statement("
            UPDATE participant_events
            SET notification_envoyee = CASE
                WHEN statut_presence IN ('confirme', 'decline', 'excuse') THEN true
                ELSE false
            END
            WHERE notification_envoyee IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration de données ne peut pas être facilement annulée
        // car elle nettoie et corrige des données existantes

        Log::info('Migration de données participant_events annulée - aucune action automatique possible');

        // Optionnellement, vous pourriez sauvegarder les données avant la migration
        // et les restaurer ici, mais c'est risqué en production
    }
};
