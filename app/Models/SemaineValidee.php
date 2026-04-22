<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemaineValidee extends Model
{
    use HasFactory;

    protected $table = 'semaines_validees';

    protected $fillable = [
        'user_id',
        'semaine',
        'annee',
        'validee_le'
    ];

    protected $casts = [
        'validee_le' => 'datetime',
        'semaine' => 'integer',
        'annee' => 'integer'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
