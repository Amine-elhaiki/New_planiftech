<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'statut',
        'telephone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_creation' => 'datetime',
            'derniere_connexion' => 'datetime',
        ];
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est technicien
     */
    public function isTechnicien(): bool
    {
        return $this->role === 'technicien';
    }

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Obtenir le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Scopes
     */

    // Utilisateurs actifs
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    // Utilisateurs par rôle
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Techniciens actifs
    public function scopeTechniciensActifs($query)
    {
        return $query->where('role', 'technicien')->where('statut', 'actif');
    }
}
