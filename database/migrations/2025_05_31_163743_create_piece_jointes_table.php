
<?php
// Migration pour la table pieces_jointes
// database/migrations/xxxx_xx_xx_create_pieces_jointes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_fichier', 255);
            $table->string('type_fichier', 50);
            $table->integer('taille');
            $table->string('chemin', 255);
            $table->foreignId('id_rapport')->constrained('rapports')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());

            // Index
            $table->index(['id_rapport', 'type_fichier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
