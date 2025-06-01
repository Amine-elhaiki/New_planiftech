<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants_evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')->constrained('evenements')->onDelete('cascade');
            $table->foreignId('id_utilisateur')->constrained('users')->onDelete('cascade');
            $table->enum('statut_presence', ['invite', 'confirme', 'decline', 'present', 'absent'])->default('invite');

            // Index pour optimiser les requêtes
            $table->index(['id_evenement', 'id_utilisateur']);
            $table->index(['id_utilisateur', 'statut_presence']);

            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['id_evenement', 'id_utilisateur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants_evenements');
    }
};
