@extends('layouts.responsable')

@section('title', 'Détails du stagiaire - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Détails du <span>stagiaire</span> 👤</h1>
    <p class="welcome-subtitle">Consultez les informations complètes du stagiaire</p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<div class="details-container">
    <div class="details-header">
        <div class="stagiaire-avatar-large">
            @if($stagiaire->avatar)
                <img src="{{ Illuminate\Support\Facades\Storage::url($stagiaire->avatar) }}" alt="Avatar">
            @else
                <i class="fas fa-user-graduate"></i>
            @endif
        </div>
        <div class="stagiaire-info-large">
            <h2>{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</h2>
            <p class="email"><i class="fas fa-envelope"></i> {{ $stagiaire->email }}</p>
            <p class="phone"><i class="fas fa-phone"></i> {{ $stagiaire->phone ?? 'Non renseigné' }}</p>
            <div class="badge {{ $stagiaire->stage ? $stagiaire->stage->statut : 'a_venir' }}">
                @if($stagiaire->stage && $stagiaire->stage->statut == 'en_cours')
                    <i class="fas fa-play-circle"></i> En cours
                @elseif($stagiaire->stage && $stagiaire->stage->statut == 'termine')
                    <i class="fas fa-check-circle"></i> Terminé
                @else
                    <i class="fas fa-clock"></i> À venir
                @endif
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('responsable.stagiaire.edit', $stagiaire->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('responsable.stagiaires') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="details-grid">
        <div class="info-card">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            <div class="info-row">
                <span class="label">Prénom :</span>
                <span class="value">{{ $stagiaire->first_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Nom :</span>
                <span class="value">{{ $stagiaire->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Email :</span>
                <span class="value">{{ $stagiaire->email }}</span>
            </div>
            <div class="info-row">
                <span class="label">Téléphone :</span>
                <span class="value">{{ $stagiaire->phone ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Adresse :</span>
                <span class="value">{{ $stagiaire->address ?? 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date de naissance :</span>
                <span class="value">{{ $stagiaire->birth_date ? \Carbon\Carbon::parse($stagiaire->birth_date)->format('d/m/Y') : 'Non renseignée' }}</span>
            </div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-briefcase"></i> Informations professionnelles</h3>
            <div class="info-row">
                <span class="label">Entreprise :</span>
                <span class="value">{{ $stagiaire->entreprise ?? 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Formation :</span>
                <span class="value">{{ $stagiaire->formation ?? 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Département :</span>
                <span class="value">{{ $stagiaire->departement ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tuteur :</span>
                <span class="value">{{ $stagiaire->tuteur ? $stagiaire->tuteur->first_name . ' ' . $stagiaire->tuteur->last_name : 'Non assigné' }}</span>
            </div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-calendar-alt"></i> Informations du stage</h3>
            @if($stagiaire->stage)
                <div class="info-row">
                    <span class="label">Date début :</span>
                    <span class="value">{{ \Carbon\Carbon::parse($stagiaire->stage->date_debut)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date fin :</span>
                    <span class="value">{{ \Carbon\Carbon::parse($stagiaire->stage->date_fin)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Durée :</span>
                    <span class="value">{{ $stagiaire->stage->duree }} jours</span>
                </div>
                <div class="info-row">
                    <span class="label">Statut :</span>
                    <span class="value">{{ $stagiaire->stage->statut == 'en_cours' ? 'En cours' : ($stagiaire->stage->statut == 'termine' ? 'Terminé' : 'À venir') }}</span>
                </div>
            @else
                <p class="text-muted">Aucun stage enregistré</p>
            @endif
        </div>

        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> Informations supplémentaires</h3>
            <div class="info-row">
                <span class="label">Bio :</span>
                <span class="value">{{ $stagiaire->bio ?? 'Aucune bio renseignée' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date d'inscription :</span>
                <span class="value">{{ \Carbon\Carbon::parse($stagiaire->created_at)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid var(--vert);
        color: var(--vert);
    }
    
    .details-container {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .details-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid var(--gris-clair);
        flex-wrap: wrap;
    }
    
    .stagiaire-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--blanc);
        overflow: hidden;
    }
    
    .stagiaire-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stagiaire-info-large {
        flex: 1;
    }
    
    .stagiaire-info-large h2 {
        color: var(--noir);
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .stagiaire-info-large p {
        color: var(--gris-fonce);
        margin-bottom: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .badge {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    
    .badge.en_cours {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .badge.termine {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gris-fonce);
        border: 1px solid var(--gris);
    }
    
    .badge.a_venir {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    
    .actions {
        display: flex;
        gap: 1rem;
    }
    
    .btn-edit, .btn-back {
        padding: 0.6rem 1.2rem;
        border-radius: 40px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-edit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }
    
    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .btn-back {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
    }
    
    .btn-back:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
    }
    
    .info-card {
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 1.5rem;
    }
    
    .info-card h3 {
        color: var(--noir);
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-card h3 i {
        color: var(--bleu);
    }
    
    .info-row {
        display: flex;
        margin-bottom: 0.8rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row .label {
        width: 120px;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .info-row .value {
        flex: 1;
        color: var(--noir);
    }
    
    .text-muted {
        color: var(--gris);
        font-style: italic;
    }
    
    @media (max-width: 768px) {
        .details-header {
            flex-direction: column;
            text-align: center;
        }
        
        .stagiaire-info-large p {
            justify-content: center;
        }
        
        .actions {
            width: 100%;
            justify-content: center;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-row .label {
            width: 100%;
            margin-bottom: 0.3rem;
        }
    }
</style>
@endsection

