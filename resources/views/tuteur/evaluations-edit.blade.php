@extends('layouts.tuteur')

@section('title', 'Modifier une évaluation')

@section('content')
<div class="container">
    <div class="welcome-section">
        <h1 class="welcome-title">Modifier <span>l'évaluation</span> ✏️</h1>
        <p class="welcome-subtitle">Modifiez l'évaluation du stagiaire</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Évaluation de {{ $evaluation->candidat->first_name }} {{ $evaluation->candidat->last_name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('tuteur.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')

                @php
                    $criteria = is_string($evaluation->criteria) ? json_decode($evaluation->criteria, true) : ($evaluation->criteria ?? []);
                    if (empty($criteria)) {
                        $criteria = [
                            ['nom' => 'Compétences techniques', 'note' => null],
                            ['nom' => 'Intégration', 'note' => null],
                            ['nom' => 'Autonomie', 'note' => null],
                        ];
                    }
                @endphp

                @foreach($criteria as $index => $criterium)
                <div class="form-group">
                    <label>{{ $criterium['nom'] }}</label>
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ isset($criterium['note']) && $criterium['note'] >= $i ? 'active' : '' }}" 
                               data-value="{{ $i }}" 
                               onclick="setRating({{ $index }}, {{ $i }})"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="criteria[{{ $index }}][nom]" value="{{ $criterium['nom'] }}">
                    <input type="hidden" name="criteria[{{ $index }}][note]" id="criteria_{{ $index }}" value="{{ $criterium['note'] ?? '' }}">
                </div>
                @endforeach

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" rows="5" class="form-control">{{ $evaluation->commentaire }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="{{ route('tuteur.evaluations') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .welcome-section {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        color: white;
        text-align: center;
    }
    
    .welcome-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .welcome-title span {
        color: #fcd34d;
    }
    
    .welcome-subtitle {
        opacity: 0.9;
    }
    
    .card {
        background: white;
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    
    .card-header {
        background: linear-gradient(135deg, var(--gris-clair), white);
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .card-header h3 {
        margin: 0;
        color: var(--noir);
        font-weight: 700;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .rating-stars {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .rating-stars i {
        font-size: 1.5rem;
        color: #d1d5db;
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
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--bleu);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 2px solid var(--gris-clair);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }
    
    .btn-cancel {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
    }
    
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
        
        .rating-stars i {
            font-size: 1.2rem;
        }
    }
</style>

<script>
    function setRating(index, value) {
        // Mettre à jour l'affichage des étoiles
        const stars = document.querySelectorAll(`.form-group:eq(${index}) .rating-stars i`);
        const starElements = document.querySelectorAll(`.form-group .rating-stars i`);
        
        // Trouver le bon groupe
        let groupIndex = 0;
        let currentGroup = null;
        
        for (let i = 0; i < starElements.length; i++) {
            if (i === index * 5) {
                currentGroup = starElements[i].parentElement;
                break;
            }
        }
        
        if (currentGroup) {
            const groupStars = currentGroup.querySelectorAll('i');
            groupStars.forEach((star, starIndex) => {
                if (starIndex < value) {
                    star.classList.add('active');
                    star.className = 'fas fa-star active';
                } else {
                    star.classList.remove('active');
                    star.className = 'far fa-star';
                }
            });
        }
        
        // Mettre à jour la valeur cachée
        document.getElementById(`criteria_${index}`).value = value;
    }
    
    // Initialiser les étoiles au chargement
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($criteria as $index => $criterium)
            @if(isset($criterium['note']) && $criterium['note'])
                setRating({{ $index }}, {{ $criterium['note'] }});
            @endif
        @endforeach
    });
</script>
@endsection
