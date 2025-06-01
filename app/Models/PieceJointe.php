<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PieceJointe extends Model
{
    use HasFactory;

    protected $table = 'pieces_jointes';

    protected $fillable = [
        'nom_fichier',
        'type_fichier',
        'taille',
        'chemin',
        'id_rapport'
    ];

    protected $casts = [
        'date_creation' => 'datetime',
        'taille' => 'integer'
    ];

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null;

    // Relations
    public function rapport()
    {
        return $this->belongsTo(Report::class, 'id_rapport');
    }

    // Accessors
    public function getTailleFormatteeAttribute()
    {
        $bytes = $this->taille;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getExtensionAttribute()
    {
        return strtoupper(pathinfo($this->nom_fichier, PATHINFO_EXTENSION));
    }

    public function getIconeAttribute()
    {
        $extension = strtolower($this->extension);

        return match($extension) {
            'pdf' => 'bi-file-earmark-pdf',
            'doc', 'docx' => 'bi-file-earmark-word',
            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image',
            'xls', 'xlsx' => 'bi-file-earmark-excel',
            'txt' => 'bi-file-earmark-text',
            default => 'bi-file-earmark'
        };
    }

    public function getCouleurAttribute()
    {
        $extension = strtolower($this->extension);

        return match($extension) {
            'pdf' => 'danger',
            'doc', 'docx' => 'primary',
            'jpg', 'jpeg', 'png', 'gif' => 'success',
            'xls', 'xlsx' => 'warning',
            'txt' => 'secondary',
            default => 'dark'
        };
    }

    // Méthodes métier
    public function existe()
    {
        return Storage::disk('public')->exists($this->chemin);
    }

    public function obtenirUrl()
    {
        if ($this->existe()) {
            return Storage::disk('public')->path($this->chemin);
        }
        return null;
    }

    public function obtenirUrlPublique()
    {
        if ($this->existe()) {
            return asset('storage/' . $this->chemin);
        }
        return null;
    }

    public function supprimer()
    {
        if ($this->existe()) {
            Storage::disk('public')->delete($this->chemin);
        }
        return $this->delete();
    }

    public function estImage()
    {
        $extensionsImages = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        return in_array(strtolower($this->extension), $extensionsImages);
    }

    public function estPdf()
    {
        return strtolower($this->extension) === 'pdf';
    }

    public function estDocument()
    {
        $extensionsDocuments = ['doc', 'docx', 'txt', 'rtf'];
        return in_array(strtolower($this->extension), $extensionsDocuments);
    }

    // Méthodes statiques
    public static function creerDepuisFichier($fichier, $rapportId)
    {
        $nomFichier = time() . '_' . $fichier->getClientOriginalName();
        $chemin = $fichier->storeAs('reports/' . $rapportId, $nomFichier, 'public');

        return static::create([
            'nom_fichier' => $fichier->getClientOriginalName(),
            'type_fichier' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
            'chemin' => $chemin,
            'id_rapport' => $rapportId
        ]);
    }

    public static function getExtensionsAutorisees()
    {
        return ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt'];
    }

    public static function getTailleMaximale()
    {
        return 5 * 1024 * 1024; // 5 MB
    }
}
