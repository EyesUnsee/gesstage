@extends('layouts.responsable')

@section('title', 'Synchronisation des candidatures')

@section('content')
<div class="container">
    <div class="welcome-section">
        <h1>Synchronisation terminée !</h1>
        <p>Résultats de la synchronisation des candidatures</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $created }}</div>
            <div class="stat-label">Candidatures créées</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $updated }}</div>
            <div class="stat-label">Candidatures mises à jour</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $skipped }}</div>
            <div class="stat-label">Candidats ignorés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $candidats->count() }}</div>
            <div class="stat-label">Total candidats</div>
        </div>
    </div>

    <div class="info-card">
        <h2>Détails</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Candidat</th>
                    <th>Statut</th>
                    <th>Raison</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $detail)
                <tr>
                    <td>{{ $detail['candidat'] }}</td>
                    <td>
                        @if($detail['status'] == 'created')
                            <span class="badge badge-success">Créée</span>
                        @elseif($detail['status'] == 'updated')
                            <span class="badge badge-info">Mise à jour</span>
                        @elseif($detail['status'] == 'skip')
                            <span class="badge badge-warning">Ignoré</span>
                        @else
                            <span class="badge badge-secondary">{{ $detail['status'] }}</span>
                        @endif
                    </td>
                    <td>{{ $detail['reason'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="actions">
        <a href="{{ route('responsable.candidatures.index') }}" class="btn-primary">
            <i class="fas fa-list"></i> Voir les candidatures
        </a>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #3b82f6;
    }
    .stat-label {
        color: #64748b;
        margin-top: 0.5rem;
    }
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-warning { background: #fed7aa; color: #92400e; }
    .badge-secondary { background: #e2e8f0; color: #475569; }
    .actions {
        text-align: center;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #10b981);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endsection
