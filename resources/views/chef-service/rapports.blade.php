@extends('layouts.chef-service')

@section('title', 'Rapports & Analyses - Chef de service')
@section('page-title', 'Rapports & Analyses')
@section('active-rapports', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    $stats = $stats ?? [
        'total_stagiaires' => 0,
        'total_tuteurs' => 0,
        'total_services' => 0,
        'taux_validation' => 0,
        'evolution_stagiaires' => 0,
        'evolution_tuteurs' => 0,
        'evolution_validation' => 0
    ];
    
    $evolutionData = $evolutionData ?? [];
    $repartitionServices = $repartitionServices ?? [];
    $topStagiaires = $topStagiaires ?? [];
    $bilansRecents = $bilansRecents ?? [];
    $period = $period ?? 'month';
@endphp

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
    .period-btn:hover { color: var(--bleu); }
    .period-btn.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
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
    .stat-card:hover { transform: translateY(-5px); border-color: var(--bleu); }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .stat-title { color: var(--gris); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }
    .stat-value { font-size: 2.4rem; font-weight: 800; color: var(--noir); margin-bottom: 0.3rem; }
    .stat-trend { font-size: 0.85rem; color: var(--gris); display: flex; align-items: center; gap: 0.5rem; }
    .trend-up { color: var(--vert); }
    .trend-down { color: var(--rouge); }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .chart-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .chart-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chart-header h2 i { color: var(--bleu); }
    
    .bar-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 250px;
        gap: 1rem;
    }
    .bar-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    .bar {
        width: 100%;
        max-width: 60px;
        background: linear-gradient(to top, var(--bleu), var(--vert));
        border-radius: 12px 12px 8px 8px;
        transition: height 0.6s ease;
        cursor: pointer;
        position: relative;
    }
    .bar:hover { opacity: 0.8; transform: scaleX(1.05); }
    .bar-label {
        margin-top: 0.8rem;
        color: var(--gris-fonce);
        font-size: 0.8rem;
        font-weight: 600;
        text-align: center;
    }
    .bar-value {
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--noir);
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.7rem;
        white-space: nowrap;
    }
    
    .donut-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }
    .donut-chart {
        position: relative;
        width: 180px;
        height: 180px;
    }
    .donut-svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .donut-bg { fill: none; stroke: var(--gris-clair); stroke-width: 20; }
    .donut-fill { fill: none; stroke: url(#gradient); stroke-width: 20; stroke-linecap: round; transition: stroke-dasharray 0.8s ease; }
    .donut-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    .donut-number { font-size: 2rem; font-weight: 800; color: var(--noir); line-height: 1; }
    .donut-label { font-size: 0.7rem; color: var(--gris); }
    .donut-legend { display: flex; flex-direction: column; gap: 0.8rem; }
    .legend-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; }
    .legend-color { width: 12px; height: 12px; border-radius: 4px; }
    
    .ranking-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .section-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
    }
    .export-btn:hover { background: var(--bleu); color: white; transform: translateY(-2px); }
    
    .ranking-table { width: 100%; border-collapse: collapse; }
    .ranking-table th {
        text-align: left;
        padding: 1rem 0.5rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    .ranking-table td {
        padding: 1rem 0.5rem;
        color: var(--noir);
        font-weight: 500;
        border-bottom: 1px solid var(--gris-clair);
    }
    .ranking-table tr:last-child td { border-bottom: none; }
    .ranking-table tr:hover { background: var(--gris-clair); }
    
    .rank-badge {
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .rank-1 { background: linear-gradient(135deg, #f59e0b, #fcd34d); }
    .rank-2 { background: linear-gradient(135deg, #94a3b8, #cbd5e1); }
    .rank-3 { background: linear-gradient(135deg, #cd7f32, #e4a56e); }
    
    .progress-bar {
        width: 100px;
        height: 6px;
        background: var(--gris-clair);
        border-radius: 3px;
        overflow: hidden;
        display: inline-block;
        margin-right: 8px;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert));
        border-radius: 3px;
        transition: width 0.6s ease;
    }
    
    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        padding: 0.2rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .bar-chart { flex-direction: column; align-items: stretch; height: auto; }
        .bar { max-width: 100%; height: 40px !important; }
        .bar-item { flex-direction: row; gap: 1rem; }
        .bar-value { top: 10px; left: auto; right: 10px; transform: none; }
        .period-selector { width: 100%; justify-content: center; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .ranking-table { display: block; overflow-x: auto; }
    }
</style>

<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="var(--bleu)" />
            <stop offset="100%" stop-color="var(--vert)" />
        </linearGradient>
    </defs>
</svg>

<!-- En-tête -->
<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Rapports & Analyses</h1>
    
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stagiaires</span>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_stagiaires'] }}</div>
        <div class="stat-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $stats['evolution_stagiaires'] }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Tuteurs</span>
            <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_tuteurs'] }}</div>
        <div class="stat-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $stats['evolution_tuteurs'] }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Services</span>
            <div class="stat-icon"><i class="fas fa-building"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_services'] }}</div>
        <div class="stat-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $stats['evolution_services'] }}</span>
            <span>nouveaux</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Taux validation</span>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="stat-value">{{ $stats['taux_validation'] }}%</div>
        <div class="stat-trend">
            <span class="trend-up"><i class="fas fa-arrow-up"></i> +{{ $stats['evolution_validation'] }}%</span>
            <span>vs période précédente</span>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-header">
            <h2><i class="fas fa-chart-line"></i> Évolution des activités</h2>
        </div>
        <div class="bar-chart" id="evolutionChart">
            @foreach($evolutionData as $data)
            <div class="bar-item">
                <div class="bar" style="height: {{ $data['height'] }}px;" data-value="{{ $data['count'] }}">
                    <span class="bar-value">{{ $data['count'] }}</span>
                </div>
                <span class="bar-label">{{ $data['mois'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <h2><i class="fas fa-chart-pie"></i> Répartition par service</h2>
        </div>
        <div class="donut-container">
            <div class="donut-chart">
                <svg class="donut-svg" viewBox="0 0 100 100">
                    <circle class="donut-bg" cx="50" cy="50" r="40"></circle>
                    <circle class="donut-fill" cx="50" cy="50" r="40" stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $stats['taux_validation'] / 100) }}"></circle>
                </svg>
                <div class="donut-text">
                    <div class="donut-number">{{ $stats['taux_validation'] }}%</div>
                    <div class="donut-label">Validés</div>
                </div>
            </div>
            <div class="donut-legend">
                @foreach($repartitionServices as $service)
                <div class="legend-item">
                    <div class="legend-color" style="background: {{ $service['color'] }}"></div>
                    <span>{{ $service['nom'] }} ({{ $service['count'] }})</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Classement des stagiaires -->
<div class="ranking-section">
    <div class="section-header">
        <h2><i class="fas fa-trophy"></i> Top stagiaires</h2>
        <button class="export-btn" onclick="exportRapportPDF()"><i class="fas fa-file-pdf"></i> Exporter PDF</button>
    </div>
    <table class="ranking-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Stagiaire</th>
                <th>Service</th>
                <th>Note moyenne</th>
                <th>Présence</th>
                <th>Évaluations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topStagiaires as $index => $stagiaire)
            <tr>
                <td><span class="rank-badge rank-{{ $index+1 }}">{{ $index+1 }}</span></td>
                <td><strong>{{ $stagiaire['nom'] }}</strong></td>
                <td>{{ $stagiaire['service'] }}</td>
                <td><span style="color: var(--vert); font-weight: 700;">{{ $stagiaire['note'] }}/20</span></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $stagiaire['presence'] }}%"></div>
                    </div>
                    <span style="font-size: 0.7rem;">{{ $stagiaire['presence'] }}%</span>
                </td>
                <td>{{ $stagiaire['evaluations'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucune donnée disponible</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Derniers bilans -->
<div class="ranking-section">
    <div class="section-header">
        <h2><i class="fas fa-file-alt"></i> Derniers bilans soumis</h2>
        <button class="export-btn" onclick="window.location.href='{{ route('chef-service.bilans') }}'"><i class="fas fa-eye"></i> Voir tous</button>
    </div>
    <table class="ranking-table">
        <thead>
            <tr>
                <th>Stagiaire</th>
                <th>Titre</th>
                <th>Date</th>
                <th>Note</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bilansRecents as $bilan)
            <tr>
                <td><strong>{{ $bilan['stagiaire'] }}</strong></td>
                <td>{{ $bilan['titre'] }}</td>
                <td>{{ $bilan['date'] }}</td>
                <td><span style="color: {{ $bilan['note'] >= 15 ? 'var(--vert)' : 'var(--orange)' }}; font-weight: 700;">{{ $bilan['note'] }}/20</span></td>
                <td><span class="badge-{{ $bilan['statut'] == 'valide' ? 'success' : 'warning' }}">{{ ucfirst($bilan['statut']) }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Aucun bilan récent</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    function toggleMenu() { document.getElementById('sidebar')?.classList.toggle('active'); }
    
    function exportRapportPDF() {
        showNotification('Information', 'Génération du PDF en cours...', 'info');
        
        // Créer un élément temporaire pour l'export
        const element = document.createElement('div');
        element.innerHTML = document.querySelector('.stats-grid').outerHTML + 
                           document.querySelector('.charts-grid').outerHTML + 
                           document.querySelectorAll('.ranking-section')[0].outerHTML + 
                           document.querySelectorAll('.ranking-section')[1].outerHTML;
        element.style.padding = '20px';
        element.style.fontFamily = 'Inter, sans-serif';
        
        // Ajouter un titre
        const title = document.createElement('h1');
        title.innerHTML = 'Rapport GesStage - ' + new Date().toLocaleDateString('fr-FR');
        title.style.textAlign = 'center';
        title.style.marginBottom = '20px';
        title.style.color = '#0f172a';
        element.insertBefore(title, element.firstChild);
        
        // Ajouter la date
        const date = document.createElement('p');
        date.innerHTML = 'Généré le ' + new Date().toLocaleString('fr-FR');
        date.style.textAlign = 'center';
        date.style.marginBottom = '30px';
        date.style.color = '#64748b';
        element.insertBefore(date, title.nextSibling);
        
        // Options pour html2pdf
        const opt = {
            margin: [10, 10, 10, 10],
            filename: `rapport_gesstage_${new Date().toISOString().slice(0,19)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        // Exporter
        html2pdf().set(opt).from(element).save().then(() => {
            showNotification('Succès', 'PDF généré avec succès', 'success');
        }).catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Erreur lors de la génération du PDF', 'error');
        });
    }
    
    // Changement de période
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const period = this.dataset.period;
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            fetch(`{{ route('chef-service.rapports') }}?period=${period}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', 'Données mises à jour', 'success');
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    });
    
    function showNotification(title, message, type) {
        const toast = document.getElementById('notificationToast');
        if (!toast) { alert(title + ': ' + message); return; }
        toast.querySelector('.toast-title').textContent = title;
        toast.querySelector('.toast-message').textContent = message;
        toast.style.borderLeftColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#3b82f6');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Animation des barres
    document.querySelectorAll('.bar').forEach(bar => {
        const height = bar.style.height;
        bar.style.height = '0';
        setTimeout(() => { bar.style.height = height; }, 200);
    });
    
    // Animation des barres de progression
    document.querySelectorAll('.progress-fill').forEach(fill => {
        const width = fill.style.width;
        fill.style.width = '0';
        setTimeout(() => { fill.style.width = width; }, 300);
    });
    
    @if(session('success')) showNotification('Succès', '{{ session('success') }}', 'success'); @endif
    @if(session('error')) showNotification('Erreur', '{{ session('error') }}', 'error'); @endif
</script>
@endsection
