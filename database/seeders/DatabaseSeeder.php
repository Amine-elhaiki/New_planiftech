<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Event;
use App\Models\Report;
use App\Models\Notification;
use App\Models\Journal;

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

        // Administrateurs
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

        // Techniciens
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

        User::create([
            'nom' => 'Berrada',
            'prenom' => 'Laila',
            'email' => 'laila.berrada@ormvat.ma',
            'password' => Hash::make('technicien123'),
            'role' => 'technicien',
            'statut' => 'actif',
            'telephone' => '+212664567890',
            'date_creation' => now(),
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
            'date_creation' => now(),
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
                'id_responsable' => 2, // Karim Amrani
            ],
            [
                'nom' => 'Maintenance préventive stations pompage',
                'description' => 'Programme de maintenance préventive annuel pour toutes les stations de pompage de la région ORMVAT.',
                'date_debut' => now()->subDays(15),
                'date_fin' => now()->addDays(60),
                'zone_geographique' => 'Ensemble région ORMVAT',
                'statut' => 'en_cours',
                'id_responsable' => 3, // Ahmed Bennani
            ],
            [
                'nom' => 'Installation compteurs secteur Fkih Ben Salah',
                'description' => 'Installation de nouveaux compteurs d\'eau intelligents dans le secteur de Fkih Ben Salah pour améliorer le suivi de la consommation.',
                'date_debut' => now()->addDays(10),
                'date_fin' => now()->addDays(90),
                'zone_geographique' => 'Fkih Ben Salah',
                'statut' => 'planifie',
                'id_responsable' => 4, // Fatima El Idrissi
            ],
            [
                'nom' => 'Contrôle qualité eau irrigation 2025',
                'description' => 'Programme annuel de contrôle de la qualité de l\'eau d\'irrigation dans tous les secteurs.',
                'date_debut' => now()->subDays(5),
                'date_fin' => now()->addDays(350),
                'zone_geographique' => 'Toutes zones',
                'statut' => 'en_cours',
                'id_responsable' => 6, // Laila Berrada
            ],
            [
                'nom' => 'Formation techniciens équipements modernes',
                'description' => 'Formation du personnel technique sur l\'utilisation et maintenance des nouveaux équipements d\'irrigation.',
                'date_debut' => now()->addDays(20),
                'date_fin' => now()->addDays(50),
                'zone_geographique' => 'Centre formation ORMVAT',
                'statut' => 'planifie',
                'id_responsable' => 2, // Karim Amrani
            ],
        ];

        foreach ($projets as $projet) {
            Project::create($projet);
        }
    }

    private function creerTaches()
    {
        $this->command->info('Création des tâches...');

        $taches = [
            // Projet 1 - Modernisation réseau irrigation Tadla-Nord
            [
                'titre' => 'Inspection canal principal secteur B4',
                'description' => 'Inspection complète du canal principal du secteur B4 pour évaluer l\'état de la structure et identifier les besoins de réparation.',
                'date_echeance' => now()->addDays(5),
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'progression' => 60,
                'id_utilisateur' => 3, // Ahmed Bennani
                'id_projet' => 1,
            ],
            [
                'titre' => 'Réparation vanne distribution B4-12',
                'description' => 'Réparation de la vanne de distribution B4-12 défectueuse.',
                'date_echeance' => now()->addDays(3),
                'priorite' => 'haute',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 5, // Youssef Alami
                'id_projet' => 1,
            ],
            [
                'titre' => 'Installation nouveau système régulation',
                'description' => 'Installation du nouveau système de régulation automatique pour le secteur B4.',
                'date_echeance' => now()->addDays(15),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 4, // Fatima El Idrissi
                'id_projet' => 1,
            ],

            // Projet 2 - Maintenance préventive stations pompage
            [
                'titre' => 'Maintenance station pompage P12',
                'description' => 'Maintenance préventive complète de la station de pompage P12 : vérification moteurs, pompes, systèmes électriques.',
                'date_echeance' => now()->addDays(7),
                'priorite' => 'moyenne',
                'statut' => 'en_cours',
                'progression' => 30,
                'id_utilisateur' => 3, // Ahmed Bennani
                'id_projet' => 2,
            ],
            [
                'titre' => 'Contrôle système électrique P15',
                'description' => 'Contrôle et maintenance du système électrique de la station P15.',
                'date_echeance' => now()->addDays(10),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 7, // Omar Zeroual
                'id_projet' => 2,
            ],
            [
                'titre' => 'Remplacement filtres station P08',
                'description' => 'Remplacement des filtres de la station de pompage P08.',
                'date_echeance' => now()->subDays(2), // En retard
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'progression' => 80,
                'id_utilisateur' => 6, // Laila Berrada
                'id_projet' => 2,
            ],

            // Projet 3 - Installation compteurs
            [
                'titre' => 'Étude emplacement compteurs zone Est',
                'description' => 'Étude préliminaire pour déterminer les emplacements optimaux des nouveaux compteurs dans la zone Est de Fkih Ben Salah.',
                'date_echeance' => now()->addDays(12),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 4, // Fatima El Idrissi
                'id_projet' => 3,
            ],

            // Projet 4 - Contrôle qualité eau
            [
                'titre' => 'Prélèvement échantillons secteur Nord',
                'description' => 'Prélèvement d\'échantillons d\'eau dans le secteur Nord pour analyse qualité.',
                'date_echeance' => now()->addDays(2),
                'priorite' => 'haute',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 6, // Laila Berrada
                'id_projet' => 4,
            ],
            [
                'titre' => 'Analyse chimique échantillons semaine 12',
                'description' => 'Analyse chimique des échantillons d\'eau prélevés la semaine 12.',
                'date_echeance' => now()->addDays(1),
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'progression' => 50,
                'id_utilisateur' => 6, // Laila Berrada
                'id_projet' => 4,
            ],

            // Tâches sans projet
            [
                'titre' => 'Rapport mensuel activités mars',
                'description' => 'Rédaction du rapport mensuel des activités techniques pour le mois de mars.',
                'date_echeance' => now()->addDays(8),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 3, // Ahmed Bennani
                'id_projet' => null,
            ],
            [
                'titre' => 'Formation nouveaux techniciens',
                'description' => 'Formation des nouveaux techniciens sur les procédures de sécurité.',
                'date_echeance' => now()->addDays(6),
                'priorite' => 'moyenne',
                'statut' => 'a_faire',
                'progression' => 0,
                'id_utilisateur' => 2, // Karim Amrani
                'id_projet' => null,
            ],
        ];

        foreach ($taches as $tache) {
            Task::create($tache);
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
                'id_organisateur' => 2, // Karim Amrani
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
                'id_organisateur' => 3, // Ahmed Bennani
                'id_projet' => 2,
            ],
            [
                'titre' => 'Formation utilisation nouveaux compteurs',
                'description' => 'Formation du personnel technique sur l\'installation et la configuration des nouveaux compteurs intelligents.',
                'type' => 'formation',
                'date_debut' => now()->addDays(5)->setTime(14, 0),
                'date_fin' => now()->addDays(5)->setTime(17, 0),
                'lieu' => 'Centre de formation ORMVAT',
                'statut' => 'planifie',
                'priorite' => 'haute',
                'id_organisateur' => 4, // Fatima El Idrissi
                'id_projet' => 3,
            ],
            [
                'titre' => 'Visite inspection secteur Fkih Ben Salah',
                'description' => 'Visite d\'inspection mensuelle du secteur de Fkih Ben Salah.',
                'type' => 'visite',
                'date_debut' => now()->addDays(3)->setTime(8, 0),
                'date_fin' => now()->addDays(3)->setTime(16, 0),
                'lieu' => 'Secteur Fkih Ben Salah',
                'statut' => 'planifie',
                'priorite' => 'normale',
                'id_organisateur' => 6, // Laila Berrada
                'id_projet' => null,
            ],
            [
                'titre' => 'Comité technique mensuel',
                'description' => 'Comité technique mensuel pour la validation des procédures et l\'examen des projets en cours.',
                'type' => 'reunion',
                'date_debut' => now()->addDays(7)->setTime(10, 0),
                'date_fin' => now()->addDays(7)->setTime(12, 0),
                'lieu' => 'Salle du conseil ORMVAT',
                'statut' => 'planifie',
                'priorite' => 'haute',
                'id_organisateur' => 1, // Admin Système
                'id_projet' => null,
            ],
        ];

        foreach ($evenements as $evenement) {
            $event = Event::create($evenement);

            // Ajouter quelques participants
            if ($event->id <= 3) { // Pour les 3 premiers événements
                $participants = [3, 4, 5, 6]; // Quelques techniciens
                foreach ($participants as $participantId) {
                    if ($participantId !== $event->id_organisateur) {
                        $event->ajouterParticipant($participantId, 'invite');
                    }
                }
            }
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
                'actions' => 'Vérification complète des pompes principales, contrôle des systèmes électriques, remplacement des filtres à air, graissage des roulements, test des systèmes de sécurité.',
                'resultats' => 'Station opérationnelle, tous les systèmes fonctionnent correctement. Débit nominal atteint. Aucune anomalie détectée.',
                'problemes' => 'Usure légère constatée sur le roulement de la pompe n°2. À surveiller lors de la prochaine maintenance.',
                'recommandations' => 'Programmer le remplacement du roulement de la pompe n°2 dans les 6 mois. Maintenir la fréquence de maintenance préventive.',
                'id_utilisateur' => 3, // Ahmed Bennani
                'id_tache' => 6, // Remplacement filtres station P08
                'id_evenement' => null,
            ],
            [
                'titre' => 'Analyse qualité eau secteur Nord - Semaine 11',
                'date_intervention' => now()->subDays(5),
                'lieu' => 'Laboratoire ORMVAT et points de prélèvement secteur Nord',
                'type_intervention' => 'controle_qualite',
                'actions' => 'Prélèvement de 12 échantillons d\'eau aux points stratégiques du secteur Nord. Analyses physico-chimiques : pH, conductivité, turbidité, chlorures, sulfates.',
                'resultats' => 'Tous les paramètres dans les normes. pH moyen : 7.2, conductivité moyenne : 850 µS/cm. Qualité excellente pour l\'irrigation.',
                'problemes' => null,
                'recommandations' => 'Continuer le suivi mensuel. Augmenter la fréquence de contrôle en période d\'étiage.',
                'id_utilisateur' => 6, // Laila Berrada
                'id_tache' => 9, // Analyse chimique échantillons semaine 12
                'id_evenement' => null,
            ],
            [
                'titre' => 'Réparation urgence vanne B4-15',
                'date_intervention' => now()->subDays(1),
                'lieu' => 'Canal principal secteur B4, vanne 15',
                'type_intervention' => 'reparation_urgence',
                'actions' => 'Intervention d\'urgence pour réparer la vanne B4-15 bloquée. Démontage du mécanisme, remplacement du joint d\'étanchéité défaillant, lubrification des axes de rotation.',
                'resultats' => 'Vanne réparée et opérationnelle. Fonctionnement fluide rétabli. Étanchéité parfaite.',
                'problemes' => 'Joint d\'étanchéité complètement usé, probablement dû à un manque d\'entretien préventif.',
                'recommandations' => 'Intégrer cette vanne dans le programme de maintenance préventive. Vérifier les autres vannes du même type.',
                'id_utilisateur' => 5, // Youssef Alami
                'id_tache' => null,
                'id_evenement' => null,
            ],
            [
                'titre' => 'Formation sécurité nouveaux techniciens',
                'date_intervention' => now()->subDays(7),
                'lieu' => 'Centre de formation ORMVAT',
                'type_intervention' => 'formation',
                'actions' => 'Formation de 4 nouveaux techniciens sur les procédures de sécurité : EPI obligatoires, protocoles d\'intervention, gestion des situations d\'urgence, premiers secours.',
                'resultats' => 'Formation complète dispensée. Tous les participants ont validé l\'évaluation finale avec succès.',
                'problemes' => null,
                'recommandations' => 'Prévoir une session de recyclage dans 6 mois. Organiser des exercices pratiques trimestriels.',
                'id_utilisateur' => 2, // Karim Amrani
                'id_tache' => 11, // Formation nouveaux techniciens
                'id_evenement' => null,
            ],
        ];

        foreach ($rapports as $rapport) {
            Report::create($rapport);
        }
    }

    private function creerNotifications()
    {
        $this->command->info('Création des notifications...');

        $utilisateurs = User::all();

        foreach ($utilisateurs as $utilisateur) {
            // Notification de bienvenue
            $utilisateur->creerNotification(
                'Bienvenue sur PlanifTech',
                'Bienvenue sur la plateforme PlanifTech de l\'ORMVAT. Vous pouvez maintenant gérer vos tâches et interventions.',
                'systeme'
            );

            // Si c'est un technicien, ajouter une notification de tâche
            if ($utilisateur->isTechnicien()) {
                $tachesEnRetard = $utilisateur->taches()->enRetard()->count();
                if ($tachesEnRetard > 0) {
                    $utilisateur->creerNotification(
                        'Tâches en retard',
                        "Vous avez $tachesEnRetard tâche(s) en retard. Veuillez consulter votre tableau de bord.",
                        'tache'
                    );
                }
            }
        }
    }

    private function creerJournaux()
    {
        $this->command->info('Création des journaux...');

        // Journaux de connexion
        $utilisateurs = User::all();
        foreach ($utilisateurs as $utilisateur) {
            Journal::enregistrerConnexion($utilisateur->id);
        }

        // Journaux de création des projets
        $projets = Project::all();
        foreach ($projets as $projet) {
            Journal::enregistrerCreation('projet', $projet->nom, $projet->id_responsable);
        }

        // Journaux de création des tâches
        $taches = Task::take(5)->get(); // Seulement les 5 premières pour éviter le spam
        foreach ($taches as $tache) {
            Journal::enregistrerCreation('tâche', $tache->titre, $tache->id_utilisateur);
        }

        // Quelques journaux d'erreur fictifs
        Journal::enregistrerErreur('Tentative de connexion échouée pour l\'utilisateur test@example.com');
        Journal::enregistrerErreur('Erreur lors de l\'upload d\'un fichier dans le rapport #3');
    }
}
