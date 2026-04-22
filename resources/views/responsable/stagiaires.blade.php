@extends('layouts.responsable')

@section('title', 'Gestion des stagiaires - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Gestion des <span>stagiaires</span> 👥</h1>
        <p class="welcome-subtitle">Consultez et gérez tous les stagiaires de la plateforme</p>
    </div>
    <a href="{{ route('responsable.stagiaires.create') }}" class="btn-add">
        <i class="fas fa-user-plus"></i>
        Ajouter un stagiaire
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
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total stagiaires</span>
            <span class="stat-icon"><i class="fas fa-users"></i></span>
        </div>
        <div class="stat-value">{{ $totalStagiaires ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: var(--vert);"></i> +{{ $nouveauxStagiaires ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">En stage actuellement</span>
            <span class="stat-icon"><i class="fas fa-clock"></i></span>
        </div>
        <div class="stat-value">{{ $stagiairesActifs ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-check-circle" style="color: var(--vert);"></i> {{ $stagesEnCours ?? 0 }} en cours
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stages terminés</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $stagiairesTermines ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-calendar-check" style="color: var(--gris);"></i> {{ $stagesTerminesCeMois ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Moyenne satisfaction</span>
            <span class="stat-icon"><i class="fas fa-star"></i></span>
        </div>
        <div class="stat-value">{{ number_format($satisfactionMoyenne ?? 0, 1) }}</div>
        <div class="stat-trend">
            <i class="fas fa-star" style="color: var(--orange);"></i> /5
        </div>
    </div>
</div>

<!-- Recherche et filtres -->
<div class="search-section">
    <form method="GET" action="{{ route('responsable.stagiaires') }}" id="filterForm">
        <div class="search-bar">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Nom, prénom, entreprise, tuteur..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i>
                Rechercher
            </button>
        </div>
        <div class="filters-row">
            <select name="departement" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les départements</option>
                @foreach($departements ?? [] as $departement)
                    <option value="{{ $departement }}" {{ request('departement') == $departement ? 'selected' : '' }}>{{ $departement }}</option>
                @endforeach
            </select>
            <select name="statut" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="a_venir" {{ request('statut') == 'a_venir' ? 'selected' : '' }}>À venir</option>
            </select>
            <select name="tuteur" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les tuteurs</option>
                @foreach($tuteurs ?? [] as $tuteur)
                    <option value="{{ $tuteur->id }}" {{ request('tuteur') == $tuteur->id ? 'selected' : '' }}>{{ $tuteur->first_name }} {{ $tuteur->last_name }}</option>
                @endforeach
            </select>
            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="nom" {{ request('sort') == 'nom' ? 'selected' : '' }}>Tri par nom</option>
                <option value="date_debut" {{ request('sort') == 'date_debut' ? 'selected' : '' }}>Tri par date début</option>
                <option value="date_fin" {{ request('sort') == 'date_fin' ? 'selected' : '' }}>Tri par date fin</option>
                <option value="progression" {{ request('sort') == 'progression' ? 'selected' : '' }}>Tri par progression</option>
            </select>
        </div>
    </form>
</div>

<!-- Tabs -->
<div class="tabs">
    <a href="{{ route('responsable.stagiaires', ['statut' => '']) }}" class="tab {{ !request('statut') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        Tous ({{ $totalStagiaires ?? 0 }})
    </a>
    <a href="{{ route('responsable.stagiaires', ['statut' => 'en_cours']) }}" class="tab {{ request('statut') == 'en_cours' ? 'active' : '' }}">
        <i class="fas fa-play-circle"></i>
        En cours ({{ $stagiairesActifs ?? 0 }})
    </a>
    <a href="{{ route('responsable.stagiaires', ['statut' => 'termine']) }}" class="tab {{ request('statut') == 'termine' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Terminés ({{ $stagiairesTermines ?? 0 }})
    </a>
</div>

<!-- Liste des stagiaires -->
<div class="stagiaires-grid">
    @forelse($stagiaires ?? [] as $stagiaire)
    <div class="stagiaire-card">
        <div class="card-header">
            <div class="stagiaire-avatar">
                @if($stagiaire->avatar)
                    <img src="{{ Illuminate\Support\Facades\Storage::url($stagiaire->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="stagiaire-info">
                <h3>{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</h3>
                <div class="stagiaire-role">
                    <i class="fas fa-graduation-cap"></i>
                    {{ $stagiaire->formation ?? 'Formation non renseignée' }}
                </div>
            </div>
        </div>
        <div class="stagiaire-details">
            <div class="detail-item">
                <i class="fas fa-building"></i>
                <span><strong>Entreprise:</strong> {{ $stagiaire->entreprise ?? 'Non renseignée' }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar-alt"></i>
                <span><strong>Période:</strong> 
                    @if($stagiaire->stage_debut && $stagiaire->stage_fin)
                        {{ \Carbon\Carbon::parse($stagiaire->stage_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($stagiaire->stage_fin)->format('d/m/Y') }}
                    @else
                        Non défini
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span><strong>Tuteur:</strong> {{ $stagiaire->tuteur->first_name ?? '' }} {{ $stagiaire->tuteur->last_name ?? 'Non assigné' }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $stagiaire->email }}</span>
            </div>
        </div>
        <div class="stagiaire-progress">
            <div class="progress-header">
                <span>Progression du stage</span>
                <span>{{ $stagiaire->progression ?? 0 }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $stagiaire->progression ?? 0 }}%;"></div>
            </div>
        </div>
        <div class="stagiaire-footer">
            <div class="stagiaire-tuteur">
                @php
                    $statutStage = $stagiaire->stage_statut ?? $stagiaire->stage->statut ?? 'a_venir';
                @endphp
                @if($statutStage == 'en_cours')
                    <i class="fas fa-check-circle" style="color: var(--vert);"></i>
                    Actif
                @elseif($statutStage == 'termine')
                    <i class="fas fa-check-circle" style="color: var(--gris);"></i>
                    Terminé
                @else
                    <i class="fas fa-clock" style="color: var(--orange);"></i>
                    À venir
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('responsable.stagiaire.show', $stagiaire->id) }}" class="btn-icon" title="Voir détails">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('responsable.stagiaire.edit', $stagiaire->id) }}" class="btn-icon" title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn-icon delete" title="Supprimer" onclick="deleteStagiaire({{ $stagiaire->id }}, '{{ addslashes($stagiaire->first_name . ' ' . $stagiaire->last_name) }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Aucun stagiaire</h3>
        <p>Aucun stagiaire trouvé pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(isset($stagiaires) && $stagiaires->count() > 10)
<div class="pagination">
    {{ $stagiaires->appends(request()->query())->links() }}
</div>
@endif

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-trash-alt"></i> Confirmer la suppression</h2>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer le stagiaire :</p>
            <p class="stagiaire-name-to-delete" style="font-weight: bold; color: var(--rouge); margin: 1rem 0;"></p>
            <p class="text-warning">Cette action est irréversible et supprimera toutes les données associées.</p>
            @if(isset($stagiaire->stage) && $stagiaire->stage)
                <p class="text-danger">⚠️ Attention : Ce stagiaire a un stage en cours.</p>
            @endif
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeDeleteModal()">Annuler</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-submit btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<style>
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
        background: linear-gradient(135deg, var(--vert), #6ee7b7);
        color: var(--blanc);
    }
    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, var(--orange), #fcd34d);
        color: var(--blanc);
    }
    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #a855f7, #c084fc);
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
    
    .search-section {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
    }
    
    .search-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .search-input {
        flex: 1;
        display: flex;
        align-items: center;
        background: var(--gris-clair);
        border-radius: 50px;
        padding: 0.5rem 1rem 0.5rem 1.5rem;
        gap: 0.8rem;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .search-input:focus-within {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .search-input i {
        color: var(--gris);
        font-size: 1rem;
    }
    
    .search-input input {
        background: transparent;
        border: none;
        color: var(--noir);
        font-size: 1rem;
        width: 100%;
        outline: none;
    }
    
    .btn-search {
        padding: 0 2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .filters-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-select {
        padding: 0.8rem 1.5rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 40px;
        color: var(--noir);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s ease;
        min-width: 180px;
        cursor: pointer;
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
    }
    
    .stagiaires-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stagiaire-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .stagiaire-card::before {
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
    
    .stagiaire-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .stagiaire-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 2rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .stagiaire-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stagiaire-info h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .stagiaire-role {
        color: var(--gris);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .stagiaire-details {
        margin: 1rem 0;
        padding: 1rem 0;
        border-top: 2px solid var(--gris-clair);
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 0.8rem;
        color: var(--gris-fonce);
        font-size: 0.95rem;
    }
    
    .detail-item i {
        width: 20px;
        color: var(--bleu);
    }
    
    .detail-item strong {
        color: var(--noir);
        font-weight: 600;
        margin-right: 0.3rem;
    }
    
    .stagiaire-progress {
        margin: 1rem 0;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        color: var(--gris-fonce);
        font-size: 0.85rem;
        margin-bottom: 0.3rem;
    }
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--gris-clair);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert));
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .stagiaire-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1rem;
    }
    
    .stagiaire-tuteur {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris);
        font-size: 0.85rem;
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
        text-decoration: none;
    }
    
    .btn-icon:hover {
        transform: translateY(-2px);
    }
    
    .btn-icon:hover {
        background: var(--bleu);
        color: var(--blanc);
    }
    
    .btn-icon.delete:hover {
        background: #ef4444;
        color: var(--blanc);
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: var(--blanc);
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .modal-header h2 {
        color: var(--noir);
        font-size: 1.3rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .modal-header h2 i {
        color: var(--bleu);
    }
    
    .modal-close {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: var(--rouge);
        color: white;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    .btn-cancel, .btn-submit {
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-cancel {
        background: var(--gris-clair);
        color: var(--gris-fonce);
    }
    
    .btn-cancel:hover {
        background: var(--blanc);
        border: 1px solid var(--gris);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, var(--rouge), #dc2626);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }
    
    .btn-danger:hover {
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
    }
    
    .text-warning {
        color: #f59e0b;
        margin-top: 0.5rem;
    }
    
    .text-danger {
        color: var(--rouge);
        margin-top: 0.5rem;
        font-weight: 600;
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
    
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .stagiaires-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-section {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .search-bar {
            flex-direction: column;
        }
        
        .btn-search {
            width: 100%;
            justify-content: center;
            padding: 1rem;
        }
        
        .filters-row {
            flex-direction: column;
        }
        
        .filter-select {
            width: 100%;
        }
        
        .tabs {
            justify-content: center;
        }
        
        .stagiaire-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .card-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    let deleteStagiaireId = null;
    let deleteStagiaireNom = null;
    
    function deleteStagiaire(id, nom) {
        deleteStagiaireId = id;
        deleteStagiaireNom = nom;
        document.querySelector('.stagiaire-name-to-delete').textContent = nom;
        document.getElementById('deleteModal').classList.add('active');
        
        // Mettre à jour l'action du formulaire de suppression
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/responsable/stagiaire/${id}`;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        deleteStagiaireId = null;
        deleteStagiaireNom = null;
    }
    
    function showStats(id, nom) {
        const notification = document.createElement('div');
        notification.className = 'stats-notification';
        notification.innerHTML = `
            <i class="fas fa-chart-line"></i>
            <div class="stats-content">
                <strong>Statistiques de ${nom}</strong>
                <p>Fonctionnalité en cours de développement</p>
                <small>Les statistiques détaillées seront disponibles prochainement</small>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--blanc);
            border-left: 4px solid var(--bleu);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: var(--shadow);
            z-index: 10000;
            max-width: 350px;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Ajouter les animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    document.addEventListener('DOMContentLoaded', () => {
        const bars = document.querySelectorAll('.progress-fill');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
</script>
@endsection
