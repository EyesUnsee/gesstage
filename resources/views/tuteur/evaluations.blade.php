@extends('layouts.tuteur')

@section('title', 'Gestion des évaluations - Tuteur')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <h1 class="welcome-title">Gestion des <span>évaluations</span> ⭐</h1>
    <p class="welcome-subtitle">Suivez et gérez toutes les évaluations de vos stagiaires</p>
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
        <div class="stat-icon">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-info">
            <h3>Total</h3>
            <div class="number">{{ $totalEvaluations ?? 0 }}</div>
            <div class="trend trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $nouvellesEvaluations ?? 0 }} cette semaine
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>Réalisées</h3>
            <div class="number">{{ $evaluationsRealisees ?? 0 }}</div>
            <div class="trend trend-up">
                <i class="fas fa-arrow-up"></i> +{{ $nouvellesRealisees ?? 0 }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>En attente</h3>
            <div class="number">{{ $evaluationsEnAttente ?? 0 }}</div>
            <div class="trend trend-down">
                <i class="fas fa-arrow-down"></i> -{{ $attenteDiminution ?? 0 }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h3>En retard</h3>
            <div class="number">{{ $evaluationsEnRetard ?? 0 }}</div>
            <div class="trend trend-down">
                <i class="fas fa-arrow-down"></i> -{{ $retardDiminution ?? 0 }}
            </div>
        </div>
    </div>
</div>

<!-- Actions row -->
<div class="header-actions-row">
    <h1>Liste des évaluations</h1>
    <div style="display: flex; gap: 1rem;">
        <button class="btn-action btn-secondary" onclick="exportEvaluations()">
            <i class="fas fa-download"></i>
            Exporter
        </button>
        
        @php
            use Illuminate\Support\Facades\Storage;
            $mesStagiaires = App\Models\User::where('role', 'candidat')
                ->where('tuteur_id', auth()->id())
                ->get();
        @endphp
        
        @if($mesStagiaires->count() > 0)
        <div class="dropdown">
            <button class="btn-action dropdown-toggle" onclick="toggleDropdown()">
                <i class="fas fa-plus"></i>
                Nouvelle évaluation
                <i class="fas fa-chevron-down" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
            </button>
            <div class="dropdown-menu" id="stagiaireDropdown">
                @foreach($mesStagiaires as $stagiaire)
                    <a href="{{ route('tuteur.evaluations.create', $stagiaire->id) }}" class="dropdown-item">
                        <div class="dropdown-avatar">
                            @if($stagiaire->avatar)
                                <img src="{{ Storage::url($stagiaire->avatar) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <i class="fas fa-user-graduate"></i>
                            @endif
                        </div>
                        <div class="dropdown-info">
                            <span class="dropdown-name">{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</span>
                            <span class="dropdown-email">{{ $stagiaire->email }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @else
        <button class="btn-action btn-secondary" disabled title="Aucun stagiaire assigné">
            <i class="fas fa-plus"></i>
            Nouvelle évaluation
        </button>
        @endif
    </div>
</div>

<!-- Tabs -->
<div class="tabs">
    <div class="tab active" onclick="switchTab('all')">
        <i class="fas fa-list"></i>
        Toutes
    </div>
    <div class="tab" onclick="switchTab('pending')">
        <i class="fas fa-clock"></i>
        En attente
        <span class="tab-badge">{{ $evaluationsEnAttente ?? 0 }}</span>
    </div>
    <div class="tab" onclick="switchTab('late')">
        <i class="fas fa-exclamation-triangle"></i>
        En retard
        <span class="tab-badge">{{ $evaluationsEnRetard ?? 0 }}</span>
    </div>
    <div class="tab" onclick="switchTab('done')">
        <i class="fas fa-check-circle"></i>
        Terminées
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <div class="filter-group">
        <select id="stagiaireFilter" onchange="filterEvaluations()">
            <option value="all">Tous les stagiaires</option>
            @foreach($stagiaires ?? [] as $stagiaire)
                <option value="{{ $stagiaire->id }}">{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <select id="typeFilter" onchange="filterEvaluations()">
            <option value="all">Tous les types</option>
            <option value="mi-parcours">Mi-parcours</option>
            <option value="finale">Finale</option>
            <option value="projet">Projet</option>
        </select>
    </div>
    <div class="filter-group">
        <select id="dateFilter" onchange="filterEvaluations()">
            <option value="all">Toutes les dates</option>
            <option value="week">Cette semaine</option>
            <option value="month">Ce mois</option>
            <option value="3months">3 derniers mois</option>
        </select>
    </div>
    <div class="filter-group">
        <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="filterEvaluations()">
    </div>
</div>

<!-- Evaluations Grid -->
<div class="evaluations-grid" id="evaluationsGrid">
    @forelse($evaluations ?? [] as $evaluation)
    <div class="evaluation-card" data-status="{{ $evaluation->statut ?? 'pending' }}" 
         data-stagiaire="{{ $evaluation->candidat_id }}"
         data-type="{{ $evaluation->type ?? '' }}"
         data-nom="{{ strtolower($evaluation->candidat->first_name ?? '') }} {{ strtolower($evaluation->candidat->last_name ?? '') }}">
        <div class="evaluation-header">
            <div class="evaluation-avatar">
                @if($evaluation->candidat && $evaluation->candidat->avatar)
                    <img src="{{ Storage::url($evaluation->candidat->avatar) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="evaluation-info">
                <h3>{{ $evaluation->candidat->first_name ?? '' }} {{ $evaluation->candidat->last_name ?? '' }}</h3>
                <div class="evaluation-meta">
                    <span><i class="fas fa-building"></i> {{ $evaluation->candidat->entreprise ?? 'Entreprise' }}</span>
                    <span><i class="fas fa-tag"></i> {{ $evaluation->type ?? 'Évaluation' }}</span>
                </div>
            </div>
            <div class="evaluation-status status-{{ $evaluation->statut ?? 'pending' }}">
                @if(($evaluation->statut ?? 'pending') == 'pending')
                    À faire
                @elseif(($evaluation->statut ?? 'pending') == 'done')
                    Terminée
                @else
                    En retard
                @endif
            </div>
        </div>
        <div class="evaluation-criteria">
            @php
                $criteria = $evaluation->criteria;
                if (is_string($criteria)) {
                    $criteria = json_decode($criteria, true);
                }
                if (empty($criteria) || !is_array($criteria)) {
                    $criteria = [
                        ['nom' => 'Compétences techniques', 'note' => null],
                        ['nom' => 'Intégration', 'note' => null],
                        ['nom' => 'Autonomie', 'note' => null],
                    ];
                }
            @endphp
            @foreach($criteria as $criterium)
            <div class="criteria-item">
                <span class="criteria-name">{{ $criterium['nom'] ?? 'Critère' }}</span>
                <div class="criteria-value">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if(isset($criterium['note']) && $criterium['note'] && $i <= floor($criterium['note']))
                                <i class="fas fa-star"></i>
                            @elseif(isset($criterium['note']) && $criterium['note'] && $i - 0.5 <= $criterium['note'])
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="score">{{ isset($criterium['note']) && $criterium['note'] ? number_format($criterium['note'], 1) . '/5' : '-' }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="evaluation-footer">
            <div class="evaluation-date">
                @if($evaluation->statut == 'done')
                    <i class="fas fa-check-circle" style="color: var(--vert);"></i> 
                    Réalisée le {{ \Carbon\Carbon::parse($evaluation->date_evaluation ?? $evaluation->updated_at)->format('d/m/Y') }}
                @else
                    <i class="far fa-calendar-alt"></i> 
                    À faire avant le {{ \Carbon\Carbon::parse($evaluation->date_limite ?? now()->addDays(7))->format('d/m/Y') }}
                @endif
            </div>
            @if($evaluation->statut == 'done')
                <button class="btn-eval btn-outline" onclick="viewEvaluation({{ $evaluation->id }})">
                    <i class="fas fa-eye"></i> Voir
                </button>
            @else
                <button class="btn-eval" onclick="openEvalModal({{ $evaluation->id }})">
                    <i class="fas fa-pen"></i> Évaluer
                </button>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-star-half-alt"></i>
        <h3>Aucune évaluation</h3>
        <p>Aucune évaluation n'a été créée pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Table View -->
<div class="table-container">
    <table class="evaluations-table">
        <thead>
             <tr>
                <th>Stagiaire</th>
                <th>Entreprise</th>
                <th>Type</th>
                <th>Date limite</th>
                <th>Statut</th>
                <th>Note</th>
                <th>Actions</th>
             </tr>
        </thead>
        <tbody id="tableViewBody">
            @foreach($evaluations ?? [] as $evaluation)
            <tr class="table-row" 
                data-status="{{ $evaluation->statut ?? 'pending' }}"
                data-stagiaire="{{ $evaluation->candidat_id }}"
                data-type="{{ $evaluation->type ?? '' }}"
                data-nom="{{ strtolower($evaluation->candidat->first_name ?? '') }} {{ strtolower($evaluation->candidat->last_name ?? '') }}">
                <td>
                    <div class="stagiaire-cell">
                        <div class="cell-avatar">
                            @if($evaluation->candidat && $evaluation->candidat->avatar)
                                <img src="{{ Storage::url($evaluation->candidat->avatar) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        {{ $evaluation->candidat->first_name ?? '' }} {{ $evaluation->candidat->last_name ?? '' }}
                    </div>
                </td>
                <td>{{ $evaluation->candidat->entreprise ?? 'Entreprise' }}</td>
                <td>{{ $evaluation->type ?? 'Évaluation' }}</td>
                <td>{{ \Carbon\Carbon::parse($evaluation->date_limite ?? now()->addDays(7))->format('d/m/Y') }}</td>
                <td>
                    <span class="evaluation-status status-{{ $evaluation->statut ?? 'pending' }}">
                        @if(($evaluation->statut ?? 'pending') == 'pending')
                            À faire
                        @elseif(($evaluation->statut ?? 'pending') == 'done')
                            Terminée
                        @else
                            En retard
                        @endif
                    </span>
                </td>
                <td>
                    @php
                        $note = $evaluation->note ?? null;
                    @endphp
                    @if($note)
                        <div class="stars-mini">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($note))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $note)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        {{ number_format($note, 1) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($evaluation->statut == 'done')
                        <button class="btn-icon-small" onclick="viewEvaluation({{ $evaluation->id }})">
                            <i class="fas fa-eye"></i>
                        </button>
                    @else
                        <button class="btn-icon-small" onclick="openEvalModal({{ $evaluation->id }})">
                            <i class="fas fa-pen"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if(empty($evaluations) || $evaluations->count() == 0)
    <div class="empty-state-small">
        <i class="fas fa-star-half-alt"></i>
        <p>Aucune évaluation trouvée</p>
    </div>
    @endif
</div>

<!-- Pagination -->
@if(isset($evaluations) && $evaluations->count() > 10)
<div class="pagination">
    <button class="page-item" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
    <div id="paginationNumbers" class="pagination-numbers"></div>
    <button class="page-item" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
</div>
@endif

<!-- Modal d'évaluation -->
<div class="modal" id="evalModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-star"></i> Évaluation - <span id="modalStagiaire">Stagiaire</span></h2>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('tuteur.evaluations.update', '__ID__') }}" method="POST" id="evaluationForm">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div id="criteriaContainer"></div>

                <div style="margin-top: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--noir);">Commentaire</label>
                    <textarea class="comment-input" name="commentaire" rows="4" placeholder="Ajoutez un commentaire sur le stagiaire..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-action">
                    <i class="fas fa-save"></i>
                    Enregistrer l'évaluation
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Variables */
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

    /* Alert styles */
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
    
    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1.2rem;
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
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.8rem;
    }
    
    .stat-info {
        flex: 1;
    }
    
    .stat-info h3 {
        color: var(--gris);
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    
    .stat-info .number {
        color: var(--noir);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
    }
    
    .stat-info .trend {
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .trend-up {
        color: var(--vert);
    }
    
    .trend-down {
        color: var(--rouge);
    }
    
    /* Header actions */
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
    
    /* Dropdown */
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-toggle {
        cursor: pointer;
    }
    
    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: white;
        min-width: 280px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border-radius: 16px;
        z-index: 1000;
        margin-top: 0.5rem;
        border: 1px solid var(--gris-clair);
        overflow: hidden;
        animation: dropdownFadeIn 0.2s ease;
    }
    
    .dropdown-menu.show {
        display: block;
    }
    
    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem 1rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--gris-clair);
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-item:hover {
        background-color: var(--gris-clair);
    }
    
    .dropdown-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .dropdown-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .dropdown-info {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .dropdown-name {
        font-weight: 600;
        color: var(--noir);
        font-size: 0.9rem;
    }
    
    .dropdown-email {
        font-size: 0.75rem;
        color: var(--gris);
    }
    
    /* Tabs */
    .tabs {
        display: flex;
        gap: 0.5rem;
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
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .tab i {
        color: var(--bleu);
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
    
    .tab.active i {
        color: var(--blanc);
    }
    
    .tab-badge {
        background: var(--rouge);
        color: var(--blanc);
        padding: 0.2rem 0.6rem;
        border-radius: 30px;
        font-size: 0.7rem;
        margin-left: 0.3rem;
    }
    
    /* Filters */
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
        min-width: 180px;
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
    
    /* Evaluations Grid */
    .evaluations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .evaluation-card {
        background: var(--blanc);
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .evaluation-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
    }
    
    .evaluation-header {
        padding: 1.2rem 1.5rem;
        background: linear-gradient(135deg, var(--gris-clair), var(--blanc));
        border-bottom: 2px solid var(--gris-clair);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .evaluation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.4rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .evaluation-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .evaluation-info {
        flex: 1;
    }
    
    .evaluation-info h3 {
        color: var(--noir);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    
    .evaluation-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--gris);
        flex-wrap: wrap;
    }
    
    .evaluation-meta i {
        color: var(--bleu);
        margin-right: 0.3rem;
    }
    
    .evaluation-status {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    
    .status-done {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .status-late {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }
    
    .evaluation-criteria {
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .criteria-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
        font-size: 0.9rem;
    }
    
    .criteria-item:last-child {
        margin-bottom: 0;
    }
    
    .criteria-name {
        color: var(--gris-fonce);
    }
    
    .criteria-value {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stars {
        color: #fbbf24;
        font-size: 0.8rem;
    }
    
    .stars i {
        margin-right: 2px;
    }
    
    .score {
        font-weight: 600;
        color: var(--noir);
    }
    
    .evaluation-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .evaluation-date {
        font-size: 0.8rem;
        color: var(--gris);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-eval {
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-eval:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
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
    
    /* Table View */
    .table-container {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        overflow-x: auto;
        margin-bottom: 2rem;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 1rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    td {
        padding: 1rem;
        border-bottom: 1px solid var(--gris-clair);
        color: var(--gris-fonce);
    }
    
    tr:last-child td {
        border-bottom: none;
    }
    
    tr:hover td {
        background: var(--gris-clair);
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
    
    .stars-mini {
        color: #fbbf24;
        font-size: 0.8rem;
        display: inline-block;
        margin-right: 0.3rem;
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
    
    /* Empty states */
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
    
    /* Pagination */
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
    
    /* Modal */
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
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow);
        border: 2px solid var(--gris-clair);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .modal-header h2 {
        color: var(--noir);
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .modal-header h2 i {
        color: var(--bleu);
    }
    
    .modal-close {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1.2rem;
    }
    
    .modal-close:hover {
        background: var(--rouge);
        color: var(--blanc);
    }
    
    .modal-body {
        margin-bottom: 1.5rem;
    }
    
    .modal-footer {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    .rating-item {
        background: var(--gris-clair);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .rating-header label {
        font-weight: 600;
        color: var(--noir);
    }
    
    .rating-stars {
        display: flex;
        gap: 0.5rem;
    }
    
    .rating-stars i {
        font-size: 1.5rem;
        color: var(--gris);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .rating-stars i.active,
    .rating-stars i.fas.fa-star {
        color: #fbbf24;
    }
    
    .rating-stars i:hover {
        transform: scale(1.1);
    }
    
    .comment-input {
        width: 100%;
        padding: 1rem;
        background: var(--blanc);
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        color: var(--noir);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
        min-height: 100px;
        resize: vertical;
    }
    
    .comment-input:focus {
        border-color: var(--bleu);
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .evaluations-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .evaluations-grid {
            grid-template-columns: 1fr;
        }
        
        .tabs {
            flex-direction: column;
        }
        
        .tab {
            width: 100%;
            justify-content: center;
        }
        
        .evaluation-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .btn-eval {
            width: 100%;
            justify-content: center;
        }
        
        .evaluation-header {
            flex-wrap: wrap;
        }
        
        .evaluation-status {
            margin-left: auto;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer .btn-action {
            width: 100%;
            justify-content: center;
        }
        
        .rating-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            gap: 1rem;
        }
        
        .header-actions-row {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .filters {
            padding: 1rem;
        }
        
        .filter-group {
            min-width: 100%;
        }
        
        .evaluation-header {
            flex-direction: column;
            text-align: center;
        }
        
        .evaluation-meta {
            justify-content: center;
        }
        
        .evaluation-status {
            margin-left: 0;
        }
        
        .criteria-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .criteria-value {
            width: 100%;
            justify-content: space-between;
        }
        
        .table-container {
            font-size: 0.8rem;
        }
        
        th, td {
            padding: 0.5rem;
        }
    }
</style>

<script>
    let currentEvaluationId = null;
    let currentPage = 1;
    const rowsPerPage = 10;
    
    function toggleDropdown() {
        const dropdown = document.getElementById('stagiaireDropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }
    
    // Fermer le dropdown si on clique ailleurs
    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
            const dropdowns = document.getElementsByClassName('dropdown-menu');
            for (let i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].classList.contains('show')) {
                    dropdowns[i].classList.remove('show');
                }
            }
        }
    }
    
    function switchTab(tab) {
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(t => t.classList.remove('active'));
        
        if (tab === 'all') tabs[0].classList.add('active');
        else if (tab === 'pending') tabs[1].classList.add('active');
        else if (tab === 'late') tabs[2].classList.add('active');
        else if (tab === 'done') tabs[3].classList.add('active');

        const cards = document.querySelectorAll('.evaluation-card');
        cards.forEach(card => {
            if (tab === 'all') card.style.display = 'block';
            else if (tab === 'pending' && card.dataset.status === 'pending') card.style.display = 'block';
            else if (tab === 'late' && card.dataset.status === 'late') card.style.display = 'block';
            else if (tab === 'done' && card.dataset.status === 'done') card.style.display = 'block';
            else card.style.display = 'none';
        });
        
        const rows = document.querySelectorAll('.table-row');
        rows.forEach(row => {
            if (tab === 'all') row.style.display = '';
            else if (tab === 'pending' && row.dataset.status === 'pending') row.style.display = '';
            else if (tab === 'late' && row.dataset.status === 'late') row.style.display = '';
            else if (tab === 'done' && row.dataset.status === 'done') row.style.display = '';
            else row.style.display = 'none';
        });
        
        currentPage = 1;
        updatePagination();
    }
    
    function filterEvaluations() {
        const stagiaire = document.getElementById('stagiaireFilter').value;
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value.toLowerCase();
        
        const cards = document.querySelectorAll('.evaluation-card');
        cards.forEach(card => {
            const cardStagiaire = card.dataset.stagiaire;
            const cardType = card.dataset.type;
            const cardNom = card.dataset.nom;
            
            let show = true;
            if (stagiaire !== 'all' && cardStagiaire != stagiaire) show = false;
            if (type !== 'all' && cardType !== type) show = false;
            if (search && !cardNom.includes(search)) show = false;
            
            card.style.display = show ? 'block' : 'none';
        });
        
        const rows = document.querySelectorAll('.table-row');
        rows.forEach(row => {
            const rowStagiaire = row.dataset.stagiaire;
            const rowType = row.dataset.type;
            const rowNom = row.dataset.nom;
            
            let show = true;
            if (stagiaire !== 'all' && rowStagiaire != stagiaire) show = false;
            if (type !== 'all' && rowType !== type) show = false;
            if (search && !rowNom.includes(search)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
        
        currentPage = 1;
        updatePagination();
    }
    
    function openEvalModal(id) {
        currentEvaluationId = id;
        document.getElementById('evalModal').classList.add('active');
        
        fetch(`/tuteur/evaluations/${id}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalStagiaire').textContent = data.stagiaire;
                
                const container = document.getElementById('criteriaContainer');
                container.innerHTML = '';
                
                let criteria = data.criteria;
                if (typeof criteria === 'string') {
                    criteria = JSON.parse(criteria);
                }
                
                criteria.forEach((criterium, index) => {
                    const ratingGroup = document.createElement('div');
                    ratingGroup.className = 'rating-item';
                    ratingGroup.innerHTML = `
                        <div class="rating-header">
                            <label>${criterium.nom}</label>
                            <div class="rating-stars" data-criteria="${index}">
                                ${generateStars(criterium.note)}
                            </div>
                        </div>
                        <input type="hidden" name="criteria[${index}][nom]" value="${criterium.nom}">
                        <input type="hidden" name="criteria[${index}][note]" id="criteria_${index}" value="${criterium.note || ''}">
                    `;
                    container.appendChild(ratingGroup);
                    
                    const stars = ratingGroup.querySelectorAll('.rating-stars i');
                    stars.forEach((star, starIndex) => {
                        star.onclick = () => setRating(index, starIndex + 1);
                    });
                });
                
                document.querySelector('.comment-input').value = data.commentaire || '';
                
                const form = document.getElementById('evaluationForm');
                form.action = form.action.replace('__ID__', id);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Impossible de charger l\'évaluation', 'error');
        });
    }
    
    function generateStars(note) {
        let stars = '';
        const rating = note || 0;
        for (let i = 1; i <= 5; i++) {
            if (rating >= i) {
                stars += '<i class="fas fa-star"></i>';
            } else if (rating >= i - 0.5) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }
    
    function setRating(ratingGroup, value) {
        const stars = document.querySelectorAll(`.rating-stars[data-criteria="${ratingGroup}"] i`);
        stars.forEach((star, index) => {
            if (index < value) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
        document.getElementById(`criteria_${ratingGroup}`).value = value;
    }
    
    function closeModal() {
        document.getElementById('evalModal').classList.remove('active');
        currentEvaluationId = null;
    }
    
    function viewEvaluation(id) {
        window.location.href = `/tuteur/evaluations/${id}`;
    }
    
    function exportEvaluations() {
        showNotification('Info', 'Export en cours de développement...', 'info');
    }
    
    function updatePagination() {
        const items = document.querySelectorAll('.evaluation-card:not([style*="display: none"])');
        const totalPages = Math.ceil(items.length / rowsPerPage);
        
        items.forEach((item, index) => {
            const page = Math.floor(index / rowsPerPage) + 1;
            item.style.display = page === currentPage ? 'block' : 'none';
        });
        
        const paginationNumbers = document.getElementById('paginationNumbers');
        if (paginationNumbers && totalPages > 0) {
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
        const items = document.querySelectorAll('.evaluation-card');
        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
        const totalPages = Math.ceil(visibleItems.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    }
    
    function showNotification(title, message, type) {
        const toast = document.createElement('div');
        toast.className = 'notification-toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-left: 4px solid ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 2000;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease;
        `;
        
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : type === 'error' ? 'fas fa-times-circle' : 'fas fa-info-circle';
        icon.style.cssText = `font-size: 1.5rem; color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};`;
        
        const content = document.createElement('div');
        content.innerHTML = `
            <div style="font-weight: 700; margin-bottom: 0.2rem;">${title}</div>
            <div style="font-size: 0.85rem; color: #6c757d;">${message}</div>
        `;
        
        toast.appendChild(icon);
        toast.appendChild(content);
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updatePagination();
    });
</script>

<style>
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
</style>
@endsection
