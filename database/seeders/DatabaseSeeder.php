<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider les tables dans le bon ordre
        DB::table('participant_events')->truncate();
        DB::table('pieces_jointes')->truncate();
        DB::table('reports')->truncate();
        DB::table('tasks')->truncate();
        DB::table('events')->truncate();
        DB::table('projects')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Création des utilisateurs...\n";
        
        // 1. Créer les utilisateurs
        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'Système',
                'email' => 'admin@planiftech.ma',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'telephone' => '+212 5 23-45-67-89',
                'adresse' => 'Siège ORMVAT, Tadla',
                'statut' => 'actif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Alaoui',
                'prenom' => 'Mohammed',
                'email' => 'chef.projet@planiftech.ma',
                'password' => Hash::make('chef123'),
                'role' => 'chef_projet',
                'telephone' => '+212 6 12-34-56-78',
                'adresse' => 'Béni-Mellal, Maroc',
                'statut' => 'actif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Benali',
                'prenom' => 'Ahmed',
                'email' => 'technicien@planiftech.ma',
                'password' => Hash::make('tech123'),
                'role' => 'technicien',
                'telephone' => '+212 6 98-76-54-32',
                'adresse' => 'Kasba Tadla, Maroc',
                'statut' => 'actif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Moussaoui',
                'prenom' => 'Fatima',
                'email' => 'f.moussaoui@planiftech.ma',
                'password' => Hash::make('fatima123'),
                'role' => 'chef_projet',
                'telephone' => '+212 6 11-22-33-44',
                'adresse' => 'Fquih Ben Salah, Maroc',
                'statut' => 'actif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Tazi',
                'prenom' => 'Youssef',
                'email' => 'y.tazi@planiftech.ma',
                'password' => Hash::make('youssef123'),
                'role' => 'technicien',
                'telephone' => '+212 6 55-66-77-88',
                'adresse' => 'Béni-Mellal, Maroc',
                'statut' => 'actif',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        echo "Création des projets...\n";

        // 2. Créer les projets (avec les bonnes colonnes)
        $projects = [
            [
                'nom' => 'Modernisation réseau irrigation Tadla-Nord',
                'description' => 'Modernisation du réseau d\'irrigation dans la zone Tadla-Nord comprenant la réhabilitation des canaux principaux et l\'installation de nouveaux systèmes de régulation.',
                'date_debut' => Carbon::now()->subMonths(2),
                'date_fin_prevue' => Carbon::now()->addMonths(4), // Utiliser date_fin_prevue
                'date_fin_reelle' => null,
                'statut' => 'en_cours',
                'priorite' => 'haute',
                'budget' => 2500000.00,
                'cout_reel' => 850000.00,
                'id_responsable' => 2, // Chef projet Mohammed Alaoui
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Maintenance stations pompage Sud',
                'description' => 'Programme de maintenance préventive et corrective des stations de pompage dans la région Sud de l\'ORMVAT.',
                'date_debut' => Carbon::now()->subWeeks(3),
                'date_fin_prevue' => Carbon::now()->addWeeks(6),
                'date_fin_reelle' => null,
                'statut' => 'en_cours',
                'priorite' => 'normale',
                'budget' => 800000.00,
                'cout_reel' => 245000.00,
                'id_responsable' => 4, // Chef projet Fatima Moussaoui
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Installation capteurs IoT',
                'description' => 'Déploiement de capteurs IoT pour le monitoring en temps réel des niveaux d\'eau et de la qualité dans les bassins de stockage.',
                'date_debut' => Carbon::now()->addWeeks(2),
                'date_fin_prevue' => Carbon::now()->addMonths(3),
                'date_fin_reelle' => null,
                'statut' => 'planifie',
                'priorite' => 'haute',
                'budget' => 1200000.00,
                'cout_reel' => null,
                'id_responsable' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Formation personnel technique',
                'description' => 'Programme de formation du personnel technique sur les nouvelles technologies de gestion des ressources en eau.',
                'date_debut' => Carbon::now()->subDays(10),
                'date_fin_prevue' => Carbon::now()->addDays(20),
                'date_fin_reelle' => null,
                'statut' => 'en_cours',
                'priorite' => 'normale',
                'budget' => 150000.00,
                'cout_reel' => 65000.00,
                'id_responsable' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('projects')->insert($projects);

        echo "Création des tâches...\n";

        // 3. Créer les tâches
        $tasks = [
            // Tâches du projet Modernisation réseau
            [
                'titre' => 'Inspection réseau principal Tadla-Nord',
                'description' => 'Inspection complète du réseau principal d\'irrigation pour identifier les points de réhabilitation prioritaires.',
                'statut' => 'termine',
                'priorite' => 'haute',
                'date_creation' => Carbon::now()->subDays(45),
                'date_echeance' => Carbon::now()->subDays(30),
                'date_debut_reelle' => Carbon::now()->subDays(45),
                'date_fin_reelle' => Carbon::now()->subDays(32),
                'duree_estimee' => 10, // jours
                'duree_reelle' => 13,
                'id_projet' => 1,
                'id_utilisateur' => 3, // Ahmed Benali
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Réhabilitation canal principal Section A',
                'description' => 'Travaux de réhabilitation du canal principal section A : curage, étanchéité et réparation des ouvrages de régulation.',
                'statut' => 'en_cours',
                'priorite' => 'haute',
                'date_creation' => Carbon::now()->subDays(20),
                'date_echeance' => Carbon::now()->addDays(15),
                'date_debut_reelle' => Carbon::now()->subDays(15),
                'date_fin_reelle' => null,
                'duree_estimee' => 30,
                'duree_reelle' => null,
                'id_projet' => 1,
                'id_utilisateur' => 5, // Youssef Tazi
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Installation régulateurs automatiques',
                'description' => 'Installation de nouveaux régulateurs automatiques sur les points stratégiques du réseau.',
                'statut' => 'a_faire',
                'priorite' => 'normale',
                'date_creation' => Carbon::now()->subDays(5),
                'date_echeance' => Carbon::now()->addDays(45),
                'date_debut_reelle' => null,
                'date_fin_reelle' => null,
                'duree_estimee' => 20,
                'duree_reelle' => null,
                'id_projet' => 1,
                'id_utilisateur' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tâches du projet Maintenance stations
            [
                'titre' => 'Maintenance station pompage SP-01',
                'description' => 'Maintenance préventive complète de la station de pompage SP-01 : révision moteurs, vérification systèmes électriques.',
                'statut' => 'termine',
                'priorite' => 'normale',
                'date_creation' => Carbon::now()->subDays(25),
                'date_echeance' => Carbon::now()->subDays(10),
                'date_debut_reelle' => Carbon::now()->subDays(23),
                'date_fin_reelle' => Carbon::now()->subDays(12),
                'duree_estimee' => 8,
                'duree_reelle' => 11,
                'id_projet' => 2,
                'id_utilisateur' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Diagnostic stations SP-02 et SP-03',
                'description' => 'Diagnostic approfondi des stations SP-02 et SP-03 pour planifier les interventions de maintenance.',
                'statut' => 'en_cours',
                'priorite' => 'haute',
                'date_creation' => Carbon::now()->subDays(12),
                'date_echeance' => Carbon::now()->addDays(3),
                'date_debut_reelle' => Carbon::now()->subDays(8),
                'date_fin_reelle' => null,
                'duree_estimee' => 15,
                'duree_reelle' => null,
                'id_projet' => 2,
                'id_utilisateur' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tâches du projet IoT
            [
                'titre' => 'Étude faisabilité capteurs IoT',
                'description' => 'Étude de faisabilité technique et économique pour le déploiement de capteurs IoT.',
                'statut' => 'a_faire',
                'priorite' => 'haute',
                'date_creation' => Carbon::now(),
                'date_echeance' => Carbon::now()->addDays(20),
                'date_debut_reelle' => null,
                'date_fin_reelle' => null,
                'duree_estimee' => 15,
                'duree_reelle' => null,
                'id_projet' => 3,
                'id_utilisateur' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tasks')->insert($tasks);

        echo "Création des événements...\n";

        // 4. Créer les événements
        $events = [
            [
                'titre' => 'Réunion lancement projet Tadla-Nord',
                'description' => 'Réunion de lancement officiel du projet de modernisation du réseau d\'irrigation Tadla-Nord avec toutes les parties prenantes.',
                'date_debut' => Carbon::now()->subDays(60)->setTime(9, 0),
                'date_fin' => Carbon::now()->subDays(60)->setTime(12, 0),
                'lieu' => 'Salle de conférence ORMVAT',
                'type' => 'reunion',
                'statut' => 'termine',
                'priorite' => 'haute',
                'id_organisateur' => 2,
                'id_projet' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Formation maintenance préventive',
                'description' => 'Session de formation du personnel technique sur les bonnes pratiques de maintenance préventive des équipements de pompage.',
                'date_debut' => Carbon::now()->addDays(5)->setTime(14, 0),
                'date_fin' => Carbon::now()->addDays(5)->setTime(17, 0),
                'lieu' => 'Centre de formation ORMVAT',
                'type' => 'formation',
                'statut' => 'planifie',
                'priorite' => 'normale',
                'id_organisateur' => 4,
                'id_projet' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Inspection terrain canal principal',
                'description' => 'Visite d\'inspection sur le terrain du canal principal pour évaluer l\'avancement des travaux de réhabilitation.',
                'date_debut' => Carbon::now()->addDays(3)->setTime(8, 0),
                'date_fin' => Carbon::now()->addDays(3)->setTime(16, 0),
                'lieu' => 'Canal principal Tadla-Nord',
                'type' => 'inspection',
                'statut' => 'planifie',
                'priorite' => 'haute',
                'id_organisateur' => 2,
                'id_projet' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Audit qualité station SP-01',
                'description' => 'Audit de qualité post-maintenance de la station de pompage SP-01 pour valider les interventions réalisées.',
                'date_debut' => Carbon::now()->subDays(2)->setTime(10, 0),
                'date_fin' => Carbon::now()->subDays(2)->setTime(15, 0),
                'lieu' => 'Station pompage SP-01',
                'type' => 'audit',
                'statut' => 'termine',
                'priorite' => 'normale',
                'id_organisateur' => 4,
                'id_projet' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('events')->insert($events);

        echo "Création des participants aux événements...\n";

        // 5. Créer les participants aux événements
        $participants = [
            // Réunion lancement projet Tadla-Nord
            ['id_evenement' => 1, 'id_utilisateur' => 1, 'statut_presence' => 'present', 'role_evenement' => 'participant'],
            ['id_evenement' => 1, 'id_utilisateur' => 2, 'statut_presence' => 'present', 'role_evenement' => 'organisateur'],
            ['id_evenement' => 1, 'id_utilisateur' => 3, 'statut_presence' => 'present', 'role_evenement' => 'participant'],
            ['id_evenement' => 1, 'id_utilisateur' => 5, 'statut_presence' => 'present', 'role_evenement' => 'participant'],

            // Formation maintenance préventive
            ['id_evenement' => 2, 'id_utilisateur' => 3, 'statut_presence' => 'confirme', 'role_evenement' => 'participant'],
            ['id_evenement' => 2, 'id_utilisateur' => 4, 'statut_presence' => 'confirme', 'role_evenement' => 'organisateur'],
            ['id_evenement' => 2, 'id_utilisateur' => 5, 'statut_presence' => 'confirme', 'role_evenement' => 'participant'],

            // Inspection terrain canal principal
            ['id_evenement' => 3, 'id_utilisateur' => 2, 'statut_presence' => 'confirme', 'role_evenement' => 'organisateur'],
            ['id_evenement' => 3, 'id_utilisateur' => 3, 'statut_presence' => 'confirme', 'role_evenement' => 'participant'],
            ['id_evenement' => 3, 'id_utilisateur' => 5, 'statut_presence' => 'confirme', 'role_evenement' => 'participant'],

            // Audit qualité station SP-01
            ['id_evenement' => 4, 'id_utilisateur' => 4, 'statut_presence' => 'present', 'role_evenement' => 'organisateur'],
            ['id_evenement' => 4, 'id_utilisateur' => 5, 'statut_presence' => 'present', 'role_evenement' => 'participant'],
        ];

        foreach ($participants as $participant) {
            $participant['date_reponse'] = now();
            $participant['created_at'] = now();
            $participant['updated_at'] = now();
        }

        DB::table('participant_events')->insert($participants);

        echo "Création des rapports...\n";

        // 6. Créer des rapports d'intervention
        $reports = [
            [
                'titre' => 'Rapport inspection réseau Tadla-Nord',
                'date_intervention' => Carbon::now()->subDays(32),
                'lieu' => 'Réseau irrigation Tadla-Nord - Secteur A',
                'type_intervention' => 'Inspection',
                'actions' => 'Inspection visuelle complète du réseau principal et des canaux secondaires. Identification des points de fuite et des zones d\'envasement. Relevé topographique des sections dégradées.',
                'resultats' => 'Identification de 12 points critiques nécessitant une intervention urgente. 3 km de canaux nécessitent un curage complet. Système de régulation vétuste à remplacer sur 5 points stratégiques.',
                'problemes' => 'Accès difficile à certaines sections en raison de la végétation. Quelques relevés topographiques incomplets due aux conditions météorologiques.',
                'recommandations' => 'Programmer les travaux de curage avant la saison d\'irrigation. Prévoir le remplacement des régulateurs dans les 2 mois. Débroussaillage nécessaire pour faciliter les futures inspections.',
                'statut' => 'valide',
                'id_utilisateur' => 3, // Ahmed Benali
                'id_tache' => 1,
                'id_evenement' => null,
                'date_creation' => Carbon::now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Rapport maintenance station SP-01',
                'date_intervention' => Carbon::now()->subDays(12),
                'lieu' => 'Station de pompage SP-01',
                'type_intervention' => 'Maintenance préventive',
                'actions' => 'Révision complète des 2 groupes électropompes. Vérification et nettoyage des systèmes électriques. Contrôle des automatismes et calibrage des sondes de niveau. Remplacement des filtres et vidange des huiles.',
                'resultats' => 'Station opérationnelle à 100%. Rendement amélioré de 8% par rapport aux mesures précédentes. Tous les paramètres de fonctionnement dans les normes.',
                'problemes' => 'Usure prématurée des roulements du groupe 2 (probablement due à un mauvais alignement). Joint défaillant détecté sur la conduite de refoulement.',
                'recommandations' => 'Prévoir le remplacement des roulements du groupe 2 dans les 3 mois. Programmer la réparation du joint de la conduite de refoulement. Renforcer la surveillance des vibrations.',
                'statut' => 'valide',
                'id_utilisateur' => 5, // Youssef Tazi
                'id_tache' => 4,
                'id_evenement' => 4,
                'date_creation' => Carbon::now()->subDays(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Rapport diagnostic stations SP-02 et SP-03',
                'date_intervention' => Carbon::now()->subDays(3),
                'lieu' => 'Stations de pompage SP-02 et SP-03',
                'type_intervention' => 'Support technique',
                'actions' => 'Diagnostic approfondi des deux stations : mesure des performances, analyse vibratoire, contrôle électrique, évaluation de l\'état des équipements.',
                'resultats' => 'SP-02 : Fonctionnement correct mais efficacité réduite de 15%. SP-03 : Problème majeur sur le système de régulation automatique, fonctionnement en mode manuel uniquement.',
                'problemes' => 'SP-02 : Encrassement important du système de filtration. SP-03 : Carte électronique de régulation défaillante, capteur de pression défectueux.',
                'recommandations' => 'SP-02 : Nettoyage approfondi du système de filtration et remplacement des éléments filtrants. SP-03 : Remplacement urgent de la carte de régulation et du capteur de pression.',
                'statut' => 'en_attente',
                'id_utilisateur' => 3, // Ahmed Benali
                'id_tache' => 5,
                'id_evenement' => null,
                'date_creation' => Carbon::now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('reports')->insert($reports);

        echo "Création des notifications...\n";

        // 7. Créer des notifications
        $notifications = [
            [
                'titre' => 'Nouveau projet assigné',
                'message' => 'Vous avez été désigné comme responsable du projet "Installation capteurs IoT".',
                'type' => 'info',
                'lu' => false,
                'id_utilisateur' => 2,
                'lien' => '/projects/3',
                'date_creation' => Carbon::now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Tâche en retard',
                'message' => 'La tâche "Diagnostic stations SP-02 et SP-03" dépasse sa date d\'échéance prévue.',
                'type' => 'warning',
                'lu' => false,
                'id_utilisateur' => 3,
                'lien' => '/tasks/5',
                'date_creation' => Carbon::now()->subHours(6),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Rapport validé',
                'message' => 'Votre rapport "Rapport maintenance station SP-01" a été validé par l\'administration.',
                'type' => 'success',
                'lu' => true,
                'id_utilisateur' => 5,
                'lien' => '/reports/2',
                'date_creation' => Carbon::now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Événement à venir',
                'message' => 'Rappel : Formation maintenance préventive prévue dans 5 jours.',
                'type' => 'info',
                'lu' => false,
                'id_utilisateur' => 3,
                'lien' => '/events/2',
                'date_creation' => Carbon::now()->subHours(12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notifications')->insert($notifications);

        echo "Base de données initialisée avec succès !\n";
        echo "Utilisateurs créés :\n";
        echo "- Admin : admin@planiftech.ma / admin123\n";
        echo "- Chef projet : chef.projet@planiftech.ma / chef123\n";
        echo "- Technicien : technicien@planiftech.ma / tech123\n";
        echo "- Chef projet 2 : f.moussaoui@planiftech.ma / fatima123\n";
        echo "- Technicien 2 : y.tazi@planiftech.ma / youssef123\n";
    }
}