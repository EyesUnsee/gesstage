<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    use HasFactory;

    protected $table = 'candidats';

    protected $fillable = [
        'user_id',
        'telephone',
        'adresse',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'niveau_etude',
        'experience',
        'competences',
        'cv_path',
        'status'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'competences' => 'array',
        'experience' => 'integer'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function stagiaire()
    {
        return $this->hasOne(Stagiaire::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    public function competences()
    {
        return $this->hasMany(Competence::class);
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

    // Accesseurs
    public function getNomCompletAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getAvatarAttribute()
    {
        return $this->user->avatar_url;
    }
}
