<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidat_id',
        'stage_id',
        'evaluateur_id',
        'titre',
        'description',
        'note',
        'commentaire',
        'evaluateur',
        'evaluateur_nom',
        'date_evaluation',
        'statut',
        'criteria',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'criteria' => 'array',
        'note' => 'decimal:1',
    ];

    /**
     * Relation avec le candidat
     */
    public function candidat()
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    /**
     * Relation avec le stage
     */
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Relation avec l'évaluateur
     */
    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    /**
     * Accesseur pour la note en étoiles
     */
    public function getStarsAttribute()
    {
        $fullStars = floor($this->note);
        $halfStar = ($this->note - $fullStars) >= 0.5;
        $emptyStars = 5 - ceil($this->note);
        
        return [
            'full' => $fullStars,
            'half' => $halfStar,
            'empty' => $emptyStars
        ];
    }

    /**
     * Scope pour les évaluations publiées
     */
    public function scopePublie($query)
    {
        return $query->where('statut', 'publie');
    }

    /**
     * Scope pour les évaluations en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
