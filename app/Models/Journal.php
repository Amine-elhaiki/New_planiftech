<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'journaux';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'type_action',
        'description',
        'utilisateur_id',
        'adresse_ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    const TYPES_ACTION = [
        'connexion' => 'Connexion',
        'modification' => 'Modification',
        'suppression' => 'Suppression',
        'creation' => 'Création',
        'erreur' => 'Erreur'
    ];

    /**
     * Relations
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Scopes
     */
    public function scopeParUtilisateur($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopeParTypeAction($query, $type)
    {
        return $query->where('type_action', $type);
    }

    public function scopeDansPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date', [$dateDebut, $dateFin]);
    }

    public function scopeRecents($query, $jours = 30)
    {
        return $query->where('date', '>=', now()->subDays($jours));
    }

    /**
     * Accesseurs
     */
    public function getTypeActionLibelleAttribute(): string
    {
        return self::TYPES_ACTION[$this->type_action] ?? $this->type_action;
    }

    /**
     * Mutateurs
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journal) {
            if (!$journal->date) {
                $journal->date = now();
            }

            if (!$journal->adresse_ip && request()) {
                $journal->adresse_ip = request()->ip();
            }

            if (!$journal->user_agent && request()) {
                $journal->user_agent = request()->userAgent();
            }
        });
    }

    /**
     * Méthodes utilitaires statiques
     */
    public static function enregistrerAction(string $typeAction, string $description, ?int $utilisateurId = null): self
    {
        return self::create([
            'type_action' => $typeAction,
            'description' => $description,
            'utilisateur_id' => $utilisateurId ?? auth()->id(),
        ]);
    }

    public static function enregistrerConnexion(int $utilisateurId): self
    {
        $utilisateur = User::find($utilisateurId);
        $nom = $utilisateur ? $utilisateur->nom_complet : "ID: $utilisateurId";

        return self::enregistrerAction(
            'connexion',
            "Connexion de l'utilisateur : $nom",
            $utilisateurId
        );
    }

    public static function enregistrerCreation(string $entite, string $nom, ?int $utilisateurId = null): self
    {
        return self::enregistrerAction(
            'creation',
            "Création $entite : $nom",
            $utilisateurId
        );
    }

    public static function enregistrerModification(string $entite, string $nom, ?int $utilisateurId = null): self
    {
        return self::enregistrerAction(
            'modification',
            "Modification $entite : $nom",
            $utilisateurId
        );
    }

    public static function enregistrerSuppression(string $entite, string $nom, ?int $utilisateurId = null): self
    {
        return self::enregistrerAction(
            'suppression',
            "Suppression $entite : $nom",
            $utilisateurId
        );
    }

    public static function enregistrerErreur(string $description, ?int $utilisateurId = null): self
    {
        return self::enregistrerAction(
            'erreur',
            $description,
            $utilisateurId
        );
    }
}
