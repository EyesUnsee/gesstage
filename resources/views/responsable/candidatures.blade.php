@extends('layouts.responsable')

@section('title', 'Gestion des candidatures - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Gestion des <span>candidatures</span> 📋</h1>
        <p class="welcome-subtitle">Consultez, filtrez et gérez toutes les candidatures reçues</p>
    </div>
    <a href="{{ route('responsable.candidatures.create') }}" class="btn-add">
        <i class="fas fa-plus-circle"></i>
        Nouvelle candidature
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Statistiques rapides -->
<div class="candidatures-stats">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total</span>
            <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
        </div>
        <div class="stat-value">{{ $totalCandidatures ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: #10b981;"></i> +{{ $nouvellesCandidatures ?? 0 }} cette semaine
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">En attente</span>
            <span class="stat-icon"><i class="fas fa-clock"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesEnAttente ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i> À traiter
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Acceptées</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesAcceptees ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-check" style="color: #10b981;"></i> +{{ $nouvellesAcceptees ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Refusées</span>
            <span class="stat-icon"><i class="fas fa-times-circle"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesRefusees ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-chart-line" style="color: #64748b;"></i> {{ $variationRefusees ?? 0 }}
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-section">
    <div class="filters-title">
        <i class="fas fa-filter"></i>
        <h3>Filtres avancés</h3>
    </div>
    <form method="GET" action="{{ route('responsable.candidatures.index') }}" id="filterForm">
        <div class="filters-grid">
            <div class="filter-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="all" {{ request('statut') == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="acceptee" {{ request('statut') == 'acceptee' ? 'selected' : '' }}>Acceptées</option>
                    <option value="refusee" {{ request('statut') == 'refusee' ? 'selected' : '' }}>Refusées</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours d'examen</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Type de stage</label>
                <select name="type">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Tous les types</option>
                    <option value="developpement" {{ request('type') == 'developpement' ? 'selected' : '' }}>Développement Web</option>
                    <option value="marketing" {{ request('type') == 'marketing' ? 'selected' : '' }}>Marketing Digital</option>
                    <option value="rh" {{ request('type') == 'rh' ? 'selected' : '' }}>Ressources Humaines</option>
                    <option value="data" {{ request('type') == 'data' ? 'selected' : '' }}>Data Science</option>
                    <option value="design" {{ request('type') == 'design' ? 'selected' : '' }}>Design</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Date de candidature</label>
                <input type="date" name="date" value="{{ request('date') }}">
            </div>
            <div class="filter-group">
                <label>Entreprise</label>
                <input type="text" name="entreprise" placeholder="Nom de l'entreprise" value="{{ request('entreprise') }}">
            </div>
        </div>
        <div class="filter-actions">
            <a href="{{ route('responsable.candidatures.index') }}" class="btn-filter reset">
                <i class="fas fa-redo-alt"></i>
                Réinitialiser
            </a>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i>
                Appliquer les filtres
            </button>
        </div>
    </form>
</div>

<!-- Tabs -->
<div class="tabs">
    <a href="{{ route('responsable.candidatures.index', ['statut' => 'all']) }}" class="tab {{ request('statut') == 'all' || !request('statut') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        Toutes ({{ $totalCandidatures ?? 0 }})
    </a>
    <a href="{{ route('responsable.candidatures.index', ['statut' => 'en_attente']) }}" class="tab {{ request('statut') == 'en_attente' ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        En attente ({{ $candidaturesEnAttente ?? 0 }})
    </a>
    <a href="{{ route('responsable.candidatures.index', ['statut' => 'acceptee']) }}" class="tab {{ request('statut') == 'acceptee' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Acceptées ({{ $candidaturesAcceptees ?? 0 }})
    </a>
    <a href="{{ route('responsable.candidatures.index', ['statut' => 'refusee']) }}" class="tab {{ request('statut') == 'refusee' ? 'active' : '' }}">
        <i class="fas fa-times-circle"></i>
        Refusées ({{ $candidaturesRefusees ?? 0 }})
    </a>
</div>

<!-- Liste des candidatures -->
<div class="candidatures-grid">
    @forelse($candidatures ?? [] as $candidature)
    <div class="candidature-card">
        <div class="card-header">
            <div class="candidature-title">
                <h3>{{ $candidature->titre }}</h3>
                <div class="candidature-company">
                    <i class="fas fa-building"></i>
                    {{ $candidature->entreprise ?? 'Entreprise non spécifiée' }}
                </div>
            </div>
            <span class="candidature-badge badge-{{ $candidature->statut }}">
                @if($candidature->statut == 'en_attente')
                    En attente
                @elseif($candidature->statut == 'acceptee')
                    Acceptée
                @elseif($candidature->statut == 'refusee')
                    Refusée
                @elseif($candidature->statut == 'en_cours')
                    En cours
                @else
                    {{ ucfirst($candidature->statut) }}
                @endif
            </span>
        </div>
        <div class="candidature-info">
            <div class="info-row">
                <i class="fas fa-user-graduate"></i>
                <span><strong>Candidat:</strong> {{ $candidature->candidat->first_name ?? '' }} {{ $candidature->candidat->last_name ?? '' }}</span>
            </div>
            <div class="info-row">
                <i class="fas fa-graduation-cap"></i>
                <span><strong>Formation:</strong> {{ $candidature->candidat->formation ?? 'Non renseignée' }}</span>
            </div>
            <div class="info-row">
                <i class="fas fa-calendar-alt"></i>
                <span><strong>Période:</strong> 
                    @if($candidature->date_debut && $candidature->date_fin)
                        {{ \Carbon\Carbon::parse($candidature->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($candidature->date_fin)->format('d/m/Y') }}
                    @else
                        Non spécifiée
                    @endif
                </span>
            </div>
            <div class="info-row">
                <i class="fas fa-envelope"></i>
                <span>{{ $candidature->candidat->email ?? '' }}</span>
            </div>
        </div>
        <div class="candidature-footer">
            <div class="candidature-date">
                @if($candidature->statut == 'acceptee' && $candidature->date_reponse)
                    <i class="far fa-check-circle" style="color: #10b981;"></i>
                    Acceptée le {{ \Carbon\Carbon::parse($candidature->date_reponse)->format('d/m/Y') }}
                @elseif($candidature->statut == 'refusee' && $candidature->date_reponse)
                    <i class="far fa-times-circle" style="color: #ef4444;"></i>
                    Refusée le {{ \Carbon\Carbon::parse($candidature->date_reponse)->format('d/m/Y') }}
                @else
                    <i class="far fa-clock"></i>
                    Reçue le {{ \Carbon\Carbon::parse($candidature->created_at)->format('d/m/Y') }}
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('responsable.candidatures.show', $candidature->id) }}" class="btn-icon view" title="Voir détails">
                    <i class="fas fa-eye"></i>
                </a>
                @if($candidature->statut == 'en_attente')
                    <form action="{{ route('responsable.candidatures.accepter', $candidature->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon accept" title="Accepter" onclick="return confirm('Êtes-vous sûr de vouloir ACCEPTER cette candidature ?')">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    <form action="{{ route('responsable.candidatures.refuser', $candidature->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon reject" title="Refuser" onclick="return confirm('Êtes-vous sûr de vouloir REFUSER cette candidature ?')">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-file-alt"></i>
        <h3>Aucune candidature</h3>
        <p>Aucune candidature trouvée pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(isset($candidatures) && $candidatures->count() > 10)
<div class="pagination">
    {{ $candidatures->appends(request()->query())->links() }}
</div>
@endif

<style>
    /* Variables CSS */
    :root {
        --bleu: #3b82f6;
        --vert: #10b981;
        --rouge: #ef4444;
        --orange: #f59e0b;
        --gris: #64748b;
        --gris-clair: #e2e8f0;
        --gris-fonce: #334155;
        --noir: #0f172a;
        --blanc: #ffffff;
        --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
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
    
    .welcome-section {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        color: white;
    }
    
    .welcome-title {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .welcome-title span {
        font-weight: 800;
    }
    
    .welcome-subtitle {
        opacity: 0.9;
    }
    
    .btn-add {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 40px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-add:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        grid-column: 1 / -1;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: var(--gris);
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        color: var(--noir);
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: var(--gris);
    }
    
    .candidatures-stats {
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
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.1;
        border-radius: 0 0 0 80px;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
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
    }
    
    .stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, var(--bleu), #93c5fd);
        color: var(--blanc);
    }
    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, var(--orange), #fcd34d);
        color: var(--blanc);
    }
    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, var(--vert), #6ee7b7);
        color: var(--blanc);
    }
    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, var(--rouge), #fca5a5);
        color: var(--blanc);
    }
    
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
    
    .filters-section {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }
    
    .filters-title {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        color: var(--noir);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    
    .filters-title i {
        color: var(--bleu);
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-group label {
        display: block;
        color: var(--gris-fonce);
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        color: var(--noir);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
    }
    
    .filter-group select:focus,
    .filter-group input:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }
    
    .btn-filter {
        padding: 0.8rem 1.5rem;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: none;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-filter:hover {
        background: var(--gris);
        color: var(--blanc);
    }
    
    .btn-filter.reset:hover {
        background: var(--rouge);
    }
    
    .tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    
    .tab {
        padding: 0.8rem 2rem;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        border-radius: 40px;
        color: var(--gris-fonce);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .tab:hover {
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .tab.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border-color: transparent;
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .tab.active i {
        color: var(--blanc);
    }
    
    .candidatures-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .candidature-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .candidature-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.03;
        border-radius: 0 0 0 100px;
    }
    
    .candidature-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 25px 40px -15px var(--bleu);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .candidature-title h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .candidature-company {
        color: var(--gris);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .candidature-company i {
        color: var(--bleu);
        font-size: 0.8rem;
    }
    
    .candidature-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .badge-en_attente {
        background: rgba(245, 158, 11, 0.1);
        color: var(--orange);
        border: 1px solid var(--orange);
    }
    
    .badge-acceptee {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .badge-refusee {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }
    
    .badge-en_cours {
        background: rgba(59, 130, 246, 0.1);
        color: var(--bleu);
        border: 1px solid var(--bleu);
    }
    
    .candidature-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin: 1rem 0;
    }
    
    .info-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris-fonce);
        font-size: 0.9rem;
    }
    
    .info-row i {
        width: 20px;
        color: var(--bleu);
        font-size: 0.9rem;
    }
    
    .info-row strong {
        color: var(--noir);
        font-weight: 600;
        margin-right: 0.3rem;
    }
    
    .candidature-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--gris-clair);
    }
    
    .candidature-date {
        color: var(--gris);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .card-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        text-decoration: none;
    }
    
    .btn-icon:hover {
        transform: translateY(-2px);
    }
    
    .btn-icon.view:hover {
        background: var(--bleu);
        color: var(--blanc);
    }
    
    .btn-icon.accept:hover {
        background: var(--vert);
        color: var(--blanc);
    }
    
    .btn-icon.reject:hover {
        background: var(--rouge);
        color: var(--blanc);
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    
    .pagination nav {
        display: flex;
        gap: 0.5rem;
    }
    
    .pagination .page-item {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .pagination .page-item:hover {
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .pagination .page-item.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border-color: transparent;
    }
    
    @media (max-width: 1200px) {
        .candidatures-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .candidatures-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .candidatures-stats {
            grid-template-columns: 1fr;
        }
        
        .filters-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-actions {
            flex-direction: column;
        }
        
        .tabs {
            justify-content: center;
        }
        
        .candidature-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .card-header {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .candidature-badge {
            align-self: flex-start;
        }
    }
</style>
@endsection
