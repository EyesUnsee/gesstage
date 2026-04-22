@extends('layouts.candidat')

@section('title', 'Mon profil - Candidat')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> Mon profil</h1>
        <p>Gérez vos informations personnelles</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-avatar-section">
            <div class="profile-avatar">
                @if($candidat->avatar)
                    {{-- Use the full namespace for Storage facade --}}
                    <img src="{{ Illuminate\Support\Facades\Storage::url($candidat->avatar) }}" 
                         alt="Avatar"
                         style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;"
                         onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar-default.png') }}';">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $candidat->first_name }} {{ $candidat->last_name }}</h2>
                <p><i class="fas fa-envelope"></i> {{ $candidat->email }}</p>
                <p><i class="fas fa-phone"></i> {{ $candidat->phone ?? 'Non renseigné' }}</p>
                <p><i class="fas fa-map-marker-alt"></i> {{ $candidat->address ?? 'Non renseigné' }}</p>
                
                {{-- Statut - Avec vérification --}}
                @php
                    $status = $candidat->status ?? 'en_attente';
                    $statusColor = match($status) {
                        'en_attente' => '#f59e0b',
                        'accepte' => '#10b981',
                        'refuse' => '#ef4444',
                        default => '#64748b'
                    };
                @endphp
                
                <div class="profile-badge">
                    <i class="fas fa-check-circle"></i>
                    Statut:
                    <span class="badge" style="background: {{ $statusColor }}; color: white; padding: 5px 10px; border-radius: 20px; margin-left: 5px;">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                {{-- Formulaire d'upload d'avatar --}}
                <form action="{{ route('candidat.profil.avatar') }}" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
                    @csrf
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <div class="input-group" style="flex: 1; min-width: 200px;">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="avatar" accept="image/*" id="avatarInput" required>
                        </div>
                        <button type="submit" class="btn-save" style="padding: 0.8rem 1.5rem; font-size: 0.9rem; background: linear-gradient(135deg, #3b82f6, #10b981);" id="uploadBtn" disabled>
                            <i class="fas fa-upload"></i> Uploader
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Rest of your view remains the same --}}
        <div class="profile-form-section">
            <h3><i class="fas fa-edit"></i> Modifier mes informations</h3>
            
            <form action="{{ route('candidat.profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', $candidat->first_name) }}" 
                               class="form-control @error('first_name') is-invalid @enderror"
                               required>
                        @error('first_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', $candidat->last_name) }}" 
                               class="form-control @error('last_name') is-invalid @enderror"
                               required>
                        @error('last_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" 
                           id="email" 
                           value="{{ $candidat->email }}" 
                           class="form-control"
                           disabled>
                    <small class="text-muted">L'email ne peut pas être modifié</small>
                </div>
                
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $candidat->phone) }}" 
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="+33 6 12 34 56 78">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="address">Adresse</label>
                    <textarea id="address" 
                              name="address" 
                              class="form-control @error('address') is-invalid @enderror"
                              rows="3"
                              placeholder="Votre adresse complète">{{ old('address', $candidat->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="birth_date">Date de naissance</label>
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="{{ old('birth_date', $candidat->birth_date) }}" 
                           class="form-control @error('birth_date') is-invalid @enderror">
                    @error('birth_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio / Présentation</label>
                    <textarea id="bio" 
                              name="bio" 
                              class="form-control @error('bio') is-invalid @enderror"
                              rows="5"
                              placeholder="Parlez-nous de vous, de vos objectifs, de vos compétences...">{{ old('bio', $candidat->bio) }}</textarea>
                    @error('bio')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('candidat.dashboard') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Your CSS and JavaScript remain the same --}}
<style>
    /* Your existing CSS styles */
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .profile-header {
        margin-bottom: 2rem;
    }
    
    .profile-header h1 {
        font-size: 2rem;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }
    
    .profile-header p {
        color: var(--gris);
    }
    
    .alert {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid var(--vert);
        color: var(--vert);
    }
    
    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
    }
    
    .profile-card {
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    
    .profile-avatar-section {
        display: flex;
        gap: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, var(--gris-clair), var(--blanc));
        border-bottom: 2px solid var(--gris-clair);
        flex-wrap: wrap;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 4rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .profile-info {
        flex: 1;
    }
    
    .profile-info h2 {
        color: var(--noir);
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }
    
    .profile-info p {
        margin-bottom: 0.5rem;
        color: var(--gris-fonce);
    }
    
    .profile-info p i {
        width: 25px;
        color: var(--bleu);
    }
    
    .profile-badge {
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .profile-form-section {
        padding: 2rem;
    }
    
    .profile-form-section h3 {
        color: var(--noir);
        margin-bottom: 1.5rem;
        font-size: 1.3rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--gris-fonce);
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid var(--gris-clair);
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--bleu);
    }
    
    .form-control.is-invalid {
        border-color: var(--rouge);
    }
    
    .invalid-feedback {
        color: var(--rouge);
        font-size: 0.85rem;
        margin-top: 0.3rem;
        display: block;
    }
    
    .text-muted {
        color: var(--gris);
        font-size: 0.85rem;
        margin-top: 0.3rem;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .btn-save {
        padding: 0.8rem 2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .btn-cancel {
        padding: 0.8rem 2rem;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-cancel:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--gris-clair);
        padding: 0.5rem 1rem;
        border-radius: 10px;
    }
    
    .input-group i {
        color: var(--gris);
    }
    
    .input-group input {
        background: transparent;
        border: none;
        outline: none;
        flex: 1;
    }
    
    @media (max-width: 768px) {
        .profile-avatar-section {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
    }
</style>

<script>
    // Activer le bouton d'upload quand un fichier est sélectionné
    document.getElementById('avatarInput').addEventListener('change', function() {
        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.disabled = !this.files.length;
    });
</script>
@endsection
