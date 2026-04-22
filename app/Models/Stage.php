<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'candidat_id',
        'tuteur_id',
        'entreprise_id',
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'statut',
        'lieu',
        'service',
        'objectifs',
        'competences',
        'duree_hebdomadaire',
        'gratification',
        'convention_path',
        'commentaire',
        'entreprise',  // Ajout de la colonne entreprise
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'gratification' => 'decimal:2',
    ];

    // Relations
    public function candidat()
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    public function tuteur()
    {
        return $this->belongsTo(User::class, 'tuteur_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    // Scopes
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeTermine($query)
    {
        return $query->where('statut', 'termine');
    }

    // Accesseurs
    public function getDureeAttribute()
    {
        return $this->date_debut->diffInDays($this->date_fin);
    }

    public function getEstActifAttribute()
    {
        return $this->statut === 'en_cours';
    }
}
