<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'journaux';

    protected $fillable = [
        'date',
        'type_action',
        'description',
        'utilisateur_id',
        'adresse_ip',
        'user_agent'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public $timestamps = false;

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    // Scopes
    public function scopeParType($query, $type)
    {
        return $query->where('type_action', $type);
    }

    public function scopeParUtilisateur($query, $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }

    public function scopeParPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date', [$dateDebut, $dateFin]);
    }

    public function scopeRecents($query, $heures = 24)
    {
        return $query->where('date', '>=', now()->subHours($heures));
    }

    public function scopeConnexions($query)
    {
        return $query->where('type_action', 'connexion');
    }

    public function scopeErreurs($query)
    {
        return $query->where('type_action', 'erreur');
    }

    // Accessors
    public function getDateFormatteeAttribute()
    {
        return $this->date->format('d/m/Y H:i:s');
    }

    public function getTypeActionLibelleAttribute()
    {
        return match($this->type_action) {
            'connexion' => 'Connexion',
            'modification' => 'Modification',
            'suppression' => 'Suppression',
            'creation' => 'Création',
            'erreur' => 'Erreur',
            default => ucfirst($this->type_action)
        };
    }

    public function getTypeActionColorAttribute()
    {
        return match($this->type_action) {
            'connexion' => 'success',
            'modification' => 'warning',
            'suppression' => 'danger',
            'creation' => 'info',
            'erreur' => 'danger',
            default => 'secondary'
        };
    }

    public function getTypeActionIconAttribute()
    {
        return match($this->type_action) {
            'connexion' => 'bi-box-arrow-in-right',
            'modification' => 'bi-pencil-square',
            'suppression' => 'bi-trash',
            'creation' => 'bi-plus-circle',
            'erreur' => 'bi-exclamation-triangle',
            default => 'bi-info-circle'
        };
    }

    public function getNavigateurAttribute()
    {
        if (empty($this->user_agent)) {
            return 'Inconnu';
        }

        $userAgent = $this->user_agent;

        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } else {
            return 'Autre';
        }
    }

    // Méthodes statiques d'enregistrement
    public static function enregistrerConnexion($userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => 'connexion',
            'description' => 'Connexion utilisateur',
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public static function enregistrerCreation($type, $objet, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => 'creation',
            'description' => "Création d'un(e) {$type} : {$objet}",
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public static function enregistrerModification($type, $objet, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => 'modification',
            'description' => "Modification d'un(e) {$type} : {$objet}",
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public static function enregistrerSuppression($type, $objet, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => 'suppression',
            'description' => "Suppression d'un(e) {$type} : {$objet}",
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public static function enregistrerErreur($description, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => 'erreur',
            'description' => $description,
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    public static function enregistrerAction($type, $description, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return static::create([
            'date' => now(),
            'type_action' => $type,
            'description' => $description,
            'utilisateur_id' => $userId,
            'adresse_ip' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    // Méthodes de nettoyage
    public static function nettoyerAnciens($jours = 90)
    {
        return static::where('date', '<', now()->subDays($jours))->delete();
    }

    public static function nettoyerConnexions($jours = 30)
    {
        return static::where('type_action', 'connexion')
                    ->where('date', '<', now()->subDays($jours))
                    ->delete();
    }

    // Statistiques
    public static function getStatistiquesParType($jours = 30)
    {
        return static::where('date', '>=', now()->subDays($jours))
                    ->selectRaw('type_action, COUNT(*) as nombre')
                    ->groupBy('type_action')
                    ->orderBy('nombre', 'desc')
                    ->pluck('nombre', 'type_action')
                    ->toArray();
    }

    public static function getStatistiquesParUtilisateur($jours = 30)
    {
        return static::with('utilisateur')
                    ->where('date', '>=', now()->subDays($jours))
                    ->selectRaw('utilisateur_id, COUNT(*) as nombre')
                    ->groupBy('utilisateur_id')
                    ->orderBy('nombre', 'desc')
                    ->get()
                    ->pluck('nombre', 'utilisateur.nom_complet')
                    ->toArray();
    }

    public static function getConnexionsRecentes($heures = 24)
    {
        return static::connexions()
                    ->where('date', '>=', now()->subHours($heures))
                    ->with('utilisateur')
                    ->orderBy('date', 'desc')
                    ->get();
    }

    public static function getErreursRecentes($heures = 24)
    {
        return static::erreurs()
                    ->where('date', '>=', now()->subHours($heures))
                    ->with('utilisateur')
                    ->orderBy('date', 'desc')
                    ->get();
    }

    public static function getActiviteRecente($limite = 50)
    {
        return static::with('utilisateur')
                    ->orderBy('date', 'desc')
                    ->limit($limite)
                    ->get();
    }

    public static function getTypesActionDisponibles()
    {
        return [
            'connexion' => 'Connexion',
            'creation' => 'Création',
            'modification' => 'Modification',
            'suppression' => 'Suppression',
            'erreur' => 'Erreur'
        ];
    }
}
