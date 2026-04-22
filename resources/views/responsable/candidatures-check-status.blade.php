@extends('layouts.responsable')

@section('title', 'État des candidatures')

@section('content')
<div class="container">
    <div class="welcome-section">
        <h1>État des candidatures</h1>
        <p>Vérification de la synchronisation entre documents et candidatures</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalCandidats }}</div>
            <div class="stat-label">Total candidats</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $candidatsAvecCV }}</div>
            <div class="stat-label">Candidats avec CV</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $candidatsAvecLettre }}</div>
            <div class="stat-label">Candidats avec lettre</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $candidatsQualifies }}</div>
            <div class="stat-label">Candidats qualifiés (CV+lettre)</div>
        </div>
    </div>

    <div class="info-card">
        <h2>État de synchronisation</h2>
        <div class="sync-status">
            <div class="status-item">
                <span class="status-label">Candidatures en base :</span>
                <span class="status-value">{{ $totalCandidatures }}</span>
            </div>
            <div class="status-item">
                <span class="status-label">Candidats qualifiés sans candidature :</span>
                <span class="status-value {{ $candidatsSansCandidature > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $candidatsSansCandidature }}
                </span>
            </div>
        </div>

        @if($candidatsSansCandidature > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Il y a {{ $candidatsSansCandidature }} candidats qui ont déposé leur dossier mais n'ont pas de candidature.
                <a href="{{ route('responsable.candidatures.sync') }}" class="btn-sync">Lancer la synchronisation</a>
            </div>

            <h3>Liste des candidats concernés :</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($candidatsManquants as $candidat)
                    <tr>
                        <td>{{ $candidat->id }}</td>
                        <td>{{ $candidat->first_name }} {{ $candidat->last_name }}</td>
                        <td>{{ $candidat->email }}</td>
                        <td>
                            <button class="btn-sm" onclick="createCandidature({{ $candidat->id }})">
                                Créer candidature
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Toutes les candidatures sont synchronisées !
            </div>
        @endif
    </div>

    <div class="actions">
        <a href="{{ route('responsable.candidatures.index') }}" class="btn-primary">
            <i class="fas fa-list"></i> Voir les candidatures
        </a>
        <a href="{{ route('responsable.candidatures.sync') }}" class="btn-secondary">
            <i class="fas fa-sync"></i> Synchroniser maintenant
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
    .sync-status {
        display: flex;
        gap: 2rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .status-item {
        flex: 1;
    }
    .status-label {
        font-weight: 600;
        color: #64748b;
    }
    .status-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-left: 1rem;
    }
    .text-danger { color: #ef4444; }
    .text-success { color: #10b981; }
    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-left: 4px solid #f59e0b;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    .btn-sync {
        background: #f59e0b;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        margin-left: auto;
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
    .btn-sm {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        cursor: pointer;
    }
    .actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
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
    .btn-secondary {
        background: #64748b;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<script>
function createCandidature(userId) {
    fetch('/responsable/candidatures/create-for-user/' + userId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              location.reload();
          } else {
              alert('Erreur: ' + data.message);
          }
      });
}
</script>
@endsection
