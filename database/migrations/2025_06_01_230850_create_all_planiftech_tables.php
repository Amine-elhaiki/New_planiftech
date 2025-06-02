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
        Schema::create('projets', function (Blueprint $table) {
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
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->date('date_echeance');
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('statut', ['a_faire', 'en_cours', 'termine'])->default('a_faire');
            $table->integer('progression')->default(0);
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_projet')->nullable()->constrained('projets')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('evenements')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->nullable();

            $table->index(['statut', 'priorite']);
            $table->index(['id_utilisateur', 'statut']);
            $table->index('date_echeance');
        });

        // 4. Table des participants aux événements
        Schema::create('participant_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')->constrained('events')->onDelete('cascade');
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('cascade');
            $table->enum('statut_presence', ['invite', 'confirme', 'decline', 'present', 'absent'])->default('invite');
            $table->timestamps();

            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['id_evenement', 'id_utilisateur']);

            // Index pour améliorer les performances
            $table->index(['id_evenement', 'statut_presence']);
            $table->index(['id_utilisateur']);
        });

        // 5. Table des rapports
        Schema::create('rapports', function (Blueprint $table) {
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
            $table->foreignId('id_tache')->nullable()->constrained('taches')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('evenements')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());

            $table->index(['id_utilisateur', 'date_intervention']);
            $table->index('type_intervention');
            $table->index('date_intervention');
        });

        // 6. Table des pièces jointes
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_fichier', 255);
            $table->string('type_fichier', 50);
            $table->integer('taille');
            $table->string('chemin', 255);
            $table->foreignId('id_rapport')->constrained('rapports')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());

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

            $table->index(['destinataire_id', 'lue']);
            $table->index('type');
        });

        // 8. Table des journaux
        Schema::create('journaux', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->default(now());
            $table->enum('type_action', ['connexion', 'modification', 'suppression', 'creation', 'erreur']);
            $table->text('description');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->index(['type_action', 'date']);
            $table->index('utilisateur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journaux');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('pieces_jointes');
        Schema::dropIfExists('rapports');
        Schema::dropIfExists('participants_evenements');
        Schema::dropIfExists('taches');
        Schema::dropIfExists('evenements');
        Schema::dropIfExists('projets');
    }
};
