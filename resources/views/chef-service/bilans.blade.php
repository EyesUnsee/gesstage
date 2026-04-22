@extends('layouts.chef-service')

@section('title', 'Bilans & Stages terminés - Chef de service')
@section('page-title', 'Bilans & Stages terminés')
@section('active-bilans', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    $bilansEnAttente = $bilansEnAttente ?? collect();
    $bilansValides = $bilansValides ?? 0;
    $stagesTermines = $stagesTermines ?? collect();
    $stats = $stats ?? ['en_attente' => 0, 'valides' => 0, 'termines' => 0, 'note_moyenne' => 0];
    $services = $services ?? collect();
    $stagiaires = $stagiaires ?? collect();
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
    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .create-bilan-btn {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border: none;
        border-radius: 14px;
        padding: 0.8rem 2rem;
        color: var(--blanc);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    .create-bilan-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px var(--bleu);
    }
    .export-btn {
        background: var(--gris-clair);
        border: none;
        border-radius: 14px;
        padding: 0.8rem 2rem;
        color: var(--gris-fonce);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .export-btn:hover {
        background: var(--blanc);
        border: 2px solid var(--bleu);
        color: var(--bleu);
        transform: translateY(-2px);
    }
    .bilans-stats {
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
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: var(--blanc);
    }
    .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #fcd34d); }
    .stat-icon.green { background: linear-gradient(135deg, #10b981, #6ee7b7); }
    .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #93c5fd); }
    .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #c4b5fd); }
    .stat-info h3 {
        color: var(--gris);
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    .stat-info .number {
        font-size: 2rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1;
    }
    .alert-section {
        margin-bottom: 2rem;
    }
    .alert-card {
        background: linear-gradient(135deg, #fffbeb, #fff);
        border-left: 6px solid #f59e0b;
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow);
    }
    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #f59e0b;
    }
    .alert-content {
        flex: 1;
    }
    .alert-title {
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 0.3rem;
        font-size: 1.1rem;
    }
    .alert-desc {
        color: var(--gris-fonce);
        font-size: 0.95rem;
    }
    .alert-action {
        display: flex;
        gap: 0.5rem;
    }
    .alert-btn {
        padding: 0.5rem 1.2rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .alert-btn.primary {
        background: #f59e0b;
        color: var(--blanc);
    }
    .alert-btn.primary:hover {
        background: #d97706;
        transform: translateY(-2px);
    }
    .content-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--gris-clair);
        padding-bottom: 0.5rem;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 0.8rem 2rem;
        background: transparent;
        border: none;
        border-radius: 12px 12px 0 0;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tab-btn i {
        color: var(--gris);
    }
    .tab-btn:hover {
        color: var(--bleu);
        background: var(--gris-clair);
    }
    .tab-btn.active {
        color: var(--bleu);
        background: var(--blanc);
        border-bottom: 3px solid var(--bleu);
    }
    .tab-badge {
        background: #ef4444;
        color: var(--blanc);
        padding: 0.2rem 0.6rem;
        border-radius: 30px;
        font-size: 0.7rem;
        margin-left: 0.5rem;
    }
    .filters-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }
    .search-filter {
        flex: 2;
        min-width: 250px;
        background: var(--gris-clair);
        border-radius: 14px;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .search-filter:focus-within {
        border-color: var(--bleu);
        background: var(--blanc);
    }
    .search-filter i {
        color: var(--gris);
    }
    .search-filter input {
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
    }
    .filter-select {
        flex: 1;
        min-width: 150px;
        background: var(--gris-clair);
        border-radius: 14px;
        padding: 0.5rem 1rem;
        border: 2px solid transparent;
        outline: none;
        cursor: pointer;
    }
    .filter-select:focus {
        border-color: var(--bleu);
        background: var(--blanc);
    }
    .bilans-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .bilan-item {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .bilan-item:hover {
        transform: translateX(5px);
        border-color: var(--bleu);
    }
    .bilan-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 6px;
        background: linear-gradient(to bottom, var(--bleu), var(--vert));
    }
    .bilan-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 2rem;
        flex-shrink: 0;
    }
    .bilan-content {
        flex: 1;
        min-width: 200px;
    }
    .bilan-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }
    .bilan-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
    }
    .bilan-service {
        background: var(--gris-clair);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        color: var(--gris-fonce);
        font-weight: 600;
    }
    .bilan-meta {
        display: flex;
        gap: 1.5rem;
        color: var(--gris);
        font-size: 0.9rem;
        flex-wrap: wrap;
    }
    .bilan-meta i {
        margin-right: 0.3rem;
        color: var(--bleu);
    }
    .bilan-status {
        padding: 0.4rem 1.2rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    .status-validated {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .bilan-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: auto;
    }
    .bilan-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .bilan-btn:hover {
        background: var(--blanc);
        border: 2px solid var(--bleu);
        color: var(--bleu);
        transform: scale(1.1);
    }
    .bilan-btn.validate:hover {
        background: #10b981;
        border-color: #10b981;
        color: var(--blanc);
    }
    .termines-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }
    .termine-card {
        background: var(--gris-clair);
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--blanc);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .termine-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
        box-shadow: 0 10px 20px -10px var(--bleu);
    }
    .termine-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981, var(--bleu));
        opacity: 0.1;
        border-radius: 0 0 0 80px;
    }
    .termine-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .termine-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981, var(--bleu));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.5rem;
    }
    .termine-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        padding: 0.2rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .termine-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 0.3rem;
    }
    .termine-service {
        color: var(--gris-fonce);
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .termine-service i {
        color: var(--bleu);
    }
    .termine-dates {
        display: flex;
        justify-content: space-between;
        color: var(--gris);
        font-size: 0.85rem;
        margin-bottom: 1rem;
        padding: 0.5rem 0;
        border-top: 1px dashed var(--gris-clair);
        border-bottom: 1px dashed var(--gris-clair);
    }
    .termine-note {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .note-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #10b981;
    }
    .termine-footer {
        display: flex;
        gap: 0.5rem;
    }
    .termine-btn {
        flex: 1;
        padding: 0.6rem;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        background: var(--blanc);
        color: var(--gris-fonce);
    }
    .termine-btn:hover {
        background: var(--bleu);
        color: var(--blanc);
        transform: translateY(-2px);
    }
    .empty-state {
        text-align: center;
        padding: 4rem;
        color: var(--gris);
        background: var(--blanc);
        border-radius: 24px;
        border: 2px solid var(--gris-clair);
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--bleu);
    }
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }
    .modal.active {
        display: flex;
    }
    .modal-content {
        background: var(--blanc);
        border-radius: 30px;
        padding: 2rem;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .modal-header h2 {
        font-size: 1.5rem;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .close-modal {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .close-modal:hover {
        background: #ef4444;
        color: var(--blanc);
    }
    .form-group {
        margin-bottom: 1.2rem;
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
        border-radius: 14px;
        outline: none;
        transition: all 0.2s ease;
    }
    .form-control:focus {
        border-color: var(--bleu);
        background: var(--blanc);
    }
    .modal-footer {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    .btn-primary {
        flex: 1;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
        border: none;
        border-radius: 14px;
        padding: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
    }
    .btn-secondary {
        flex: 1;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: none;
        border-radius: 14px;
        padding: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        background: var(--blanc);
        border: 2px solid var(--gris);
    }
    @media (max-width: 1200px) {
        .bilans-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .bilans-stats {
            grid-template-columns: 1fr;
        }
        .bilan-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .bilan-actions {
            margin-left: 0;
            width: 100%;
            justify-content: flex-end;
        }
        .content-tabs {
            flex-direction: column;
        }
        .tab-btn {
            width: 100%;
            justify-content: center;
        }
        .action-buttons {
            flex-direction: column;
            width: 100%;
        }
        .action-buttons button {
            width: 100%;
            justify-content: center;
        }
        .alert-card {
            flex-direction: column;
            text-align: center;
        }
        .alert-action {
            width: 100%;
            justify-content: center;
        }
        .termines-grid {
            grid-template-columns: 1fr;
        }
        .filters-section {
            flex-direction: column;
        }
        .modal-footer {
            flex-direction: column;
        }
    }
</style>

<!-- En-tête de page -->
<div class="page-header">
    <h1>
        <i class="fas fa-file-alt"></i>
        Bilans de stage
    </h1>
    <div class="action-buttons">
        <button class="create-bilan-btn" onclick="openBilanModal()">
            <i class="fas fa-plus"></i> Nouveau bilan
        </button>
       
    </div>
</div>

<!-- Statistiques -->
<div class="bilans-stats">
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>En attente</h3>
            <div class="number">{{ $stats['en_attente'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>Validés</h3>
            <div class="number">{{ $stats['valides'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-flag-checkered"></i>
        </div>
        <div class="stat-info">
            <h3>Stages terminés</h3>
            <div class="number">{{ $stats['termines'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-info">
            <h3>Note moyenne</h3>
            <div class="number">{{ number_format($stats['note_moyenne'], 1) }}/5</div>
        </div>
    </div>
</div>

<!-- Alerte -->
@if($stats['en_attente'] > 0)
<div class="alert-section">
    <div class="alert-card">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Bilans en attente</div>
            <div class="alert-desc">{{ $stats['en_attente'] }} bilan(s) en attente de validation</div>
        </div>
        <div class="alert-action">
            <button class="alert-btn primary" onclick="switchTab('bilans')">Voir</button>
        </div>
    </div>
</div>
@endif

<!-- Tabs -->
<div class="content-tabs">
    <button class="tab-btn active" onclick="switchTab('bilans')">
        <i class="fas fa-file-alt"></i> Bilans en attente
        <span class="tab-badge">{{ $stats['en_attente'] }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('termines')">
        <i class="fas fa-flag-checkered"></i> Stages terminés
        <span class="tab-badge">{{ $stats['termines'] }}</span>
    </button>
</div>

<!-- Vue Bilans -->
<div id="bilansView" class="bilans-view">
    <div class="filters-section">
        <div class="search-filter">
            <i class="fas fa-search"></i>
            <input type="text" id="searchBilan" placeholder="Rechercher par stagiaire...">
        </div>
        <select class="filter-select" id="serviceFilter">
            <option value="">Tous les services</option>
            @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->nom }}</option>
            @endforeach
        </select>
    </div>

    <div class="bilans-list" id="bilansList">
        @forelse($bilansEnAttente as $bilan)
        <div class="bilan-item" data-id="{{ $bilan->id }}" data-stagiaire="{{ strtolower($bilan->stagiaire_nom ?? '') }}" data-service="{{ $bilan->service_id ?? '' }}">
            <div class="bilan-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="bilan-content">
                <div class="bilan-header">
                    <span class="bilan-title">{{ $bilan->stagiaire_nom ?? 'Stagiaire' }}</span>
                    <span class="bilan-service">{{ $bilan->service_nom ?? 'Service' }}</span>
                </div>
                <div class="bilan-meta">
                    <span><i class="fas fa-calendar"></i> Stage du {{ isset($bilan->date_debut) ? Carbon::parse($bilan->date_debut)->format('d/m/Y') : 'N/A' }} au {{ isset($bilan->date_fin) ? Carbon::parse($bilan->date_fin)->format('d/m/Y') : 'N/A' }}</span>
                    <span><i class="fas fa-user-tie"></i> Tuteur: {{ $bilan->tuteur_nom ?? 'Non assigné' }}</span>
                </div>
            </div>
            <div class="bilan-status status-pending">En attente</div>
            <div class="bilan-actions">
                <button class="bilan-btn" onclick="viewBilan({{ $bilan->id }})"><i class="fas fa-eye"></i></button>
                <button class="bilan-btn validate" onclick="validateBilan({{ $bilan->id }})"><i class="fas fa-check"></i></button>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <p>Aucun bilan en attente</p>
            <small>Tous les bilans sont validés</small>
        </div>
        @endforelse
    </div>
</div>

<!-- Vue Stages terminés -->
<div id="terminesView" class="termines-view" style="display: none;">
    <div class="filters-section">
        <div class="search-filter">
            <i class="fas fa-search"></i>
            <input type="text" id="searchTermine" placeholder="Rechercher un stagiaire...">
        </div>
        <select class="filter-select" id="serviceTermineFilter">
            <option value="">Tous les services</option>
            @foreach($services as $service)
            <option value="{{ $service->id }}">{{ $service->nom }}</option>
            @endforeach
        </select>
    </div>

    <div class="termines-grid" id="terminesGrid">
        @forelse($stagesTermines as $stage)
        <div class="termine-card" data-id="{{ $stage->id }}" data-stagiaire="{{ strtolower($stage->stagiaire_nom ?? '') }}" data-service="{{ $stage->service_id ?? '' }}">
            <div class="termine-header">
                <div class="termine-avatar"><i class="fas fa-user-graduate"></i></div>
                <span class="termine-badge">Terminé</span>
            </div>
            <div class="termine-name">{{ $stage->stagiaire_nom ?? 'Stagiaire' }}</div>
            <div class="termine-service"><i class="fas fa-building"></i> {{ $stage->service_nom ?? 'Service' }}</div>
            <div class="termine-dates">
                <span><i class="fas fa-calendar-alt"></i> {{ isset($stage->date_debut) ? Carbon::parse($stage->date_debut)->format('d/m/Y') : 'N/A' }}</span>
                <span><i class="fas fa-flag-checkered"></i> {{ isset($stage->date_fin) ? Carbon::parse($stage->date_fin)->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="termine-note"><span class="note-value">{{ $stage->note ?? 'N/A' }}</span><span class="note-max">/20</span></div>
            <div class="termine-footer">
                <button class="termine-btn" onclick="viewStage({{ $stage->id }})"><i class="fas fa-eye"></i> Détails</button>
                <button class="termine-btn" onclick="archiveStage({{ $stage->id }})"><i class="fas fa-archive"></i> Archiver</button>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column: 1/-1;">
            <i class="fas fa-flag-checkered"></i>
            <p>Aucun stage terminé</p>
            <small>Les stages terminés apparaîtront ici</small>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Nouveau Bilan -->
<div class="modal" id="bilanModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-file-alt"></i> Créer un bilan</h2>
            <button class="close-modal" onclick="closeBilanModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="bilanForm" method="POST" action="{{ route('chef-service.bilans.store') }}">
            @csrf
            <div class="form-group">
                <label>Titre du bilan *</label>
                <input type="text" name="titre" class="form-control" placeholder="Ex: Bilan de stage - Développement Web" required>
            </div>
            <div class="form-group">
                <label>Stagiaire *</label>
                <select name="stagiaire_id" class="form-control" required>
                    <option value="">-- Sélectionner un stagiaire --</option>
                    @foreach($stagiaires as $stagiaire)
                    <option value="{{ $stagiaire->id }}">{{ $stagiaire->first_name ?? '' }} {{ $stagiaire->last_name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Contenu du bilan *</label>
                <textarea name="contenu" class="form-control" rows="5" placeholder="Description du bilan..." required></textarea>
            </div>
            <div class="form-group">
                <label>Note (sur 20)</label>
                <input type="number" name="note" class="form-control" min="0" max="20" step="0.5" placeholder="Ex: 15.5">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeBilanModal()">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.toggle('active');
    }
    
    function switchTab(tabName) {
        const bilansView = document.getElementById('bilansView');
        const terminesView = document.getElementById('terminesView');
        if (bilansView) bilansView.style.display = 'none';
        if (terminesView) terminesView.style.display = 'none';
        if (tabName === 'bilans' && bilansView) bilansView.style.display = 'block';
        else if (terminesView) terminesView.style.display = 'block';
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        if (event && event.target) event.target.classList.add('active');
    }
    
    function openBilanModal() {
        const modal = document.getElementById('bilanModal');
        if (modal) modal.classList.add('active');
    }
    
    function closeBilanModal() {
        const modal = document.getElementById('bilanModal');
        if (modal) modal.classList.remove('active');
    }
    
    // Soumission du formulaire via AJAX
    const bilanForm = document.getElementById('bilanForm');
    if (bilanForm) {
        bilanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Envoi...';
            submitBtn.disabled = true;
            fetch(this.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    closeBilanModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Erreur';
                    if (data.errors) errorMsg = Object.values(data.errors).flat().join('\n');
                    showNotification('Erreur', errorMsg, 'error');
                }
            })
            .catch(error => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        });
    }
    
    function viewBilan(id) { window.location.href = `/chef-service/bilans/${id}`; }
    function viewStage(id) { window.location.href = `/chef-service/stages/${id}`; }
    
    function validateBilan(id) {
        if (confirm('Valider ce bilan ?')) {
            fetch(`/chef-service/bilans/${id}/valider`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) { showNotification('Succès', data.message, 'success'); setTimeout(() => location.reload(), 1000); }
                else showNotification('Erreur', data.message, 'error');
            })
            .catch(error => showNotification('Erreur', 'Une erreur est survenue', 'error'));
        }
    }
    
    function archiveStage(id) {
        if (confirm('Archiver ce stage ?')) {
            fetch(`/chef-service/stages/${id}/archive`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) { showNotification('Succès', data.message, 'success'); setTimeout(() => location.reload(), 1000); }
                else showNotification('Erreur', data.message, 'error');
            })
            .catch(error => showNotification('Erreur', 'Une erreur est survenue', 'error'));
        }
    }
    
    function exportData() { window.location.href = '{{ route("chef-service.bilans.export.excel") }}'; }
    
    function showNotification(title, message, type) {
        const toast = document.getElementById('notificationToast');
        if (!toast) { alert(title + ': ' + message); return; }
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');
        if (toastTitle) toastTitle.textContent = title;
        if (toastMessage) toastMessage.textContent = message;
        toast.style.borderLeftColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#3b82f6');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Filtres
    document.getElementById('searchBilan')?.addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#bilansList .bilan-item').forEach(item => {
            item.style.display = (item.dataset.stagiaire || '').includes(term) ? 'flex' : 'none';
        });
    });
    document.getElementById('searchTermine')?.addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#terminesGrid .termine-card').forEach(card => {
            card.style.display = (card.dataset.stagiaire || '').includes(term) ? 'block' : 'none';
        });
    });
    
    // Fermer le modal en cliquant en dehors
    const modal = document.getElementById('bilanModal');
    if (modal) modal.addEventListener('click', function(e) { if (e.target === this) closeBilanModal(); });
    
    @if(session('success'))
        showNotification('Succès', '{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showNotification('Erreur', '{{ session('error') }}', 'error');
    @endif
</script>
@endsection
