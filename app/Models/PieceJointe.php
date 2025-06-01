<?php
/**
 * Modèle ParticipantEvent - Table pivot pour les participants aux événements
 */
class ParticipantEvent extends Model
{
    use HasFactory;

    protected $table = 'participants_evenements';

    protected $fillable = [
        'id_evenement',
        'id_utilisateur',
        'statut_presence',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les statuts de présence
    const STATUT_INVITE = 'invite';
    const STATUT_CONFIRME = 'confirme';
    const STATUT_DECLINE = 'decline';
    const STATUT_PRESENT = 'present';
    const STATUT_ABSENT = 'absent';

    /**
     * Relation avec l'événement
     */
    public function evenement()
    {
        return $this->belongsTo(Event::class, 'id_evenement');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur');
    }

    /**
     * Obtenir le libellé formaté du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->statut_presence) {
            self::STATUT_INVITE => 'Invité',
            self::STATUT_CONFIRME => 'Confirmé',
            self::STATUT_DECLINE => 'Décliné',
            self::STATUT_PRESENT => 'Présent',
            self::STATUT_ABSENT => 'Absent',
            default => ucfirst($this->statut_presence)
        };
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->statut_presence) {
            self::STATUT_INVITE => 'secondary',
            self::STATUT_CONFIRME => 'primary',
            self::STATUT_DECLINE => 'danger',
            self::STATUT_PRESENT => 'success',
            self::STATUT_ABSENT => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Confirmer la participation
     */
    public function confirm(): bool
    {
        return $this->update(['statut_presence' => self::STATUT_CONFIRME]);
    }

    /**
     * Décliner la participation
     */
    public function decline(): bool
    {
        return $this->update(['statut_presence' => self::STATUT_DECLINE]);
    }

    /**
     * Marquer comme présent
     */
    public function markAsPresent(): bool
    {
        return $this->update(['statut_presence' => self::STATUT_PRESENT]);
    }

    /**
     * Marquer comme absent
     */
    public function markAsAbsent(): bool
    {
        return $this->update(['statut_presence' => self::STATUT_ABSENT]);
    }

    /**
     * Scope pour les participants confirmés
     */
    public function scopeConfirmed($query)
    {
        return $query->where('statut_presence', self::STATUT_CONFIRME);
    }

    /**
     * Scope pour les participants présents
     */
    public function scopePresent($query)
    {
        return $query->where('statut_presence', self::STATUT_PRESENT);
    }
}

/**
 * Modèle PieceJointe - Gestion des fichiers joints aux rapports
 */
class PieceJointe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pieces_jointes';

    protected $fillable = [
        'nom_fichier',
        'type_fichier',
        'taille',
        'chemin',
        'id_rapport',
    ];

    protected $casts = [
        'taille' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relation avec le rapport
     */
    public function rapport()
    {
        return $this->belongsTo(Report::class, 'id_rapport');
    }

    /**
     * Obtenir la taille formatée du fichier
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->taille;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Obtenir l'extension du fichier
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->nom_fichier, PATHINFO_EXTENSION);
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function isImage(): bool
    {
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($this->type_fichier, $imageTypes);
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function isPdf(): bool
    {
        return $this->type_fichier === 'application/pdf';
    }

    /**
     * Vérifier si le fichier est un document Word
     */
    public function isWordDocument(): bool
    {
        $wordTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        return in_array($this->type_fichier, $wordTypes);
    }

    /**
     * Obtenir l'icône appropriée pour le type de fichier
     */
    public function getIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'bi-image';
        } elseif ($this->isPdf()) {
            return 'bi-file-pdf';
        } elseif ($this->isWordDocument()) {
            return 'bi-file-word';
        } else {
            return 'bi-file-earmark';
        }
    }

    /**
     * Obtenir la couleur de l'icône selon le type
     */
    public function getIconColorAttribute(): string
    {
        if ($this->isImage()) {
            return 'text-success';
        } elseif ($this->isPdf()) {
            return 'text-danger';
        } elseif ($this->isWordDocument()) {
            return 'text-primary';
        } else {
            return 'text-secondary';
        }
    }

    /**
     * Obtenir l'URL de téléchargement
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('reports.attachments.download', $this->id);
    }

    /**
     * Obtenir l'URL de prévisualisation (pour les images)
     */
    public function getPreviewUrlAttribute(): ?string
    {
        if ($this->isImage()) {
            return asset('storage/' . $this->chemin);
        }
        return null;
    }

    /**
     * Scope pour les images
     */
    public function scopeImages($query)
    {
        return $query->whereIn('type_fichier', [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp'
        ]);
    }

    /**
     * Scope pour les documents PDF
     */
    public function scopePdfs($query)
    {
        return $query->where('type_fichier', 'application/pdf');
    }

    /**
     * Scope pour les fichiers volumineux (plus de 1MB)
     */
    public function scopeLargeFiles($query)
    {
        return $query->where('taille', '>', 1048576);
    }

    /**
     * Boot method pour les événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pieceJointe) {
            // Supprimer le fichier physique lors de la suppression du modèle
            if (\Storage::disk('public')->exists($pieceJointe->chemin)) {
                \Storage::disk('public')->delete($pieceJointe->chemin);
            }
        });
    }
}
