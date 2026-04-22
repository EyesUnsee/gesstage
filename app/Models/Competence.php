<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidat_id',
        'nom',
        'valeur',
        'categorie'
    ];

    protected $casts = [
        'valeur' => 'integer'
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }
}
