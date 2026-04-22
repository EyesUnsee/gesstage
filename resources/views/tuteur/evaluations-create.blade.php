@extends('layouts.tuteur')

@section('title', 'Nouvelle évaluation - Tuteur')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <h1 class="welcome-title">Nouvelle <span>évaluation</span> ✍️</h1>
    <p class="welcome-subtitle">Créez une nouvelle évaluation pour un stagiaire</p>
</div>

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<div class="form-container">
    <form action="{{ route('tuteur.evaluations.store') }}" method="POST" class="evaluation-form">
        @csrf
        
        <div class="form-section">
            <h3><i class="fas fa-user-graduate"></i> Informations générales</h3>
            
            <div class="form-group">
                <label for="candidat_id">Stagiaire *</label>
                <select name="candidat_id" id="candidat_id" class="form-control" required>
                    <option value="">Sélectionner un stagiaire</option>
                    @foreach($stagiaires as $stagiaire)
                        <option value="{{ $stagiaire->id }}" {{ old('candidat_id') == $stagiaire->id ? 'selected' : '' }}>
                            {{ $stagiaire->first_name }} {{ $stagiaire->last_name }} - {{ $stagiaire->email }}
                        </option>
                    @endforeach
                </select>
                @error('candidat_id')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="titre">Titre de l'évaluation *</label>
                    <input type="text" name="titre" id="titre" class="form-control" value="{{ old('titre') }}" placeholder="Ex: Évaluation mi-parcours" required>
                    @error('titre')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="type">Type d'évaluation *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Sélectionner un type</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="date_limite">Date limite</label>
                <input type="date" name="date_limite" id="date_limite" class="form-control" value="{{ old('date_limite', now()->addDays(7)->format('Y-m-d')) }}">
                @error('date_limite')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-star"></i> Critères d'évaluation</h3>
            <p class="form-help">Notez chaque critère de 1 à 5 étoiles</p>
            
            <div id="criteria-container">
                @foreach($criteria as $index => $criterium)
                <div class="rating-item">
                    <div class="rating-header">
                        <label>{{ $criterium['nom'] }}</label>
                        <div class="rating-stars" data-criteria="{{ $index }}">
                            <i class="far fa-star" onclick="setRating({{ $index }}, 1)"></i>
                            <i class="far fa-star" onclick="setRating({{ $index }}, 2)"></i>
                            <i class="far fa-star" onclick="setRating({{ $index }}, 3)"></i>
                            <i class="far fa-star" onclick="setRating({{ $index }}, 4)"></i>
                            <i class="far fa-star" onclick="setRating({{ $index }}, 5)"></i>
                        </div>
                    </div>
                    <input type="hidden" name="criteria[{{ $index }}][nom]" value="{{ $criterium['nom'] }}">
                    <input type="hidden" name="criteria[{{ $index }}][note]" id="criteria_{{ $index }}" value="{{ old('criteria.' . $index . '.note') }}">
                </div>
                @endforeach
            </div>
            
            <button type="button" class="btn-add-criteria" onclick="addCriteria()">
                <i class="fas fa-plus"></i> Ajouter un critère
            </button>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-comment"></i> Commentaire</h3>
            <textarea name="commentaire" class="form-control" rows="5" placeholder="Ajoutez un commentaire sur le stagiaire...">{{ old('commentaire') }}</textarea>
            @error('commentaire')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <a href="{{ route('tuteur.evaluations') }}" class="btn-cancel">Annuler</a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Créer l'évaluation
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
    
    .form-help {
        color: var(--gris);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1rem;
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
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        color: var(--noir);
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
        min-height: 120px;
    }
    
    .error-text {
        color: var(--rouge);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }
    
    .rating-item {
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .rating-header label {
        font-weight: 600;
        color: var(--noir);
    }
    
    .rating-stars {
        display: flex;
        gap: 0.5rem;
    }
    
    .rating-stars i {
        font-size: 1.5rem;
        color: var(--gris);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .rating-stars i.active,
    .rating-stars i.fas.fa-star {
        color: #fbbf24;
    }
    
    .rating-stars i:hover {
        transform: scale(1.1);
    }
    
    .btn-add-criteria {
        background: var(--blanc);
        border: 2px dashed var(--bleu);
        color: var(--bleu);
        padding: 0.6rem 1.2rem;
        border-radius: 40px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .btn-add-criteria:hover {
        background: var(--bleu);
        color: var(--blanc);
        border-color: var(--bleu);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .btn-cancel, .btn-submit {
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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
        
        .rating-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<script>
    let criteriaCount = {{ count($criteria) }};
    
    function setRating(ratingGroup, value) {
        const stars = document.querySelectorAll(`.rating-stars[data-criteria="${ratingGroup}"] i`);
        stars.forEach((star, index) => {
            if (index < value) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
        document.getElementById(`criteria_${ratingGroup}`).value = value;
    }
    
    function addCriteria() {
        const container = document.getElementById('criteria-container');
        const newIndex = criteriaCount;
        
        const newCriteria = document.createElement('div');
        newCriteria.className = 'rating-item';
        newCriteria.innerHTML = `
            <div class="rating-header">
                <input type="text" name="criteria[${newIndex}][nom]" class="form-control" placeholder="Nom du critère" style="flex: 1;" required>
                <div class="rating-stars" data-criteria="${newIndex}">
                    <i class="far fa-star" onclick="setRating(${newIndex}, 1)"></i>
                    <i class="far fa-star" onclick="setRating(${newIndex}, 2)"></i>
                    <i class="far fa-star" onclick="setRating(${newIndex}, 3)"></i>
                    <i class="far fa-star" onclick="setRating(${newIndex}, 4)"></i>
                    <i class="far fa-star" onclick="setRating(${newIndex}, 5)"></i>
                </div>
                <button type="button" class="remove-criteria" onclick="this.closest('.rating-item').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="hidden" name="criteria[${newIndex}][note]" id="criteria_${newIndex}">
        `;
        
        container.appendChild(newCriteria);
        criteriaCount++;
    }
</script>
@endsection
