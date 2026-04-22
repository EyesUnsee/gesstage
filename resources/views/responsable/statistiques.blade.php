@extends('layouts.responsable')

@section('title', 'Statistiques - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Statistiques <span>détaillées</span></h1>
        <p class="welcome-subtitle">Analysez les performances et l'activité de la plateforme</p>
    </div>
    <button class="btn-export" onclick="exportRapport()">
        <i class="fas fa-download"></i> Exporter
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Période selector -->
<div class="period-selector">
    <div class="period-tabs">
        <button class="period-tab {{ ($periode ?? 'month') == 'week' ? 'active' : '' }}" onclick="changePeriode('week')">Semaine</button>
        <button class="period-tab {{ ($periode ?? 'month') == 'month' ? 'active' : '' }}" onclick="changePeriode('month')">Mois</button>
        <button class="period-tab {{ ($periode ?? 'month') == 'quarter' ? 'active' : '' }}" onclick="changePeriode('quarter')">Trimestre</button>
        <button class="period-tab {{ ($periode ?? 'month') == 'year' ? 'active' : '' }}" onclick="changePeriode('year')">Année</button>
    </div>
    <div class="custom-date">
        <input type="date" class="date-input" id="date_debut" value="{{ $dateDebut ?? date('Y-m-d', strtotime('-30 days')) }}">
        <span>—</span>
        <input type="date" class="date-input" id="date_fin" value="{{ $dateFin ?? date('Y-m-d') }}">
        <button class="btn-date" onclick="appliquerDates()">OK</button>
    </div>
</div>

<!-- Cartes statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stages en cours</span>
            <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
        </div>
        <div class="stat-value">{{ $stagesEnCours ?? 0 }}</div>
        <div class="stat-trend {{ ($variationStagesEnCours ?? 0) >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ ($variationStagesEnCours ?? 0) >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($variationStagesEnCours ?? 0) }} vs mois dernier
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stages terminés</span>
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="stat-value">{{ $stagesTermines ?? 0 }}</div>
        <div class="stat-trend {{ ($variationStagesTermines ?? 0) >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ ($variationStagesTermines ?? 0) >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($variationStagesTermines ?? 0) }} vs année dernière
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Candidatures</span>
            <div class="stat-icon purple"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="stat-value">{{ $totalCandidatures ?? 0 }}</div>
        <div class="stat-trend {{ ($variationCandidatures ?? 0) >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ ($variationCandidatures ?? 0) >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($variationCandidatures ?? 0) }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Satisfaction</span>
            <div class="stat-icon orange"><i class="fas fa-star"></i></div>
        </div>
        <div class="stat-value">{{ $tauxSatisfaction ?? 0 }}%</div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i> +5%
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="charts-grid">
    <!-- Stages par mois -->
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-bar"></i> Stages par mois ({{ date('Y') }})</h3>
        </div>
        <div class="bar-chart">
            @foreach($stagesParMois ?? [] as $mois => $data)
            <div class="bar-container">
                <div class="bar" style="height: {{ max(($data['courant'] / max($stagesParMoisMax, 1)) * 150, 5) }}px;">
                    <span class="bar-value">{{ $data['courant'] }}</span>
                </div>
                <span class="bar-label">{{ $mois }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Répartition des stagiaires -->
    <div class="chart-card">
        <div class="chart-header">
            <h3><i class="fas fa-chart-pie"></i> Stagiaires par département</h3>
        </div>
        <div class="pie-container">
            @if(count($repartitionDepartements ?? []) > 0)
                <div class="pie-legend">
                    @foreach($repartitionDepartements ?? [] as $dep)
                    <div class="legend-item">
                        <div class="legend-color" style="background: {{ $dep['couleur'] }};"></div>
                        <span><strong>{{ $dep['nom'] }}</strong> - {{ $dep['total'] }} stagiaires ({{ $dep['pourcentage'] }}%)</span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">Aucune donnée disponible</div>
            @endif
        </div>
    </div>
</div>

<!-- Évolution des inscriptions -->
<div class="chart-card full-width">
    <div class="chart-header">
        <h3><i class="fas fa-chart-line"></i> Évolution des inscriptions (6 derniers mois)</h3>
        <div class="legend-group">
            <span class="legend-dot blue"></span> Stagiaires
            <span class="legend-dot green"></span> Tuteurs
        </div>
    </div>
    <div class="line-container">
        <canvas id="evolutionChart" height="250"></canvas>
    </div>
</div>





<style>
    /* Styles généraux */
    .welcome-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .welcome-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--noir);
    }
    
    .welcome-title span {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .welcome-subtitle {
        color: var(--gris);
        margin-top: 0.3rem;
    }
    
    .btn-export {
        padding: 0.7rem 1.5rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59,130,246,0.3);
    }
    
    .alert {
        padding: 0.8rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid var(--vert);
        color: var(--vert);
    }
    
    /* Période selector */
    .period-selector {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        border: 1px solid var(--gris-clair);
    }
    
    .period-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .period-tab {
        padding: 0.5rem 1rem;
        background: var(--gris-clair);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    
    .period-tab:hover {
        background: var(--bleu);
        color: white;
    }
    
    .period-tab.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }
    
    .custom-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .date-input {
        padding: 0.5rem 0.8rem;
        border: 1px solid var(--gris-clair);
        border-radius: 6px;
        font-size: 0.85rem;
    }
    
    .btn-date {
        padding: 0.5rem 1rem;
        background: var(--gris-clair);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-date:hover {
        background: var(--bleu);
        color: white;
    }
    
    /* Cartes stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        border: 1px solid var(--gris-clair);
        transition: all 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
    }
    
    .stat-title {
        color: var(--gris);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .stat-icon.blue { background: linear-gradient(135deg, var(--bleu), #60a5fa); }
    .stat-icon.green { background: linear-gradient(135deg, var(--vert), #34d399); }
    .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
    .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--noir);
        line-height: 1.2;
        margin-bottom: 0.5rem;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
    }
    
    .stat-trend.up {
        color: var(--vert);
        background: rgba(16, 185, 129, 0.1);
    }
    
    .stat-trend.down {
        color: var(--rouge);
        background: rgba(239, 68, 68, 0.1);
    }
    
    /* Graphiques */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        border: 1px solid var(--gris-clair);
    }
    
    .chart-card.full-width {
        grid-column: span 2;
        margin-bottom: 1.5rem;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--gris-clair);
    }
    
    .chart-header h3 {
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .chart-header h3 i {
        color: var(--bleu);
    }
    
    .legend-group {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
    }
    
    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 0.3rem;
    }
    
    .legend-dot.blue { background: var(--bleu); }
    .legend-dot.green { background: var(--vert); }
    
    /* Bar chart */
    .bar-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 180px;
        padding: 0.5rem 0;
    }
    
    .bar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
        width: 40px;
    }
    
    .bar {
        width: 30px;
        background: linear-gradient(180deg, var(--bleu), #93c5fd);
        border-radius: 4px 4px 0 0;
        transition: height 0.5s ease;
        position: relative;
        cursor: pointer;
    }
    
    .bar-value {
        position: absolute;
        top: -22px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        background: var(--noir);
        color: white;
        padding: 0.1rem 0.3rem;
        border-radius: 4px;
        opacity: 0;
        white-space: nowrap;
        transition: opacity 0.2s;
    }
    
    .bar:hover .bar-value {
        opacity: 1;
    }
    
    .bar-label {
        font-size: 0.7rem;
        color: var(--gris);
    }
    
    /* Pie chart simplifié */
    .pie-container {
        padding: 1rem 0;
    }
    
    .pie-legend {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 0.85rem;
        padding: 0.5rem;
        background: var(--gris-clair);
        border-radius: 8px;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }
    
    /* Line chart */
    .line-container {
        position: relative;
        height: 250px;
    }
    
    /* Indicateurs */
    .stats-mini-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .indicator-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        text-align: center;
        border: 1px solid var(--gris-clair);
    }
    
    .indicator-value {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .indicator-label {
        font-size: 0.8rem;
        color: var(--gris);
        margin-bottom: 0.8rem;
    }
    
    .indicator-bar {
        height: 4px;
        background: var(--gris-clair);
        border-radius: 2px;
        overflow: hidden;
    }
    
    .indicator-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert));
        border-radius: 2px;
        transition: width 0.5s ease;
    }
    
    /* Performance */
    .performance-item {
        margin-bottom: 1.2rem;
    }
    
    .performance-header {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .performance-score {
        font-weight: 700;
        color: var(--bleu);
    }
    
    .performance-bar {
        height: 8px;
        background: var(--gris-clair);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .performance-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    
    .performance-stats {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--gris);
    }
    
    .performance-stats i {
        margin-right: 0.3rem;
    }
    
    /* Tableau */
    .table-responsive {
        overflow-x: auto;
    }
    
    .simple-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .simple-table th,
    .simple-table td {
        padding: 0.8rem 0.5rem;
        text-align: left;
        border-bottom: 1px solid var(--gris-clair);
    }
    
    .simple-table th {
        color: var(--gris);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
    }
    
    .simple-table tr:hover td {
        background: var(--gris-clair);
    }
    
    .rank {
        text-align: center;
        font-weight: 600;
    }
    
    .rank i {
        font-size: 1.1rem;
    }
    
    .mini-progress {
        width: 80px;
        height: 4px;
        background: var(--gris-clair);
        border-radius: 2px;
        overflow: hidden;
        display: inline-block;
    }
    
    .mini-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--vert), #34d399);
        border-radius: 2px;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--gris);
    }
    
    .text-center {
        text-align: center;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .stats-mini-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .charts-grid {
            grid-template-columns: 1fr;
        }
        .chart-card.full-width {
            grid-column: span 1;
        }
        .stats-mini-grid {
            grid-template-columns: 1fr;
        }
        .period-selector {
            flex-direction: column;
            align-items: stretch;
        }
        .custom-date {
            justify-content: center;
        }
        .pie-legend {
            grid-template-columns: 1fr;
        }
        .bar-chart {
            justify-content: center;
            gap: 0.5rem;
        }
        .welcome-section {
            flex-direction: column;
            text-align: center;
        }
        .welcome-title {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .stat-value {
            font-size: 1.5rem;
        }
        .bar-container {
            width: 35px;
        }
        .bar {
            width: 25px;
        }
        .period-tab {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }
        .date-input {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
        }
        .simple-table {
            font-size: 0.75rem;
        }
        .mini-progress {
            width: 50px;
        }
        .legend-item {
            font-size: 0.75rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function changePeriode(periode) {
        window.location.href = '{{ route("responsable.statistiques") }}?periode=' + periode;
    }
    
    function appliquerDates() {
        const dateDebut = document.getElementById('date_debut').value;
        const dateFin = document.getElementById('date_fin').value;
        if (dateDebut && dateFin) {
            window.location.href = '{{ route("responsable.statistiques") }}?date_debut=' + dateDebut + '&date_fin=' + dateFin;
        }
    }
    
    function exportRapport() {
        window.location.href = '{{ route("responsable.statistiques.export.pdf") }}';
    }
    
    // Initialisation des graphiques
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique d'évolution
        const ctx = document.getElementById('evolutionChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($evolutionLabels ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin']) !!},
                    datasets: [
                        {
                            label: 'Stagiaires',
                            data: {!! json_encode($evolutionStagiaires ?? []) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: 'white',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Tuteurs',
                            data: {!! json_encode($evolutionTuteurs ?? []) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: 'white',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { 
                            position: 'top', 
                            labels: { 
                                boxWidth: 12, 
                                font: { size: 11 } 
                            } 
                        },
                        tooltip: { 
                            mode: 'index', 
                            intersect: false 
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#e2e8f0' } 
                        },
                        x: { 
                            grid: { display: false } 
                        }
                    }
                }
            });
        }
        
        // Animation des barres
        document.querySelectorAll('.bar').forEach(bar => {
            const height = bar.style.height;
            bar.style.height = '0';
            setTimeout(() => { 
                bar.style.height = height; 
            }, 100);
        });
        
        // Animation des barres de performance
        document.querySelectorAll('.performance-fill').forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0';
            setTimeout(() => { 
                fill.style.width = width; 
            }, 200);
        });
        
        // Animation des indicateurs
        document.querySelectorAll('.indicator-fill').forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0';
            setTimeout(() => { 
                fill.style.width = width; 
            }, 300);
        });
    });
</script>
@endsection
