<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table participant_events existe déjà
        if (!Schema::hasTable('participant_events')) {
            // Si elle n'existe pas, la créer avec la structure complète
            $this->createParticipantEventsTable();
        } else {
            // Si elle existe, la mettre à jour
            $this->updateParticipantEventsTable();
        }
    }

    /**
     * Créer la table participant_events avec la structure complète
     */
    private function createParticipantEventsTable(): void
    {
        Schema::create('participant_events', function (Blueprint $table) {
            $table->id();

            // Clés étrangères avec gestion des suppressions
            $table->foreignId('id_evenement')
                  ->constrained('events')
                  ->onDelete('cascade');

            $table->foreignId('id_utilisateur')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Statut de présence avec toutes les valeurs possibles
            $table->enum('statut_presence', [
                'invite',      // Invité (par défaut)
                'confirme',    // Confirmé sa présence
                'decline',     // Décliné l'invitation
                'present',     // Présent (marqué pendant/après l'événement)
                'absent',      // Absent (marqué pendant/après l'événement)
                'excuse'       // Excusé (absent justifié)
            ])->default('invite');

            // Rôle dans l'événement
            $table->enum('role_evenement', [
                'organisateur',
                'participant',
                'intervenant',
                'observateur'
            ])->default('participant');

            // Métadonnées de gestion
            $table->timestamp('date_invitation')->nullable();
            $table->timestamp('date_reponse')->nullable();
            $table->text('commentaire')->nullable();
            $table->text('notes_organisateur')->nullable();

            // Gestion des notifications
            $table->boolean('notification_envoyee')->default(false);
            $table->boolean('rappel_envoye')->default(false);

            // Métadonnées supplémentaires
            $table->json('preferences')->nullable(); // Préférences du participant
            $table->string('statut_transport', 50)->nullable(); // Transport utilisé
            $table->text('besoins_speciaux')->nullable(); // Besoins spéciaux ou accessibilité

            // Timestamps et soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Contraintes d'unicité
            $table->unique(['id_evenement', 'id_utilisateur'], 'unique_participant_event');

            // Index pour les performances
            $this->addIndexes($table);
        });
    }

    /**
     * Mettre à jour la table participant_events existante
     */
    private function updateParticipantEventsTable(): void
    {
        Schema::table('participant_events', function (Blueprint $table) {

            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('participant_events', 'role_evenement')) {
                $table->enum('role_evenement', [
                    'organisateur',
                    'participant',
                    'intervenant',
                    'observateur'
                ])->default('participant')->after('statut_presence');
            }

            if (!Schema::hasColumn('participant_events', 'date_invitation')) {
                $table->timestamp('date_invitation')->nullable()->after('role_evenement');
            }

            if (!Schema::hasColumn('participant_events', 'date_reponse')) {
                $table->timestamp('date_reponse')->nullable()->after('date_invitation');
            }

            if (!Schema::hasColumn('participant_events', 'commentaire')) {
                $table->text('commentaire')->nullable()->after('date_reponse');
            }

            if (!Schema::hasColumn('participant_events', 'notes_organisateur')) {
                $table->text('notes_organisateur')->nullable()->after('commentaire');
            }

            if (!Schema::hasColumn('participant_events', 'notification_envoyee')) {
                $table->boolean('notification_envoyee')->default(false)->after('notes_organisateur');
            }

            if (!Schema::hasColumn('participant_events', 'rappel_envoye')) {
                $table->boolean('rappel_envoye')->default(false)->after('notification_envoyee');
            }

            // Nouvelles colonnes pour des fonctionnalités avancées
            if (!Schema::hasColumn('participant_events', 'preferences')) {
                $table->json('preferences')->nullable()->after('rappel_envoye');
            }

            if (!Schema::hasColumn('participant_events', 'statut_transport')) {
                $table->string('statut_transport', 50)->nullable()->after('preferences');
            }

            if (!Schema::hasColumn('participant_events', 'besoins_speciaux')) {
                $table->text('besoins_speciaux')->nullable()->after('statut_transport');
            }

            // Ajouter soft deletes si pas présent
            if (!Schema::hasColumn('participant_events', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Mettre à jour l'énumération statut_presence pour inclure 'excuse'
        $this->updateStatusPresenceEnum();

        // Ajouter les index manquants
        $this->addMissingIndexes();
    }

    /**
     * Ajouter tous les index nécessaires
     */
    private function addIndexes(Blueprint $table): void
    {
        // Index composés pour les requêtes fréquentes
        $table->index(['id_evenement', 'statut_presence'], 'idx_event_status');
        $table->index(['id_evenement', 'role_evenement'], 'idx_event_role');
        $table->index(['id_utilisateur', 'statut_presence'], 'idx_user_status');
        $table->index(['id_utilisateur', 'created_at'], 'idx_user_chronology');

        // Index pour les notifications
        $table->index(['notification_envoyee', 'created_at'], 'idx_notifications');
        $table->index(['rappel_envoye', 'created_at'], 'idx_reminders');

        // Index pour les statistiques
        $table->index(['statut_presence', 'created_at'], 'idx_stats_presence');
        $table->index(['role_evenement', 'created_at'], 'idx_stats_roles');

        // Index pour les requêtes de performance
        $table->index(['id_evenement', 'deleted_at'], 'idx_event_active');
        $table->index(['statut_presence', 'deleted_at'], 'idx_status_active');
    }

    /**
     * Ajouter les index manquants à une table existante
     */
    private function addMissingIndexes(): void
    {
        // Liste des index à vérifier/ajouter
        $indexesToAdd = [
            ['columns' => ['id_evenement', 'statut_presence'], 'name' => 'idx_event_status'],
            ['columns' => ['id_evenement', 'role_evenement'], 'name' => 'idx_event_role'],
            ['columns' => ['id_utilisateur', 'statut_presence'], 'name' => 'idx_user_status'],
            ['columns' => ['id_utilisateur', 'created_at'], 'name' => 'idx_user_chronology'],
            ['columns' => ['notification_envoyee', 'created_at'], 'name' => 'idx_notifications'],
            ['columns' => ['rappel_envoye', 'created_at'], 'name' => 'idx_reminders'],
            ['columns' => ['statut_presence', 'created_at'], 'name' => 'idx_stats_presence'],
            ['columns' => ['role_evenement', 'created_at'], 'name' => 'idx_stats_roles'],
            ['columns' => ['id_evenement', 'deleted_at'], 'name' => 'idx_event_active'],
            ['columns' => ['statut_presence', 'deleted_at'], 'name' => 'idx_status_active']
        ];

        foreach ($indexesToAdd as $indexInfo) {
            try {
                if (!$this->indexExists('participant_events', $indexInfo['name'])) {
                    $columnsList = implode(', ', $indexInfo['columns']);
                    DB::statement("CREATE INDEX {$indexInfo['name']} ON participant_events ({$columnsList})");
                }
            } catch (\Exception $e) {
                Log::warning("Impossible de créer l'index {$indexInfo['name']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Mettre à jour l'énumération statut_presence
     */
    private function updateStatusPresenceEnum(): void
    {
        try {
            $connection = Schema::getConnection();
            $connection->statement("
                ALTER TABLE participant_events
                MODIFY COLUMN statut_presence ENUM(
                    'invite',
                    'confirme',
                    'decline',
                    'present',
                    'absent',
                    'excuse'
                ) DEFAULT 'invite'
            ");
        } catch (\Exception $e) {
            Log::warning("Impossible de mettre à jour l'enum statut_presence: " . $e->getMessage());
        }
    }

    /**
     * Vérifier si un index existe
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection();
            $result = $connection->select("
                SELECT COUNT(*) as count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                AND table_name = ?
                AND index_name = ?
            ", [$table, $indexName]);

            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('participant_events')) {
            Schema::table('participant_events', function (Blueprint $table) {
                $columnsToDropIfExist = [
                    'preferences',
                    'statut_transport',
                    'besoins_speciaux'
                ];

                foreach ($columnsToDropIfExist as $column) {
                    if (Schema::hasColumn('participant_events', $column)) {
                        $table->dropColumn($column);
                    }
                }

                if (Schema::hasColumn('participant_events', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });

            $this->dropAddedIndexes();
        }
    }

    /**
     * Supprimer les index ajoutés
     */
    private function dropAddedIndexes(): void
    {
        $indexesToDrop = [
            'idx_event_status', 'idx_event_role', 'idx_user_status', 'idx_user_chronology',
            'idx_notifications', 'idx_reminders', 'idx_stats_presence', 'idx_stats_roles',
            'idx_event_active', 'idx_status_active'
        ];

        foreach ($indexesToDrop as $indexName) {
            try {
                if ($this->indexExists('participant_events', $indexName)) {
                    DB::statement("DROP INDEX {$indexName} ON participant_events");
                }
            } catch (\Exception $e) {
                Log::warning("Impossible de supprimer l'index {$indexName}: " . $e->getMessage());
            }
        }
    }
};