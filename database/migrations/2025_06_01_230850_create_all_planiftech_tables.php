<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table des utilisateurs - SEULEMENT si elle n'existe pas déjà
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nom', 50);
                $table->string('prenom', 50);
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('role', ['admin', 'chef_projet', 'technicien'])->default('technicien');
                $table->string('telephone', 20)->nullable();
                $table->text('adresse')->nullable();
                $table->enum('statut', ['actif', 'inactif'])->default('actif');
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();

                $table->index(['role', 'statut']);
                $table->index('email');
            });

            // Aussi créer les tables liées aux utilisateurs Laravel
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        } else {
            // Si la table existe déjà, mettre à jour sa structure
            Schema::table('users', function (Blueprint $table) {
                // Ajouter les colonnes manquantes si elles n'existent pas
                if (!Schema::hasColumn('users', 'nom')) {
                    $table->string('nom', 50)->after('id');
                }
                if (!Schema::hasColumn('users', 'prenom')) {
                    $table->string('prenom', 50)->after('nom');
                }
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'chef_projet', 'technicien'])->default('technicien')->after('password');
                }
                if (!Schema::hasColumn('users', 'telephone')) {
                    $table->string('telephone', 20)->nullable()->after('role');
                }
                if (!Schema::hasColumn('users', 'adresse')) {
                    $table->text('adresse')->nullable()->after('telephone');
                }
                if (!Schema::hasColumn('users', 'statut')) {
                    $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('adresse');
                }
            });

            // Ajouter les index s'ils n'existent pas
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->index(['role', 'statut']);
                    $table->index('email'); // Peut déjà exister
                });
            } catch (\Exception $e) {
                // Ignorer si les index existent déjà
            }
        }

        // 2. Table des projets
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description');
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->date('date_fin_reelle')->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'suspendu', 'annule'])->default('planifie');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('cout_reel', 15, 2)->nullable();
            $table->foreignId('id_responsable')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['statut', 'priorite']);
            $table->index(['date_debut', 'date_fin_prevue']);
        });

        // 3. Table des tâches
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->enum('statut', ['a_faire', 'en_cours', 'termine', 'suspendu', 'annule'])->default('a_faire');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->date('date_creation')->default(now());
            $table->date('date_echeance');
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();
            $table->integer('duree_estimee')->nullable();
            $table->integer('duree_reelle')->nullable();
            $table->foreignId('id_projet')->constrained('projects')->onDelete('cascade');
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['statut', 'priorite']);
            $table->index(['date_echeance', 'statut']);
            $table->index(['id_projet', 'id_utilisateur']);
        });

        // 4. Table des événements
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->datetime('date_debut');
            $table->datetime('date_fin');
            $table->string('lieu', 100);
            $table->enum('type', ['reunion', 'formation', 'intervention', 'visite', 'maintenance', 'inspection', 'audit'])->default('reunion');
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule', 'reporte'])->default('planifie');
            $table->enum('priorite', ['normale', 'haute', 'urgente'])->default('normale');
            $table->foreignId('id_organisateur')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_projet')->nullable()->constrained('projects')->onDelete('set null');
            $table->timestamps();

            $table->index(['date_debut', 'date_fin']);
            $table->index(['type', 'statut']);
            $table->index(['id_organisateur', 'id_projet']);
        });

        // 5. Table des participants aux événements
        Schema::create('participant_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')->constrained('events')->onDelete('cascade');
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('cascade');
            $table->enum('statut_presence', ['invite', 'confirme', 'decline', 'present', 'absent'])->default('invite');
            $table->enum('role_evenement', ['participant', 'organisateur', 'moderateur'])->default('participant');
            $table->timestamp('date_reponse')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->unique(['id_evenement', 'id_utilisateur']);
            $table->index(['statut_presence', 'created_at'], 'idx_stats_presence');
            $table->index(['role_evenement', 'created_at'], 'idx_stats_roles');
        });

        // 6. Table des rapports
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
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_tache')->nullable()->constrained('tasks')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('events')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamps();

            $table->index('date_intervention');
            $table->index(['id_utilisateur', 'statut']);
            $table->index('statut');
        });

        // 7. Table des pièces jointes
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_fichier', 255);
            $table->string('nom_original', 255);
            $table->string('type_fichier', 100);
            $table->bigInteger('taille');
            $table->string('chemin', 500);
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('id_rapport')->constrained('reports')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());
            $table->timestamps();

            $table->index(['id_rapport']);
            $table->index(['type_fichier']);
        });

        // 8. Table des notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('message');
            $table->enum('type', ['info', 'success', 'warning', 'error'])->default('info');
            $table->boolean('lu')->default(false);
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('cascade');
            $table->string('lien')->nullable();
            $table->timestamp('date_creation')->default(now());
            $table->timestamps();

            $table->index(['id_utilisateur', 'lu']);
            $table->index('date_creation');
        });

        // 9. Table des journaux d'activité
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('action', 100);
            $table->text('description');
            $table->string('table_affectee', 50);
            $table->unsignedBigInteger('id_enregistrement')->nullable();
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('restrict');
            $table->timestamp('date_action')->default(now());
            $table->string('adresse_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['id_utilisateur', 'date_action']);
            $table->index(['table_affectee', 'id_enregistrement']);
            $table->index('date_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('pieces_jointes');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('participant_events');
        Schema::dropIfExists('events');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
        
        // Ne supprimer users que si on l'a créée nous-mêmes
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};