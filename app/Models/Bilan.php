<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bilan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bilans';

    protected $fillable = [
        'stagiaire_id',
        'tuteur_id',
        'service_id',
        'contenu',
        'note',
        'statut',
        'date_soumission',
        'date_validation',
        'valide_par'
    ];

    protected $casts = [
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
        'note' => 'float'
    ];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    public function tuteur()
    {
        return $this->belongsTo(User::class, 'tuteur_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }
}
