<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'nom',
        'code',
        'description',
        'responsable_id',
        'email',
        'telephone',
        'adresse',
        'logo',
        'is_active',
        'statut',
        'tags'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'service_id');
    }

    public function stagiaires()
    {
        return $this->hasMany(User::class, 'service_id')->where('role', 'candidat');
    }

    public function tuteurs()
    {
        return $this->hasMany(User::class, 'service_id')->where('role', 'tuteur');
    }

    public function sanctions()
    {
        return $this->hasMany(Sanction::class, 'service_id');
    }
}
