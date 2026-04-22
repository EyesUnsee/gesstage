<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'siret',
        'code_naf',
        'forme_juridique',
        'email',
        'telephone',
        'fax',
        'site_web',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'secteur_activite',
        'nombre_salaries',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function stages()
    {
        return $this->hasMany(Stage::class);
    }
}
