
<?php
// Migration pour la table notifications
// database/migrations/xxxx_xx_xx_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('message');
            $table->enum('type', ['tache', 'evenement', 'systeme', 'projet'])->default('systeme');
            $table->boolean('lue')->default(false);
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('date_creation')->default(now());
            $table->timestamp('date_lecture')->nullable();

            // Index
            $table->index(['destinataire_id', 'lue']);
            $table->index(['type', 'date_creation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
