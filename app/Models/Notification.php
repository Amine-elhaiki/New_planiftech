<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    public $timestamps = false;

    protected $fillable = [
        'titre',
        'message',
        'type',
        'lue',
        'destinataire_id',
    ];

    protected function casts(): array
    {
        return [
            'lue' => 'boolean',
            'date_creation' => 'datetime',
            'date_lecture' => 'datetime',
        ];
    }

    const TYPES = [
        'tache' => 'Tâche',
        'evenement' => 'Événement',
        'systeme' => 'Système',
        'projet' => 'Projet'
    ];

    /**
     * Relations
     */
    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    /**
     * Scopes
     */
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    public function scopeParDestinataire($query, $userId)
    {
        return $query->where('destinataire_id', $userId);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Accesseurs
     */
    public function getTypeLibelleAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Mutateurs
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notification) {
            $notification->date_creation = now();
        });
    }

    /**
     * Méthodes utilitaires
     */
    public function marquerCommeLue(): bool
    {
        if (!$this->lue) {
            $this->lue = true;
            $this->date_lecture = now();
            return $this->save();
        }
        return false;
    }

    public static function creerNotification(int $destinataireId, string $titre, string $message, string $type = 'systeme'): self
    {
        return self::create([
            'destinataire_id' => $destinataireId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
        ]);
    }
}
