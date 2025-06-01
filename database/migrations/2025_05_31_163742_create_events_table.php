
<?php
// Migration pour la table evenements
// database/migrations/xxxx_xx_xx_create_evenements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->enum('type', ['intervention', 'reunion', 'formation', 'visite'])->default('intervention');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('lieu', 100);
            $table->string('coordonnees_gps', 50)->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule', 'reporte'])->default('planifie');
            $table->enum('priorite', ['normale', 'haute', 'urgente'])->default('normale');
            $table->foreignId('id_organisateur')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_projet')->nullable()->constrained('projets')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->default(now());

            // Index
            $table->index(['type', 'statut']);
            $table->index(['date_debut', 'date_fin']);
            $table->index('id_organisateur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
