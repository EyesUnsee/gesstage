<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'titre',
        'type',
        'fichier_path',
        'fichier_nom',
        'taille',
        'description',
        'statut',
        'commentaire',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taille' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs qui ne doivent pas être inclus dans les tableaux.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Relation avec l'utilisateur (candidat)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accesseur pour la taille lisible (format human friendly)
     */
    public function getTailleLisibleAttribute()
    {
        $bytes = $this->taille * 1024; // Convertir KB en bytes
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Accesseur pour l'extension du fichier
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->fichier_nom ?? $this->fichier_path, PATHINFO_EXTENSION);
    }

    /**
     * Accesseur pour l'icône selon le type de document
     */
    public function getIconeAttribute()
    {
        return match($this->type) {
            'convention' => 'fa-file-signature',
            'rapport' => 'fa-file-alt',
            'attestation' => 'fa-certificate',
            default => 'fa-file',
        };
    }

    /**
     * Accesseur pour la couleur selon le statut
     */
    public function getCouleurStatutAttribute()
    {
        return match($this->statut) {
            'valide' => 'success',
            'rejete' => 'danger',
            'en_attente' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Accesseur pour le libellé du statut
     */
    public function getLibelleStatutAttribute()
    {
        return match($this->statut) {
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'en_attente' => 'En attente',
            default => 'Inconnu',
        };
    }

    /**
     * Accesseur pour le nom du type de document
     */
    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            'convention' => 'Convention de stage',
            'rapport' => 'Rapport de stage',
            'attestation' => 'Attestation de stage',
            'autre' => 'Autre document',
            default => $this->type,
        };
    }

    /**
     * Vérifier si le document est validé
     */
    public function isValide()
    {
        return $this->statut === 'valide';
    }

    /**
     * Vérifier si le document est en attente
     */
    public function isEnAttente()
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifier si le document est rejeté
     */
    public function isRejete()
    {
        return $this->statut === 'rejete';
    }

    /**
     * Scope pour les documents validés
     */
    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope pour les documents en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope pour les documents rejetés
     */
    public function scopeRejetes($query)
    {
        return $query->where('statut', 'rejete');
    }

    /**
     * Scope pour les documents par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les documents d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
