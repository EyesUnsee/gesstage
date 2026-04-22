<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Validation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'titre',
        'type',
        'description',
        'fichier_path',
        'fichier_nom',
        'statut',
        'urgent',
        'valide_par',
        'motif_rejet',
        'date_reponse'
    ];

    protected $casts = [
        'urgent' => 'boolean',
        'date_reponse' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Accesseurs
    public function getIconeAttribute()
    {
        return match($this->type) {
            'bilan' => 'fa-file-alt',
            'inscription' => 'fa-user-plus',
            'convention' => 'fa-file-signature',
            default => 'fa-file',
        };
    }

    public function getStagiaireNomAttribute()
    {
        return $this->user?->first_name . ' ' . $this->user?->last_name;
    }

    public function getServiceNomAttribute()
    {
        return $this->service?->nom;
    }

    public function getJoursAttenteAttribute()
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    public function getMessageDateAttribute()
    {
        $jours = $this->jours_attente;
        
        if ($this->urgent) {
            return '⚠️ URGENT - À traiter immédiatement';
        }
        
        if ($jours == 0) {
            return '📅 Reçu aujourd\'hui';
        } elseif ($jours == 1) {
            return '📅 Reçu hier';
        } elseif ($jours <= 3) {
            return "📅 Reçu il y a {$jours} jours";
        } else {
            return "⏳ En attente depuis {$jours} jours";
        }
    }
}
