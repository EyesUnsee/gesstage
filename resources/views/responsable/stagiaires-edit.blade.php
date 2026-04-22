@extends('layouts.responsable')

@section('title', 'Modifier le stagiaire - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Modifier le <span>stagiaire</span> ✏️</h1>
    <p class="welcome-subtitle">Modifiez les informations du stagiaire</p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="form-container">
    <form action="{{ route('responsable.stagiaire.update', $stagiaire->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $stagiaire->first_name) }}" required>
                    @error('first_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $stagiaire->last_name) }}" required>
                    @error('last_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="{{ $stagiaire->email }}" disabled>
                    <small class="text-muted">L'email ne peut pas être modifié</small>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $stagiaire->phone) }}">
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $stagiaire->address) }}">
                    @error('address')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
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
                            <option value="{{ $tuteur->id }}" {{ old('tuteur_id', $stagiaire->tuteur_id) == $tuteur->id ? 'selected' : '' }}>
                                {{ $tuteur->first_name }} {{ $tuteur->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tuteur_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Entreprise</label>
                    <input type="text" name="entreprise" class="form-control" value="{{ old('entreprise', $stagiaire->entreprise) }}">
                    @error('entreprise')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Formation</label>
                    <input type="text" name="formation" class="form-control" value="{{ old('formation', $stagiaire->formation) }}">
                    @error('formation')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Département</label>
                    <input type="text" name="departement" class="form-control" value="{{ old('departement', $stagiaire->departement) }}">
                    @error('departement')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-calendar-alt"></i> Informations du stage</h3>
            
            @php
                $dateDebut = $stagiaire->stage ? $stagiaire->stage->date_debut : null;
                $dateFin = $stagiaire->stage ? $stagiaire->stage->date_fin : null;
                $statutStage = $stagiaire->stage ? $stagiaire->stage->statut : 'a_venir';
                $duree = $dateDebut && $dateFin ? $dateDebut->diffInDays($dateFin) : 0;
                $dureeMois = $dateDebut && $dateFin ? ceil($duree / 30) : 0;
            @endphp
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date début de stage</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ old('date_debut', $dateDebut ? \Carbon\Carbon::parse($dateDebut)->format('Y-m-d') : '') }}">
                    @error('date_debut')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Date fin de stage</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ old('date_fin', $dateFin ? \Carbon\Carbon::parse($dateFin)->format('Y-m-d') : '') }}">
                    @error('date_fin')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label>Durée du stage</label>
                <input type="text" id="duree" class="form-control" readonly value="{{ $dureeMois ? $dureeMois . ' mois (' . $duree . ' jours)' : '' }}" placeholder="Calculée automatiquement">
                <small class="form-text">La durée est calculée automatiquement à partir des dates</small>
            </div>
            
            <div class="form-group">
                <label>Statut du stage</label>
                <select name="stage_statut" class="form-control">
                    <option value="a_venir" {{ old('stage_statut', $statutStage) == 'a_venir' ? 'selected' : '' }}>À venir</option>
                    <option value="en_cours" {{ old('stage_statut', $statutStage) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="termine" {{ old('stage_statut', $statutStage) == 'termine' ? 'selected' : '' }}>Terminé</option>
                </select>
                @error('stage_statut')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Informations supplémentaires</h3>
            
            <div class="form-group">
                <label>Bio / Présentation</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="Parlez du stagiaire...">{{ old('bio', $stagiaire->bio) }}</textarea>
                @error('bio')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('responsable.stagiaires') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Enregistrer les modifications
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
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid var(--vert);
        color: var(--vert);
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
    }
    
    .text-muted {
        font-size: 0.75rem;
        color: var(--gris);
        margin-top: 0.25rem;
        display: block;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: var(--gris);
        margin-top: 0.25rem;
        display: block;
    }
    
    .error-text {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
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
    
    select.form-control {
        cursor: pointer;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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
        
        .form-container {
            padding: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .form-section h3 {
            font-size: 1.1rem;
        }
        
        .form-control {
            font-size: 0.9rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');
        const dureeField = document.getElementById('duree');
        
        function calculerDuree() {
            if (dateDebut.value && dateFin.value) {
                const debut = new Date(dateDebut.value);
                const fin = new Date(dateFin.value);
                
                if (fin >= debut) {
                    const diffTime = Math.abs(fin - debut);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const diffMois = Math.ceil(diffDays / 30);
                    dureeField.value = diffMois + ' mois (' + diffDays + ' jours)';
                } else {
                    dureeField.value = 'Date de fin antérieure à la date de début';
                }
            } else {
                dureeField.value = '';
            }
        }
        
        if (dateDebut && dateFin) {
            dateDebut.addEventListener('change', calculerDuree);
            dateFin.addEventListener('change', calculerDuree);
            calculerDuree();
        }
        
        // Animation des champs au focus
        const inputs = document.querySelectorAll('.form-control:not([readonly])');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
@endsection
