<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use HasFactory;

    protected $table = 'activites';

    protected $fillable = [
        'titre',
        'description',
        'icone',
        'type',
        'statut',
        'user_id',
        'user_nom',
        'reference_id',
        'reference_type',
        'lu'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope pour les activités non lues
    public function scopeNonLues($query)
    {
        return $query->where('lu', false);
    }

    // Scope pour un utilisateur spécifique
    public function scopePourUtilisateur($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
