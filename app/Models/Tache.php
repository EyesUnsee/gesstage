<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tache extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'priorite',
        'categorie',
        'echeance',
        'terminee',
        'cree_par_tuteur',
        'jour_semaine',
    ];

    protected $casts = [
        'echeance' => 'date',
        'terminee' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function scopeEnCours($query)
    {
        return $query->where('terminee', false);
    }
    
    public function scopeTerminees($query)
    {
        return $query->where('terminee', true);
    }
    
    public function scopeHautePriorite($query)
    {
        return $query->where('priorite', 'high');
    }
}
