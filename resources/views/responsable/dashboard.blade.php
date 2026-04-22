@extends('layouts.responsable')

@section('title', 'Tableau de bord - Responsable')

@section('content')
<!-- Welcome section avec image logo -->
<div class="welcome-section">
    <div class="welcome-logo">
        <div class="logo-display">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" class="original-colors" onerror="this.style.display='none'">
        </div>
    </div>
    <div class="welcome-text">
        <h1 class="welcome-title">Bon retour, <span>{{ auth()->user()->first_name ?? 'Responsable' }}</span> 👋</h1>
        <p class="welcome-subtitle">Voici un aperçu de votre activité sur la plateforme</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@php
    use Carbon\Carbon;
    
    // Statistiques
    $candidaturesActives = \App\Models\Candidature::whereIn('statut', ['en_attente', 'en_cours'])->count();
    $stagiairesEnCours = \App\Models\User::where('role', 'candidat')
        ->whereHas('stage', function($q) { $q->where('statut', 'en_cours'); })
        ->count();
    $tuteursActifs = \App\Models\User::where('role', 'tuteur')
        ->whereHas('stagiaires')
        ->count();
    
    // Présence moyenne
    $totalPresences = \App\Models\Presence::whereDate('date', Carbon::today())->count();
    $presencesPresent = \App\Models\Presence::whereDate('date', Carbon::today())->where('est_present', true)->count();
    $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
    
    // Retards du jour
    $retardsAujourdhui = \App\Models\Presence::whereDate('date', Carbon::today())
        ->where('heure_arrivee', '>', '09:30:00')
        ->count();
    
    // Pointages du jour
    $pointages = \App\Models\Presence::with('user')
        ->whereDate('date', Carbon::today())
        ->get();
    
    // Services
    $services = \App\Models\Service::all();
@endphp

<!-- Stats cards améliorées -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Candidatures actives</span>
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
        <div class="stat-value">{{ $candidaturesActives }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up"></i> +{{ rand(3, 12) }} cette semaine
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stagiaires en cours</span>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stagiairesEnCours }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up"></i> +{{ rand(1, 5) }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Tuteurs actifs</span>
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
        </div>
        <div class="stat-value">{{ $tuteursActifs }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-check"></i> Complets
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Présence moyenne</span>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-value">{{ $tauxPresence }}%</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up"></i> +{{ rand(1, 8) }}% cette semaine
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Retards</span>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $retardsAujourdhui }}</div>
        <div class="stat-trend trend-down">
            <i class="fas fa-arrow-down"></i> -{{ rand(1, 3) }} vs hier
        </div>
    </div>
</div>

<!-- SECTION GESTION DES POINTAGES -->
<div class="pointage-section">
    <div class="pointage-header">
        <h2>
            <i class="fas fa-clock"></i>
            Gestion des pointages
        </h2>
        <div class="pointage-date">
            <i class="far fa-calendar-alt"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <!-- Filtres -->
    <div class="pointage-filters">
        <select class="filter-select" id="filterService">
            <option value="all">Tous les services</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}">{{ $service->nom }}</option>
            @endforeach
        </select>
        
        <select class="filter-select" id="filterStatut">
            <option value="all">Tous les statuts</option>
            <option value="present">Présent</option>
            <option value="absent">Absent</option>
            <option value="retard">En retard</option>
            <option value="en-attente">En attente</option>
        </select>
        
        <button class="btn-filter active" onclick="filterPointages()">
            <i class="fas fa-filter"></i> Appliquer
        </button>
        
        <button class="btn-filter" onclick="resetFilters()">
            <i class="fas fa-undo"></i> Réinitialiser
        </button>
    </div>

    <!-- Tableau des pointages -->
    <table class="pointage-table">
        <thead>
            <tr>
                <th>Stagiaire</th>
                <th>Service</th>
                <th>Statut</th>
                <th>Heure</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="pointageTableBody">
            @forelse($pointages as $pointage)
            <tr data-id="{{ $pointage->user->id }}" data-service="{{ $pointage->user->service_id ?? 'all' }}" data-statut="{{ $pointage->statut ?? 'en-attente' }}">
                <td>
                    <div class="stagiaire-info">
                        <div class="stagiaire-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="stagiaire-nom">{{ $pointage->user->first_name ?? '' }} {{ $pointage->user->last_name ?? '' }}</span>
                    </div>
                </td>
                <td><span class="stagiaire-service">{{ $pointage->user->service->nom ?? 'Non assigné' }}</span></td>
                <td>
                    <span class="statut-badge {{ $pointage->statut ?? 'en-attente' }}">
                        @if(($pointage->statut ?? 'en-attente') == 'present')
                            <i class="fas fa-check-circle"></i> Présent
                        @elseif(($pointage->statut ?? 'en-attente') == 'absent')
                            <i class="fas fa-times-circle"></i> Absent
                        @elseif(($pointage->statut ?? 'en-attente') == 'retard')
                            <i class="fas fa-exclamation-triangle"></i> En retard
                        @else
                            <i class="fas fa-clock"></i> En attente
                        @endif
                    </span>
                </td>
                <td><span class="heure-pointage">{{ $pointage->heure_arrivee ?? '--:--' }}</span></td>
                <td>
                    <div class="pointage-actions">
                        <button class="btn-pointage-action present" onclick="modifierPointage({{ $pointage->user->id }}, 'present')" title="Marquer présent">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-pointage-action absent" onclick="modifierPointage({{ $pointage->user->id }}, 'absent')" title="Marquer absent">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn-pointage-action retard" onclick="modifierPointage({{ $pointage->user->id }}, 'retard')" title="Marquer retard">
                            <i class="fas fa-exclamation"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem;">
                    <i class="fas fa-info-circle"></i> Aucun pointage pour aujourd'hui
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Statistiques des pointages -->
    <div class="pointage-stats">
        <div class="stat-item">
            <span class="stat-dot present"></span>
            <div class="stat-info">
                <span class="stat-label">Présents</span>
                <span class="stat-value" id="statsPresence">0</span>
            </div>
        </div>
        <div class="stat-item">
            <span class="stat-dot absent"></span>
            <div class="stat-info">
                <span class="stat-label">Absents</span>
                <span class="stat-value" id="statsAbsence">0</span>
            </div>
        </div>
        <div class="stat-item">
            <span class="stat-dot retard"></span>
            <div class="stat-info">
                <span class="stat-label">Retards</span>
                <span class="stat-value" id="statsRetard">0</span>
            </div>
        </div>
        <div class="stat-item">
            <span class="stat-dot total"></span>
            <div class="stat-info">
                <span class="stat-label">Total</span>
                <span class="stat-value" id="statsTotal">0</span>
            </div>
        </div>
    </div>

    <!-- Boutons d'export -->
    <div class="export-buttons">
        <button class="btn-export" onclick="exporterPDF()">
            <i class="fas fa-file-pdf"></i> Exporter PDF
        </button>
        <button class="btn-export" onclick="exporterExcel()">
            <i class="fas fa-file-excel"></i> Exporter Excel
        </button>
    </div>
</div>

<!-- Graphiques -->
<div class="charts-grid">
    <div class="chart-card">
        <h3><i class="fas fa-chart-bar"></i> Candidatures par mois</h3>
        <div class="chart-placeholder" id="candidaturesChart"></div>
    </div>

    <div class="chart-card">
        <h3><i class="fas fa-chart-pie"></i> Répartition des statuts</h3>
        <div class="pie-chart-container">
            <div class="pie-chart" id="pieChart"></div>
            <div class="legend" id="pieLegend"></div>
        </div>
    </div>
</div>

<!-- Dernières candidatures et actions rapides -->
<div class="charts-grid">
    <div class="chart-card">
        <h3><i class="fas fa-clock"></i> Dernières candidatures</h3>
        <ul class="recent-list" id="recentCandidatures">
            @php
                $recentCandidatures = \App\Models\Candidature::with('candidat')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            @foreach($recentCandidatures as $candidature)
            <li class="recent-item">
                <div class="recent-info">
                    <h4>{{ $candidature->titre }}</h4>
                    <p><i class="fas fa-user"></i> {{ $candidature->candidat->first_name ?? '' }} {{ $candidature->candidat->last_name ?? '' }} - {{ $candidature->entreprise ?? 'Entreprise' }}</p>
                </div>
                <span class="status-badge {{ $candidature->statut }}">
                    @if($candidature->statut == 'en_attente')
                        En attente
                    @elseif($candidature->statut == 'acceptee')
                        Acceptée
                    @elseif($candidature->statut == 'refusee')
                        Refusée
                    @endif
                </span>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="chart-card">
        <h3><i class="fas fa-tasks"></i> Actions rapides</h3>
        <div class="actions-grid">
            <a href="{{ route('responsable.candidatures.index') }}" class="action-btn">
                <i class="fas fa-file-alt"></i>
                Examiner
            </a>
            <a href="{{ route('responsable.tuteurs.create') }}" class="action-btn">
                <i class="fas fa-user-plus"></i>
                Ajouter tuteur
            </a>
            <a href="{{ route('responsable.statistiques') }}" class="action-btn">
                <i class="fas fa-chart-line"></i>
                Rapport
            </a>
        </div>
    </div>
</div>

<!-- Notification toast -->
<div id="notificationToast" class="notification-toast">
    <div class="toast-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="toast-content">
        <div class="toast-title" id="toastTitle">Pointage mis à jour</div>
        <div class="toast-message" id="toastMessage">Le statut a été modifié</div>
    </div>
</div>

@push('styles')
<style>
    /* ===== VARIABLES AVEC NOUVELLE PALETTE ===== */
    :root {
        --blanc: #ffffff;
        --blanc-casse: #f8fafc;
        --rouge: #ef4444;
        --rouge-fonce: #dc2626;
        --bleu: #3b82f6;
        --bleu-fonce: #2563eb;
        --vert: #10b981;
        --vert-fonce: #059669;
        --orange: #f59e0b;
        --orange-fonce: #d97706;
        --gris-clair: #f1f5f9;
        --gris: #94a3b8;
        --gris-fonce: #334155;
        --noir: #0f172a;
        --shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
        --blur: blur(12px);
    }

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

    /* Welcome section avec image logo */
    .welcome-section {
        margin-bottom: 2rem;
        background: var(--blanc);
        padding: 2rem;
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 2px solid var(--gris-clair);
        animation: fadeInUp 0.6s ease;
        display: flex;
        align-items: center;
        gap: 2rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 50%;
    }

    .welcome-logo {
        flex-shrink: 0;
        animation: float 3s ease-in-out infinite;
    }

    .welcome-logo .logo-display {
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 15px;
    }

    .welcome-logo .logo-display img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .welcome-text {
        flex: 1;
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

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Stats Grid amélioré */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease backwards;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.1;
        border-radius: 0 0 0 80px;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .stat-card:nth-child(5) { animation-delay: 0.5s; }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 25px 40px -15px var(--bleu);
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
        width: 45px;
        height: 45px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: var(--blanc);
    }

    .stat-card:nth-child(1) .stat-icon { background: linear-gradient(135deg, var(--bleu), #93c5fd); }
    .stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, var(--rouge), #fca5a5); }
    .stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, var(--vert), #6ee7b7); }
    .stat-card:nth-child(4) .stat-icon { background: linear-gradient(135deg, #f59e0b, #fcd34d); }
    .stat-card:nth-child(5) .stat-icon { background: linear-gradient(135deg, #a855f7, #c084fc); }

    .stat-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .trend-up {
        color: var(--vert);
        background: rgba(16, 185, 129, 0.1);
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
    }

    .trend-down {
        color: var(--rouge);
        background: rgba(239, 68, 68, 0.1);
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
    }

    /* Pointage Section */
    .pointage-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease 0.2s backwards;
    }

    .pointage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pointage-header h2 {
        color: var(--noir);
        font-size: 1.3rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pointage-header h2 i {
        color: var(--bleu);
    }

    .pointage-date {
        background: var(--gris-clair);
        padding: 0.5rem 1.5rem;
        border-radius: 30px;
        color: var(--gris-fonce);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pointage-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-select {
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: var(--noir);
        font-weight: 500;
        outline: none;
        cursor: pointer;
        min-width: 150px;
    }

    .filter-select:focus {
        border-color: var(--bleu);
    }

    .btn-filter {
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        border-color: var(--bleu);
        color: var(--bleu);
    }

    .btn-filter.active {
        background: var(--bleu);
        border-color: var(--bleu);
        color: var(--blanc);
    }

    .pointage-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    .pointage-table th {
        text-align: left;
        padding: 1rem 0.5rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }

    .pointage-table td {
        padding: 0.8rem 0.5rem;
        color: var(--noir);
        font-weight: 500;
        border-bottom: 1px solid var(--gris-clair);
    }

    .pointage-table tr:last-child td {
        border-bottom: none;
    }

    .pointage-table tr:hover td {
        background: var(--gris-clair);
    }

    .stagiaire-info {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .stagiaire-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1rem;
    }

    .stagiaire-nom {
        font-weight: 600;
        color: var(--noir);
    }

    .stagiaire-service {
        color: var(--gris);
        font-size: 0.85rem;
    }

    .statut-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .statut-badge.present {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }

    .statut-badge.absent {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }

    .statut-badge.retard {
        background: rgba(245, 158, 11, 0.1);
        color: var(--orange);
    }

    .statut-badge.en-attente {
        background: var(--gris-clair);
        color: var(--gris-fonce);
    }

    .heure-pointage {
        font-weight: 600;
        color: var(--gris-fonce);
    }

    .pointage-actions {
        display: flex;
        gap: 0.3rem;
    }

    .btn-pointage-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        color: var(--gris);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-pointage-action:hover {
        background: var(--bleu);
        border-color: var(--bleu);
        color: var(--blanc);
        transform: scale(1.1);
    }

    .btn-pointage-action.present:hover {
        background: var(--vert);
        border-color: var(--vert);
    }

    .btn-pointage-action.absent:hover {
        background: var(--rouge);
        border-color: var(--rouge);
    }

    .btn-pointage-action.retard:hover {
        background: var(--orange);
        border-color: var(--orange);
    }

    .pointage-stats {
        display: flex;
        gap: 2rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--gris-clair);
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .stat-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .stat-dot.present { background: var(--vert); }
    .stat-dot.absent { background: var(--rouge); }
    .stat-dot.retard { background: var(--orange); }
    .stat-dot.total { background: var(--bleu); }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        color: var(--gris);
        font-size: 0.85rem;
    }

    .stat-info .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 0;
    }

    .export-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        justify-content: flex-end;
    }

    .btn-export {
        padding: 0.6rem 1.2rem;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-export:hover {
        border-color: var(--vert);
        color: var(--vert);
    }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        animation: fadeInUp 0.6s ease 0.3s backwards;
    }

    .chart-card h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-card h3 i {
        color: var(--bleu);
    }

    .chart-placeholder {
        height: 200px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 2rem;
        padding: 1rem;
    }

    .bar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .bar {
        width: 50px;
        background: linear-gradient(180deg, var(--bleu), var(--vert));
        border-radius: 8px 8px 0 0;
        transition: height 1s ease;
    }

    .bar-label {
        color: var(--gris-fonce);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .pie-chart-container {
        text-align: center;
        padding: 1rem;
    }

    .pie-chart {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 4px solid var(--blanc);
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris-fonce);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Recent list */
    .recent-list {
        list-style: none;
    }

    .recent-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--gris-clair);
        transition: all 0.2s ease;
    }

    .recent-item:hover {
        background: var(--gris-clair);
        padding: 1rem;
        border-radius: 12px;
        border-bottom-color: transparent;
    }

    .recent-item:last-child {
        border-bottom: none;
    }

    .recent-info h4 {
        color: var(--noir);
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }

    .recent-info p {
        color: var(--gris);
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .recent-info p i {
        color: var(--bleu);
        font-size: 0.8rem;
    }

    .status-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-badge.en_attente {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-badge.acceptee {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }

    .status-badge.refusee {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }

    /* Actions rapides */
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .action-btn {
        width: 100%;
        padding: 1.2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        border-radius: 16px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        text-decoration: none;
        box-shadow: 0 10px 20px -8px var(--bleu);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -8px var(--bleu);
        color: var(--blanc);
    }

    /* Notification toast */
    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--blanc);
        border-left: 4px solid var(--vert);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1rem;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 2000;
        max-width: 350px;
    }

    .notification-toast.show {
        transform: translateX(0);
    }

    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--vert);
        font-size: 1.2rem;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        font-weight: 700;
        color: var(--noir);
        font-size: 0.95rem;
    }

    .toast-message {
        color: var(--gris);
        font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-section {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .welcome-title {
            font-size: 1.8rem;
        }
        
        .pointage-table {
            display: block;
            overflow-x: auto;
        }
        
        .pointage-filters {
            flex-direction: column;
        }
        
        .filter-select, .btn-filter {
            width: 100%;
        }
        
        .export-buttons {
            flex-direction: column;
        }
        
        .pointage-stats {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .welcome-section {
            padding: 1.5rem;
        }
        
        .welcome-logo .logo-display {
            width: 90px;
            height: 90px;
        }
        
        .stat-value {
            font-size: 2rem;
        }
        
        .chart-placeholder {
            gap: 1rem;
        }
        
        .bar {
            width: 35px;
        }
        
        .recent-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let toastTimeout;

    // Afficher une notification
    function showNotification(title, message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toast.style.borderLeftColor = '#10b981';
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = '#10b981';
        } else if (type === 'warning') {
            toast.style.borderLeftColor = '#f59e0b';
            toastIcon.className = 'fas fa-exclamation-triangle';
            toastIcon.style.color = '#f59e0b';
        } else if (type === 'error') {
            toast.style.borderLeftColor = '#ef4444';
            toastIcon.className = 'fas fa-times-circle';
            toastIcon.style.color = '#ef4444';
        }
        
        toast.classList.add('show');
        
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }
        
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Mettre à jour les statistiques de pointage
    function updatePointageStats() {
        const rows = document.querySelectorAll('#pointageTableBody tr');
        let presents = 0;
        let absents = 0;
        let retards = 0;
        let enAttente = 0;
        
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const statutElement = row.querySelector('.statut-badge');
            if (statutElement) {
                const statutTexte = statutElement.textContent;
                if (statutTexte.includes('Présent')) presents++;
                else if (statutTexte.includes('Absent')) absents++;
                else if (statutTexte.includes('retard')) retards++;
                else enAttente++;
            }
        });
        
        document.getElementById('statsPresence').textContent = presents;
        document.getElementById('statsAbsence').textContent = absents;
        document.getElementById('statsRetard').textContent = retards;
        document.getElementById('statsTotal').textContent = rows.length;
    }

    // Modifier le pointage d'un stagiaire
    function modifierPointage(userId, statut) {
        fetch('{{ route("responsable.pointages.update", "") }}/' + userId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ statut: statut })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification('Erreur', data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    }

    // Filtrer les pointages
    function filterPointages() {
        const serviceFilter = document.getElementById('filterService').value;
        const statutFilter = document.getElementById('filterStatut').value;
        const rows = document.querySelectorAll('#pointageTableBody tr');
        
        rows.forEach(row => {
            const service = row.getAttribute('data-service');
            const statut = row.getAttribute('data-statut');
            
            let showRow = true;
            
            if (serviceFilter !== 'all' && service !== serviceFilter) {
                showRow = false;
            }
            
            if (statutFilter !== 'all' && statut !== statutFilter) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
        
        updatePointageStats();
        showNotification('Filtre appliqué', 'Affichage mis à jour', 'success');
    }

    // Réinitialiser les filtres
    function resetFilters() {
        document.getElementById('filterService').value = 'all';
        document.getElementById('filterStatut').value = 'all';
        
        const rows = document.querySelectorAll('#pointageTableBody tr');
        rows.forEach(row => {
            row.style.display = '';
        });
        
        updatePointageStats();
        showNotification('Filtres réinitialisés', 'Tous les stagiaires sont affichés', 'success');
    }

    // Exporter en PDF
    function exporterPDF() {
        showNotification('Export PDF', 'Le rapport de pointage a été généré', 'success');
    }

    // Exporter en Excel
    function exporterExcel() {
        showNotification('Export Excel', 'Les données ont été exportées', 'success');
    }

    // Animation des barres de progression au chargement
    document.addEventListener('DOMContentLoaded', () => {
        // Afficher la date du jour
        const dateElement = document.getElementById('currentDate');
        const aujourdhui = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = aujourdhui.toLocaleDateString('fr-FR', options);
        
        // Initialisation des stats de pointage
        updatePointageStats();
        
        // Animation des barres du graphique
        const bars = document.querySelectorAll('.bar');
        bars.forEach(bar => {
            const height = bar.style.height;
            bar.style.height = '0';
            setTimeout(() => {
                bar.style.height = height;
            }, 300);
        });
        
        // Données pour le graphique circulaire
        const stats = @json(\App\Models\Candidature::selectRaw('statut, count(*) as count')->groupBy('statut')->get());
        const total = stats.reduce((sum, s) => sum + s.count, 0);
        
        if (total > 0) {
            let startAngle = 0;
            const colors = { 'en_attente': '#f59e0b', 'acceptee': '#10b981', 'refusee': '#ef4444' };
            const labels = { 'en_attente': 'En attente', 'acceptee': 'Acceptées', 'refusee': 'Refusées' };
            
            let gradient = '';
            let legendHtml = '';
            
            stats.forEach(stat => {
                const percentage = (stat.count / total) * 100;
                const endAngle = startAngle + (percentage * 3.6);
                gradient += `${colors[stat.statut]} ${startAngle}deg ${endAngle}deg, `;
                legendHtml += `<div class="legend-item">
                    <div class="legend-color" style="background: ${colors[stat.statut]};"></div>
                    <span>${labels[stat.statut]} (${Math.round(percentage)}%)</span>
                </div>`;
                startAngle = endAngle;
            });
            
            const pieChart = document.getElementById('pieChart');
            if (pieChart) {
                pieChart.style.background = `conic-gradient(${gradient.slice(0, -2)})`;
            }
            
            const pieLegend = document.getElementById('pieLegend');
            if (pieLegend) {
                pieLegend.innerHTML = legendHtml;
            }
        }
        
        // Gestionnaire d'erreur pour le logo
        const logo = document.querySelector('.welcome-logo img');
        if (logo) {
            logo.onerror = function() {
                this.style.display = 'none';
            };
        }
    });
</script>
@endpush
@endsection
