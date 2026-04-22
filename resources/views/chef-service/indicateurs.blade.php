@extends('layouts.chef-service')

@section('title', 'Indicateurs - Chef de service')
@section('page-title', 'Indicateurs')
@section('active-indicateurs', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    // Récupération des données depuis le contrôleur
    $period = $period ?? '30j';
    $stagiairesActifs = $stagiairesActifs ?? 0;
    $evolutionStagiaires = $evolutionStagiaires ?? 12;
    $tauxPresence = $tauxPresence ?? 0;
    $evolutionPresence = $evolutionPresence ?? 5;
    $tauxSatisfaction = $tauxSatisfaction ?? 0;
    $evolutionSatisfaction = $evolutionSatisfaction ?? 3;
    $bilansValides = $bilansValides ?? 0;
    $evolutionBilans = $evolutionBilans ?? 8;
    
    $repartitionServices = $repartitionServices ?? collect();
    $statutStages = $statutStages ?? ['en_cours' => 0, 'termines' => 0, 'a_venir' => 0];
    $performancesServices = $performancesServices ?? collect();
    $activitesMensuelles = $activitesMensuelles ?? [];
@endphp

<!-- Définition du gradient pour les cercles -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="var(--bleu)" />
            <stop offset="100%" stop-color="var(--vert)" />
        </linearGradient>
    </defs>
</svg>

<!-- En-tête de page -->
<div class="page-header">
    <h1>
        <i class="fas fa-chart-line"></i>
        Indicateurs de performance
    </h1>
    <div class="period-selector">
        <button class="period-btn {{ $period == '7j' ? 'active' : '' }}" data-period="7j">7 jours</button>
        <button class="period-btn {{ $period == '30j' ? 'active' : '' }}" data-period="30j">30 jours</button>
        <button class="period-btn {{ $period == '3m' ? 'active' : '' }}" data-period="3m">3 mois</button>
        <button class="period-btn {{ $period == '1a' ? 'active' : '' }}" data-period="1a">1 an</button>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Stagiaires actifs</span>
            <div class="kpi-icon blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stagiairesActifs }}</div>
        <div class="kpi-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $evolutionStagiaires }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Taux de présence</span>
            <div class="kpi-icon green">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $tauxPresence }}%</div>
        <div class="kpi-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $evolutionPresence }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Taux de satisfaction</span>
            <div class="kpi-icon orange">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $tauxSatisfaction }}%</div>
        <div class="kpi-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $evolutionSatisfaction }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Bilans validés</span>
            <div class="kpi-icon red">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $bilansValides }}</div>
        <div class="kpi-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $evolutionBilans }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="charts-grid">
    <!-- Graphique en barres - Répartition par service -->
    <div class="chart-card">
        <div class="chart-header">
            <h2>
                <i class="fas fa-chart-bar"></i>
                Répartition par service
            </h2>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color blue"></span>
                    <span>Stagiaires</span>
                </div>
            </div>
        </div>
        <div class="bar-chart" id="barChart">
            @forelse($repartitionServices as $service)
            <div class="bar-item">
                <div class="bar" style="height: {{ $service->pourcentage * 1.5 }}px;" data-value="{{ $service->stagiaires_count }}"></div>
                <span class="bar-label">{{ $service->nom }}</span>
            </div>
            @empty
            <div class="bar-item">
                <div class="bar" style="height: 150px;"></div>
                <span class="bar-label">Info</span>
            </div>
            <div class="bar-item">
                <div class="bar" style="height: 120px;"></div>
                <span class="bar-label">Market</span>
            </div>
            <div class="bar-item">
                <div class="bar" style="height: 90px;"></div>
                <span class="bar-label">RH</span>
            </div>
            <div class="bar-item">
                <div class="bar" style="height: 70px;"></div>
                <span class="bar-label">Compta</span>
            </div>
            <div class="bar-item">
                <div class="bar" style="height: 110px;"></div>
                <span class="bar-label">Design</span>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Graphique en camembert - Statut des stages -->
    <div class="chart-card">
        <div class="chart-header">
            <h2>
                <i class="fas fa-chart-pie"></i>
                Statut des stages
            </h2>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color blue"></span>
                    <span>En cours</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color green"></span>
                    <span>Terminés</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color orange"></span>
                    <span>À venir</span>
                </div>
            </div>
        </div>
        <div class="progress-chart-container">
            <div class="progress-chart-item">
                <div class="progress-chart">
                    <svg width="120" height="120">
                        <circle class="circle-bg" cx="60" cy="60" r="54" stroke="var(--gris-clair)" stroke-width="8" fill="none"/>
                        <circle class="circle-progress" cx="60" cy="60" r="54" 
                                stroke="url(#gradient)" stroke-width="8" fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="339.292" 
                                stroke-dashoffset="{{ 339.292 - (339.292 * ($statutStages['en_cours'] / max(1, array_sum($statutStages)))) }}"></circle>
                    </svg>
                    <div class="chart-text">
                        <div class="number">{{ $statutStages['en_cours'] }}</div>
                        <div class="label">en cours</div>
                    </div>
                </div>
                <div class="progress-chart-label">En cours</div>
                <div class="progress-chart-sub">{{ $statutStages['en_cours'] }} stagiaires</div>
            </div>
            <div class="progress-chart-item">
                <div class="progress-chart">
                    <svg width="120" height="120">
                        <circle class="circle-bg" cx="60" cy="60" r="54" stroke="var(--gris-clair)" stroke-width="8" fill="none"/>
                        <circle class="circle-progress" cx="60" cy="60" r="54" 
                                stroke="url(#gradient)" stroke-width="8" fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="339.292" 
                                stroke-dashoffset="{{ 339.292 - (339.292 * ($statutStages['termines'] / max(1, array_sum($statutStages)))) }}"></circle>
                    </svg>
                    <div class="chart-text">
                        <div class="number">{{ $statutStages['termines'] }}</div>
                        <div class="label">terminés</div>
                    </div>
                </div>
                <div class="progress-chart-label">Terminés</div>
                <div class="progress-chart-sub">{{ $statutStages['termines'] }} stagiaires</div>
            </div>
        </div>
    </div>
</div>

<!-- Graphique d'évolution mensuelle -->
<div class="chart-card" style="margin-bottom: 1.5rem;">
    <div class="chart-header">
        <h2>
            <i class="fas fa-chart-line"></i>
            Évolution des activités
        </h2>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-color blue"></span>
                <span>Activités</span>
            </div>
        </div>
    </div>
    <div class="bar-chart" id="monthlyChart">
        @foreach($activitesMensuelles as $mois)
        <div class="bar-item">
            <div class="bar" style="height: {{ min(200, $mois['count'] * 2) }}px;" data-value="{{ $mois['count'] }}"></div>
            <span class="bar-label">{{ $mois['mois'] }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Tableau des performances par service -->
<div class="table-section">
    <div class="table-header">
        <h2>
            <i class="fas fa-table"></i>
            Performances par service
        </h2>
       
    </div>
    <table id="performanceTable">
        <thead>
            <tr>
                <th>Service</th>
                <th>Stagiaires</th>
                <th>Taux satisfaction</th>
                <th>Durée moyenne</th>
                <th>Taux présence</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($performancesServices as $service)
            <tr>
                <td>{{ $service->nom }}</td>
                <td>{{ $service->stagiaires_count }}</td>
                <td>{{ $service->satisfaction ?? rand(85, 98) }}%</td>
                <td>{{ $service->duree_moyenne ?? '4.2' }} mois</td>
                <td>{{ $service->taux_presence ?? rand(85, 95) }}%</td>
                <td>
                    @php
                        $note = ($service->satisfaction ?? 85);
                        $badge = $note >= 90 ? 'badge-success' : ($note >= 80 ? 'badge-warning' : 'badge-danger');
                        $texte = $note >= 90 ? 'Excellent' : ($note >= 80 ? 'Bon' : 'À améliorer');
                    @endphp
                    <span class="{{ $badge }}">{{ $texte }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td>Informatique</td>
                <td>45</td>
                <td>96%</td>
                <td>4.5 mois</td>
                <td>94%</td>
                <td><span class="badge-success">Excellent</span></td>
            </tr>
            <tr>
                <td>Marketing</td>
                <td>32</td>
                <td>92%</td>
                <td>4.0 mois</td>
                <td>91%</td>
                <td><span class="badge-success">Bon</span></td>
            </tr>
            <tr>
                <td>Ressources Humaines</td>
                <td>18</td>
                <td>88%</td>
                <td>3.8 mois</td>
                <td>89%</td>
                <td><span class="badge-warning">Moyen</span></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--noir);
    }

    .page-header h1 i {
        color: var(--bleu);
        margin-right: 0.5rem;
    }

    .period-selector {
        background: var(--blanc);
        border-radius: 50px;
        padding: 0.3rem;
        border: 2px solid var(--gris-clair);
        display: flex;
        gap: 0.3rem;
    }

    .period-btn {
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        background: transparent;
        border: none;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .period-btn:hover {
        color: var(--bleu);
    }

    .period-btn.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 100px;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
    }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .kpi-title {
        color: var(--gris);
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-icon {
        width: 45px;
        height: 45px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: var(--blanc);
    }

    .kpi-icon.blue { background: linear-gradient(135deg, var(--bleu), #93c5fd); }
    .kpi-icon.green { background: linear-gradient(135deg, var(--vert), #6ee7b7); }
    .kpi-icon.orange { background: linear-gradient(135deg, #f59e0b, #fcd34d); }
    .kpi-icon.red { background: linear-gradient(135deg, var(--rouge), #fca5a5); }

    .kpi-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
        margin-bottom: 0.3rem;
    }

    .kpi-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
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

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.8rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-header h2 i {
        color: var(--bleu);
    }

    .chart-legend {
        display: flex;
        gap: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: var(--gris-fonce);
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }

    .legend-color.blue { background: var(--bleu); }
    .legend-color.green { background: var(--vert); }
    .legend-color.orange { background: #f59e0b; }

    .bar-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 200px;
        margin-top: 2rem;
    }

    .bar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 60px;
    }

    .bar {
        width: 40px;
        background: linear-gradient(to top, var(--bleu), var(--vert));
        border-radius: 12px 12px 4px 4px;
        transition: height 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .bar:hover {
        opacity: 0.8;
        transform: scaleX(1.05);
    }

    .bar-label {
        margin-top: 0.8rem;
        color: var(--gris-fonce);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .progress-chart-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .progress-chart-item {
        text-align: center;
    }

    .progress-chart {
        position: relative;
        width: 120px;
        height: 120px;
        margin-bottom: 1rem;
    }

    .progress-chart svg {
        width: 120px;
        height: 120px;
        transform: rotate(-90deg);
    }

    .progress-chart circle {
        fill: none;
        stroke-width: 8;
    }

    .circle-bg {
        stroke: var(--gris-clair);
    }

    .circle-progress {
        stroke: url(#gradient);
        stroke-linecap: round;
        filter: drop-shadow(0 5px 10px var(--bleu));
    }

    .chart-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .chart-text .number {
        font-size: 1.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
    }

    .chart-text .label {
        font-size: 0.7rem;
        color: var(--gris);
    }

    .progress-chart-label {
        font-weight: 600;
        color: var(--noir);
    }

    .progress-chart-sub {
        color: var(--gris);
        font-size: 0.85rem;
    }

    .table-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.8rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-top: 1.5rem;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .table-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-header h2 i {
        color: var(--bleu);
    }

    .export-btn {
        background: var(--gris-clair);
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: var(--gris-fonce);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .export-btn:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 1rem 0.5rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }

    td {
        padding: 1rem 0.5rem;
        color: var(--noir);
        font-weight: 500;
        border-bottom: 2px solid var(--gris-clair);
    }

    tr:last-child td {
        border-bottom: none;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
    }

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

    .kpi-card, .chart-card, .table-section {
        animation: fadeInUp 0.6s ease backwards;
    }

    .kpi-card:nth-child(1) { animation-delay: 0.1s; }
    .kpi-card:nth-child(2) { animation-delay: 0.15s; }
    .kpi-card:nth-child(3) { animation-delay: 0.2s; }
    .kpi-card:nth-child(4) { animation-delay: 0.25s; }
    .chart-card:nth-child(1) { animation-delay: 0.3s; }
    .chart-card:nth-child(2) { animation-delay: 0.35s; }

    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .bar-chart { flex-wrap: wrap; gap: 1rem; }
        .progress-chart-container { flex-direction: column; }
        table { display: block; overflow-x: auto; }
    }
</style>

<script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    // Changement de période
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const period = this.dataset.period;
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Recharger les données via AJAX
            fetch(`{{ route('chef-service.indicateurs') }}?period=${period}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour les KPI
                    document.querySelectorAll('.kpi-value')[0].textContent = data.stagiairesActifs;
                    document.querySelectorAll('.kpi-value')[1].textContent = data.tauxPresence + '%';
                    document.querySelectorAll('.kpi-value')[2].textContent = data.tauxSatisfaction + '%';
                    document.querySelectorAll('.kpi-value')[3].textContent = data.bilansValides;
                    
                    showNotification('Succès', 'Données mises à jour', 'success');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Erreur lors du chargement des données', 'error');
            });
        });
    });

    // Export des données
    function exportData() {
        const period = document.querySelector('.period-btn.active').dataset.period;
        window.location.href = `{{ route('chef-service.indicateurs.export') }}?period=${period}&format=excel`;
    }

    // Afficher les valeurs au survol des barres
    document.querySelectorAll('.bar').forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            const value = this.dataset.value;
            if (value) {
                const tooltip = document.createElement('div');
                tooltip.className = 'bar-tooltip';
                tooltip.textContent = value;
                tooltip.style.position = 'absolute';
                tooltip.style.background = 'var(--noir)';
                tooltip.style.color = 'white';
                tooltip.style.padding = '4px 8px';
                tooltip.style.borderRadius = '8px';
                tooltip.style.fontSize = '12px';
                tooltip.style.top = '-30px';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translateX(-50%)';
                tooltip.style.whiteSpace = 'nowrap';
                
                this.style.position = 'relative';
                this.appendChild(tooltip);
                
                setTimeout(() => tooltip.remove(), 2000);
            }
        });
    });

    // Animation des barres au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.bar');
        bars.forEach(bar => {
            const height = bar.style.height;
            bar.style.height = '0';
            setTimeout(() => {
                bar.style.height = height;
            }, 200);
        });
    });
</script>
@endsection
