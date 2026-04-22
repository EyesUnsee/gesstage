@extends('layouts.responsable')

@section('title', 'Ajouter un responsable - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Ajouter un <span>responsable</span> 👨‍💼</h1>
    <p class="welcome-subtitle">Créez un nouveau compte responsable</p>
</div>

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="form-container">
    <form action="{{ route('responsable.responsables.store') }}" method="POST">
        @csrf
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-briefcase"></i> Informations professionnelles</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Département</label>
                    <select name="departement" class="form-control">
                        <option value="">Sélectionner un département</option>
                        <option value="Administration" {{ old('departement') == 'Administration' ? 'selected' : '' }}>Administration</option>
                        <option value="Ressources Humaines" {{ old('departement') == 'Ressources Humaines' ? 'selected' : '' }}>Ressources Humaines</option>
                        <option value="Direction" {{ old('departement') == 'Direction' ? 'selected' : '' }}>Direction</option>
                        <option value="Pédagogie" {{ old('departement') == 'Pédagogie' ? 'selected' : '' }}>Pédagogie</option>
                        <option value="Finance" {{ old('departement') == 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="Communication" {{ old('departement') == 'Communication' ? 'selected' : '' }}>Communication</option>
                    </select>
                    @error('departement')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Poste</label>
                    <input type="text" name="poste" class="form-control" value="{{ old('poste') }}" placeholder="Directeur, Chef de service...">
                    @error('poste')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Informations supplémentaires</h3>
            
            <div class="form-group">
                <label>Bio / Présentation</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="Parlez du responsable...">{{ old('bio') }}</textarea>
                @error('bio')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('responsable.responsables.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Ajouter le responsable
            </button>
        </div>
    </form>
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
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
    }
    
    .form-container {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .form-section h3 {
        color: var(--noir);
        margin-bottom: 1.5rem;
        font-size: 1.2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-section h3 i {
        color: var(--bleu);
        font-size: 1.3rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    select.form-control {
        cursor: pointer;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    .error-text {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .btn-cancel, .btn-submit {
        padding: 0.8rem 2rem;
        border-radius: 40px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-cancel {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
    }
    
    .btn-cancel:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel, .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
