@extends('layouts.responsable')

@section('title', 'Gestion des Encadreurs - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Gestion des <span>Encadreurs</span> 👨‍🏫</h1>
        <p class="welcome-subtitle">Consultez et gérez tous les Encadreurs de la plateforme</p>
    </div>
    <a href="{{ route('responsable.tuteurs.create') }}" class="btn-add">
        <i class="fas fa-user-plus"></i>
        Ajouter un Encadreur
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
            <span class="stat-title">Total Encadreurs</span>
            <span class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></span>
        </div>
        <div class="stat-value">{{ $totalTuteurs ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: var(--vert);"></i> +{{ $nouveauxTuteurs ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Encadreurs actifs</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $tuteursActifs ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-user-check" style="color: var(--vert);"></i> {{ $pourcentageActifs ?? 0 }}%
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Stagiaires encadrés</span>
            <span class="stat-icon"><i class="fas fa-users"></i></span>
        </div>
        <div class="stat-value">{{ $totalStagiairesEncadres ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: var(--vert);"></i> +{{ $nouveauxStagiairesEncadres ?? 0 }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Moyenne par Encadreurs</span>
            <span class="stat-icon"><i class="fas fa-chart-line"></i></span>
        </div>
        <div class="stat-value">{{ $moyenneParTuteur ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-user-graduate"></i> stagiaires
        </div>
    </div>
</div>

<!-- Recherche et filtres -->
<div class="search-section">
    <form method="GET" action="{{ route('responsable.tuteurs') }}" id="filterForm">
        <div class="search-bar">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Nom, prénom, département, email..." value="{{ request('search') }}">
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
            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="nom" {{ request('sort') == 'nom' ? 'selected' : '' }}>Tri par nom</option>
                <option value="departement" {{ request('sort') == 'departement' ? 'selected' : '' }}>Tri par département</option>
                <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>Tri par expérience</option>
                <option value="stagiaires" {{ request('sort') == 'stagiaires' ? 'selected' : '' }}>Tri par nombre de stagiaires</option>
            </select>
        </div>
    </form>
</div>

<!-- Tabs -->
<div class="tabs">
    <a href="{{ route('responsable.tuteurs', ['statut' => '']) }}" class="tab {{ !request('statut') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        Tous ({{ $totalTuteurs ?? 0 }})
    </a>
    <a href="{{ route('responsable.tuteurs', ['statut' => 'actif']) }}" class="tab {{ request('statut') == 'actif' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Actifs ({{ $tuteursActifs ?? 0 }})
    </a>
</div>

<!-- Liste des tuteurs -->
<div class="tuteurs-grid">
    @forelse($tuteurs ?? [] as $tuteur)
    <div class="tuteur-card">
        <div class="card-header">
            <div class="tuteur-avatar">
                @if($tuteur->avatar)
                    <img src="{{ Illuminate\Support\Facades\Storage::url($tuteur->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-chalkboard-teacher"></i>
                @endif
            </div>
            <div class="tuteur-info">
                <h3>{{ $tuteur->first_name }} {{ $tuteur->last_name }}</h3>
                <div class="tuteur-department">
                    <i class="fas {{ $tuteur->departement == 'Développement Web' ? 'fa-code' : ($tuteur->departement == 'Marketing Digital' ? 'fa-chart-line' : ($tuteur->departement == 'Data Science' ? 'fa-database' : 'fa-building')) }}"></i>
                    {{ $tuteur->departement ?? 'Département non défini' }}
                </div>
            </div>
        </div>
        <div class="tuteur-details">
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $tuteur->email }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-phone"></i>
                <span>{{ $tuteur->phone ?? 'Non renseigné' }}</span>
            </div>
        </div>
        <div class="tuteur-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $tuteur->stagiaires_count ?? 0 }}</div>
                <div class="stat-label">Stagiaires</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $tuteur->evaluations_count ?? 0 }}</div>
                <div class="stat-label">Évaluations</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $tuteur->satisfaction ?? 0 }}%</div>
                <div class="stat-label">Satisfaction</div>
            </div>
        </div>
        <div class="tuteur-footer">
            <div class="tuteur-status">
                @if($tuteur->is_active)
                    @if(($tuteur->stagiaires_count ?? 0) >= 3)
                        <i class="fas fa-circle status-busy"></i>
                        <span>Complet ({{ $tuteur->stagiaires_count }}/3)</span>
                    @elseif(($tuteur->stagiaires_count ?? 0) > 0)
                        <i class="fas fa-circle status-active"></i>
                        <span>Occupé ({{ $tuteur->stagiaires_count }}/3)</span>
                    @else
                        <i class="fas fa-circle status-available"></i>
                        <span>Disponible ({{ 3 - ($tuteur->stagiaires_count ?? 0) }} places)</span>
                    @endif
                @else
                    <i class="fas fa-circle status-inactive"></i>
                    <span>Inactif</span>
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('responsable.tuteurs.edit', $tuteur->id) }}" class="btn-icon edit" title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn-icon assign" title="Assigner un stagiaire" onclick="openAssignModal({{ $tuteur->id }}, '{{ addslashes($tuteur->first_name . ' ' . $tuteur->last_name) }}')">
                    <i class="fas fa-user-plus"></i>
                </button>
                <button class="btn-icon delete" title="Supprimer" onclick="deleteTuteur({{ $tuteur->id }}, '{{ addslashes($tuteur->first_name . ' ' . $tuteur->last_name) }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>Aucun tuteur</h3>
        <p>Aucun tuteur trouvé pour le moment</p>
    </div>
    @endforelse
</div>

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
            <p>Êtes-vous sûr de vouloir supprimer le tuteur :</p>
            <p class="tuteur-name-to-delete" style="font-weight: bold; color: var(--rouge); margin: 1rem 0;"></p>
            <p class="text-warning">Cette action est irréversible et supprimera toutes les données associées.</p>
            @if(isset($stagiairesCount) && $stagiairesCount > 0)
                <p class="text-danger">⚠️ Attention : Ce tuteur encadre actuellement {{ $stagiairesCount }} stagiaire(s).</p>
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

<!-- Modal d'assignation de stagiaire -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-user-plus"></i> Assigner un stagiaire</h2>
            <button class="modal-close" onclick="closeAssignModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('responsable.tuteurs.assign') }}" method="POST" id="assignForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Tuteur</label>
                    <input type="text" id="tuteurNom" class="form-control" readonly>
                    <input type="hidden" name="tuteur_id" id="tuteurId">
                </div>
                <div class="form-group">
                    <label>Stagiaire *</label>
                    <select name="stagiaire_id" class="form-control" required>
                        <option value="">Sélectionner un stagiaire</option>
                        @foreach($stagiairesSansTuteur ?? [] as $stagiaire)
                            <option value="{{ $stagiaire->id }}">{{ $stagiaire->first_name }} {{ $stagiaire->last_name }} - {{ $stagiaire->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date d'assignation</label>
                    <input type="date" name="date_assignation" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="3" placeholder="Commentaire optionnel..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAssignModal()">Annuler</button>
                <button type="submit" class="btn-submit">Assigner le stagiaire</button>
            </div>
        </form>
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
        background: linear-gradient(135deg, #8b5cf6, #c084fc);
        color: var(--blanc);
    }
    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, var(--vert), #6ee7b7);
        color: var(--blanc);
    }
    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #ec4899, #f9a8d4);
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
        border-color: #8b5cf6;
        color: #8b5cf6;
    }
    
    .tab.active {
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        color: var(--blanc);
        border-color: transparent;
    }
    
    .tuteurs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .tuteur-card {
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
    
    .tuteur-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        opacity: 0.03;
        border-radius: 0 0 0 100px;
    }
    
    .tuteur-card:hover {
        transform: translateY(-5px);
        border-color: #8b5cf6;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .tuteur-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 2rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .tuteur-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .tuteur-info h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .tuteur-department {
        color: var(--gris);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .tuteur-department i {
        color: #8b5cf6;
        font-size: 0.8rem;
    }
    
    .tuteur-details {
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
    
    .detail-item:last-child {
        margin-bottom: 0;
    }
    
    .detail-item i {
        width: 20px;
        color: #8b5cf6;
        font-size: 1rem;
    }
    
    .detail-item strong {
        color: var(--noir);
        font-weight: 600;
        margin-right: 0.3rem;
    }
    
    .tuteur-stats {
        display: flex;
        justify-content: space-around;
        margin: 1rem 0;
        padding: 0.5rem;
        background: var(--gris-clair);
        border-radius: 16px;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--noir);
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: var(--gris);
        font-weight: 500;
    }
    
    .tuteur-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1rem;
    }
    
    .tuteur-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }
    
    .status-active {
        color: var(--vert);
    }
    
    .status-busy {
        color: #f59e0b;
    }
    
    .status-available {
        color: #10b981;
    }
    
    .status-inactive {
        color: var(--rouge);
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
    
    .btn-icon.edit:hover {
        background: #3b82f6;
        color: var(--blanc);
    }
    
    .btn-icon.assign:hover {
        background: #10b981;
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
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
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
        .tuteurs-grid {
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
        
        .tuteur-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .card-header {
            flex-direction: column;
            text-align: center;
        }
        
        .tuteur-department {
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .stat-value {
            font-size: 2rem;
        }
        
        .btn-add {
            width: 100%;
            justify-content: center;
        }
        
        .tuteur-stats {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>

<script>
    let deleteTuteurId = null;
    let deleteTuteurNom = null;
    
    function openAssignModal(tuteurId, tuteurNom) {
        document.getElementById('tuteurId').value = tuteurId;
        document.getElementById('tuteurNom').value = tuteurNom;
        document.getElementById('assignModal').classList.add('active');
    }
    
    function closeAssignModal() {
        document.getElementById('assignModal').classList.remove('active');
    }
    
    function deleteTuteur(id, nom) {
        deleteTuteurId = id;
        deleteTuteurNom = nom;
        document.querySelector('.tuteur-name-to-delete').textContent = nom;
        document.getElementById('deleteModal').classList.add('active');
        
        // Mettre à jour l'action du formulaire de suppression
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/responsable/tuteurs/${id}`;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        deleteTuteurId = null;
        deleteTuteurNom = null;
    }
    
    function showTuteurStats(id, nom) {
        showNotification('Statistiques', `Statistiques de ${nom} en cours de chargement...`, 'info');
        setTimeout(() => {
            window.location.href = `/responsable/tuteurs/${id}/statistiques`;
        }, 500);
    }
    
    function showNotification(title, message, type) {
        const notification = document.createElement('div');
        const colors = { success: 'var(--vert)', error: 'var(--rouge)', info: '#8b5cf6' };
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-times-circle' : 'fa-info-circle')}"></i>
            <div>
                <strong>${title}</strong>
                <p>${message}</p>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--blanc);
            border-left: 4px solid ${colors[type]};
            border-radius: 12px;
            padding: 1rem;
            box-shadow: var(--shadow);
            z-index: 10000;
            max-width: 350px;
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => notification.style.animation = 'slideOut 0.3s ease', 3000);
        setTimeout(() => notification.remove(), 3300);
    }
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
