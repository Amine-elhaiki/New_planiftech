<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'énumération du rôle pour inclure 'chef_projet'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'technicien', 'chef_projet') DEFAULT 'technicien'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'énumération précédente
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'technicien') DEFAULT 'technicien'");
    }
};