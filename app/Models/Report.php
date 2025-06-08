<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory;

protected $table = 'reports';

    protected $fillable = [
        'titre',
        'date_intervention',
        'lieu',
        'type_intervention',
        'actions',
        'resultats',
        'problemes',
        'recommandations',
        'id_utilisateur',
        'id_tache',
        'id_evenement'
    ];

    protected $casts = [
        'date_intervention' => 'date',
        'date_creation' => 'datetime'
    ];

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null; // ou 'date_modification' si vous l'avez

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    public function tache()
    {
        return $this->belongsTo(Task::class, 'id_tache');
    }

    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class, 'id_rapport');
    }

    // Scopes
    public function scopeParUtilisateur($query, $userId)
    {
        return $query->where('id_utilisateur', $userId);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_intervention', $type);
    }

    public function scopeParLieu($query, $lieu)
    {
        return $query->where('lieu', 'like', "%{$lieu}%");
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_intervention', [$dateDebut, $dateFin]);
    }

    public function scopeDuMois($query, $mois = null, $annee = null)
    {
        $mois = $mois ?: now()->month;
        $annee = $annee ?: now()->year;

        return $query->whereMonth('date_intervention', $mois)
                    ->whereYear('date_intervention', $annee);
    }

    public function scopeRecents($query, $jours = 30)
    {
        return $query->where('date_creation', '>=', now()->subDays($jours));
    }

    // Accessors
    public function getDateInterventionFormatteeAttribute()
    {
        return $this->date_intervention->format('d/m/Y');
    }

    public function getDateCreationFormatteeAttribute()
    {
        return $this->date_creation->format('d/m/Y H:i');
    }

    public function getTypeInterventionLibelleAttribute()
    {
        return match($this->type_intervention) {
            'maintenance_preventive' => 'Maintenance préventive',
            'maintenance_corrective' => 'Maintenance corrective',
            'reparation_urgence' => 'Réparation d\'urgence',
            'installation' => 'Installation',
            'controle_qualite' => 'Contrôle qualité',
            'inspection' => 'Inspection',
            'formation' => 'Formation',
            'visite' => 'Visite',
            default => ucfirst(str_replace('_', ' ', $this->type_intervention))
        };
    }

    public function getNombrePiecesJointesAttribute()
    {
        return $this->piecesJointes()->count();
    }

    public function getTailleResumeeAttribute()
    {
        $longueur = strlen($this->actions) + strlen($this->resultats) +
                   strlen($this->problemes ?? '') + strlen($this->recommandations ?? '');

        if ($longueur > 2000) {
            return 'Détaillé';
        } elseif ($longueur > 1000) {
            return 'Moyen';
        } else {
            return 'Succinct';
        }
    }

    // Méthodes métier
    public function peutEtreModifiePar($user)
    {
        // Un rapport peut être modifié par son auteur dans les 48h ou par un admin
        return $user->isAdmin() ||
               ($this->id_utilisateur == $user->id &&
                $this->date_creation->diffInHours(now()) <= 48);
    }

    public function peutEtreSupprimePar($user)
    {
        return $user->isAdmin() || $this->id_utilisateur == $user->id;
    }

    public function ajouterPieceJointe($fichier)
    {
        return PieceJointe::creerDepuisFichier($fichier, $this->id);
    }

    public function supprimerPieceJointe($pieceJointeId)
    {
        $pieceJointe = $this->piecesJointes()->findOrFail($pieceJointeId);
        return $pieceJointe->supprimer();
    }

    public function obtenirProjetAssocie()
    {
        if ($this->tache && $this->tache->projet) {
            return $this->tache->projet;
        }

        if ($this->evenement && $this->evenement->projet) {
            return $this->evenement->projet;
        }

        return null;
    }

    public function aDesProblemes()
    {
        return !empty($this->problemes);
    }

    public function aDesRecommandations()
    {
        return !empty($this->recommandations);
    }

    public function genererResume($longueur = 200)
    {
        $texte = $this->actions . ' ' . $this->resultats;

        if (strlen($texte) <= $longueur) {
            return $texte;
        }

        return substr($texte, 0, $longueur) . '...';
    }

    public function obtenirMotsCles()
    {
        $texte = strtolower($this->titre . ' ' . $this->type_intervention . ' ' .
                           $this->lieu . ' ' . $this->actions);

        // Mots courants à ignorer
        $motsIgnores = ['le', 'la', 'les', 'de', 'du', 'des', 'et', 'ou', 'mais',
                       'pour', 'avec', 'dans', 'sur', 'par', 'une', 'un'];

        $mots = str_word_count($texte, 1, 'àâäéèêëïîôöùûüÿç');
        $mots = array_filter($mots, function($mot) use ($motsIgnores) {
            return strlen($mot) > 3 && !in_array($mot, $motsIgnores);
        });

        return array_slice(array_unique($mots), 0, 10);
    }

    // Méthodes statiques
    public static function getTypesInterventionDisponibles()
    {
        return [
            'maintenance_preventive' => 'Maintenance préventive',
            'maintenance_corrective' => 'Maintenance corrective',
            'reparation_urgence' => 'Réparation d\'urgence',
            'installation' => 'Installation',
            'controle_qualite' => 'Contrôle qualité',
            'inspection' => 'Inspection',
            'formation' => 'Formation',
            'visite' => 'Visite',
            'autre' => 'Autre'
        ];
    }

    public static function getRapportsDuMois($mois = null, $annee = null)
    {
        return static::duMois($mois, $annee)
                    ->with(['utilisateur', 'tache', 'evenement'])
                    ->orderBy('date_intervention', 'desc')
                    ->get();
    }

    public static function getStatistiquesParType()
    {
        return static::selectRaw('type_intervention, COUNT(*) as nombre')
                    ->groupBy('type_intervention')
                    ->orderBy('nombre', 'desc')
                    ->pluck('nombre', 'type_intervention')
                    ->toArray();
    }

    public static function getStatistiquesParUtilisateur()
    {
        return static::with('utilisateur')
                    ->selectRaw('id_utilisateur, COUNT(*) as nombre')
                    ->groupBy('id_utilisateur')
                    ->orderBy('nombre', 'desc')
                    ->get()
                    ->pluck('nombre', 'utilisateur.nom_complet')
                    ->toArray();
    }

    public static function getStatistiquesParLieu()
    {
        return static::selectRaw('lieu, COUNT(*) as nombre')
                    ->groupBy('lieu')
                    ->orderBy('nombre', 'desc')
                    ->limit(10)
                    ->pluck('nombre', 'lieu')
                    ->toArray();
    }

    public static function getStatistiquesGlobales()
    {
        return [
            'total' => static::count(),
            'ce_mois' => static::duMois()->count(),
            'avec_problemes' => static::whereNotNull('problemes')
                                    ->where('problemes', '!=', '')
                                    ->count(),
            'avec_pieces_jointes' => static::whereHas('piecesJointes')->count(),
            'moyenne_par_utilisateur' => round(static::count() / User::count(), 1)
        ];
    }
}
