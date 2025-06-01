<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Modèle Report - Gestion des rapports d'intervention
 */
class Report extends Model
{
    use HasFactory, SoftDeletes;

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
        'id_evenement',
    ];

    protected $casts = [
        'date_intervention' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'date_intervention',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relation avec l'utilisateur qui a créé le rapport
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    /**
     * Relation avec la tâche associée
     */
    public function tache()
    {
        return $this->belongsTo(Task::class, 'id_tache');
    }

    /**
     * Relation avec l'événement associé
     */
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    /**
     * Relation avec les pièces jointes
     */
    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class, 'id_rapport');
    }

    /**
     * Vérifier si le rapport a des pièces jointes
     */
    public function hasPiecesJointes(): bool
    {
        return $this->piecesJointes()->exists();
    }

    /**
     * Obtenir la taille totale des pièces jointes en Mo
     */
    public function getTotalAttachmentSizeAttribute(): float
    {
        $totalSize = $this->piecesJointes()->sum('taille');
        return round($totalSize / (1024 * 1024), 2);
    }

    /**
     * Vérifier si le rapport peut être modifié
     */
    public function canBeModified(): bool
    {
        // Les rapports peuvent être modifiés dans les 48h suivant leur création
        return $this->created_at->diffInHours(now()) <= 48;
    }

    /**
     * Scope pour les rapports récents
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope pour les rapports d'un type d'intervention
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type_intervention', $type);
    }

    /**
     * Scope pour les rapports d'une période
     */
    public function scopeBetweenDates($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('date_intervention', [$startDate, $endDate]);
    }

    /**
     * Obtenir le nom de fichier pour l'export PDF
     */
    public function getPdfFilename(): string
    {
        $date = $this->date_intervention->format('Y-m-d');
        $titre = str_replace(' ', '_', $this->titre);
        return "rapport_{$this->id}_{$date}_{$titre}.pdf";
    }
}
