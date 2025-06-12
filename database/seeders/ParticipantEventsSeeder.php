<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\User;
use App\Models\ParticipantEvent;

class ParticipantEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les vérifications de clés étrangères temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vider la table (optionnel, pour les tests)
        if (app()->environment(['local', 'testing'])) {
            ParticipantEvent::truncate();
        }

        // Récupérer les événements et utilisateurs existants
        $events = Event::all();
        $users = User::where('statut', 'actif')->get();

        if ($events->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Aucun événement ou utilisateur trouvé. Assurez-vous d\'avoir des données de base.');
            return;
        }

        foreach ($events as $event) {
            $this->createParticipationsForEvent($event, $users);
        }

        // Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Participations aux événements créées avec succès !');
    }

    /**
     * Créer les participations pour un événement donné
     */
    private function createParticipationsForEvent(Event $event, $users): void
    {
        // S'assurer que l'organisateur est participant
        $this->createOrganizerParticipation($event);

        // Ajouter d'autres participants aléatoirement
        $otherUsers = $users->where('id', '!=', $event->id_organisateur);
        $numberOfParticipants = rand(2, min(8, $otherUsers->count()));

        $selectedUsers = $otherUsers->random(min($numberOfParticipants, $otherUsers->count()));

        foreach ($selectedUsers as $user) {
            $this->createParticipantParticipation($event, $user);
        }
    }

    /**
     * Créer la participation de l'organisateur
     */
    private function createOrganizerParticipation(Event $event): void
    {
        // Vérifier si la participation existe déjà
        $existing = ParticipantEvent::where('id_evenement', $event->id)
                                  ->where('id_utilisateur', $event->id_organisateur)
                                  ->first();

        if (!$existing) {
            ParticipantEvent::create([
                'id_evenement' => $event->id,
                'id_utilisateur' => $event->id_organisateur,
                'statut_presence' => 'confirme',
                'role_evenement' => 'organisateur',
                'date_invitation' => $event->created_at,
                'date_reponse' => $event->created_at,
                'commentaire' => 'Organisateur de l\'événement',
                'notification_envoyee' => true,
                'rappel_envoye' => false,
                'preferences' => json_encode([
                    'rappel_email' => true,
                    'rappel_sms' => true,
                    'langue_preferee' => 'fr'
                ])
            ]);
        }
    }

    /**
     * Créer la participation d'un utilisateur normal
     */
    private function createParticipantParticipation(Event $event, User $user): void
    {
        // Vérifier si la participation existe déjà
        $existing = ParticipantEvent::where('id_evenement', $event->id)
                                  ->where('id_utilisateur', $user->id)
                                  ->first();

        if ($existing) {
            return; // Participation déjà existante
        }

        // Définir le statut aléatoirement selon des probabilités réalistes
        $statut = $this->getRandomStatus();
        $role = $this->getRandomRole();

        // Dates logiques
        $dateInvitation = $event->created_at->addMinutes(rand(1, 60));
        $dateReponse = in_array($statut, ['confirme', 'decline', 'excuse'])
                      ? $dateInvitation->addHours(rand(1, 48))
                      : null;

        try {
            ParticipantEvent::create([
                'id_evenement' => $event->id,
                'id_utilisateur' => $user->id,
                'statut_presence' => $statut,
                'role_evenement' => $role,
                'date_invitation' => $dateInvitation,
                'date_reponse' => $dateReponse,
                'commentaire' => $this->getRandomComment($statut),
                'notes_organisateur' => $this->getRandomOrganizerNotes(),
                'notification_envoyee' => true,
                'rappel_envoye' => rand(0, 1),
                'statut_transport' => $this->getRandomTransport(),
                'besoins_speciaux' => $this->getRandomSpecialNeeds(),
                'preferences' => json_encode([
                    'rappel_email' => (bool)rand(0, 1),
                    'rappel_sms' => (bool)rand(0, 1),
                    'langue_preferee' => 'fr'
                ])
            ]);
        } catch (\Exception $e) {
            Log::warning("Erreur lors de la création de la participation: " . $e->getMessage());
        }
    }

    /**
     * Obtenir un statut de présence aléatoire avec des probabilités réalistes
     */
    private function getRandomStatus(): string
    {
        $statuses = [
            'confirme' => 40,  // 40% de chance
            'invite' => 30,    // 30% de chance
            'decline' => 15,   // 15% de chance
            'present' => 10,   // 10% de chance (événements passés)
            'absent' => 4,     // 4% de chance
            'excuse' => 1      // 1% de chance
        ];

        return $this->weightedRandom($statuses);
    }

    /**
     * Obtenir un rôle aléatoire avec des probabilités réalistes
     */
    private function getRandomRole(): string
    {
        $roles = [
            'participant' => 70,   // 70% de chance
            'intervenant' => 20,   // 20% de chance
            'observateur' => 10    // 10% de chance
        ];

        return $this->weightedRandom($roles);
    }

    /**
     * Sélection aléatoire pondérée
     */
    private function weightedRandom(array $weights): string
    {
        $rand = rand(1, array_sum($weights));

        foreach ($weights as $option => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $option;
            }
        }

        return array_key_first($weights);
    }

    /**
     * Obtenir un commentaire aléatoire selon le statut
     */
    private function getRandomComment(string $statut): ?string
    {
        $comments = [
            'confirme' => [
                'Je serai présent.',
                'Merci pour l\'invitation, je confirme ma présence.',
                'Aucun problème pour participer.',
                null // Pas de commentaire
            ],
            'decline' => [
                'Désolé, je ne pourrai pas participer.',
                'Conflit d\'agenda, impossible pour moi.',
                'Je ne serai pas disponible à cette date.',
                'Merci pour l\'invitation mais je dois décliner.'
            ],
            'excuse' => [
                'Excuse pour absence de dernière minute.',
                'Problème de santé, je ne peux pas venir.',
                'Urgence familiale, désolé.'
            ]
        ];

        if (!isset($comments[$statut])) {
            return null;
        }

        $options = $comments[$statut];
        return $options[array_rand($options)];
    }

    /**
     * Obtenir des notes organisateur aléatoires
     */
    private function getRandomOrganizerNotes(): ?string
    {
        $notes = [
            'Participant régulier et fiable.',
            'Expert dans ce domaine.',
            'Nouveau participant à suivre.',
            'Besoin de briefing préalable.',
            null, null, null // Majoritairement pas de notes
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Obtenir un moyen de transport aléatoire
     */
    private function getRandomTransport(): ?string
    {
        $transports = [
            'voiture_personnelle',
            'transport_commun',
            'vehicule_service',
            'covoiturage',
            null, null // Souvent non spécifié
        ];

        return $transports[array_rand($transports)];
    }

    /**
     * Obtenir des besoins spéciaux aléatoires
     */
    private function getRandomSpecialNeeds(): ?string
    {
        // La plupart n'ont pas de besoins spéciaux
        if (rand(1, 100) > 15) {
            return null;
        }

        $needs = [
            'Accès handicapé requis',
            'Régime alimentaire particulier',
            'Place de parking réservée',
            'Assistance auditive'
        ];

        return $needs[array_rand($needs)];
    }
}
