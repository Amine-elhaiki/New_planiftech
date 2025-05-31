<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Administrateur principal
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@ormvat.ma',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+212523481234',
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Technicien exemple 1
        User::create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'email' => 'ahmed.bennani@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212661234567',
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Technicien exemple 2
        User::create([
            'nom' => 'El Idrissi',
            'prenom' => 'Fatima',
            'email' => 'fatima.elidrissi@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212662345678',
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Technicien exemple 3
        User::create([
            'nom' => 'Alami',
            'prenom' => 'Youssef',
            'email' => 'youssef.alami@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212663456789',
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Administrateur secondaire
        User::create([
            'nom' => 'Amrani',
            'prenom' => 'Karim',
            'email' => 'karim.amrani@ormvat.ma',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+212523481235',
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);
    }
}
