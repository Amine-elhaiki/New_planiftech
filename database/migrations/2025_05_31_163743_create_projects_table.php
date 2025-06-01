
<?php
// Migration pour la table projets
// database/migrations/xxxx_xx_xx_create_projets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('zone_geographique', 100);
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'suspendu'])->default('planifie');
            $table->foreignId('id_responsable')->constrained('users')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->default(now());

            // Index
            $table->index(['statut', 'id_responsable']);
            $table->index(['date_debut', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
