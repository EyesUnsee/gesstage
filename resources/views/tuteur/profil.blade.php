@extends('layouts.tuteur')

@section('title', 'Mon profil - Tuteur')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <h1 class="welcome-title">Mon <span>profil</span> 👤</h1>
    <p class="welcome-subtitle">Gérez vos informations personnelles et votre activité de tuteur</p>
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

<!-- Profile header -->
<div class="profile-header">
    <div class="profile-avatar-large">
        @if($user->avatar)
            <img src="{{ Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
        @else
            <i class="fas fa-chalkboard-teacher"></i>
        @endif
    </div>
    <div class="profile-info">
        <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
        <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
        <p><i class="fas fa-phone"></i> {{ $user->phone ?? 'Non renseigné' }}</p>
        <p><i class="fas fa-map-marker-alt"></i> {{ $user->address ?? 'Non renseigné' }}</p>
        <p><i class="fas fa-building"></i> {{ $user->departement ?? 'Département non renseigné' }}</p>
        <div class="profile-badge">
            <i class="fas fa-check-circle"></i>
            Profil tuteur certifié
        </div>
        
        <!-- Formulaire d'upload d'avatar -->
        <form action="{{ route('tuteur.profil.avatar') }}" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
            @csrf
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <div class="input-group" style="flex: 1; min-width: 200px;">
                    <i class="fas fa-camera"></i>
                    <input type="file" name="avatar" accept="image/*" id="avatarInput" required>
                </div>
                <button type="submit" class="btn-save" style="padding: 0.6rem 1.2rem; font-size: 0.9rem;" id="uploadBtn" disabled>
                    <i class="fas fa-upload"></i> Changer l'avatar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats mini -->
<div class="stats-mini-grid">
    <div class="stat-mini-card">
        <div class="number">{{ $stagiairesCount ?? 0 }}</div>
        <div class="label">Stagiaires suivis</div>
    </div>
    <div class="stat-mini-card">
        <div class="number">{{ $evaluationsCount ?? 0 }}</div>
        <div class="label">Évaluations</div>
    </div>
    <div class="stat-mini-card">
        <div class="number">{{ $experience ?? '5+' }}</div>
        <div class="label">Années d'expérience</div>
    </div>
</div>

<!-- Profile form -->
<div class="profile-form">
    <form action="{{ route('tuteur.profil.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Informations personnelles</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>
                    @error('first_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>
                    @error('last_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email professionnel</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" value="{{ $user->email }}" disabled>
                        <small class="text-muted">L'email ne peut pas être modifié</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Adresse</label>
                    <div class="input-group">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}">
                    </div>
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
                    <label>Département</label>
                    <div class="input-group">
                        <i class="fas fa-building"></i>
                        <input type="text" name="departement" value="{{ old('departement', $user->departement ?? '') }}" placeholder="Votre département">
                    </div>
                    @error('departement')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
               
            </div>
          </div>
         

        <div class="form-section">
            <h3><i class="fas fa-graduation-cap"></i> Expertise et domaines</h3>
            <div class="form-group">
                <label>Domaines d'expertise</label>
                <div class="competences-container" id="expertiseSkills">
                    @php
                        $expertises = [];
                        if(!empty($user->expertises)) {
                            $expertises = is_array($user->expertises) ? $user->expertises : json_decode($user->expertises, true) ?? [];
                        }
                    @endphp
                    @forelse($expertises as $expertise)
                        <span class="competence-tag" data-value="{{ $expertise }}">
                            {{ $expertise }} <i class="fas fa-times"></i>
                        </span>
                    @empty
                        <span class="competence-tag" data-value="Développement Web">
                            Développement Web <i class="fas fa-times"></i>
                        </span>
                        <span class="competence-tag" data-value="Base de données">
                            Base de données <i class="fas fa-times"></i>
                        </span>
                        <span class="competence-tag" data-value="Architecture logicielle">
                            Architecture logicielle <i class="fas fa-times"></i>
                        </span>
                    @endforelse
                    <span class="competence-tag add-skill" onclick="addSkill()">
                        <i class="fas fa-plus"></i> Ajouter
                    </span>
                </div>
                <input type="hidden" name="expertises" id="expertisesInput" value='{{ json_encode($expertises) }}'>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Encadrement max</label>
                    <div class="input-group">
                        <i class="fas fa-users"></i>
                        <select name="max_stagiaires">
                            <option value="5" {{ ($user->max_stagiaires ?? 8) == 5 ? 'selected' : '' }}>5 stagiaires</option>
                            <option value="8" {{ ($user->max_stagiaires ?? 8) == 8 ? 'selected' : '' }}>8 stagiaires</option>
                            <option value="10" {{ ($user->max_stagiaires ?? 8) == 10 ? 'selected' : '' }}>10 stagiaires</option>
                            <option value="15" {{ ($user->max_stagiaires ?? 8) == 15 ? 'selected' : '' }}>15 stagiaires</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Années d'expérience</label>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <select name="experience">
                            <option value="1-3" {{ ($user->experience ?? '5-10') == '1-3' ? 'selected' : '' }}>1-3 ans</option>
                            <option value="3-5" {{ ($user->experience ?? '5-10') == '3-5' ? 'selected' : '' }}>3-5 ans</option>
                            <option value="5-10" {{ ($user->experience ?? '5-10') == '5-10' ? 'selected' : '' }}>5-10 ans</option>
                            <option value="10+" {{ ($user->experience ?? '5-10') == '10+' ? 'selected' : '' }}>10+ ans</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Informations supplémentaires</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>LinkedIn</label>
                    <div class="input-group">
                        <i class="fab fa-linkedin"></i>
                        <input type="text" name="linkedin" value="{{ old('linkedin', $user->linkedin ?? '') }}" placeholder="linkedin.com/in/votre-profil">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Disponibilités</label>
                <div class="input-group">
                    <i class="fas fa-clock"></i>
                    <input type="text" name="disponibilites" value="{{ old('disponibilites', $user->disponibilites ?? '') }}" placeholder="Vos jours et horaires de disponibilité">
                </div>
            </div>
            <div class="form-group">
                <label>Bio / Présentation</label>
                <div class="input-group">
                    <i class="fas fa-align-left"></i>
                    <textarea name="bio" rows="4" placeholder="Parlez de vous, de votre parcours, de votre pédagogie...">{{ old('bio', $user->bio) }}</textarea>
                </div>
                @error('bio')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i>
            Enregistrer les modifications
        </button>
    </form>
</div>

<style>
    /* Styles spécifiques au profil qui ne sont pas dans le layout */
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
    
    .profile-header {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2.5rem;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }
    
    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 180px;
    }
    
    .profile-avatar-large {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        border: 4px solid var(--blanc);
        box-shadow: 0 20px 30px -10px var(--bleu);
        color: var(--blanc);
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-info {
        flex: 1;
    }
    
    .profile-info h2 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.8rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .profile-info p {
        color: var(--gris-fonce);
        margin-bottom: 0.5rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    
    .profile-info i {
        width: 24px;
        color: var(--bleu);
    }
    
    .profile-badge {
        background: var(--gris-clair);
        padding: 0.3rem 1.5rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--gris-fonce);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.8rem;
    }
    
    .stats-mini-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
    }
    
    .stat-mini-card {
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .stat-mini-card:hover {
        border-color: var(--bleu);
        background: var(--blanc);
        transform: translateY(-2px);
    }
    
    .stat-mini-card .number {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
    }
    
    .stat-mini-card .label {
        color: var(--gris);
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .profile-form {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    
    .form-section {
        margin-bottom: 2.5rem;
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
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .form-section h3 i {
        color: var(--bleu);
        font-size: 1.3rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        color: var(--gris-fonce);
        margin-bottom: 0.6rem;
        font-weight: 600;
        font-size: 0.95rem;
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
        font-size: 1rem;
    }
    
    .input-group:focus-within i {
        color: var(--bleu);
    }
    
    .input-group input,
    .input-group select,
    .input-group textarea {
        width: 100%;
        padding: 10px 0;
        background: none;
        border: none;
        color: var(--noir);
        font-size: 1rem;
        outline: none;
    }
    
    .input-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .input-group select {
        cursor: pointer;
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
    
    .competences-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-top: 0.5rem;
    }
    
    .competence-tag {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        cursor: default;
    }
    
    .competence-tag i {
        color: var(--bleu);
        font-size: 0.8rem;
        cursor: pointer;
    }
    
    .competence-tag i:hover {
        color: var(--rouge);
    }
    
    .add-skill {
        cursor: pointer;
        background: var(--blanc);
        border: 2px dashed var(--bleu);
        color: var(--bleu);
    }
    
    .add-skill:hover {
        border-color: var(--vert);
        color: var(--vert);
        transform: scale(1.02);
    }
    
    .btn-save {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        box-shadow: 0 20px 30px -12px var(--bleu);
    }
    
    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 25px 40px -12px var(--bleu);
    }
    
    .btn-save[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .profile-info p {
            justify-content: center;
        }
        
        .profile-avatar-large {
            width: 110px;
            height: 110px;
            font-size: 3rem;
        }
        
        .profile-info h2 {
            font-size: 1.8rem;
        }
        
        .stats-mini-grid {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        
        .btn-save {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .profile-avatar-large {
            width: 90px;
            height: 90px;
            font-size: 2.5rem;
        }
        
        .profile-info h2 {
            font-size: 1.5rem;
        }
        
        .profile-info p {
            font-size: 0.95rem;
        }
        
        .profile-form {
            padding: 1.5rem;
        }
        
        .form-section h3 {
            font-size: 1.1rem;
        }
    }
</style>

<script>
    let expertiseSkills = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Activer le bouton d'upload quand un fichier est sélectionné
        const avatarInput = document.getElementById('avatarInput');
        const uploadBtn = document.getElementById('uploadBtn');
        
        if (avatarInput && uploadBtn) {
            avatarInput.addEventListener('change', function() {
                uploadBtn.disabled = !this.files.length;
            });
        }
        
        // Initialiser le tableau d'expertises
        const expertiseInput = document.getElementById('expertisesInput');
        if (expertiseInput && expertiseInput.value) {
            try {
                expertiseSkills = JSON.parse(expertiseInput.value);
            } catch(e) {
                expertiseSkills = [];
            }
        }
        
        // Gérer la suppression des compétences
        document.querySelectorAll('.competence-tag i.fa-times').forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.stopPropagation();
                const tag = this.closest('.competence-tag');
                if (tag && !tag.classList.contains('add-skill')) {
                    const value = tag.getAttribute('data-value') || tag.textContent.replace('×', '').trim();
                    expertiseSkills = expertiseSkills.filter(s => s !== value);
                    updateExpertisesInput();
                    tag.remove();
                }
            });
        });
    });
    
    function addSkill() {
        const newSkill = prompt('Nouvelle compétence :');
        if (newSkill && newSkill.trim() !== '') {
            const skillName = newSkill.trim();
            const container = document.getElementById('expertiseSkills');
            const addButton = container.querySelector('.add-skill');
            
            const newTag = document.createElement('span');
            newTag.className = 'competence-tag';
            newTag.setAttribute('data-value', skillName);
            newTag.innerHTML = `${skillName} <i class="fas fa-times"></i>`;
            
            newTag.querySelector('i').addEventListener('click', function(e) {
                e.stopPropagation();
                expertiseSkills = expertiseSkills.filter(s => s !== skillName);
                updateExpertisesInput();
                newTag.remove();
            });
            
            container.insertBefore(newTag, addButton);
            expertiseSkills.push(skillName);
            updateExpertisesInput();
        }
    }
    
    function updateExpertisesInput() {
        const input = document.getElementById('expertisesInput');
        if (input) {
            input.value = JSON.stringify(expertiseSkills);
        }
    }
</script>
@endsection
