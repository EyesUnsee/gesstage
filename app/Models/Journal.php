<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    // Spécifier explicitement le nom de la table
    protected $table = 'journaux';

    protected $fillable = [
        'user_id',
        'titre',
        'contenu',
        'categorie',
        'date_journal',
        'statut',
        'commentaire_tuteur',
        'date_validation',
        'date_rejet'
    ];

    protected $casts = [
        'date_journal' => 'date',
        'date_validation' => 'datetime',
        'date_rejet' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
