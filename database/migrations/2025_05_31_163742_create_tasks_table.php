<?php
// Migration pour la table taches
// database/migrations/xxxx_xx_xx_create_taches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description');
            $table->date('date_echeance');
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('statut', ['a_faire', 'en_cours', 'termine'])->default('a_faire');
            $table->integer('progression')->default(0);
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_projet')->nullable()->constrained('projets')->onDelete('set null');
            $table->foreignId('id_evenement')->nullable()->constrained('evenements')->onDelete('set null');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_modification')->default(now());

            // Index
            $table->index(['statut', 'priorite']);
            $table->index(['id_utilisateur', 'statut']);
            $table->index('date_echeance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
