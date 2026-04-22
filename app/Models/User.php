<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'service_id',
        'tuteur_id',
        'phone',
        'address',
        'birth_date',
        'bio',
        'avatar',
        'is_active',
        'status',
        'entreprise',
        'formation',
        'departement',
        'poste',
        'bureau',
        'max_stagiaires',
        'experience',
        'linkedin',
        'disponibilites',
        'expertises',
        'last_login_at',
        'dossier_valide',
        'date_validation_dossier'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'max_stagiaires' => 'integer',
        'dossier_valide' => 'boolean',
        'date_validation_dossier' => 'datetime'
    ];

    // ========== VÉRIFICATIONS DE RÔLE ==========

    /**
     * Vérifier le rôle de l'utilisateur
     */
    public function isCandidat()
    {
        return $this->role === 'candidat';
    }

    public function isTuteur()
    {
        return $this->role === 'tuteur';
    }

    public function isResponsable()
    {
        return $this->role === 'responsable';
    }

    public function isChefService()
    {
        return $this->role === 'chef-service';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // ========== VALIDATION DOSSIER ==========

    /**
     * Vérifier si le dossier du candidat est validé
     */
    public function hasValidDossier()
    {
        return $this->dossier_valide && $this->role === 'candidat';
    }

    /**
     * Vérifier si le candidat est en attente de validation de dossier
     */
    public function isWaitingDossierValidation()
    {
        return !$this->dossier_valide && $this->role === 'candidat';
    }

    /**
     * Vérifier si le dossier est complet
     */
    public function isDossierComplet()
    {
        if ($this->role !== 'candidat') return false;
        
        $hasCV = $this->documents()->where('type', 'cv')->exists();
        $hasLM = $this->documents()->where('type', 'lettre_motivation')->exists();
        $hasProfile = $this->phone && $this->address;
        
        return $hasCV && $hasLM && $hasProfile;
    }

    /**
     * Valider le dossier du candidat
     */
    public function validerDossier()
    {
        if ($this->isDossierComplet()) {
            $this->update([
                'dossier_valide' => true,
                'date_validation_dossier' => now(),
                'status' => 'actif'
            ]);
            return true;
        }
        return false;
    }

    // ========== RELATIONS PRINCIPALES ==========

    /**
     * Relation avec le service (pour tous les utilisateurs)
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ========== RELATIONS POUR LES CANDIDATS ==========

    /**
     * Relation avec le tuteur (encadrant)
     * Pour un candidat : récupérer son tuteur
     */
    public function tuteur()
    {
        return $this->belongsTo(User::class, 'tuteur_id')->where('role', 'tuteur');
    }

    /**
     * Stage de l'utilisateur (pour les candidats)
     */
    public function stage()
    {
        return $this->hasOne(Stage::class, 'candidat_id');
    }

    /**
     * Tâches de l'utilisateur (pour les candidats)
     */
    public function taches()
    {
        return $this->hasMany(Tache::class, 'user_id');
    }

    /**
     * Présences de l'utilisateur (pour les candidats)
     */
    public function presences()
    {
        return $this->hasMany(Presence::class, 'user_id');
    }

    /**
     * Documents de l'utilisateur (pour les candidats)
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    /**
     * Évaluations reçues (pour les candidats)
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'candidat_id');
    }

    /**
     * Journaux de bord (pour les candidats)
     */
    public function journaux()
    {
        return $this->hasMany(Journal::class, 'user_id');
    }

    /**
     * Bilans du stagiaire
     */
    public function bilans()
    {
        return $this->hasMany(Bilan::class, 'stagiaire_id');
    }

    /**
     * Validations en attente
     */
    public function validations()
    {
        return $this->hasMany(Validation::class, 'user_id');
    }

    // ========== RELATIONS POUR LES TUTEURS ==========

    /**
     * Les stagiaires encadrés par ce tuteur
     * Pour un tuteur : récupérer ses stagiaires
     */
    public function stagiaires()
    {
        return $this->hasMany(User::class, 'tuteur_id')
            ->where('role', 'candidat');
    }

    /**
     * Évaluations données (pour les tuteurs)
     */
    public function evaluationsDonnees()
    {
        return $this->hasMany(Evaluation::class, 'evaluateur_id');
    }

    // ========== RELATIONS POUR LE CHEF DE SERVICE ==========

    /**
     * Les stagiaires du service (pour chef de service)
     */
    public function stagiairesService()
    {
        return $this->hasMany(User::class, 'service_id', 'service_id')
            ->where('role', 'candidat');
    }

    /**
     * Les tuteurs du service (pour chef de service)
     */
    public function tuteursService()
    {
        return $this->hasMany(User::class, 'service_id', 'service_id')
            ->where('role', 'tuteur');
    }

    /**
     * Les documents en attente du service
     */
    public function documentsEnAttenteService()
    {
        return $this->hasManyThrough(
            Document::class,
            User::class,
            'service_id',
            'user_id',
            'service_id',
            'id'
        )->where('documents.statut', 'en_attente')
         ->where('users.role', 'candidat');
    }

    /**
     * Les sanctions du service
     */
    public function sanctionsService()
    {
        return $this->hasManyThrough(
            Sanction::class,
            User::class,
            'service_id',
            'stagiaire_id',
            'service_id',
            'id'
        );
    }

    // ========== RELATIONS POUR LES SANCTIONS ==========

    /**
     * Sanctions reçues (pour les stagiaires)
     */
    public function sanctions()
    {
        return $this->hasMany(Sanction::class, 'stagiaire_id');
    }

    /**
     * Sanctions données (pour les chefs de service)
     */
    public function sanctionsDonnees()
    {
        return $this->hasMany(Sanction::class, 'cree_par');
    }

    // ========== AUTRES RELATIONS ==========

    /**
     * Notifications de l'utilisateur
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Messages envoyés
     */
    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    /**
     * Messages reçus
     */
    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }

    /**
     * Activités de l'utilisateur
     */
    public function activites()
    {
        return $this->hasMany(Activite::class, 'user_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les utilisateurs par rôle
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope pour les utilisateurs par service
     */
    public function scopeService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope pour les utilisateurs par département
     */
    public function scopeDepartement($query, $departement)
    {
        return $query->where('departement', $departement);
    }

    /**
     * Scope pour les candidats (stagiaires)
     */
    public function scopeCandidats($query)
    {
        return $query->where('role', 'candidat');
    }

    /**
     * Scope pour les tuteurs
     */
    public function scopeTuteurs($query)
    {
        return $query->where('role', 'tuteur');
    }

    /**
     * Scope pour les responsables
     */
    public function scopeResponsables($query)
    {
        return $query->where('role', 'responsable');
    }

    /**
     * Scope pour les chefs de service
     */
    public function scopeChefsService($query)
    {
        return $query->where('role', 'chef-service');
    }

    /**
     * Scope pour les utilisateurs sans tuteur
     */
    public function scopeSansTuteur($query)
    {
        return $query->whereNull('tuteur_id');
    }

    /**
     * Scope pour les candidats en attente de validation
     */
    public function scopeEnAttenteValidation($query)
    {
        return $query->where('role', 'candidat')
            ->where('dossier_valide', false);
    }

    /**
     * Scope pour les candidats validés
     */
    public function scopeValides($query)
    {
        return $query->where('role', 'candidat')
            ->where('dossier_valide', true);
    }

    // ========== MÉTHODES UTILES ==========

    /**
     * Vérifier si l'utilisateur a un tuteur
     */
    public function hasTuteur()
    {
        return !is_null($this->tuteur_id);
    }

    /**
     * Vérifier si l'utilisateur a un stage actif
     */
    public function hasActiveStage()
    {
        $stage = $this->stage;
        return $stage && $stage->statut === 'en_cours';
    }

    /**
     * Obtenir le nom complet
     */
    public function getFullNameAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';
        $name = $this->name ?? '';
        
        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        }
        if ($firstName) {
            return $firstName;
        }
        if ($lastName) {
            return $lastName;
        }
        return $name ?: 'Utilisateur';
    }

    /**
     * Obtenir l'initiale pour l'avatar
     */
    public function getInitialAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';
        
        if ($firstName && $lastName) {
            return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        }
        if ($firstName) {
            return strtoupper(substr($firstName, 0, 1));
        }
        if ($lastName) {
            return strtoupper(substr($lastName, 0, 1));
        }
        return 'U';
    }

    /**
     * Obtenir l'avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }
        return asset('assets/images/avatar-default.png');
    }

    /**
     * Obtenir le libellé du rôle
     */
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'candidat' => 'Stagiaire',
            'tuteur' => 'Tuteur',
            'responsable' => 'Responsable',
            'chef-service' => 'Chef de service',
            'admin' => 'Administrateur',
            default => 'Utilisateur',
        };
    }

    /**
     * Obtenir la couleur du rôle
     */
    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'candidat' => 'blue',
            'tuteur' => 'green',
            'responsable' => 'orange',
            'chef-service' => 'purple',
            'admin' => 'red',
            default => 'gray',
        };
    }

    /**
     * Obtenir le statut du dossier
     */
    public function getDossierStatutAttribute()
    {
        if ($this->role !== 'candidat') return 'N/A';
        
        if ($this->dossier_valide) {
            return 'Validé';
        }
        
        if ($this->isDossierComplet()) {
            return 'Complet - En attente de validation';
        }
        
        return 'Incomplet';
    }

    /**
     * Obtenir la progression du dossier (0-100)
     */
    public function getDossierProgressionAttribute()
    {
        if ($this->role !== 'candidat') return 0;
        
        $progress = 0;
        
        // CV (25%)
        if ($this->documents()->where('type', 'cv')->exists()) $progress += 25;
        
        // Lettre de motivation (25%)
        if ($this->documents()->where('type', 'lettre_motivation')->exists()) $progress += 25;
        
        // Téléphone (15%)
        if ($this->phone) $progress += 15;
        
        // Adresse (15%)
        if ($this->address) $progress += 15;
        
        // Formation (10%)
        if ($this->formation) $progress += 10;
        
        // Université (10%)
        if ($this->universite) $progress += 10;
        
        return min(100, $progress);
    }

    /**
     * Obtenir le nombre de stagiaires (pour tuteur ou chef de service)
     */
    public function getNombreStagiairesAttribute()
    {
        if ($this->role === 'tuteur') {
            return $this->stagiaires()->count();
        }
        if ($this->role === 'chef-service' && $this->service_id) {
            return User::where('role', 'candidat')
                ->where('service_id', $this->service_id)
                ->count();
        }
        return 0;
    }

    /**
     * Obtenir le nombre de stagiaires actifs
     */
    public function getNombreStagiairesActifsAttribute()
    {
        if ($this->role === 'tuteur') {
            return $this->stagiaires()
                ->whereHas('stage', function($q) {
                    $q->where('statut', 'en_cours');
                })
                ->count();
        }
        if ($this->role === 'chef-service' && $this->service_id) {
            return User::where('role', 'candidat')
                ->where('service_id', $this->service_id)
                ->whereHas('stage', function($q) {
                    $q->where('statut', 'en_cours');
                })
                ->count();
        }
        return 0;
    }

    /**
     * Vérifier si le tuteur peut encadrer plus de stagiaires
     */
    public function canEncadrerPlusStagiaires()
    {
        if ($this->role !== 'tuteur') {
            return false;
        }
        $currentCount = $this->stagiaires()->count();
        $maxStagiaires = $this->max_stagiaires ?? 5;
        return $currentCount < $maxStagiaires;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique parmi plusieurs
     */
    public function hasRole($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role, $roles);
    }

    /**
     * Mettre à jour la date de dernière connexion
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Obtenir le nom du service
     */
    public function getServiceNameAttribute()
    {
        return $this->service?->nom;
    }

    /**
     * Vérifier si l'utilisateur est banni
     */
    public function isBanni()
    {
        return $this->status === 'banni' || !$this->is_active;
    }

    /**
     * Bannir l'utilisateur
     */
    public function bannir($motif = null)
    {
        $this->is_active = false;
        $this->status = 'banni';
        $this->save();
        
        // Créer une sanction
        Sanction::create([
            'stagiaire_id' => $this->id,
            'service_id' => $this->service_id,
            'type' => 'exclusion',
            'motif' => $motif ?? 'Bannissement par l\'administrateur',
            'gravite' => 'elevee',
            'statut' => 'actif',
            'cree_par' => auth()->id()
        ]);
    }

    /**
     * Réactiver l'utilisateur
     */
    public function reactiver()
    {
        $this->is_active = true;
        $this->status = 'actif';
        $this->save();
        
        // Terminer les sanctions actives
        Sanction::where('stagiaire_id', $this->id)
            ->where('statut', 'actif')
            ->update(['statut' => 'termine']);
    }
}
