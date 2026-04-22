<?php
// app/Models/Candidature.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    protected $table = 'candidatures';

    protected $fillable = [
        'candidat_id',
        'titre',
        'entreprise',
        'type',
        'statut',
        'date_debut',
        'date_fin',
        'date_reponse',
        'description',
        'cv_path',
        'lettre_motivation_path'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_reponse' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_ACCEPTEE = 'acceptee';
    const STATUT_REFUSEE = 'refusee';

    const STATUTS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_EN_COURS => 'En cours d\'examen',
        self::STATUT_ACCEPTEE => 'Acceptée',
        self::STATUT_REFUSEE => 'Refusée'
    ];

    // Types de stage
    const TYPE_DEVELOPPEMENT = 'developpement';
    const TYPE_MARKETING = 'marketing';
    const TYPE_RH = 'rh';
    const TYPE_DATA = 'data';
    const TYPE_DESIGN = 'design';

    const TYPES = [
        self::TYPE_DEVELOPPEMENT => 'Développement Web',
        self::TYPE_MARKETING => 'Marketing Digital',
        self::TYPE_RH => 'Ressources Humaines',
        self::TYPE_DATA => 'Data Science',
        self::TYPE_DESIGN => 'Design'
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    // Scopes pour les filtres
    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    public function scopeAcceptees($query)
    {
        return $query->where('statut', self::STATUT_ACCEPTEE);
    }

    public function scopeRefusees($query)
    {
        return $query->where('statut', self::STATUT_REFUSEE);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    // Accesseurs
    public function getStatutLabelAttribute()
    {
        return self::STATUTS[$this->statut] ?? ucfirst($this->statut);
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPeriodeStageAttribute()
    {
        if ($this->date_debut && $this->date_fin) {
            return $this->date_debut->format('d/m/Y') . ' - ' . $this->date_fin->format('d/m/Y');
        }
        return 'Non spécifiée';
    }
}
