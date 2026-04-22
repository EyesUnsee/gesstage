@extends('layouts.chef-service')

@section('title', 'Tableau de bord - Chef de service')
@section('page-title', 'Tableau de bord')
@section('active-dashboard', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    // Récupération des données depuis le contrôleur
    $stagiairesActifs = $stagiairesActifs ?? 0;
    $validationsEnAttente = $validationsEnAttente ?? 0;
    $bilansEnAttente = $bilansEnAttente ?? 0;
    $tauxPresence = $tauxPresence ?? 0;
    $evolutionStagiaires = $evolutionStagiaires ?? 0;
    $evolutionValidations = $evolutionValidations ?? 0;
    $evolutionPresence = $evolutionPresence ?? 5;
    $evolutionBilans = $evolutionBilans ?? 2;
    $activitesRecentes = $activitesRecentes ?? collect();
    $validationsUrgentes = $validationsUrgentes ?? collect();
    $services = $services ?? collect();
    $indicateursPerformance = $indicateursPerformance ?? [];
    
    // Progression globale du service
    $progressionGlobale = $stagiairesActifs > 0 ? min(100, round(($validationsEnAttente > 0 ? 70 : 85) + ($tauxPresence / 10))) : 0;
    
    // Message d'objectif
    if ($progressionGlobale < 30) {
        $objectifMessage = 'Le service a besoin d\'être dynamisé. Augmentez les validations et le suivi des stagiaires.';
    } elseif ($progressionGlobale < 70) {
        $objectifMessage = 'Bonne progression ! Continuez à valider les bilans et à suivre les présences.';
    } else {
        $objectifMessage = 'Excellent travail ! Votre service atteint ses objectifs. Continuez sur cette lancée.';
    }
    
    // Compétences/Indicateurs du service
    $competences = $indicateursPerformance ?: [
        ['nom' => 'Gestion d\'équipe', 'valeur' => 75],
        ['nom' => 'Suivi des stages', 'valeur' => 68],
        ['nom' => 'Validation des bilans', 'valeur' => 52],
        ['nom' => 'Organisation', 'valeur' => 80],
    ];
    
    // Évolution des présences par jour (pour le graphique)
    $presencesParJour = \App\Models\Presence::where('date', '>=', Carbon::now()->subDays(30))
        ->when(auth()->user()->service_id, function($query) {
            $query->whereHas('user', function($q) {
                $q->where('service_id', auth()->user()->service_id);
            });
        })
        ->orderBy('date')
        ->get()
        ->groupBy(function($item) {
            return Carbon::parse($item->date)->format('d/m');
        });
@endphp

<!-- Section de bienvenue -->
<div class="welcome-section">
    <div class="welcome-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'">
    </div>
    <div class="welcome-text">
        <h1 class="welcome-title">Bonjour, <span>{{ auth()->user()->first_name ?? 'Chef' }}</span> 👋</h1>
        <p class="welcome-subtitle">Voici un résumé de l'activité de votre service</p>
        @if(isset($serviceName))
            <div class="service-info">
                <i class="fas fa-building"></i>
                Service : <strong>{{ $serviceName }}</strong>
                <span class="badge-service">{{ $stagiairesActifs }} stagiaires actifs</span>
            </div>
        @endif
    </div>
</div>

<!-- Statistiques principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stagiaires actifs</span>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stagiairesActifs }}</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $evolutionStagiaires }}%
            </span>
        </div>
        <div class="stat-detail">
            <small>vs mois dernier</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Validations en attente</span>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $validationsEnAttente }}</div>
        <div class="stat-trend">
            <span class="trend-{{ $evolutionValidations >= 0 ? 'up' : 'down' }}">
                <i class="fas fa-arrow-{{ $evolutionValidations >= 0 ? 'up' : 'down' }}"></i> {{ abs($evolutionValidations) }}
            </span>
        </div>
        <div class="stat-detail">
            <small>vs hier</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Taux de présence</span>
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ $tauxPresence }}%</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $evolutionPresence }}%
            </span>
        </div>
        <div class="stat-detail">
            <small>ce mois</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Bilans à valider</span>
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
        <div class="stat-value">{{ $bilansEnAttente }}</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $evolutionBilans }}
            </span>
        </div>
        <div class="stat-detail">
            <small>cette semaine</small>
        </div>
    </div>
</div>

<!-- Section Progression et Validations -->
<div class="dashboard-grid">
    <!-- Section Progression globale -->
    <div class="progress-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-chart-line"></i>
                Progression du service
            </h2>
            <span class="progress-badge">{{ $progressionGlobale }}%</span>
        </div>

        <svg width="0" height="0">
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#4361ee" />
                    <stop offset="100%" stop-color="#06ffa5" />
                </linearGradient>
            </defs>
        </svg>

        <div class="progress-overall">
            <div class="progress-circle">
                <svg width="120" height="120">
                    <circle class="circle-bg" cx="60" cy="60" r="54" stroke="#f1f5f9" stroke-width="8" fill="none"/>
                    <circle class="circle-progress" cx="60" cy="60" r="54" 
                            stroke="url(#gradient)" stroke-width="8" fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="339.292" 
                            stroke-dashoffset="{{ 339.292 - (339.292 * $progressionGlobale / 100) }}"></circle>
                </svg>
                <div class="circle-text">
                    <div class="number">{{ $progressionGlobale }}%</div>
                    <div class="label">global</div>
                </div>
            </div>
            <div class="progress-stats">
                <h3>Objectif du service</h3>
                <p>{{ $objectifMessage }}</p>
                <div class="progress-meta">
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span>{{ $stagiairesActifs }} stagiaires</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-check-double"></i>
                        <span>{{ $validationsEnAttente }} validations</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{ $tauxPresence }}% présence</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="progress-items">
            @foreach($competences as $competence)
            <div class="progress-item">
                <div class="progress-label">
                    <span>{{ $competence['nom'] }}</span>
                    <span>{{ $competence['valeur'] }}%</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $competence['valeur'] }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Évolution des présences du service -->
        <div class="presence-evolution">
            <h4><i class="fas fa-chart-line"></i> Présences (30 derniers jours)</h4>
            <div class="presence-bars">
                @forelse($presencesParJour as $jour => $presencesJour)
                    @php
                        $estPresent = $presencesJour->first()->est_present ?? false;
                        $barHeight = $estPresent ? 40 : 20;
                        $barColor = $estPresent ? '#10b981' : '#ef4444';
                    @endphp
                    <div class="presence-bar-container" title="{{ $jour }} - {{ $estPresent ? 'Présent' : 'Absent' }}">
                        <div class="presence-bar" style="height: {{ $barHeight }}px; background: {{ $barColor }};"></div>
                        <span class="presence-label">{{ $jour }}</span>
                    </div>
                @empty
                    <div class="empty-presence">
                        <i class="fas fa-chart-line"></i>
                        <p>Aucune donnée de présence disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section Validations urgentes -->
    <div class="validations-section">
        <div class="validations-header">
            <h2>
                <i class="fas fa-check-double"></i>
                Validations urgentes
            </h2>
            <span class="pending-count">{{ $validationsUrgentes->count() }}</span>
        </div>

        <ul class="validations-list">
            @forelse($validationsUrgentes as $validation)
            <li class="validation-item" data-id="{{ $validation->id }}">
                <div class="validation-avatar">
                    <i class="fas {{ $validation->icone ?? 'fa-file-alt' }}"></i>
                </div>
                <div class="validation-content">
                    <div class="validation-title">{{ $validation->titre }}</div>
                    <div class="validation-meta">
                        <span><i class="fas fa-user"></i> {{ $validation->user_nom ?? 'Stagiaire' }}</span>
                        <span><i class="fas fa-clock"></i> {{ $validation->urgence ?? 'En attente' }}</span>
                    </div>
                </div>
                <div class="validation-action">
                    <button class="validation-btn" onclick="approuverValidation({{ $validation->id }})" title="Approuver">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="validation-btn reject" onclick="refuserValidation({{ $validation->id }})" title="Refuser">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </li>
            @empty
            <li class="validation-item empty-validation">
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Aucune validation urgente</p>
                    <small>Toutes les validations sont à jour</small>
                </div>
            </li>
            @endforelse
        </ul>

        <div class="view-all" onclick="window.location.href='{{ route('chef-service.validations') }}'">
            Voir toutes les validations <i class="fas fa-arrow-right"></i>
        </div>
    </div>
</div>

<!-- Section Activités et Services -->
<div class="bottom-grid">
    <!-- Activité récente -->
    <div class="activity-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-history"></i>
                Activité récente
            </h2>
            <span class="view-all" onclick="window.location.href='{{ route('chef-service.activites') }}'">
                Voir tout <i class="fas fa-arrow-right"></i>
            </span>
        </div>

        <ul class="activity-list">
            @forelse($activitesRecentes as $activite)
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas {{ $activite->icone ?? 'fa-file-alt' }}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">{{ $activite->titre }}</div>
                    <div class="activity-meta">
                        <span><i class="fas fa-user"></i> {{ $activite->user_nom ?? 'Utilisateur' }}</span>
                        <span><i class="fas fa-clock"></i> {{ Carbon::parse($activite->created_at)->diffForHumans() }}</span>
                        <span class="activity-badge badge-{{ $activite->type ?? 'info' }}">{{ $activite->statut ?? 'Nouveau' }}</span>
                    </div>
                </div>
            </li>
            @empty
            <li class="activity-item empty-activity">
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>Aucune activité récente</p>
                    <small>Les nouvelles activités apparaîtront ici</small>
                </div>
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Services -->
    <div class="services-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-building"></i>
                Services
            </h2>
            <span class="view-all" onclick="window.location.href='{{ route('chef-service.services') }}'">
                Gérer <i class="fas fa-arrow-right"></i>
            </span>
        </div>

        <div class="services-grid">
            @forelse($services as $service)
            <div class="service-card" onclick="window.location.href='{{ route('chef-service.services.show', $service->id) }}'">
                <div class="service-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="service-name">{{ $service->nom }}</div>
                <div class="service-count">{{ $service->stagiaires_count ?? 0 }} stagiaires</div>
            </div>
            @empty
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="service-name">Aucun service</div>
                <div class="service-count">0 stagiaires</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Notification toast -->
<div id="notificationToast" class="notification-toast">
    <div class="toast-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="toast-content">
        <div class="toast-title">Succès</div>
        <div class="toast-message">Action effectuée avec succès</div>
    </div>
</div>

<style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --bleu: #4361ee;
        --vert: #10b981;
        --vert-light: #06ffa5;
        --rouge: #ef476f;
        --noir: #2b2d42;
        --gris: #6c757d;
        --gris-clair: #f8f9fa;
        --gris-fonce: #495057;
        --blanc: #ffffff;
        --shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
    }

    .stat-detail {
        margin-top: 0.5rem;
        font-size: 0.7rem;
        color: var(--gris);
    }

    .service-info {
        margin-top: 1rem;
        padding: 0.8rem 1rem;
        background: rgba(67, 97, 238, 0.1);
        border-radius: 12px;
        border-left: 4px solid var(--bleu);
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .service-info i {
        color: var(--bleu);
    }

    .badge-service {
        display: inline-block;
        margin-left: 0.5rem;
        padding: 0.2rem 0.6rem;
        background: var(--gris-clair);
        border-radius: 20px;
        font-size: 0.75rem;
        color: var(--gris-fonce);
    }

    .welcome-section {
        display: flex;
        align-items: center;
        gap: 2rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        padding: 2rem;
        border-radius: 24px;
        margin-bottom: 2rem;
        color: white;
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
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-10px, -10px); }
    }

    .welcome-logo img, .welcome-icon-placeholder {
        height: 80px;
        width: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .welcome-icon-placeholder i {
        font-size: 2.5rem;
        color: white;
    }

    .welcome-logo img:hover, .welcome-icon-placeholder:hover {
        transform: scale(1.05) rotate(5deg);
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .welcome-title span {
        color: #fcd34d;
    }

    .welcome-subtitle {
        opacity: 0.9;
        margin-top: 0.5rem;
        margin-bottom: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        transition: all 0.3s;
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
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        opacity: 0.05;
        border-radius: 0 0 0 80px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: var(--shadow);
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
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }

    .stat-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        font-size: 0.85rem;
        color: var(--gris);
    }

    .trend-up {
        color: var(--vert);
    }

    .trend-down {
        color: var(--rouge);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .progress-section, .validations-section, .activity-section, .services-section {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-header h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header h2 i {
        color: var(--bleu);
    }

    .view-all {
        color: var(--bleu);
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
    }

    .view-all:hover {
        color: var(--vert);
        gap: 0.5rem;
    }

    .progress-badge {
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        color: white;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .progress-overall {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid var(--gris-clair);
        flex-wrap: wrap;
    }

    .progress-circle {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .circle-text .number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--bleu);
        line-height: 1;
    }

    .circle-text .label {
        font-size: 0.7rem;
        color: var(--gris);
    }

    .progress-stats {
        flex: 1;
    }

    .progress-stats h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .progress-stats p {
        color: var(--gris);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .progress-meta {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--gris-fonce);
    }

    .meta-item i {
        color: var(--bleu);
    }

    .progress-items {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .progress-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: var(--gris-fonce);
    }

    .progress-bar-bg {
        height: 8px;
        background: var(--gris-clair);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert-light));
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .presence-evolution {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--gris-clair);
    }

    .presence-evolution h4 {
        font-size: 0.9rem;
        margin-bottom: 1rem;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .presence-evolution h4 i {
        color: var(--bleu);
    }

    .presence-bars {
        display: flex;
        gap: 0.3rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .presence-bar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 40px;
    }

    .presence-bar {
        width: 30px;
        border-radius: 4px 4px 0 0;
        transition: height 0.3s ease;
        margin-bottom: 0.3rem;
    }

    .presence-label {
        font-size: 0.6rem;
        color: var(--gris);
        transform: rotate(-45deg);
        white-space: nowrap;
    }

    .validations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .pending-count {
        background: var(--rouge);
        color: white;
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .validations-list, .activity-list {
        list-style: none;
        margin-bottom: 1rem;
        max-height: 350px;
        overflow-y: auto;
    }

    .validation-item, .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        padding: 1rem;
        border-bottom: 1px solid var(--gris-clair);
        transition: background 0.2s ease;
    }

    .validation-item:hover, .activity-item:hover {
        background: var(--gris-clair);
        border-radius: 12px;
    }

    .validation-avatar, .activity-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .validation-avatar {
        background: linear-gradient(135deg, var(--rouge), #fca5a5);
    }

    .activity-icon {
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
    }

    .validation-content, .activity-content {
        flex: 1;
    }

    .validation-title, .activity-title {
        font-weight: 600;
        color: var(--noir);
        margin-bottom: 0.3rem;
    }

    .validation-meta, .activity-meta {
        display: flex;
        gap: 0.8rem;
        font-size: 0.7rem;
        flex-wrap: wrap;
        color: var(--gris);
    }

    .validation-meta i, .activity-meta i {
        margin-right: 0.2rem;
    }

    .validation-action {
        display: flex;
        gap: 0.3rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .validation-item:hover .validation-action {
        opacity: 1;
    }

    .validation-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: white;
        border: 2px solid var(--gris-clair);
        color: var(--gris);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .validation-btn:hover {
        background: var(--vert);
        border-color: var(--vert);
        color: white;
        transform: scale(1.1);
    }

    .validation-btn.reject:hover {
        background: var(--rouge);
        border-color: var(--rouge);
    }

    .activity-badge {
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .badge-info {
        background: rgba(67, 97, 238, 0.1);
        color: var(--bleu);
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .service-card {
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .service-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
        box-shadow: 0 10px 20px -10px var(--bleu);
    }

    .service-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin: 0 auto 0.5rem;
    }

    .service-name {
        font-weight: 700;
        color: var(--noir);
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
    }

    .service-count {
        color: var(--gris);
        font-size: 0.7rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--gris);
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--vert);
    }

    .empty-state p {
        margin-bottom: 0.3rem;
    }

    .empty-state small {
        font-size: 0.7rem;
    }

    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        border-left: 4px solid var(--vert);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 2000;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .notification-toast.show {
        transform: translateX(0);
    }

    .toast-title {
        font-weight: 700;
        color: var(--noir);
    }

    .toast-message {
        color: var(--gris);
        font-size: 0.85rem;
    }

    /* Scrollbars */
    .validations-list::-webkit-scrollbar,
    .activity-list::-webkit-scrollbar,
    .presence-bars::-webkit-scrollbar {
        width: 6px;
        height: 4px;
    }

    .validations-list::-webkit-scrollbar-track,
    .activity-list::-webkit-scrollbar-track,
    .presence-bars::-webkit-scrollbar-track {
        background: var(--gris-clair);
        border-radius: 10px;
    }

    .validations-list::-webkit-scrollbar-thumb,
    .activity-list::-webkit-scrollbar-thumb,
    .presence-bars::-webkit-scrollbar-thumb {
        background: var(--bleu);
        border-radius: 10px;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .dashboard-grid, .bottom-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .welcome-section {
            flex-direction: column;
            text-align: center;
        }

        .progress-overall {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .progress-meta {
            justify-content: center;
        }

        .service-info {
            justify-content: center;
        }

        .presence-bar-container {
            min-width: 35px;
        }

        .presence-bar {
            width: 25px;
        }

        .validation-action {
            opacity: 1;
        }

        .services-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stat-value {
            font-size: 1.8rem;
        }

        .welcome-title {
            font-size: 1.5rem;
        }

        .welcome-logo img, .welcome-icon-placeholder {
            height: 60px;
            width: 60px;
        }

        .presence-bar-container {
            min-width: 30px;
        }

        .presence-bar {
            width: 20px;
        }
    }
</style>

<script>
    let toastTimeout;

    function approuverValidation(id) {
        if (confirm('Approuver cette validation ?')) {
            fetch(`/chef-service/validations/${id}/approuver`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message || 'Validation approuvée', 'success');
                    const item = document.querySelector(`.validation-item[data-id="${id}"]`);
                    if (item) item.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showNotification('Erreur', data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }

    function refuserValidation(id) {
        if (confirm('Refuser cette validation ?')) {
            fetch(`/chef-service/validations/${id}/refuser`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message || 'Validation refusée', 'warning');
                    const item = document.querySelector(`.validation-item[data-id="${id}"]`);
                    if (item) item.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showNotification('Erreur', data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }

    function showNotification(title, message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toast.style.borderLeftColor = 'var(--vert)';
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = 'var(--vert)';
        } else if (type === 'warning') {
            toast.style.borderLeftColor = '#f59e0b';
            toastIcon.className = 'fas fa-exclamation-triangle';
            toastIcon.style.color = '#f59e0b';
        } else if (type === 'error') {
            toast.style.borderLeftColor = 'var(--rouge)';
            toastIcon.className = 'fas fa-times-circle';
            toastIcon.style.color = 'var(--rouge)';
        }
        
        toast.classList.add('show');
        
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }
        
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.progress-bar-fill');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 200);
        });
        
        const presenceBars = document.querySelectorAll('.presence-bar');
        presenceBars.forEach(bar => {
            const height = bar.style.height;
            bar.style.height = '0';
            setTimeout(() => {
                bar.style.height = height;
            }, 300);
        });
    });
</script>
@endsection
