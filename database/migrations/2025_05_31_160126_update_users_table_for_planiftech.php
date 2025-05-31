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
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes existantes si elles existent
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Ajouter les nouvelles colonnes
            $table->string('nom', 50)->after('id');
            $table->string('prenom', 50)->after('nom');
            $table->enum('role', ['admin', 'technicien'])->default('technicien')->after('email');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('role');
            $table->string('telephone', 20)->nullable()->after('statut');
            $table->timestamp('date_creation')->default(now())->after('telephone');
            $table->timestamp('derniere_connexion')->nullable()->after('date_creation');

            // Index pour optimiser les requêtes
            $table->index(['role', 'statut']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropColumn([
                'nom',
                'prenom',
                'role',
                'statut',
                'telephone',
                'date_creation',
                'derniere_connexion'
            ]);

            // Supprimer les index
            $table->dropIndex(['users_role_statut_index']);
            $table->dropIndex(['users_statut_index']);

            // Remettre la colonne name si nécessaire
            $table->string('name')->after('id');
        });
    }
};
