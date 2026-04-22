<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'description',
        'entreprise',
        'lieu',
        'type',
        'statut',
        'date_debut',
        'date_fin',
        'competences',
        'duree',
        'gratification',
        'places',
        'date_limite',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_limite' => 'datetime',
    ];

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }
}
