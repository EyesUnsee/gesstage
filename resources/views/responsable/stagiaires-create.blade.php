@extends('layouts.responsable')

@section('title', 'Ajouter un stagiaire - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Ajouter un <span>stagiaire</span> 👤</h1>
    <p class="welcome-subtitle">Créez un nouveau compte stagiaire</p>
</div>

<div class="form-container">
    <form action="{{ route('responsable.stagiaires.store') }}" method="POST">
        @csrf
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    @error('first_name') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    @error('last_name') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                    @error('phone') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password') <span class="error">{{ $message }}</span> @enderror
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
                    <label>Tuteur</label>
                    <select name="tuteur_id" class="form-control">
                        <option value="">Aucun tuteur</option>
                        @foreach($tuteurs as $tuteur)
                            <option value="{{ $tuteur->id }}" {{ old('tuteur_id') == $tuteur->id ? 'selected' : '' }}>
                                {{ $tuteur->first_name }} {{ $tuteur->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tuteur_id') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Entreprise</label>
                    <input type="text" name="entreprise" class="form-control" value="{{ old('entreprise') }}">
                    @error('entreprise') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Formation</label>
                    <input type="text" name="formation" class="form-control" value="{{ old('formation') }}">
                    @error('formation') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Département</label>
                    <input type="text" name="departement" class="form-control" value="{{ old('departement') }}">
                    @error('departement') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-calendar-alt"></i> Informations du stage</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date début de stage</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}">
                    @error('date_debut') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Date fin de stage</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}">
                    @error('date_fin') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label>Durée du stage (en mois)</label>
                <input type="number" name="duree" class="form-control" id="duree" readonly placeholder="Calculée automatiquement">
                <small class="form-text">La durée est calculée automatiquement à partir des dates</small>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('responsable.stagiaires') }}" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-submit">Ajouter le stagiaire</button>
        </div>
    </form>
</div>

<style>
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
    
    .form-control[readonly] {
        background: var(--gris);
        cursor: not-allowed;
    }
    
    .error {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .form-text {
        color: var(--gris);
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
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
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
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel, .btn-submit {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateDebut = document.querySelector('input[name="date_debut"]');
        const dateFin = document.querySelector('input[name="date_fin"]');
        const dureeField = document.getElementById('duree');
        
        function calculerDuree() {
            if (dateDebut.value && dateFin.value) {
                const debut = new Date(dateDebut.value);
                const fin = new Date(dateFin.value);
                const diffTime = Math.abs(fin - debut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const diffMonths = Math.ceil(diffDays / 30);
                dureeField.value = diffMonths + ' mois (' + diffDays + ' jours)';
            } else {
                dureeField.value = '';
            }
        }
        
        dateDebut.addEventListener('change', calculerDuree);
        dateFin.addEventListener('change', calculerDuree);
    });
</script>
@endsection
