@extends('layouts.responsable')

@section('title', 'Ajouter un tuteur - Responsable')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">Ajouter un <span>tuteur</span> 👨‍🏫</h1>
    <p class="welcome-subtitle">Créez un nouveau compte tuteur</p>
</div>

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="form-container">
    <form action="{{ route('responsable.tuteurs.store') }}" method="POST">
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
                        <option value="Développement Web" {{ old('departement') == 'Développement Web' ? 'selected' : '' }}>Développement Web</option>
                        <option value="Marketing Digital" {{ old('departement') == 'Marketing Digital' ? 'selected' : '' }}>Marketing Digital</option>
                        <option value="Ressources Humaines" {{ old('departement') == 'Ressources Humaines' ? 'selected' : '' }}>Ressources Humaines</option>
                        <option value="Data Science" {{ old('departement') == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                        <option value="Design UI/UX" {{ old('departement') == 'Design UI/UX' ? 'selected' : '' }}>Design UI/UX</option>
                        <option value="Communication" {{ old('departement') == 'Communication' ? 'selected' : '' }}>Communication</option>
                    </select>
                    @error('departement')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Poste</label>
                    <input type="text" name="poste" class="form-control" value="{{ old('poste') }}" placeholder="Maître de conférences, Professeur...">
                    @error('poste')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Université / École</label>
                    <input type="text" name="universite" class="form-control" value="{{ old('universite') }}" placeholder="Université Claude Bernard Lyon 1">
                    @error('universite')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Bureau</label>
                    <input type="text" name="bureau" class="form-control" value="{{ old('bureau') }}" placeholder="Bâtiment A - Bureau 204">
                    @error('bureau')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Années d'expérience</label>
                    <select name="experience" class="form-control">
                        <option value="">Sélectionner</option>
                        <option value="1-3 ans" {{ old('experience') == '1-3 ans' ? 'selected' : '' }}>1-3 ans</option>
                        <option value="3-5 ans" {{ old('experience') == '3-5 ans' ? 'selected' : '' }}>3-5 ans</option>
                        <option value="5-10 ans" {{ old('experience') == '5-10 ans' ? 'selected' : '' }}>5-10 ans</option>
                        <option value="10+ ans" {{ old('experience') == '10+ ans' ? 'selected' : '' }}>10+ ans</option>
                    </select>
                    @error('experience')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Entreprise</label>
                    <input type="text" name="entreprise" class="form-control" value="{{ old('entreprise') }}" placeholder="Nom de l'entreprise">
                    @error('entreprise')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-graduation-cap"></i> Expertises</h3>
            
            <div class="form-group">
                <label>Domaines d'expertise</label>
                <div class="competences-container" id="expertiseSkills">
                    <span class="competence-tag add-skill" onclick="addSkill()">
                        <i class="fas fa-plus"></i> Ajouter une expertise
                    </span>
                </div>
                <input type="hidden" name="expertises" id="expertisesInput" value='[]'>
                <small class="form-text">Cliquez sur ajouter pour ajouter des compétences</small>
                @error('expertises')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
       
        
        <div class="form-actions">
            <a href="{{ route('responsable.tuteurs') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Ajouter le tuteur
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
        min-height: 80px;
    }
    
    .input-group {
        display: flex;
        align-items: center;
        background: var(--gris-clair);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .input-group:focus-within {
        border-color: var(--bleu);
        background: var(--blanc);
    }
    
    .input-group i {
        color: var(--gris);
        margin-right: 10px;
        font-size: 1rem;
    }
    
    .input-group input {
        background: none;
        border: none;
        padding: 0.8rem 0;
        outline: none;
        width: 100%;
    }
    
    .error-text {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: var(--gris);
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

<script>
    let expertiseSkills = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        const expertiseInput = document.getElementById('expertisesInput');
        if (expertiseInput && expertiseInput.value) {
            try {
                expertiseSkills = JSON.parse(expertiseInput.value);
                expertiseSkills.forEach(skill => {
                    addSkillTag(skill);
                });
            } catch(e) {
                expertiseSkills = [];
            }
        }
    });
    
    function addSkill() {
        const newSkill = prompt('Nouvelle expertise :');
        if (newSkill && newSkill.trim() !== '') {
            const skillName = newSkill.trim();
            if (!expertiseSkills.includes(skillName)) {
                expertiseSkills.push(skillName);
                addSkillTag(skillName);
                updateExpertisesInput();
            }
        }
    }
    
    function addSkillTag(skillName) {
        const container = document.getElementById('expertiseSkills');
        const addButton = container.querySelector('.add-skill');
        
        const newTag = document.createElement('span');
        newTag.className = 'competence-tag';
        newTag.setAttribute('data-value', skillName);
        newTag.innerHTML = `${skillName} <i class="fas fa-times"></i>`;
        
        newTag.querySelector('i').addEventListener('click', function(e) {
            e.stopPropagation();
            const value = this.closest('.competence-tag').getAttribute('data-value');
            expertiseSkills = expertiseSkills.filter(s => s !== value);
            updateExpertisesInput();
            this.closest('.competence-tag').remove();
        });
        
        container.insertBefore(newTag, addButton);
    }
    
    function updateExpertisesInput() {
        document.getElementById('expertisesInput').value = JSON.stringify(expertiseSkills);
    }
</script>
@endsection
