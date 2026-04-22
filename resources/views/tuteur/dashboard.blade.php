@extends('layouts.tuteur')

@section('title', 'Tableau de bord - Encadreur')

@section('content')
<!-- Welcome section avec image logo -->
<div class="welcome-section">
    <div class="welcome-logo">
        <div class="logo-display">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" class="original-colors">
        </div>
    </div>
    <div class="welcome-text">
        <h1 class="welcome-title">Tableau de bord <span>Encadreur</span> 📊</h1>
        <p class="welcome-subtitle">Suivez la progression de vos stagiaires et gérez vos évaluations</p>
    </div>
</div>

<!-- Stats cards améliorées -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stagiaires actifs</span>
            <span class="stat-icon"><i class="fas fa-users"></i></span>
        </div>
        <div class="stat-value">{{ $stagiairesActifs ?? $stagiaires->count() ?? 0 }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up"></i> +{{ $nouveauxStagiaires ?? 0 }} cette semaine
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Évaluations à faire</span>
            <span class="stat-icon"><i class="fas fa-clock"></i></span>
        </div>
        <div class="stat-value">{{ $evaluationsAFaire ?? 0 }}</div>
        <div class="stat-trend trend-down">
            <i class="fas fa-exclamation-circle"></i> @if(($evaluationsAFaire ?? 0) > 0) Urgent @else À jour @endif
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Évaluations faites</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $evaluationsFaites ?? 0 }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-check"></i> +{{ $nouvellesEvaluations ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Journaux à valider</span>
            <span class="stat-icon"><i class="fas fa-book-open"></i></span>
        </div>
        <div class="stat-value">{{ $journauxAValider ?? 0 }}</div>
        <div class="stat-trend trend-down">
            <i class="fas fa-clock"></i> En attente
        </div>
    </div>
</div>

<!-- Mes stagiaires -->
<div class="section-title">
    <i class="fas fa-user-graduate"></i>
    <h2>Mes stagiaires</h2>
</div>

<div class="stagiaires-grid">
    @forelse($stagiaires ?? [] as $stagiaire)
    <div class="stagiaire-card">
        <div class="stagiaire-avatar">
            @if($stagiaire->avatar)
               <img src="{{ asset('storage/' . $stagiaire->avatar) }}" alt="{{ $stagiaire->first_name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                <i class="fas fa-user-graduate"></i>
            @endif
        </div>
        <div class="stagiaire-info">
            <h3>{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</h3>
            <p><i class="fas fa-graduation-cap"></i> {{ $stagiaire->formation ?? 'Stagiaire' }}</p>
            <p><i class="fas fa-building"></i> {{ $stagiaire->entreprise_nom ?? $stagiaire->entreprise ?? 'Entreprise' }}</p>
            <div class="stagiaire-progress">
                <div class="progress-header">
                    <span>Progression du journal</span>
                    <span>{{ $stagiaire->progression ?? 0 }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stagiaire->progression ?? 0 }}%;"></div>
                </div>
            </div>
        </div>
        <a href="{{ route('tuteur.stagiaire.show', $stagiaire->id) }}" class="btn-view">Suivre</a>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Aucun stagiaire pour le moment</h3>
        <p>Les stagiaires vous seront assignés prochainement</p>
    </div>
    @endforelse
</div>

<!-- Évaluations en attente -->
<div class="section-title">
    <i class="fas fa-clock"></i>
    <h2>Évaluations en attente</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Stagiaire</th>
                <th>Type</th>
                <th>Date limite</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluationsEnAttente ?? [] as $evaluation)
            <tr>
                <td>{{ $evaluation->stagiaire_nom ?? $evaluation->candidat->first_name }} {{ $evaluation->candidat->last_name ?? '' }}</td>
                <td>{{ $evaluation->type ?? 'Évaluation' }}</td>
                <td>{{ \Carbon\Carbon::parse($evaluation->date_limite ?? $evaluation->created_at)->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('tuteur.evaluations.edit', $evaluation->id) }}" class="btn-table">
                        <i class="fas fa-edit"></i> Évaluer
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">
                    <div class="empty-state-small">
                        <i class="fas fa-check-circle"></i>
                        <p>Aucune évaluation en attente</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    /* Styles additionnels pour le dashboard tuteur */
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        grid-column: 1 / -1;
    }

    .empty-state i {
        font-size: 3rem;
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

    .empty-state-small {
        text-align: center;
        padding: 2rem;
    }

    .empty-state-small i {
        font-size: 2rem;
        color: var(--vert);
        margin-bottom: 0.5rem;
    }

    .empty-state-small p {
        color: var(--gris);
    }

    .text-center {
        text-align: center;
    }

    .stagiaire-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .btn-table i {
        margin-right: 0.3rem;
    }
</style>

@push('scripts')
<script>
    // Animation des barres de progression au chargement
    document.addEventListener('DOMContentLoaded', () => {
        const bars = document.querySelectorAll('.progress-fill');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
</script>
@endpush
@endsection
