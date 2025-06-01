
<?php
// Migration pour la table journaux
// database/migrations/xxxx_xx_xx_create_journaux_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journaux', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->default(now());
            $table->enum('type_action', ['connexion', 'modification', 'suppression', 'creation', 'erreur']);
            $table->text('description');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('adresse_ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            // Index
            $table->index(['date', 'type_action']);
            $table->index(['utilisateur_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journaux');
    }
};
