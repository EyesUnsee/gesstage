@extends('layouts.chef-service')

@section('title', 'Gestion des validations - Chef de service')
@section('page-title', 'Validations')
@section('active-validations', 'active')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Gestion des <span>validations</span> ✅</h1>
        <p class="welcome-subtitle">Consultez, filtrez et gérez toutes les demandes de validation</p>
    </div>
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

@php
    use Carbon\Carbon;
    
    // Données réelles provenant du contrôleur
    $validations = $validations ?? collect();
    $stats = $stats ?? ['total' => 0, 'urgent' => 0, 'en_attente' => 0, 'traitees' => 0];
    
    // Calcul des stats supplémentaires avec filter (pour collection)
    $totalCandidatures = $validations->count();
    $candidaturesEnAttente = $validations->where('statut', 'en_attente')->count();
    $candidaturesAcceptees = $validations->where('statut', 'approuve')->count();
    $candidaturesRefusees = $validations->where('statut', 'rejete')->count();
    
    // Filtrer par date (utilisation de filter sur collection)
    $nouvellesCandidatures = $validations->filter(function($v) {
        return $v->created_at && $v->created_at >= Carbon::now()->subDays(7);
    })->count();
    
    $nouvellesAcceptees = $validations->filter(function($v) {
        return $v->statut == 'approuve' && $v->created_at && $v->created_at >= Carbon::now()->subDays(30);
    })->count();
    
    // Variation des refusées
    $refuseesMoisActuel = $validations->filter(function($v) {
        return $v->statut == 'rejete' && $v->created_at && $v->created_at->month == Carbon::now()->month;
    })->count();
    
    $refuseesMoisPrecedent = $validations->filter(function($v) {
        return $v->statut == 'rejete' && $v->created_at && $v->created_at->month == Carbon::now()->subMonth()->month;
    })->count();
    
    $variationRefusees = $refuseesMoisActuel - $refuseesMoisPrecedent;
    $variationRefusees = ($variationRefusees >= 0 ? '+' : '') . $variationRefusees;
@endphp

<!-- Statistiques rapides -->
<div class="validations-stats">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total</span>
            <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
        </div>
        <div class="stat-value">{{ $totalCandidatures }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: #10b981;"></i> +{{ $nouvellesCandidatures }} cette semaine
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">En attente</span>
            <span class="stat-icon"><i class="fas fa-clock"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesEnAttente }}</div>
        <div class="stat-trend">
            <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i> À traiter
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Approuvées</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesAcceptees }}</div>
        <div class="stat-trend">
            <i class="fas fa-check" style="color: #10b981;"></i> +{{ $nouvellesAcceptees }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Rejetées</span>
            <span class="stat-icon"><i class="fas fa-times-circle"></i></span>
        </div>
        <div class="stat-value">{{ $candidaturesRefusees }}</div>
        <div class="stat-trend">
            <i class="fas fa-chart-line" style="color: #64748b;"></i> {{ $variationRefusees }}
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-section">
    <div class="filters-title">
        <i class="fas fa-filter"></i>
        <h3>Filtres avancés</h3>
    </div>
    <form method="GET" action="{{ route('chef-service.validations') }}" id="filterForm">
        <div class="filters-grid">
            <div class="filter-group">
                <label>Statut</label>
                <select name="statut">
                    <option value="all" {{ request('statut') == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="approuve" {{ request('statut') == 'approuve' ? 'selected' : '' }}>Approuvées</option>
                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejetées</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Type de validation</label>
                <select name="type">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Tous les types</option>
                    <option value="bilan" {{ request('type') == 'bilan' ? 'selected' : '' }}>Bilans</option>
                    <option value="inscription" {{ request('type') == 'inscription' ? 'selected' : '' }}>Inscriptions</option>
                    <option value="convention" {{ request('type') == 'convention' ? 'selected' : '' }}>Conventions</option>
                    <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Priorité</label>
                <select name="priorite">
                    <option value="all" {{ request('priorite') == 'all' ? 'selected' : '' }}>Toutes</option>
                    <option value="urgent" {{ request('priorite') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="normal" {{ request('priorite') == 'normal' ? 'selected' : '' }}>Normal</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Date de demande</label>
                <input type="date" name="date" value="{{ request('date') }}">
            </div>
        </div>
        <div class="filter-actions">
            <a href="{{ route('chef-service.validations') }}" class="btn-filter reset">
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
    <a href="{{ route('chef-service.validations', ['statut' => 'all']) }}" class="tab {{ request('statut') == 'all' || !request('statut') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        Toutes ({{ $totalCandidatures }})
    </a>
    <a href="{{ route('chef-service.validations', ['statut' => 'en_attente']) }}" class="tab {{ request('statut') == 'en_attente' ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        En attente ({{ $candidaturesEnAttente }})
    </a>
    <a href="{{ route('chef-service.validations', ['statut' => 'approuve']) }}" class="tab {{ request('statut') == 'approuve' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Approuvées ({{ $candidaturesAcceptees }})
    </a>
    <a href="{{ route('chef-service.validations', ['statut' => 'rejete']) }}" class="tab {{ request('statut') == 'rejete' ? 'active' : '' }}">
        <i class="fas fa-times-circle"></i>
        Rejetées ({{ $candidaturesRefusees }})
    </a>
</div>

<!-- Liste des validations -->
<div class="validations-grid">
    @forelse($validations as $validation)
    <div class="validation-card">
        <div class="card-header">
            <div class="validation-title">
                <h3>{{ $validation->titre ?? 'Demande de validation' }}</h3>
                <div class="validation-type">
                    <i class="fas {{ $validation->icone ?? 'fa-file-alt' }}"></i>
                    {{ ucfirst($validation->type ?? 'Document') }}
                </div>
            </div>
            <span class="validation-badge badge-{{ $validation->statut }}">
                @if($validation->statut == 'en_attente')
                    <i class="fas fa-clock"></i> En attente
                @elseif($validation->statut == 'approuve')
                    <i class="fas fa-check-circle"></i> Approuvée
                @elseif($validation->statut == 'rejete')
                    <i class="fas fa-times-circle"></i> Rejetée
                @endif
                @if($validation->urgent ?? false)
                    <span class="urgent-badge">Urgent</span>
                @endif
            </span>
        </div>
        <div class="validation-info">
            <div class="info-row">
                <i class="fas fa-user-graduate"></i>
                <span><strong>Stagiaire:</strong> {{ $validation->stagiaire_nom ?? ($validation->user->first_name ?? '') }} {{ $validation->user->last_name ?? '' }}</span>
            </div>
            <div class="info-row">
                <i class="fas fa-building"></i>
                <span><strong>Service:</strong> {{ $validation->service_nom ?? ($validation->user->service->nom ?? 'Non assigné') }}</span>
            </div>
            <div class="info-row">
                <i class="fas fa-calendar-alt"></i>
                <span><strong>Date de demande:</strong> {{ isset($validation->created_at) ? Carbon::parse($validation->created_at)->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
            @if($validation->date_reponse)
            <div class="info-row">
                <i class="fas fa-reply"></i>
                <span><strong>Date de réponse:</strong> {{ Carbon::parse($validation->date_reponse)->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($validation->motif_rejet)
            <div class="info-row">
                <i class="fas fa-comment"></i>
                <span><strong>Motif:</strong> {{ $validation->motif_rejet }}</span>
            </div>
            @endif
        </div>
        <div class="validation-footer">
            <div class="validation-date">
                @if($validation->statut == 'approuve')
                    <i class="far fa-check-circle" style="color: #10b981;"></i>
                    Approuvée le {{ Carbon::parse($validation->date_reponse)->format('d/m/Y') }}
                @elseif($validation->statut == 'rejete')
                    <i class="far fa-times-circle" style="color: #ef4444;"></i>
                    Rejetée le {{ Carbon::parse($validation->date_reponse)->format('d/m/Y') }}
                @else
                    @if($validation->urgent ?? false)
                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                        <span style="color: #f59e0b; font-weight: 600;">URGENT - À traiter rapidement</span>
                    @else
                        <i class="far fa-clock"></i>
                        En attente depuis {{ $validation->jours_attente ?? 0 }} jours
                    @endif
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('chef-service.validations.show', $validation->id) }}" class="btn-icon view" title="Voir détails">
                    <i class="fas fa-eye"></i>
                </a>
                @if($validation->statut == 'en_attente')
                    <form action="{{ route('chef-service.validations.approuver', $validation->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon accept" title="Approuver" onclick="return confirm('Approuver cette demande ?')">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    <form action="{{ route('chef-service.validations.refuser', $validation->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon reject" title="Rejeter" onclick="return confirm('Rejeter cette demande ?')">
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
        <h3>Aucune validation</h3>
        <p>Aucune demande de validation trouvée pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(isset($validations) && method_exists($validations, 'links') && $validations->hasPages())
<div class="pagination">
    {{ $validations->appends(request()->query())->links() }}
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
    
    .validations-stats {
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
    
    .validations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .validation-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .validation-card::before {
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
    
    .validation-card:hover {
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
    
    .validation-title h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .validation-type {
        color: var(--gris);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .validation-type i {
        color: var(--bleu);
        font-size: 0.8rem;
    }
    
    .validation-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .badge-en_attente {
        background: rgba(245, 158, 11, 0.1);
        color: var(--orange);
        border: 1px solid var(--orange);
    }
    
    .badge-approuve {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .badge-rejete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }
    
    .urgent-badge {
        background: var(--orange);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        font-size: 0.65rem;
        margin-left: 0.5rem;
    }
    
    .validation-info {
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
    
    .validation-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid var(--gris-clair);
    }
    
    .validation-date {
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
        .validations-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .validations-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .validations-stats {
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
        
        .validation-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .card-header {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .validation-badge {
            align-self: flex-start;
        }
    }
</style>
@endsection
