<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer les utilisateurs
        $this->creerUtilisateurs();

        // 2. Créer les projets
        $this->creerProjets();

        // 3. Créer les tâches
        $this->creerTaches();

        // 4. Créer les événements
        $this->creerEvenements();

        // 5. Créer les rapports
        $this->creerRapports();

        // 6. Créer des notifications
        $this->creerNotifications();

        // 7. Créer des entrées de journal
        $this->creerJournaux();

        $this->command->info('Base de données seedée avec succès !');
    }

    private function creerUtilisateurs()
    {
        $this->command->info('Création des utilisateurs...');

        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@ormvat.ma',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+212523481234',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'Amrani',
            'prenom' => 'Karim',
            'email' => 'karim.amrani@ormvat.ma',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+212523481235',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'email' => 'ahmed.bennani@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212661234567',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'El Idrissi',
            'prenom' => 'Fatima',
            'email' => 'fatima.elidrissi@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212662345678',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'Alami',
            'prenom' => 'Youssef',
            'email' => 'youssef.alami@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212663456789',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'Berrada',
            'prenom' => 'Laila',
            'email' => 'laila.berrada@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212664567890',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nom' => 'Zeroual',
            'prenom' => 'Omar',
            'email' => 'omar.zeroual@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212665678901',
            'email_verified_at' => now(),
        ]);
    }

    private function creerProjets()
    {
        $this->command->info('Création des projets...');

        $projets = [
            [
                'nom' => 'Modernisation réseau irrigation Tadla-Nord',
                'description' => 'Modernisation du réseau d\'irrigation dans la zone Tadla-Nord comprenant la réhabilitation des canaux principaux et l\'installation de nouveaux systèmes de régulation.',
                'date_debut' => now()->subDays(30),
                'date_fin' => now()->addDays(120),
                'zone_geographique' => 'Tadla-Nord',
                'statut' => 'en_cours',
                'id_responsable' => 2,
            ],
            [
                'nom' => 'Maintenance préventive stations pompage',
                'description' => 'Programme de maintenance préventive annuel pour toutes les stations de pompage de la région ORMVAT.',
                'date_debut' => now()->subDays(15),
                'date_fin' => now()->addDays(60),
                'zone_geographique' => 'Ensemble région ORMVAT',
                'statut' => 'en_cours',
                'id_responsable' => 3,
            ],
            [
                'nom' => 'Installation compteurs secteur Fkih Ben Salah',
                'description' => 'Installation de nouveaux compteurs d\'eau intelligents dans le secteur de Fkih Ben Salah pour améliorer le suivi de la consommation.',
                'date_debut' => now()->addDays(10),
                'date_fin' => now()->addDays(90),
                'zone_geographique' => 'Fkih Ben Salah',
                'statut' => 'planifie',
                'id_responsable' => 4,
            ],
        ];

        foreach ($projets as $projet) {
            DB::table('projets')->insert($projet);
        }
    }

    private function creerTaches()
    {
        $this->command->info('Création des tâches...');

        $taches = [
            [
                'titre' => 'Inspection canal principal secteur B4',
                'description' => 'Inspection complète du canal principal du secteur B4 pour évaluer l\'état de la structure et identifier les besoins de réparation.',
                'date_echeance' => now()->addDays(5),
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'progression' => 60,
                'id_utilisateur' => 3,
                'id_projet' => 1,
            ],
            [
                'titre' => 'Réparation vanne distribution B4-12',
                'description' => 'Réparation de la vanne de distribution B4-12 défectueuse.',
                'date_echeance' => now()->addDays(3),
                'priorite' => 'haute',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 5,
                'id_projet' => 1,
            ],
            [
                'titre' => 'Installation nouveau système régulation',
                'description' => 'Installation du nouveau système de régulation automatique pour le secteur B4.',
                'date_echeance' => now()->addDays(15),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 4,
                'id_projet' => 1,
            ],
        ];

        foreach ($taches as $tache) {
            DB::table('taches')->insert($tache);
        }
    }

    private function creerEvenements()
    {
        $this->command->info('Création des événements...');

        $evenements = [
            [
                'titre' => 'Réunion équipe maintenance hebdomadaire',
                'description' => 'Réunion hebdomadaire de l\'équipe de maintenance pour faire le point sur les interventions en cours.',
                'type' => 'reunion',
                'date_debut' => now()->addDays(1)->setTime(9, 0),
                'date_fin' => now()->addDays(1)->setTime(10, 30),
                'lieu' => 'Salle de réunion ORMVAT',
                'statut' => 'planifie',
                'priorite' => 'normale',
                'id_organisateur' => 2,
                'id_projet' => null,
            ],
            [
                'titre' => 'Intervention urgence station P12',
                'description' => 'Intervention d\'urgence sur la station de pompage P12 suite à une panne électrique.',
                'type' => 'intervention',
                'date_debut' => now()->addHours(4),
                'date_fin' => now()->addHours(8),
                'lieu' => 'Station pompage P12, secteur Tadla-Nord',
                'statut' => 'planifie',
                'priorite' => 'urgente',
                'id_organisateur' => 3,
                'id_projet' => 2,
            ],
        ];

        foreach ($evenements as $evenement) {
            DB::table('evenements')->insert($evenement);
        }
    }

    private function creerRapports()
    {
        $this->command->info('Création des rapports...');

        $rapports = [
            [
                'titre' => 'Maintenance station pompage P08 - Mars 2025',
                'date_intervention' => now()->subDays(3),
                'lieu' => 'Station pompage P08',
                'type_intervention' => 'maintenance_preventive',
                'actions' => 'Vérification complète des pompes principales, contrôle des systèmes électriques.',
                'resultats' => 'Station opérationnelle, tous les systèmes fonctionnent correctement.',
                'problemes' => null,
                'recommandations' => 'Programmer le remplacement du roulement de la pompe n°2 dans les 6 mois.',
                'id_utilisateur' => 3,
                'id_tache' => null,
                'id_evenement' => null,
            ],
        ];

        foreach ($rapports as $rapport) {
            DB::table('rapports')->insert($rapport);
        }
    }

    private function creerNotifications()
    {
        $this->command->info('Création des notifications...');

        $utilisateurs = User::all();

        foreach ($utilisateurs as $utilisateur) {
            DB::table('notifications')->insert([
                'titre' => 'Bienvenue sur PlanifTech',
                'message' => 'Bienvenue sur la plateforme PlanifTech de l\'ORMVAT.',
                'type' => 'systeme',
                'destinataire_id' => $utilisateur->id,
                'lue' => false,
            ]);
        }
    }

    private function creerJournaux()
    {
        $this->command->info('Création des journaux...');

        DB::table('journaux')->insert([
            'type_action' => 'creation',
            'description' => 'Initialisation de la base de données PlanifTech',
            'utilisateur_id' => 1,
            'adresse_ip' => '127.0.0.1',
        ]);
    }
}
