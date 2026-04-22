@extends('layouts.tuteur')

@section('title', 'Gestion des stagiaires - Tuteur')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <h1 class="welcome-title">Gestion des <span>stagiaires</span> 👥</h1>
    <p class="welcome-subtitle">Suivez et encadrez tous vos stagiaires en un coup d'œil</p>
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

<!-- Stats cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total</span>
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalStagiaires ?? 0 }}</div>
        <div class="stat-label">stagiaires</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Actifs</span>
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ $stagiairesActifs ?? 0 }}</div>
        <div class="stat-label">en stage</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Évaluations</span>
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalEvaluations ?? 0 }}</div>
        <div class="stat-label">réalisées</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">À évaluer</span>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $aEvaluer ?? 0 }}</div>
        <div class="stat-label">en attente</div>
    </div>
</div>

<!-- Actions row -->
<div class="header-actions-row">
    <h1>Liste des stagiaires</h1>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <div class="view-toggle">
            <button class="toggle-btn active" onclick="toggleView('grid')" id="gridViewBtn">
                <i class="fas fa-th-large"></i>
            </button>
            <button class="toggle-btn" onclick="toggleView('table')" id="tableViewBtn">
                <i class="fas fa-list"></i>
            </button>
        </div>
        <button class="btn-action btn-secondary" onclick="exportList()">
            <i class="fas fa-download"></i>
            Exporter
        </button>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <div class="filter-group">
        <select id="promotionFilter" onchange="filterStagiaires()">
            <option value="all">Toutes les promotions</option>
            <option value="M2">Master 2</option>
            <option value="M1">Master 1</option>
            <option value="L3">Licence 3</option>
            <option value="L2">Licence 2</option>
        </select>
    </div>
    <div class="filter-group">
        <select id="statutFilter" onchange="filterStagiaires()">
            <option value="all">Tous les statuts</option>
            <option value="actif">Actif</option>
            <option value="termine">Stage terminé</option>
        </select>
    </div>
    <div class="filter-group">
        <select id="entrepriseFilter" onchange="filterStagiaires()">
            <option value="all">Toutes les entreprises</option>
            @foreach($entreprises ?? [] as $entreprise)
                <option value="{{ $entreprise }}">{{ $entreprise }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <input type="text" id="searchInput" placeholder="Rechercher par nom..." onkeyup="filterStagiaires()">
    </div>
</div>

<!-- Grid View -->
<div class="stagiaires-grid" id="gridView">
    @forelse($stagiaires ?? [] as $stagiaire)
    <div class="stagiaire-card" 
         data-nom="{{ strtolower($stagiaire->first_name . ' ' . $stagiaire->last_name) }}"
         data-promotion="{{ $stagiaire->promotion ?? '' }}"
         data-statut="{{ $stagiaire->statut_stage ?? 'actif' }}"
         data-entreprise="{{ $stagiaire->entreprise_nom ?? $stagiaire->entreprise ?? '' }}">
        <div class="stagiaire-header">
            <div class="stagiaire-avatar">
                @if($stagiaire->avatar)
                    <img src="{{ Illuminate\Support\Facades\Storage::url($stagiaire->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="stagiaire-info">
                <h3>{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</h3>
                <span class="badge">{{ $stagiaire->promotion ?? 'Stage' }}</span>
            </div>
        </div>
        <div class="stagiaire-details">
            <div class="detail-item">
                <i class="fas fa-building"></i>
                <span>{{ $stagiaire->entreprise_nom ?? $stagiaire->entreprise ?? 'Non renseigné' }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar"></i>
                <span>Stage du {{ \Carbon\Carbon::parse($stagiaire->stage_debut ?? now())->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($stagiaire->stage_fin ?? now()->addMonths(3))->format('d/m/Y') }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-tasks"></i>
                <span>{{ $stagiaire->mission ?? 'Mission non spécifiée' }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $stagiaire->email }}</span>
            </div>
        </div>
        <div class="stagiaire-progress">
            <div class="progress-item">
                <div class="progress-header">
                    <span>Journal de bord</span>
                    <span>{{ $stagiaire->progression_journal ?? 0 }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stagiaire->progression_journal ?? 0 }}%;"></div>
                </div>
            </div>
            <div class="progress-item">
                <div class="progress-header">
                    <span>Évaluation</span>
                    <span>{{ $stagiaire->progression_evaluation ?? 0 }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stagiaire->progression_evaluation ?? 0 }}%;"></div>
                </div>
            </div>
        </div>
        <div class="stagiaire-footer">
            <a href="{{ route('tuteur.stagiaire.show', $stagiaire->id) }}" class="btn-stagiaire btn-primary">
                <i class="fas fa-eye"></i> Profil
            </a>
            <a href="{{ route('tuteur.evaluations.edit', $stagiaire->id) }}" class="btn-stagiaire btn-outline">
                <i class="fas fa-star"></i> Évaluer
            </a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Aucun stagiaire</h3>
        <p>Vous n'avez pas encore de stagiaires assignés</p>
    </div>
    @endforelse
</div>

<!-- Table View -->
<div class="table-view" id="tableView">
    <table class="documents-table">
        <thead>
            <tr>
                <th>Stagiaire</th>
                <th>Promotion</th>
                <th>Entreprise</th>
                <th>Période</th>
                <th>Journal</th>
                <th>Évaluation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tableViewBody">
            @foreach($stagiaires ?? [] as $stagiaire)
            <tr class="table-row" 
                data-nom="{{ strtolower($stagiaire->first_name . ' ' . $stagiaire->last_name) }}"
                data-promotion="{{ $stagiaire->promotion ?? '' }}"
                data-statut="{{ $stagiaire->statut_stage ?? 'actif' }}"
                data-entreprise="{{ $stagiaire->entreprise_nom ?? $stagiaire->entreprise ?? '' }}">
                <td>
                    <div class="stagiaire-cell">
                        <div class="cell-avatar">
                            @if($stagiaire->avatar)
                                <img src="{{ Illuminate\Support\Facades\Storage::url($stagiaire->avatar) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        {{ $stagiaire->first_name }} {{ $stagiaire->last_name }}
                    </div>
                </td>
                <td>{{ $stagiaire->promotion ?? 'Stage' }}</td>
                <td>{{ $stagiaire->entreprise_nom ?? $stagiaire->entreprise ?? 'Non renseigné' }}</td>
                <td>{{ \Carbon\Carbon::parse($stagiaire->stage_debut ?? now())->format('d/m') }} - {{ \Carbon\Carbon::parse($stagiaire->stage_fin ?? now()->addMonths(3))->format('d/m') }}</td>
                <td>
                    <div class="progress-cell">
                        {{ $stagiaire->progression_journal ?? 0 }}%
                        <div class="progress-mini">
                            <div class="progress-mini-fill" style="width: {{ $stagiaire->progression_journal ?? 0 }}%;"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="progress-cell">
                        {{ $stagiaire->progression_evaluation ?? 0 }}%
                        <div class="progress-mini">
                            <div class="progress-mini-fill" style="width: {{ $stagiaire->progression_evaluation ?? 0 }}%;"></div>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="{{ route('tuteur.stagiaire.show', $stagiaire->id) }}" class="btn-icon-small" title="Voir profil">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('tuteur.evaluations.edit', $stagiaire->id) }}" class="btn-icon-small" title="Évaluer">
                        <i class="fas fa-star"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if(empty($stagiaires) || $stagiaires->count() == 0)
    <div class="empty-state-small">
        <i class="fas fa-users"></i>
        <p>Aucun stagiaire trouvé</p>
    </div>
    @endif
</div>

<!-- Pagination -->
@if(isset($stagiaires) && $stagiaires->count() > 10)
<div class="pagination">
    <button class="page-item" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
    <div id="paginationNumbers" class="pagination-numbers"></div>
    <button class="page-item" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
</div>
@endif

<style>
    /* Styles spécifiques pour la gestion des stagiaires */
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
    
    .header-actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .header-actions-row h1 {
        color: var(--noir);
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }
    
    .toggle-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: 2px solid transparent;
        color: var(--gris-fonce);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .toggle-btn.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }
    
    .toggle-btn:hover {
        border-color: var(--bleu);
    }
    
    .btn-action {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 30px -12px var(--bleu);
        color: var(--blanc);
    }
    
    .btn-secondary {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
        box-shadow: none;
    }
    
    .btn-secondary:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .filters {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 0.8rem 1.2rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        color: var(--noir);
        font-size: 0.95rem;
        font-weight: 500;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .filter-group input {
        cursor: text;
    }
    
    .filter-group select:focus,
    .filter-group input:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .stagiaires-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stagiaire-card {
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stagiaire-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 25px 40px -15px var(--bleu);
    }
    
    .stagiaire-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, var(--gris-clair), var(--blanc));
        border-bottom: 2px solid var(--gris-clair);
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .stagiaire-header::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.1;
        border-radius: 50%;
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
        border: 3px solid var(--blanc);
        box-shadow: 0 10px 20px -8px var(--bleu);
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .stagiaire-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stagiaire-info {
        flex: 1;
    }
    
    .stagiaire-info h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .stagiaire-info .badge {
        display: inline-block;
        padding: 0.2rem 0.8rem;
        background: rgba(59, 130, 246, 0.1);
        color: var(--bleu);
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        border: 1px solid var(--bleu);
        margin-top: 0.3rem;
    }
    
    .stagiaire-details {
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 0.8rem;
        color: var(--gris-fonce);
        font-size: 0.9rem;
    }
    
    .detail-item:last-child {
        margin-bottom: 0;
    }
    
    .detail-item i {
        width: 20px;
        color: var(--bleu);
        font-size: 0.95rem;
    }
    
    .stagiaire-progress {
        padding: 1rem 1.5rem;
        background: var(--gris-clair);
    }
    
    .progress-item {
        margin-bottom: 0.8rem;
    }
    
    .progress-item:last-child {
        margin-bottom: 0;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        color: var(--gris-fonce);
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    
    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--blanc);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert));
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .stagiaire-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        gap: 0.8rem;
        background: var(--blanc);
    }
    
    .btn-stagiaire {
        flex: 1;
        padding: 0.8rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        text-decoration: none;
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
        color: var(--blanc);
    }
    
    .btn-outline {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
    }
    
    .btn-outline:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .table-view {
        display: none;
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        overflow-x: auto;
    }
    
    .table-view.active {
        display: block;
    }
    
    .stagiaire-cell {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    
    .cell-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1rem;
        overflow: hidden;
    }
    
    .cell-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .progress-cell {
        width: 100px;
    }
    
    .progress-mini {
        height: 6px;
        background: var(--gris-clair);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 0.3rem;
    }
    
    .progress-mini-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--bleu), var(--vert));
        border-radius: 10px;
    }
    
    .btn-icon-small {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--gris-clair);
        border: 2px solid transparent;
        color: var(--gris-fonce);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        margin-right: 0.3rem;
    }
    
    .btn-icon-small:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
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
    
    .empty-state-small {
        text-align: center;
        padding: 2rem;
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        margin-top: 1rem;
    }
    
    .empty-state-small i {
        font-size: 2rem;
        color: var(--vert);
        margin-bottom: 0.5rem;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    
    .page-item {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        color: var(--gris-fonce);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 600;
    }
    
    .page-item.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border-color: transparent;
    }
    
    .page-item:hover {
        border-color: var(--bleu);
    }
    
    @media (max-width: 768px) {
        .stagiaires-grid {
            grid-template-columns: 1fr;
        }
        
        .header-actions-row {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .view-toggle {
            margin-left: 0;
        }
        
        .filters {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .stagiaire-footer {
            flex-direction: column;
        }
        
        .table-view {
            font-size: 0.85rem;
        }
        
        th, td {
            padding: 0.8rem 0.5rem;
        }
    }
</style>

<script>
    let currentPage = 1;
    const rowsPerPage = 10;
    let currentView = 'grid';
    
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    
    function toggleView(view) {
        const gridView = document.getElementById('gridView');
        const tableView = document.getElementById('tableView');
        const gridBtn = document.getElementById('gridViewBtn');
        const tableBtn = document.getElementById('tableViewBtn');
        currentView = view;
        
        if (view === 'grid') {
            gridView.style.display = 'grid';
            tableView.classList.remove('active');
            gridBtn.classList.add('active');
            tableBtn.classList.remove('active');
        } else {
            gridView.style.display = 'none';
            tableView.classList.add('active');
            tableBtn.classList.add('active');
            gridBtn.classList.remove('active');
        }
        updatePagination();
    }
    
    function filterStagiaires() {
        const promotion = document.getElementById('promotionFilter').value;
        const statut = document.getElementById('statutFilter').value;
        const entreprise = document.getElementById('entrepriseFilter').value;
        const search = document.getElementById('searchInput').value.toLowerCase();
        
        // Filtrer la vue grille
        const cards = document.querySelectorAll('.stagiaire-card');
        cards.forEach(card => {
            const cardPromotion = card.dataset.promotion;
            const cardStatut = card.dataset.statut;
            const cardEntreprise = card.dataset.entreprise;
            const cardNom = card.dataset.nom;
            
            let show = true;
            if (promotion !== 'all' && cardPromotion !== promotion) show = false;
            if (statut !== 'all' && cardStatut !== statut) show = false;
            if (entreprise !== 'all' && cardEntreprise !== entreprise) show = false;
            if (search && !cardNom.includes(search)) show = false;
            
            card.style.display = show ? 'block' : 'none';
        });
        
        // Filtrer la vue tableau
        const rows = document.querySelectorAll('.table-row');
        rows.forEach(row => {
            const rowPromotion = row.dataset.promotion;
            const rowStatut = row.dataset.statut;
            const rowEntreprise = row.dataset.entreprise;
            const rowNom = row.dataset.nom;
            
            let show = true;
            if (promotion !== 'all' && rowPromotion !== promotion) show = false;
            if (statut !== 'all' && rowStatut !== statut) show = false;
            if (entreprise !== 'all' && rowEntreprise !== entreprise) show = false;
            if (search && !rowNom.includes(search)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
        
        currentPage = 1;
        updatePagination();
    }
    
    function updatePagination() {
        const items = currentView === 'grid' 
            ? Array.from(document.querySelectorAll('.stagiaire-card')).filter(item => item.style.display !== 'none')
            : Array.from(document.querySelectorAll('.table-row')).filter(item => item.style.display !== 'none');
        
        const totalPages = Math.ceil(items.length / rowsPerPage);
        
        items.forEach((item, index) => {
            const page = Math.floor(index / rowsPerPage) + 1;
            item.style.display = page === currentPage ? (currentView === 'grid' ? 'block' : '') : 'none';
        });
        
        const paginationNumbers = document.getElementById('paginationNumbers');
        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = 'page-item' + (i === currentPage ? ' active' : '');
                btn.textContent = i;
                btn.onclick = () => goToPage(i);
                paginationNumbers.appendChild(btn);
            }
        }
    }
    
    function goToPage(page) {
        currentPage = page;
        updatePagination();
    }
    
    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    }
    
    function nextPage() {
        const items = currentView === 'grid' 
            ? Array.from(document.querySelectorAll('.stagiaire-card')).filter(item => item.style.display !== 'none')
            : Array.from(document.querySelectorAll('.table-row')).filter(item => item.style.display !== 'none');
        const totalPages = Math.ceil(items.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    }
    
    function exportList() {
        alert('Export de la liste des stagiaires en cours...');
    }
    
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const burgerMenu = document.querySelector('.burger-menu');
        
        if (window.innerWidth <= 768) {
            if (sidebar && burgerMenu && !sidebar.contains(event.target) && !burgerMenu.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        updatePagination();
    });
</script>
@endsection
