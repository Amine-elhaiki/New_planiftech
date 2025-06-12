<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table des projets
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('zone_geographique', 100);
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'suspendu'])->default('planifie');
            $table->foreignId('id_responsable')->constrained('users')->onDelete('restrict');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->nullable();
            $table->timestamps();

            $table->index(['statut', 'id_responsable']);
            $table->index('date_debut');
        });

        // 2. Table des événements
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 255);
            $table->text('description');
            $table->enum('type', ['intervention', 'reunion', 'formation', 'visite']);
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('lieu', 255);
            $table->string('coordonnees_gps', 100)->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule', 'reporte'])->default('planifie');
            $table->enum('priorite', ['normale', 'haute', 'urgente'])->default('normale');
            $table->foreignId('id_organisateur')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_projet')->nullable()->constrained('projects')->onDelete('set null');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_modification')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['date_debut', 'date_fin']);
            $table->index(['type', 'statut']);
            $table->index(['id_organisateur']);
            $table->index(['priorite']);
        });

        // 3. Table des tâches
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->date('date_echeance');
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('statut', ['a_faire', 'en_cours', 'termine'])->default('a_faire');
            $table->integer('progression')->default(0);
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_projet')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('events')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->nullable();
            $table->timestamps();

            $table->index(['statut', 'priorite']);
            $table->index(['id_utilisateur', 'statut']);
            $table->index('date_echeance');
        });

        // 4. Table des participants aux événements
       Schema::create('participant_events', function (Blueprint $table) {
            $table->id();

            // Clés étrangères avec gestion améliorée des suppressions
            $table->foreignId('id_evenement')
                  ->constrained('events')
                  ->onDelete('cascade'); // OK pour cascade car si événement supprimé, pas de participants

            $table->foreignId('id_utilisateur')
                  ->constrained('users')
                  ->onDelete('restrict'); // RESTRICT pour préserver l'historique

            // Statut de présence avec valeurs étendues
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

            // Métadonnées supplémentaires
            $table->timestamp('date_invitation')->nullable(); // Quand l'invitation a été envoyée
            $table->timestamp('date_reponse')->nullable();    // Quand la réponse a été donnée
            $table->text('commentaire')->nullable();          // Commentaire du participant
            $table->text('notes_organisateur')->nullable();   // Notes privées de l'organisateur

            // Gestion des notifications
            $table->boolean('notification_envoyee')->default(false);
            $table->boolean('rappel_envoye')->default(false);

            // Timestamps standard
            $table->timestamps();

            // Soft deletes pour garder l'historique
            $table->softDeletes();

            // CONTRAINTES ET INDEX

            // Contrainte d'unicité principale (plus souple)
            $table->unique(['id_evenement', 'id_utilisateur'], 'unique_participant_event');

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
        });

        // 5. Table des rapports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->date('date_intervention');
            $table->string('lieu', 100);
            $table->string('type_intervention', 50);
            $table->text('actions');
            $table->text('resultats');
            $table->text('problemes')->nullable();
            $table->text('recommandations')->nullable();
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_tache')->nullable()->constrained('tasks')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('events')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamps();

            $table->index(['id_utilisateur', 'date_intervention']);
            $table->index('type_intervention');
            $table->index('date_intervention');
        });

        // 6. Table des pièces jointes
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('nom_fichier', 255);
            $table->string('type_fichier', 50);
            $table->integer('taille');
            $table->string('chemin', 255);
            $table->foreignId('id_rapport')->constrained('reports')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());
            $table->timestamps();

            $table->index('id_rapport');
        });

        // 7. Table des notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('message');
            $table->enum('type', ['tache', 'evenement', 'projet', 'systeme'])->default('systeme');
            $table->boolean('lue')->default(false);
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_lecture')->nullable();
            $table->timestamps();

            $table->index(['destinataire_id', 'lue']);
            $table->index('type');
        });

        // 8. Table des journaux
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->default(now());
            $table->enum('type_action', ['connexion', 'modification', 'suppression', 'creation', 'erreur']);
            $table->text('description');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['type_action', 'date']);
            $table->index('utilisateur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('participant_events');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('events');
        Schema::dropIfExists('projects');
    }
};
