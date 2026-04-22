<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de statistiques - GesStage</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #2563eb;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 12px;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            width: 23%;
            text-align: center;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin: 20px 0 15px 0;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f1f5f9;
            font-weight: bold;
        }
        .bar-chart {
            margin: 15px 0;
        }
        .bar-item {
            margin-bottom: 10px;
        }
        .bar-label {
            display: inline-block;
            width: 40px;
            font-size: 12px;
        }
        .bar-line {
            display: inline-block;
            height: 20px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            border-radius: 4px;
            vertical-align: middle;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
        }
        .date-range {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>GesStage</h1>
        <div class="subtitle">Rapport de statistiques</div>
    </div>
    
    <div class="date-range">
        Période: 
        @if($periode == 'custom')
            Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
        @else
            {{ ucfirst($periode) }}
        @endif
        - Généré le {{ $date }}
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stagesEnCours }}</div>
            <div class="stat-label">Stages en cours</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stagesTermines }}</div>
            <div class="stat-label">Stages terminés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $totalCandidatures }}</div>
            <div class="stat-label">Candidatures</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $tauxSatisfaction }}%</div>
            <div class="stat-label">Satisfaction</div>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $tauxPresence }}%</div>
            <div class="stat-label">Taux de présence</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $tauxCompletion }}%</div>
            <div class="stat-label">Documents validés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ array_sum($stagesParMois) }}</div>
            <div class="stat-label">Stages créés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count($topTuteurs) }}</div>
            <div class="stat-label">Tuteurs actifs</div>
        </div>
    </div>
    
    <div class="section-title">Stages par mois ({{ date('Y') }})</div>
    <div class="bar-chart">
        @php $max = max($stagesParMois) ?: 1; @endphp
        @foreach($stagesParMois as $mois => $count)
        <div class="bar-item">
            <span class="bar-label">{{ $mois }}</span>
            <div class="bar-line" style="width: {{ ($count / $max) * 200 }}px;"></div>
            <span style="margin-left: 10px; font-size: 12px;">{{ $count }}</span>
        </div>
        @endforeach
    </div>
    
    <div class="section-title">Top 10 des tuteurs</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tuteur</th>
                <th>Département</th>
                <th>Stagiaires</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topTuteurs as $index => $tuteur)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tuteur['nom'] }}</td>
                <td>{{ $tuteur['departement'] }}</td>
                <td>{{ $tuteur['stagiaires'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        GesStage - Plateforme de gestion de stages<br>
        Rapport généré automatiquement le {{ $date }}
    </div>
</body>
</html>
