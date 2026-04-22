@extends('layouts.candidat')

@section('title', 'Mes évaluations - Candidat')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <h1 class="welcome-title">Mes <span>évaluations</span> ⭐</h1>
    <p class="welcome-subtitle">Consultez toutes vos évaluations de stage et de projet</p>
</div>

<!-- Stats cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Moyenne générale</span>
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="stat-value">{{ $moyenneGenerale ?? '4.5' }}</div>
        <div class="stat-label">sur 5</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Évaluations reçues</span>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $evaluationsRecues ?? $evaluations->count() ?? 0 }}</div>
        <div class="stat-label">au total</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">En attente</span>
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
        <div class="stat-value">{{ $enAttente ?? 0 }}</div>
        <div class="stat-label">évaluations</div>
    </div>
</div>

<!-- Evaluations list -->
<div class="evaluations-list">
    @forelse($evaluations ?? [] as $evaluation)
    <div class="evaluation-card">
        <div class="evaluation-header">
            <span class="evaluation-title">{{ $evaluation->titre ?? 'Évaluation' }}</span>
            <span class="evaluation-date">
                <i class="far fa-calendar-alt"></i> 
                {{ \Carbon\Carbon::parse($evaluation->date_evaluation ?? $evaluation->created_at)->format('d F Y') }}
            </span>
        </div>
        
        <div class="evaluation-criteria">
            @foreach($evaluation->criteria ?? [] as $criterium)
            <div class="criteria-item">
                <span class="criteria-name">{{ $criterium->nom }}</span>
                <div class="criteria-score">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($criterium->note))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $criterium->note)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="score-value">{{ number_format($criterium->note, 1) }}/5</span>
                </div>
            </div>
            @endforeach
            
            @if(empty($evaluation->criteria))
            <div class="criteria-item">
                <span class="criteria-name">Note globale</span>
                <div class="criteria-score">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($evaluation->note ?? 0))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= ($evaluation->note ?? 0))
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="score-value">{{ number_format($evaluation->note ?? 0, 1) }}/5</span>
                </div>
            </div>
            @endif
        </div>

        @if($evaluation->commentaire ?? false)
        <div class="evaluation-comment">
            <i class="fas fa-quote-right"></i>
            "{{ $evaluation->commentaire }}"
        </div>
        @endif

        <div class="evaluation-footer">
            <div class="evaluator">
                <i class="fas fa-user-tie"></i>
                <span>{{ $evaluation->evaluateur_nom ?? $evaluation->evaluateur ?? 'Évaluateur' }}</span>
            </div>
            <a href="{{ route('candidat.evaluations.show', $evaluation->id) }}" class="btn-view">
                <i class="fas fa-arrow-right"></i>
                Voir détails
            </a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-star-half-alt"></i>
        <h3>Aucune évaluation pour le moment</h3>
        <p>Vos évaluations apparaîtront ici une fois disponibles</p>
    </div>
    @endforelse
</div>

<style>
    /* ===== VARIABLES ===== */
    :root {
        --blanc: #ffffff;
        --blanc-casse: #f8fafc;
        --rouge: #ef4444;
        --rouge-fonce: #dc2626;
        --bleu: #3b82f6;
        --bleu-fonce: #2563eb;
        --vert: #10b981;
        --vert-fonce: #059669;
        --gris-clair: #f1f5f9;
        --gris: #94a3b8;
        --gris-fonce: #334155;
        --noir: #0f172a;
        --shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
    }

    /* Welcome section */
    .welcome-section {
        margin-bottom: 2rem;
        background: var(--blanc);
        padding: 2rem;
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 2px solid var(--gris-clair);
        animation: fadeInUp 0.6s ease;
    }

    .welcome-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .welcome-title span {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .welcome-subtitle {
        color: var(--gris);
        font-size: 1rem;
    }

    /* Stats cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.8rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        animation: fadeInUp 0.6s ease backwards;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.1;
        border-radius: 0 0 0 100px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-title {
        color: var(--gris);
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.4rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
        margin-bottom: 0.3rem;
    }

    .stat-label {
        color: var(--gris-fonce);
        font-size: 0.95rem;
    }

    /* Evaluations list */
    .evaluations-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .evaluation-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease backwards;
        position: relative;
        overflow: hidden;
    }

    .evaluation-card:nth-child(1) { animation-delay: 0.3s; }
    .evaluation-card:nth-child(2) { animation-delay: 0.4s; }
    .evaluation-card:nth-child(3) { animation-delay: 0.5s; }

    .evaluation-card:hover {
        transform: translateX(5px);
        border-color: var(--bleu);
    }

    .evaluation-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.03;
        border-radius: 0 0 0 150px;
    }

    .evaluation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .evaluation-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--noir);
    }

    .evaluation-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris);
        font-size: 0.95rem;
        background: var(--gris-clair);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-weight: 500;
    }

    .evaluation-date i {
        color: var(--bleu);
    }

    .evaluation-criteria {
        margin: 1.5rem 0;
        background: var(--gris-clair);
        border-radius: 20px;
        padding: 1.5rem;
    }

    .criteria-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--blanc);
    }

    .criteria-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .criteria-name {
        color: var(--gris-fonce);
        font-weight: 600;
        font-size: 1rem;
    }

    .criteria-score {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stars {
        color: #fbbf24;
        font-size: 0.9rem;
    }

    .stars i {
        margin-right: 2px;
    }

    .score-value {
        font-weight: 700;
        color: var(--noir);
        background: var(--blanc);
        padding: 0.3rem 0.8rem;
        border-radius: 30px;
        font-size: 0.9rem;
    }

    .evaluation-comment {
        background: linear-gradient(135deg, var(--gris-clair), var(--blanc));
        padding: 1.5rem;
        border-radius: 16px;
        margin: 1.5rem 0;
        font-style: italic;
        color: var(--gris-fonce);
        border-left: 4px solid var(--bleu);
        position: relative;
    }

    .evaluation-comment i {
        color: var(--bleu);
        opacity: 0.3;
        font-size: 1.5rem;
        position: absolute;
        bottom: 1rem;
        right: 1rem;
    }

    .evaluation-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--gris-clair);
    }

    .evaluator {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: var(--gris-clair);
        padding: 0.5rem 1.2rem;
        border-radius: 40px;
        font-weight: 500;
        color: var(--gris-fonce);
    }

    .evaluator i {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 0.9rem;
    }

    .btn-view {
        padding: 0.6rem 1.8rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 40px;
        color: var(--blanc);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -10px var(--bleu);
        border-color: var(--blanc);
        color: var(--blanc);
    }

    .btn-view i {
        font-size: 0.9rem;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--blanc);
        border-radius: 24px;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--gris);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--gris);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .evaluation-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .criteria-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .criteria-score {
            width: 100%;
            justify-content: space-between;
        }

        .welcome-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 480px) {
        .welcome-title {
            font-size: 1.5rem;
        }

        .welcome-section {
            padding: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
        }

        .evaluation-card {
            padding: 1.5rem;
        }

        .evaluation-title {
            font-size: 1.1rem;
        }

        .evaluation-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-view {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
