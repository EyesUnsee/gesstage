<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sanction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sanctions';

    protected $fillable = [
        'stagiaire_id',
        'service_id',
        'type',
        'motif',
        'gravite',
        'duree',
        'statut',
        'cree_par'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation avec le stagiaire
    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    // Relation avec le service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Relation avec le créateur de la sanction
    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    // Scope pour les sanctions actives
    public function scopeActives($query)
    {
        return $query->where('statut', 'actif');
    }

    // Scope pour les exclusions
    public function scopeExclusions($query)
    {
        return $query->where('type', 'exclusion')->where('statut', 'actif');
    }
}
