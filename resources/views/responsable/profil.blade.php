@extends('layouts.responsable')

@section('title', 'Mon profil - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Mon <span>profil</span> 👤</h1>
        <p class="welcome-subtitle">Gérez vos informations personnelles et les paramètres de votre compte</p>
    </div>
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

<!-- Profile Grid -->
<div class="profile-grid">
    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-avatar-large">
            @if($user->avatar)
                <img src="{{ Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                <i class="fas fa-user-tie"></i>
            @endif
        </div>
        <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
        <div class="profile-role-badge">
            <i class="fas fa-crown"></i> Responsable Administrateur
        </div>

        

        <div class="profile-contact">
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">Email</div>
                    <div class="contact-value">{{ $user->email }}</div>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">Téléphone</div>
                    <div class="contact-value">{{ $user->phone ?? 'Non renseigné' }}</div>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="contact-info">
                    <div class="contact-label">Département</div>
                    <div class="contact-value">{{ $user->departement ?? 'Administration' }}</div>
                </div>
            </div>
            
        </div>
        
        <!-- Formulaire d'upload d'avatar -->
        <form action="{{ route('responsable.profil.avatar') }}" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
            @csrf
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <div class="input-group" style="flex: 1; min-width: 200px;">
                    <i class="fas fa-camera"></i>
                    <input type="file" name="avatar" accept="image/*" id="avatarInput" required>
                </div>
                <button type="submit" class="btn-save" style="padding: 0.6rem 1.2rem; font-size: 0.9rem;" id="uploadBtn" disabled>
                    <i class="fas fa-upload"></i> Changer
                </button>
            </div>
        </form>
    </div>

    <!-- Profile Form -->
    <div class="profile-form">
        <form action="{{ route('responsable.profil.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user-edit"></i>
                    <h3>Informations personnelles</h3>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email professionnel</label>
                        <input type="email" value="{{ $user->email }}" disabled>
                        <small class="text-muted">L'email ne peut pas être modifié</small>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+33 6 12 34 56 78">
                        @error('phone')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    
                    <div class="form-group">
                        <label>Département</label>
                        <input type="text" name="departement" value="{{ old('departement', $user->departement) }}" placeholder="Votre département">
                        @error('departement')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Votre adresse">
                        @error('address')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                   
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    <h3>Informations supplémentaires</h3>
                </div>

                <div class="form-group">
                    <label>Bio / Présentation</label>
                    <textarea name="bio" rows="4" placeholder="Parlez de vous, de votre parcours, de vos compétences...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('responsable.dashboard') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Security Section -->
<div class="security-section">
    <div class="form-section-title">
        <i class="fas fa-shield-alt"></i>
        <h3>Sécurité du compte</h3>
    </div>

    <div class="security-item">
        <div class="security-info">
            <h4>Changer le mot de passe</h4>
            <p>Modifiez votre mot de passe pour renforcer la sécurité de votre compte</p>
        </div>
        <button class="btn-security" onclick="openPasswordModal()">
            <i class="fas fa-key"></i> Modifier
        </button>
    </div>

    <div class="security-item">
        <div class="security-info">
            <h4>Session active</h4>
            <p>Vous êtes connecté depuis {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : 'récemment' }}</p>
        </div>
        <span class="security-badge">
            <i class="fas fa-check-circle"></i> Actif
        </span>
    </div>
</div>

<!-- Modal Changer mot de passe -->
<div class="modal" id="passwordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Changer le mot de passe</h2>
            <button class="modal-close" onclick="closePasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('responsable.profil.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">Annuler</button>
                <button type="submit" class="btn-primary">Changer le mot de passe</button>
            </div>
        </form>
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
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
    }
    
    .text-muted {
        font-size: 0.75rem;
        color: var(--gris);
        margin-left: 0.5rem;
    }
    
    .error-text {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .profile-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 150px;
    }
    
    .profile-avatar-large {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 3.5rem;
        margin: 0 auto 1.5rem;
        border: 4px solid var(--blanc);
        box-shadow: 0 15px 30px -10px var(--bleu);
        overflow: hidden;
    }
    
    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-card h2 {
        color: var(--noir);
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .profile-role-badge {
        display: inline-block;
        padding: 0.4rem 1.5rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px -5px var(--bleu);
    }
    
    .profile-stats {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
        border-top: 2px solid var(--gris-clair);
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--noir);
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: var(--gris);
        font-weight: 500;
    }
    
    .profile-contact {
        text-align: left;
        margin-top: 1.5rem;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--gris-clair);
    }
    
    .contact-item:last-child {
        border-bottom: none;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        background: var(--gris-clair);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bleu);
        font-size: 1.1rem;
    }
    
    .contact-info {
        flex: 1;
    }
    
    .contact-label {
        font-size: 0.8rem;
        color: var(--gris);
        font-weight: 500;
    }
    
    .contact-value {
        font-size: 0.95rem;
        color: var(--noir);
        font-weight: 600;
    }
    
    .profile-form {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    
    .form-section-title i {
        color: var(--bleu);
        font-size: 1.3rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        width: 100%;
    }
    
    .form-group label {
        display: block;
        color: var(--gris-fonce);
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 1rem 1.2rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 14px;
        color: var(--noir);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .btn-primary {
        padding: 1rem 2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
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
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -8px var(--bleu);
    }
    
    .btn-secondary {
        padding: 1rem 2rem;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris-clair);
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
    
    .btn-secondary:hover {
        background: var(--blanc);
        border-color: var(--gris);
        transform: translateY(-2px);
    }
    
    .security-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .security-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: var(--gris-clair);
        border-radius: 16px;
        margin-bottom: 1rem;
    }
    
    .security-info h4 {
        color: var(--noir);
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .security-info p {
        color: var(--gris);
        font-size: 0.9rem;
    }
    
    .security-badge {
        padding: 0.3rem 1rem;
        background: var(--vert);
        color: var(--blanc);
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .btn-security {
        padding: 0.8rem 1.5rem;
        background: var(--blanc);
        color: var(--bleu);
        border: 2px solid var(--bleu);
        border-radius: 40px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-security:hover {
        background: var(--bleu);
        color: var(--blanc);
        transform: translateY(-2px);
    }
    
    .input-group {
        display: flex;
        align-items: center;
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 0.5rem 1.2rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .input-group:focus-within {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .input-group i {
        color: var(--gris);
        margin-right: 10px;
    }
    
    .input-group input {
        width: 100%;
        padding: 10px 0;
        background: none;
        border: none;
        outline: none;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: var(--blanc);
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .modal-header h2 {
        color: var(--noir);
        font-size: 1.3rem;
    }
    
    .modal-close {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
    }
    
    .modal-close:hover {
        background: var(--rouge);
        color: var(--blanc);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        outline: none;
    }
    
    .form-control:focus {
        border-color: var(--bleu);
        background: var(--blanc);
    }
    
    @media (max-width: 1200px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        
        .btn-primary, .btn-secondary {
            width: 100%;
            justify-content: center;
        }
        
        .security-item {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .profile-stats {
            gap: 1rem;
        }
        
        .profile-avatar-large {
            width: 100px;
            height: 100px;
            font-size: 3rem;
        }
    }
</style>

<script>
    function openPasswordModal() {
        document.getElementById('passwordModal').classList.add('active');
    }
    
    function closePasswordModal() {
        document.getElementById('passwordModal').classList.remove('active');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatarInput');
        const uploadBtn = document.getElementById('uploadBtn');
        
        if (avatarInput && uploadBtn) {
            avatarInput.addEventListener('change', function() {
                uploadBtn.disabled = !this.files.length;
            });
        }
    });
</script>
@endsection
