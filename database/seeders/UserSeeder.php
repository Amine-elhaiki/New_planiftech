<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer un administrateur par défaut
        User::updateOrCreate(
            ['email' => 'admin@ormvat.ma'],
            [
                'nom' => 'Administrateur',
                'prenom' => 'Système',
                'email' => 'admin@ormvat.ma',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'statut' => 'actif',
                'telephone' => '+212 523 123 456',
                'email_verified_at' => now(),
                'date_creation' => now(),
            ]
        );

        // Créer un technicien par défaut
        User::updateOrCreate(
            ['email' => 'tech@ormvat.ma'],
            [
                'nom' => 'Bennani',
                'prenom' => 'Ahmed',
                'email' => 'tech@ormvat.ma',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'statut' => 'actif',
                'telephone' => '+212 523 123 457',
                'email_verified_at' => now(),
                'date_creation' => now(),
            ]
        );

        // Créer quelques autres techniciens pour les tests
        User::updateOrCreate(
            ['email' => 'mohamed.alami@ormvat.ma'],
            [
                'nom' => 'Alami',
                'prenom' => 'Mohamed',
                'email' => 'mohamed.alami@ormvat.ma',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'statut' => 'actif',
                'telephone' => '+212 523 123 458',
                'email_verified_at' => now(),
                'date_creation' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'fatima.zahra@ormvat.ma'],
            [
                'nom' => 'Zahra',
                'prenom' => 'Fatima',
                'email' => 'fatima.zahra@ormvat.ma',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'statut' => 'actif',
                'telephone' => '+212 523 123 459',
                'email_verified_at' => now(),
                'date_creation' => now(),
            ]
        );

        $this->command->info('✅ Utilisateurs créés avec succès !');
        $this->command->info('📧 Admin: admin@ormvat.ma / password');
        $this->command->info('👷 Technicien: tech@ormvat.ma / password');
    }
}
