@extends('layouts.responsable')

@section('title', 'Détails du responsable - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Détails du <span>responsable</span> 👨‍💼</h1>
    <p class="welcome-subtitle">Consultez les informations complètes du responsable</p>
</div>

<div class="details-container">
    <div class="details-header">
        <div class="avatar-large">
            @if($responsable->avatar)
                <img src="{{ Illuminate\Support\Facades\Storage::url($responsable->avatar) }}" alt="Avatar">
            @else
                <i class="fas fa-user-tie"></i>
            @endif
        </div>
        <div class="info-large">
            <h2>{{ $responsable->first_name }} {{ $responsable->last_name }}</h2>
            <p><i class="fas fa-envelope"></i> {{ $responsable->email }}</p>
            <p><i class="fas fa-phone"></i> {{ $responsable->phone ?? 'Non renseigné' }}</p>
            <div class="badge {{ $responsable->is_active ? 'active' : 'inactive' }}">
                @if($responsable->is_active)
                    <i class="fas fa-check-circle"></i> Actif
                @else
                    <i class="fas fa-times-circle"></i> Inactif
                @endif
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('responsable.responsables.edit', $responsable->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('responsable.responsables.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="details-grid">
        <div class="info-card">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            <div class="info-row">
                <span class="label">Prénom :</span>
                <span class="value">{{ $responsable->first_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Nom :</span>
                <span class="value">{{ $responsable->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Email :</span>
                <span class="value">{{ $responsable->email }}</span>
            </div>
            <div class="info-row">
                <span class="label">Téléphone :</span>
                <span class="value">{{ $responsable->phone ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Date d'inscription :</span>
                <span class="value">{{ \Carbon\Carbon::parse($responsable->created_at)->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-briefcase"></i> Informations professionnelles</h3>
            <div class="info-row">
                <span class="label">Département :</span>
                <span class="value">{{ $responsable->departement ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Poste :</span>
                <span class="value">{{ $responsable->poste ?? 'Non renseigné' }}</span>
            </div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> Informations supplémentaires</h3>
            <div class="info-row">
                <span class="label">Bio :</span>
                <span class="value">{{ $responsable->bio ?? 'Aucune bio renseignée' }}</span>
            </div>
        </div>
    </div>
</div>

<style>
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
    
    .avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--blanc);
        overflow: hidden;
    }
    
    .avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .info-large {
        flex: 1;
    }
    
    .info-large h2 {
        color: var(--noir);
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .info-large p {
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
    
    .badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .badge.inactive {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
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
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        color: var(--blanc);
    }
    
    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px #8b5cf6;
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
        color: #8b5cf6;
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
    
    @media (max-width: 768px) {
        .details-header {
            flex-direction: column;
            text-align: center;
        }
        
        .info-large p {
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
