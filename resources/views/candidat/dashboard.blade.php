@extends('layouts.candidat')

@section('title', 'Tableau de bord - Candidat')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'">
    </div>
    <div class="welcome-text">
        <h1 class="welcome-title">Bon retour, <span>{{ auth()->user()->first_name ?? 'Candidat' }}</span> 👋</h1>
        <p class="welcome-subtitle">Gérez votre progression et suivez vos activités</p>
        @php
            $tuteur = \App\Models\User::find(auth()->user()->tuteur_id);
        @endphp
        @if($tuteur)
            <div class="tuteur-info">
                <i class="fas fa-chalkboard-teacher"></i>
                Votre encadrant : <strong>{{ $tuteur->first_name }} {{ $tuteur->last_name }}</strong>
                <span class="badge-tuteur">{{ $tuteur->email }}</span>
            </div>
        @endif
    </div>
</div>

@php
    use Carbon\Carbon;
    
    // ========== STATISTIQUES DES TÂCHES ==========
    $totalTaches = \App\Models\Tache::where('user_id', auth()->id())->count();
    $tachesTerminees = \App\Models\Tache::where('user_id', auth()->id())->where('terminee', true)->count();
    $tachesEnCours = $totalTaches - $tachesTerminees;
    
    // Progression globale basée sur les tâches terminées
    $progressionGlobale = $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : 0;
    
    // Variation de progression
    $semaineDerniere = \App\Models\Tache::where('user_id', auth()->id())
        ->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])
        ->where('terminee', true)
        ->count();
    $semaineAvant = \App\Models\Tache::where('user_id', auth()->id())
        ->whereBetween('created_at', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])
        ->where('terminee', true)
        ->count();
    
    if ($semaineAvant > 0) {
        $progressionVariation = round((($semaineDerniere - $semaineAvant) / $semaineAvant) * 100);
    } elseif ($semaineDerniere > 0) {
        $progressionVariation = 100;
    } else {
        $progressionVariation = 0;
    }
    
    // ========== STATISTIQUES DES PRÉSENCES ==========
    $presences = \App\Models\Presence::where('user_id', auth()->id())
        ->where('date', '>=', Carbon::now()->subDays(30))
        ->get();
    
    $totalPresences = $presences->count();
    $presencesPresent = $presences->where('est_present', true)->count();
    $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100) : 0;
    
    $presencesSemaine = \App\Models\Presence::where('user_id', auth()->id())
        ->whereBetween('date', [Carbon::now()->subWeek(), Carbon::now()])
        ->where('est_present', true)
        ->count();
    $presencesSemaineAvant = \App\Models\Presence::where('user_id', auth()->id())
        ->whereBetween('date', [Carbon::now()->subWeeks(2), Carbon::now()->subWeek()])
        ->where('est_present', true)
        ->count();
    
    if ($presencesSemaineAvant > 0) {
        $presenceVariation = round((($presencesSemaine - $presencesSemaineAvant) / $presencesSemaineAvant) * 100);
    } elseif ($presencesSemaine > 0) {
        $presenceVariation = 100;
    } else {
        $presenceVariation = 0;
    }
    
    // ========== DOCUMENTS ==========
    $documentsCount = \App\Models\Document::where('user_id', auth()->id())->count();
    $documentsNouveaux = \App\Models\Document::where('user_id', auth()->id())
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->count();
    
    // ========== STAGE ==========
    $stage = \App\Models\Stage::where('candidat_id', auth()->id())
        ->where('statut', 'en_cours')
        ->first();
    
    $joursRestants = 0;
    $dureeStage = 0;
    $joursEffectues = 0;
    $progressionStage = 0;
    
    if ($stage && $stage->date_debut && $stage->date_fin) {
        $dateDebut = Carbon::parse($stage->date_debut);
        $dateFin = Carbon::parse($stage->date_fin);
        $aujourdhui = Carbon::now();
        
        $dureeStage = $dateDebut->diffInDays($dateFin);
        $joursEffectues = $dateDebut->diffInDays(min($aujourdhui, $dateFin));
        $joursRestants = max(0, $dateFin->diffInDays($aujourdhui));
        $progressionStage = $dureeStage > 0 ? round(($joursEffectues / $dureeStage) * 100) : 0;
        
        $progressionGlobale = round(($progressionStage * 0.6) + ($progressionGlobale * 0.4));
    }
    
    // ========== OBJECTIF MESSAGE ==========
    if ($progressionGlobale < 30) {
        $objectifMessage = 'Commencez à travailler sur vos tâches et à pointer vos présences pour progresser !';
    } elseif ($progressionGlobale < 70) {
        $objectifMessage = 'Bonne progression ! Continuez vos efforts pour atteindre vos objectifs.';
    } else {
        $objectifMessage = 'Excellent travail ! Vous êtes presque à la fin de votre stage.';
    }
    
    // ========== COMPÉTENCES ==========
    $competences = [
        ['nom' => 'Compétences techniques', 'valeur' => $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : rand(30, 70)],
        ['nom' => 'Autonomie', 'valeur' => $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : rand(40, 80)],
        ['nom' => 'Communication', 'valeur' => $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : rand(35, 75)],
        ['nom' => 'Gestion de projet', 'valeur' => $totalTaches > 0 ? round(($tachesTerminees / $totalTaches) * 100) : rand(25, 65)],
    ];
    
    // ========== TÂCHES RÉCENTES ==========
    $taches = \App\Models\Tache::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
@endphp

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Présences</span>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $tauxPresence }}%</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $presenceVariation }}%
            </span>
        </div>
        <div class="stat-detail">
            <small>{{ $presencesPresent }}/{{ $totalPresences }} jours</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Documents</span>
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
        <div class="stat-value">{{ $documentsCount }}</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $documentsNouveaux }}
            </span>
        </div>
        <div class="stat-detail">
            <small>dernière semaine</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Tâches</span>
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalTaches }}</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-check"></i> {{ $tachesTerminees }} terminées
            </span>
        </div>
        <div class="stat-detail">
            <small>{{ $tachesEnCours }} en cours</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Progression</span>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-value">{{ $progressionGlobale }}%</div>
        <div class="stat-trend">
            <span class="trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $progressionVariation }}%
            </span>
        </div>
        <div class="stat-detail">
            <small>{{ $tachesTerminees }}/{{ $totalTaches }} tâches</small>
        </div>
    </div>
</div>

<!-- Section Progression et Tâches -->
<div class="dashboard-grid">
    <!-- Section Progression -->
    <div class="progress-section">
        <div class="section-header">
            <h2>
                <i class="fas fa-chart-line"></i>
                Progression globale
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
                <h3>Objectif de stage</h3>
                <p>{{ $objectifMessage }}</p>
                <div class="progress-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $joursRestants }} jours restants</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-chart-simple"></i>
                        <span>{{ $joursEffectues }}/{{ $dureeStage }} jours</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $tachesTerminees }}/{{ $totalTaches }} tâches</span>
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
        
        <!-- Évolution des présences -->
        <div class="presence-evolution">
            <h4><i class="fas fa-chart-line"></i> Évolution des présences (30 jours)</h4>
            <div class="presence-bars">
                @php
                    $presencesParJour = \App\Models\Presence::where('user_id', auth()->id())
                        ->where('date', '>=', Carbon::now()->subDays(30))
                        ->orderBy('date')
                        ->get()
                        ->groupBy(function($item) {
                            return Carbon::parse($item->date)->format('d/m');
                        });
                @endphp
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

    <!-- Section Tâches -->
    <div class="tasks-section">
        <div class="tasks-header">
            <h2>
                <i class="fas fa-tasks"></i>
                Tâches à accomplir
            </h2>
            <span class="task-count">{{ $tachesEnCours }}</span>
        </div>

        <ul class="tasks-list" id="tasksList">
            @forelse($taches as $tache)
            <li class="task-item {{ $tache->terminee ? 'completed' : '' }}" data-id="{{ $tache->id }}">
                <div class="task-checkbox {{ $tache->terminee ? 'completed' : '' }}" 
                     onclick="toggleTask({{ $tache->id }}, this)">
                    <i class="fas fa-check"></i>
                </div>
                <div class="task-content">
                    <div class="task-title">{{ $tache->titre }}</div>
                    <div class="task-meta">
                        <span class="task-priority priority-{{ $tache->priorite ?? 'medium' }}">
                            {{ $tache->priorite == 'high' ? 'Haute' : ($tache->priorite == 'low' ? 'Basse' : 'Moyenne') }}
                        </span>
                        <span class="task-deadline">
                            <i class="far fa-clock"></i> 
                            @if($tache->echeance)
                                À faire avant {{ Carbon::parse($tache->echeance)->format('d/m/Y') }}
                            @else
                                Créée {{ $tache->created_at->diffForHumans() }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="task-actions">
                    @if(!$tache->terminee)
                        <button class="task-btn" onclick="editTask({{ $tache->id }})" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                    @endif
                    <button class="task-btn" onclick="deleteTask({{ $tache->id }})" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </li>
            @empty
            <li class="task-item empty-task">
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Aucune tâche pour le moment</p>
                    <small>Ajoutez une tâche ci-dessous</small>
                </div>
            </li>
            @endforelse
        </ul>

        <div class="add-task">
            <input type="text" id="nouvelleTache" placeholder="Ajouter une nouvelle tâche..." onkeypress="handleKeyPress(event)">
            <button onclick="ajouterTache()"><i class="fas fa-plus"></i></button>
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
    
    .tuteur-info {
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
    
    .tuteur-info i {
        color: var(--bleu);
    }
    
    .badge-tuteur {
        display: inline-block;
        margin-left: 0.5rem;
        padding: 0.2rem 0.6rem;
        background: var(--gris-clair);
        border-radius: 20px;
        font-size: 0.75rem;
        color: var(--gris-fonce);
    }
    
    .task-priority {
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .priority-high {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }
    
    .priority-medium {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .priority-low {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
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
    
    .welcome-logo img {
        height: 80px;
        width: auto;
        transition: transform 0.3s ease;
    }
    
    .welcome-logo img:hover {
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
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .progress-section {
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
    
    .empty-presence {
        text-align: center;
        padding: 1rem;
        color: var(--gris);
    }
    
    .empty-presence i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .tasks-section {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
    }
    
    .tasks-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .tasks-header h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .tasks-header h2 i {
        color: var(--bleu);
    }
    
    .task-count {
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        color: white;
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    
    .tasks-list {
        list-style: none;
        margin-bottom: 1rem;
        max-height: 350px;
        overflow-y: auto;
    }
    
    .task-item {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        padding: 1rem;
        border-bottom: 1px solid var(--gris-clair);
        transition: background 0.2s ease;
    }
    
    .task-item:hover {
        background: var(--gris-clair);
        border-radius: 12px;
    }
    
    .task-item.completed .task-title {
        text-decoration: line-through;
        color: var(--gris);
    }
    
    .task-checkbox {
        width: 22px;
        height: 22px;
        border: 2px solid var(--gris);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
        background: white;
    }
    
    .task-checkbox.completed {
        background: var(--vert);
        border-color: var(--vert);
    }
    
    .task-checkbox i {
        font-size: 0.7rem;
        color: white;
    }
    
    .task-content {
        flex: 1;
    }
    
    .task-title {
        font-weight: 600;
        color: var(--noir);
        margin-bottom: 0.3rem;
    }
    
    .task-meta {
        display: flex;
        gap: 0.8rem;
        font-size: 0.7rem;
        flex-wrap: wrap;
    }
    
    .task-deadline {
        color: var(--gris);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .task-actions {
        display: flex;
        gap: 0.3rem;
    }
    
    .task-btn {
        background: none;
        border: none;
        color: var(--gris);
        cursor: pointer;
        padding: 5px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .task-btn:hover {
        background: var(--rouge);
        color: white;
    }
    
    .empty-task {
        justify-content: center;
        padding: 2rem;
    }
    
    .empty-state {
        text-align: center;
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
        font-size: 0.75rem;
    }
    
    .add-task {
        display: flex;
        gap: 0.8rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--gris-clair);
    }
    
    .add-task input {
        flex: 1;
        padding: 0.8rem 1rem;
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .add-task input:focus {
        border-color: var(--bleu);
    }
    
    .add-task button {
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--bleu), var(--vert-light));
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .add-task button:hover {
        transform: scale(1.05);
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
    
    .tasks-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .tasks-list::-webkit-scrollbar-track {
        background: var(--gris-clair);
        border-radius: 10px;
    }
    
    .tasks-list::-webkit-scrollbar-thumb {
        background: var(--bleu);
        border-radius: 10px;
    }
    
    .presence-bars::-webkit-scrollbar {
        height: 4px;
    }
    
    .presence-bars::-webkit-scrollbar-track {
        background: var(--gris-clair);
        border-radius: 10px;
    }
    
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
        .dashboard-grid {
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
        
        .tuteur-info {
            justify-content: center;
        }
        
        .presence-bar-container {
            min-width: 35px;
        }
        
        .presence-bar {
            width: 25px;
        }
    }
    
    @media (max-width: 480px) {
        .stat-value {
            font-size: 1.8rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .welcome-logo img {
            height: 60px;
        }
        
        .task-meta {
            flex-direction: column;
            gap: 0.3rem;
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

    function toggleTask(id, element) {
        const checkbox = element;
        
        fetch(`/candidat/taches/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const taskItem = checkbox.closest('.task-item');
                taskItem.classList.toggle('completed');
                checkbox.classList.toggle('completed');
                showNotification('Succès', data.message || 'Tâche mise à jour', 'success');
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

    function editTask(id) {
        window.location.href = `/candidat/taches/${id}/edit`;
    }

    function deleteTask(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
            fetch(`/candidat/taches/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message || 'Tâche supprimée', 'success');
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

    function ajouterTache() {
        const input = document.getElementById('nouvelleTache');
        const titre = input.value.trim();
        
        if (!titre) {
            showNotification('Erreur', 'Veuillez entrer une tâche', 'error');
            return;
        }

        fetch('{{ route("candidat.taches.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ titre: titre })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                showNotification('Succès', data.message || 'Tâche ajoutée', 'success');
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

    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            ajouterTache();
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
