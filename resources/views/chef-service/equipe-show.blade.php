@extends('layouts.chef-service')

@section('title', 'Détails du membre - Chef de service')
@section('page-title', 'Détails du membre')
@section('active-equipe', 'active')

@section('content')
@php
    use Carbon\Carbon;
@endphp

<style>
    .detail-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }

    .detail-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .detail-avatar {
        width: 100px;
        height: 100px;
        border-radius: 30px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 3rem;
    }

    .detail-info h2 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .detail-info p {
        color: var(--gris);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-section {
        background: var(--gris-clair);
        border-radius: 20px;
        padding: 1.5rem;
    }

    .info-section h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-section h3 i {
        color: var(--bleu);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .info-label {
        color: var(--gris);
        font-weight: 500;
    }

    .info-value {
        font-weight: 600;
        color: var(--noir);
    }

    .back-btn {
        background: var(--gris-clair);
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: var(--gris-fonce);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }

    .back-btn:hover {
        background: var(--bleu);
        color: var(--blanc);
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        .detail-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="detail-card">
    <button class="back-btn" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Retour
    </button>

    <div class="detail-header">
        <div class="detail-avatar">
            @if($membre->avatar)
                <img src="{{ asset('storage/' . $membre->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 30px; object-fit: cover;">
            @else
                <i class="fas fa-user"></i>
            @endif
        </div>
        <div class="detail-info">
            <h2>{{ $membre->first_name }} {{ $membre->last_name }}</h2>
            <p><i class="fas fa-envelope"></i> {{ $membre->email }}</p>
            <p><i class="fas fa-phone"></i> {{ $membre->phone ?? 'Non renseigné' }}</p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3><i class="fas fa-id-card"></i> Informations personnelles</h3>
            <div class="info-row">
                <span class="info-label">Rôle</span>
                <span class="info-value">{{ $membre->role == 'tuteur' ? 'Tuteur' : 'Stagiaire' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut</span>
                <span class="info-value">{{ $membre->is_active ? 'Actif' : 'Inactif' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date d'inscription</span>
                <span class="info-value">{{ Carbon::parse($membre->created_at)->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3><i class="fas fa-chart-line"></i> Statistiques</h3>
            <div class="info-row">
                <span class="info-label">Stagiaires encadrés</span>
                <span class="info-value">{{ $membre->stagiaires_count ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Évaluations</span>
                <span class="info-value">{{ $membre->evaluations_count ?? 0 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Taux de présence</span>
                <span class="info-value">{{ $membre->presence_taux ?? 0 }}%</span>
            </div>
        </div>
    </div>
</div>

<script>
    function showNotification(title, message, type) {
        const toast = document.getElementById('notificationToast');
        if (toast) {
            toast.querySelector('.toast-title').textContent = title;
            toast.querySelector('.toast-message').textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    }
</script>
@endsection
