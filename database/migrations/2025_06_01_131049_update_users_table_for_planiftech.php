<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer name si elle existe
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Ajouter les nouvelles colonnes (SANS date_modification)
            $table->string('nom', 50)->after('id');
            $table->string('prenom', 50)->after('nom');
            $table->enum('role', ['admin', 'technicien'])->default('technicien')->after('email');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('role');
            $table->string('telephone', 20)->nullable()->after('statut');
            $table->timestamp('date_creation')->default(now())->after('telephone');
            $table->timestamp('derniere_connexion')->nullable()->after('date_creation');

            // Index
            $table->index(['role', 'statut']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nom', 'prenom', 'role', 'statut', 'telephone',
                'date_creation', 'derniere_connexion'
            ]);
            $table->string('name')->after('id');
        });
    }
};
