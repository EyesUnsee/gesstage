@extends('layouts.chef-service')

@section('title', 'Services & Sanctions - Chef de service')
@section('page-title', 'Services & Sanctions')
@section('active-services', 'active')

@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    
    // Récupération des données depuis le contrôleur
    $services = $services ?? collect();
    $sanctionsData = $sanctionsData ?? collect();
    $bannis = $bannis ?? collect();
    $stagesFinis = $stagesFinis ?? collect();
    
    $sanctionsEnAttente = $sanctionsEnAttente ?? 0;
    $sanctionsActives = $sanctionsActives ?? 0;
    $totalBannis = $totalBannis ?? 0;
    $responsables = $responsables ?? collect();
    $stagiaires = $stagiaires ?? collect();
@endphp

<style>
    /* ===== DÉFINITION DES VARIABLES ===== */
    :root {
        --orange: #f59e0b;
        --orange-fonce: #d97706;
    }
    
    /* ===== STYLES SPÉCIFIQUES SERVICES ===== */
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

    .add-service-btn {
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

    .add-service-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px var(--bleu);
    }

    .sanction-btn {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
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
        box-shadow: 0 10px 20px -8px #f59e0b;
    }

    .sanction-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px #f59e0b;
    }

    /* Alertes */
    .alert-section {
        margin-bottom: 2rem;
    }

    .alert-card {
        background: linear-gradient(135deg, #fff5f5, #fff);
        border-left: 6px solid var(--rouge);
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow);
        margin-bottom: 1rem;
        animation: slideIn 0.3s ease;
    }

    .alert-card.warning {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb, #fff);
    }

    .alert-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--rouge);
    }

    .alert-card.warning .alert-icon {
        background: rgba(245, 158, 11, 0.1);
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
        font-size: 0.9rem;
    }

    .alert-btn.primary {
        background: var(--rouge);
        color: var(--blanc);
    }

    .alert-btn.primary:hover {
        background: var(--rouge-fonce);
        transform: translateY(-2px);
    }

    .alert-btn.secondary {
        background: var(--gris-clair);
        color: var(--gris-fonce);
    }

    .alert-btn.secondary:hover {
        background: var(--blanc);
        border: 2px solid var(--gris);
    }

    /* Stats rapides */
    .sanctions-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card-small {
        background: var(--blanc);
        border-radius: 20px;
        padding: 1.2rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card-small:hover {
        transform: translateY(-3px);
        border-color: var(--rouge);
    }

    .stat-icon-small {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--rouge), #fca5a5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.3rem;
    }

    .stat-icon-small.orange {
        background: linear-gradient(135deg, #f59e0b, #fcd34d);
    }

    .stat-icon-small.blue {
        background: linear-gradient(135deg, var(--bleu), #93c5fd);
    }

    .stat-info-small h3 {
        color: var(--gris);
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .stat-info-small .number {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1;
    }

    /* Filtres */
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
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }

    .search-filter i {
        color: var(--gris);
    }

    .search-filter input {
        background: transparent;
        border: none;
        color: var(--noir);
        font-size: 0.95rem;
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
        color: var(--noir);
        font-weight: 500;
        outline: none;
        cursor: pointer;
    }

    .filter-select:focus {
        border-color: var(--bleu);
        background: var(--blanc);
    }

    /* Tabs */
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
        position: relative;
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

    .tab-btn.active i {
        color: var(--bleu);
    }

    .tab-badge {
        background: var(--rouge);
        color: var(--blanc);
        padding: 0.2rem 0.6rem;
        border-radius: 30px;
        font-size: 0.7rem;
        margin-left: 0.5rem;
    }

    /* Services Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .service-card {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease backwards;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 120px;
    }

    .service-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 20px 40px -15px var(--bleu);
    }

    .service-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .service-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.8rem;
        box-shadow: 0 10px 20px -8px var(--bleu);
    }

    .service-status {
        padding: 0.3rem 1.2rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-actif {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }

    .status-inactif {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }

    .status-en-attente {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .service-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }

    .service-responsable {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris-fonce);
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .service-responsable i {
        color: var(--bleu);
    }

    .service-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
        border-top: 2px solid var(--gris-clair);
        border-bottom: 2px solid var(--gris-clair);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--noir);
        line-height: 1.2;
    }

    .stat-label {
        color: var(--gris);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .service-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
    }

    .service-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .service-tag {
        background: var(--gris-clair);
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
        font-size: 0.75rem;
        color: var(--gris-fonce);
        font-weight: 600;
    }

    .service-actions {
        display: flex;
        gap: 0.3rem;
    }

    .service-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .service-btn:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
        transform: scale(1.1);
    }

    /* Tableaux */
    .sanctions-table-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        margin-top: 1rem;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header h3 {
        font-size: 1.2rem;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-header h3 i {
        color: var(--rouge);
    }

    .export-btn {
        background: var(--gris-clair);
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
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
        border: 2px solid var(--gris);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 1rem 0.5rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }

    td {
        padding: 1rem 0.5rem;
        color: var(--noir);
        font-weight: 500;
        border-bottom: 2px solid var(--gris-clair);
    }

    tr:last-child td {
        border-bottom: none;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .action-cell {
        display: flex;
        gap: 0.5rem;
    }

    .table-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .table-btn:hover {
        background: var(--rouge);
        color: var(--blanc);
        transform: scale(1.1);
    }

    .table-btn.view:hover {
        background: var(--bleu);
    }

    /* Empty state */
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

    .empty-state p {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .empty-state small {
        font-size: 0.85rem;
    }

    .text-center {
        text-align: center;
    }

    /* Modals */
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

    .modal-header h2 i {
        color: var(--bleu);
    }

    .modal-header.sanction h2 i {
        color: var(--rouge);
    }

    .close-modal {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .close-modal:hover {
        background: var(--rouge);
        color: var(--blanc);
    }

    .form-group {
        margin-bottom: 1.2rem;
    }

    .form-group label {
        display: block;
        color: var(--gris-fonce);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 14px;
        color: var(--noir);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--bleu);
        background: var(--blanc);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }

    .modal-footer {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-primary {
        flex: 1;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border: none;
        border-radius: 14px;
        padding: 0.8rem;
        color: var(--blanc);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }

    .btn-danger {
        flex: 1;
        background: linear-gradient(135deg, var(--rouge), #f87171);
        border: none;
        border-radius: 14px;
        padding: 0.8rem;
        color: var(--blanc);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--rouge);
    }

    .btn-secondary {
        flex: 1;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 14px;
        padding: 0.8rem;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: var(--blanc);
        border-color: var(--gris);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .sanctions-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .services-grid {
            grid-template-columns: 1fr;
        }
        .sanctions-stats {
            grid-template-columns: 1fr;
        }
        .filters-section {
            flex-direction: column;
            align-items: stretch;
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
        table {
            display: block;
            overflow-x: auto;
        }
        .content-tabs {
            flex-direction: column;
        }
        .tab-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .service-stats {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .service-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        .modal-footer {
            flex-direction: column;
        }
        .modal-footer button {
            width: 100%;
        }
    }
</style>

<!-- En-tête de page -->
<div class="page-header">
    <h1>
        <i class="fas fa-building"></i>
        Services & Sanctions
    </h1>
    <div class="action-buttons">
        <button class="add-service-btn" onclick="openModal('service')">
            <i class="fas fa-plus"></i> Nouveau service
        </button>
        <button class="sanction-btn" onclick="openModal('sanction')">
            <i class="fas fa-gavel"></i> Sanctionner
        </button>
    </div>
</div>

<!-- Alertes -->
@if($sanctionsEnAttente > 0)
<div class="alert-section">
    <div class="alert-card warning">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Sanctions en attente de traitement</div>
            <div class="alert-desc">{{ $sanctionsEnAttente }} stagiaire(s) nécessitent une décision disciplinaire</div>
        </div>
        <div class="alert-action">
            <button class="alert-btn primary" onclick="switchTab('sanctions')">Traiter</button>
            <button class="alert-btn secondary" onclick="dismissAlert(this)">Plus tard</button>
        </div>
    </div>
</div>
@endif

<!-- Statistiques -->
<div class="sanctions-stats">
    <div class="stat-card-small">
        <div class="stat-icon-small">
            <i class="fas fa-ban"></i>
        </div>
        <div class="stat-info-small">
            <h3>Stagiaires bannis</h3>
            <div class="number">{{ $totalBannis }}</div>
        </div>
    </div>
    <div class="stat-card-small">
        <div class="stat-icon-small orange">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="stat-info-small">
            <h3>Sanctions actives</h3>
            <div class="number">{{ $sanctionsActives }}</div>
        </div>
    </div>
    <div class="stat-card-small">
        <div class="stat-icon-small blue">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info-small">
            <h3>En attente</h3>
            <div class="number">{{ $sanctionsEnAttente }}</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-section">
    <div class="search-filter">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Rechercher par nom, service, motif...">
    </div>
    <select class="filter-select" id="typeFilter">
        <option value="">Tous les types</option>
        <option value="service">Services</option>
        <option value="sanction">Sanctions</option>
        <option value="banni">Stagiaires bannis</option>
        <option value="fini">Stages finis</option>
    </select>
    <select class="filter-select" id="sortFilter">
        <option value="date">Trier par date</option>
        <option value="gravite">Trier par gravité</option>
        <option value="service">Trier par service</option>
    </select>
</div>

<!-- Tabs -->
<div class="content-tabs">
    <button class="tab-btn active" onclick="switchTab('services')">
        <i class="fas fa-building"></i> Services
        <span class="tab-badge">{{ $services->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('sanctions')">
        <i class="fas fa-gavel"></i> Sanctions
        <span class="tab-badge">{{ $sanctionsData->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('bannis')">
        <i class="fas fa-ban"></i> Stagiaires bannis
        <span class="tab-badge">{{ $bannis->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('finis')">
        <i class="fas fa-flag-checkered"></i> Stages finis
        <span class="tab-badge">{{ $stagesFinis->count() }}</span>
    </button>
</div>

<!-- Vue Services -->
<div id="servicesView" class="services-view">
    <div class="services-grid">
        @forelse($services as $service)
        <div class="service-card" data-id="{{ $service->id }}">
            <div class="service-header">
                <div class="service-icon">
                    <i class="fas fa-building"></i>
                </div>
                <span class="service-status status-{{ $service->statut ?? 'actif' }}">
                    {{ ucfirst($service->statut ?? 'Actif') }}
                </span>
            </div>
            <div class="service-title">{{ $service->nom }}</div>
            <div class="service-responsable">
                <i class="fas fa-user-tie"></i>
                Responsable: {{ $service->responsable_nom ?? 'Non assigné' }}
            </div>
            <div class="service-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $service->stagiaires_count ?? 0 }}</div>
                    <div class="stat-label">Stagiaires</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $service->tuteurs_count ?? 0 }}</div>
                    <div class="stat-label">Tuteurs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $service->sanctions_count ?? 0 }}</div>
                    <div class="stat-label">Sanctions</div>
                </div>
            </div>
            <div class="service-footer">
                <div class="service-tags">
                    @foreach(($service->tags ?? []) as $tag)
                    <span class="service-tag">{{ $tag }}</span>
                    @endforeach
                </div>
                <div class="service-actions">
                    
                    <button class="service-btn" onclick="editService({{ $service->id }})" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="service-btn" onclick="deleteService({{ $service->id }})" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <p>Aucun service disponible</p>
            <small>Cliquez sur "Nouveau service" pour en créer un</small>
        </div>
        @endforelse
    </div>
</div>

<!-- Vue Sanctions -->
<div id="sanctionsView" class="sanctions-view" style="display: none;">
    <div class="sanctions-table-section">
        <div class="table-header">
            <h3>
                <i class="fas fa-gavel"></i>
                Liste des sanctions
            </h3>
            <button class="export-btn" onclick="exportData('sanctions')">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
        
        @if($sanctionsData->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Stagiaire</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Motif</th>
                        <th>Gravité</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sanctionsData as $sanction)
                    <tr>
                        <td>{{ $sanction->stagiaire_nom }}</td>
                        <td>{{ $sanction->service_nom }}</td>
                        <td>
                            @php
                                $typeIcon = $sanction->type == 'exclusion' ? 'fa-ban' : ($sanction->type == 'suspension' ? 'fa-pause-circle' : 'fa-exclamation-triangle');
                                $typeColor = $sanction->type == 'exclusion' ? 'danger' : ($sanction->type == 'suspension' ? 'warning' : 'info');
                            @endphp
                            <span style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas {{ $typeIcon }}" style="color: var(--{{ $typeColor }});"></i>
                                {{ ucfirst($sanction->type) }}
                            </span>
                        </td>
                        <td>{{ Str::limit($sanction->motif, 40) }}</td>
                        <td>
                            <span class="badge-{{ $sanction->gravite == 'elevee' ? 'danger' : ($sanction->gravite == 'moyenne' ? 'warning' : 'success') }}">
                                {{ ucfirst($sanction->gravite) }}
                            </span>
                        </td>
                        <td>{{ Carbon::parse($sanction->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge-{{ $sanction->statut == 'actif' ? 'warning' : 'success' }}">
                                {{ ucfirst($sanction->statut) }}
                                @if($sanction->duree)
                                    <small>({{ $sanction->duree }})</small>
                                @endif
                            </span>
                        </td>
                        <td class="action-cell">
                            
                            <button class="table-btn" onclick="deleteSanction({{ $sanction->id }})" title="Supprimer"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-gavel"></i>
            <p>Aucune sanction enregistrée</p>
            <small>Cliquez sur "Sanctionner" pour ajouter une sanction</small>
        </div>
        @endif
    </div>
</div>

<!-- Vue Bannis -->
<div id="bannisView" class="bannis-view" style="display: none;">
    <div class="sanctions-table-section">
        <div class="table-header">
            <h3>
                <i class="fas fa-ban"></i>
                Stagiaires bannis
            </h3>
            <button class="export-btn" onclick="exportData('bannis')">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
        
        @if($bannis->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Stagiaire</th>
                        <th>Service</th>
                        <th>Motif du bannissement</th>
                        <th>Date</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bannis as $banni)
                    <tr>
                        <td>{{ $banni->stagiaire_nom ?? 'Inconnu' }}</td>
                        <td>{{ $banni->service_nom ?? 'N/A' }}</td>
                        <td>{{ Str::limit($banni->motif ?? '', 50) }}</td>
                        <td>{{ isset($banni->date_bannissement) ? Carbon::parse($banni->date_bannissement)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $banni->duree ?? 'Permanent' }}</td>
                        <td class="action-cell">
                           
                            <button class="table-btn" onclick="appealBanni({{ $banni->id }})" title="Faire appel"><i class="fas fa-gavel"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-ban"></i>
            <p>Aucun stagiaire banni</p>
            <small>Les stagiaires exclus définitivement apparaîtront ici</small>
        </div>
        @endif
    </div>
</div>

<!-- Vue Stages Finis -->
<div id="finisView" class="finis-view" style="display: none;">
    <div class="sanctions-table-section">
        <div class="table-header">
            <h3>
                <i class="fas fa-flag-checkered"></i>
                Stages terminés
            </h3>
            <button class="export-btn" onclick="exportData('finis')">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
        
        @if($stagesFinis->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Stagiaire</th>
                        <th>Service</th>
                        <th>Date de fin</th>
                        <th>Note finale</th>
                        <th>Bilan</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stagesFinis as $stage)
                    <tr>
                        <td>{{ $stage->stagiaire_nom ?? 'Inconnu' }}</td>
                        <td>{{ $stage->service_nom ?? 'N/A' }}</td>
                        <td>{{ isset($stage->date_fin) ? Carbon::parse($stage->date_fin)->format('d/m/Y') : 'N/A' }}</td>
                        <td><span class="badge-success">{{ $stage->note_finale ?? 'Non noté' }}</span></td>
                        <td>
                            <span class="badge-{{ $stage->bilan_statut == 'valide' ? 'success' : 'warning' }}">
                                {{ ucfirst($stage->bilan_statut ?? 'En attente') }}
                            </span>
                        </td>
                        <td class="action-cell">
                         
                            @if(($stage->bilan_statut ?? '') != 'valide')
                            <button class="table-btn" onclick="validateBilan({{ $stage->id }})" title="Valider le bilan"><i class="fas fa-check"></i></button>
                            @endif
                            <button class="table-btn" onclick="archiveStage({{ $stage->id }})" title="Archiver"><i class="fas fa-archive"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-flag-checkered"></i>
            <p>Aucun stage terminé</p>
            <small>Les stages terminés apparaîtront ici</small>
        </div>
        @endif
    </div>
</div>

<!-- Modal Service -->
<div class="modal" id="serviceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-building"></i>
                <span id="serviceModalTitle">Ajouter un service</span>
            </h2>
            <button class="close-modal" onclick="closeModal('service')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="serviceForm" method="POST" action="{{ route('chef-service.services.store') }}">
            @csrf
            <input type="hidden" id="serviceId" name="service_id">
            <div class="form-group">
                <label>Nom du service</label>
                <input type="text" id="serviceName" name="nom" class="form-control" placeholder="Ex: Informatique" required>
            </div>
            <div class="form-group">
                <label>Responsable</label>
                <select id="serviceManager" name="responsable_id" class="form-control" required>
                    <option value="">Sélectionner un responsable</option>
                    @foreach($responsables as $responsable)
                    <option value="{{ $responsable->id }}">{{ $responsable->first_name ?? '' }} {{ $responsable->last_name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="serviceDesc" name="description" class="form-control" rows="3" placeholder="Description du service..."></textarea>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select id="serviceStatus" name="statut" class="form-control">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                    <option value="en-attente">En attente</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tags (séparés par des virgules)</label>
                <input type="text" id="serviceTags" name="tags" class="form-control" placeholder="Ex: Développement, Réseau">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('service')">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sanction -->
<div class="modal" id="sanctionModal">
    <div class="modal-content">
        <div class="modal-header sanction">
            <h2>
                <i class="fas fa-gavel"></i>
                <span>Sanctionner un stagiaire</span>
            </h2>
            <button class="close-modal" onclick="closeModal('sanction')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="sanctionForm" method="POST" action="{{ route('chef-service.sanctions.store') }}">
            @csrf
            <div class="form-group">
                <label>Stagiaire</label>
                <select id="sanctionStagiaire" name="stagiaire_id" class="form-control" required>
                    <option value="">Sélectionner un stagiaire</option>
                    @foreach($stagiaires as $stagiaire)
                    <option value="{{ $stagiaire->id }}">{{ $stagiaire->first_name ?? '' }} {{ $stagiaire->last_name ?? '' }} - {{ $stagiaire->service_nom ?? 'Sans service' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Type de sanction</label>
                <select id="sanctionType" name="type" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    <option value="avertissement">⚠️ Avertissement</option>
                    <option value="suspension">⏸️ Suspension temporaire</option>
                    <option value="exclusion">🚫 Exclusion définitive (ban)</option>
                    <option value="retenue">💰 Retenue sur gratification</option>
                </select>
            </div>
            <div class="form-group">
                <label>Motif</label>
                <textarea id="sanctionMotif" name="motif" class="form-control" rows="3" placeholder="Détailler le motif de la sanction..." required></textarea>
            </div>
            <div class="form-group">
                <label>Gravité</label>
                <select id="sanctionGravite" name="gravite" class="form-control" required>
                    <option value="faible">🟢 Faible</option>
                    <option value="moyenne">🟡 Moyenne</option>
                    <option value="elevee">🔴 Élevée</option>
                </select>
            </div>
            <div class="form-group">
                <label>Durée (si temporaire)</label>
                <input type="text" id="sanctionDuree" name="duree" class="form-control" placeholder="Ex: 1 mois, 3 mois, Permanent...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('sanction')">Annuler</button>
                <button type="submit" class="btn-danger">Appliquer la sanction</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentServiceId = null;
    
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('active');
    }
    
    function switchTab(tabName) {
        document.getElementById('servicesView').style.display = 'none';
        document.getElementById('sanctionsView').style.display = 'none';
        document.getElementById('bannisView').style.display = 'none';
        document.getElementById('finisView').style.display = 'none';
        document.getElementById(tabName + 'View').style.display = 'block';
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        if (event && event.target) {
            event.target.classList.add('active');
        }
    }
    
    function openModal(type, id = null) {
        if (type === 'service') {
            currentServiceId = id;
            const modalTitle = document.getElementById('serviceModalTitle');
            const form = document.getElementById('serviceForm');
            
            if (id) {
                modalTitle.textContent = 'Modifier le service';
                fetch(`/chef-service/services/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('serviceName').value = data.nom;
                        document.getElementById('serviceManager').value = data.responsable_id;
                        document.getElementById('serviceDesc').value = data.description || '';
                        document.getElementById('serviceStatus').value = data.statut || 'actif';
                        document.getElementById('serviceTags').value = data.tags || '';
                        document.getElementById('serviceId').value = id;
                    })
                    .catch(error => console.error('Erreur:', error));
            } else {
                modalTitle.textContent = 'Ajouter un service';
                form.reset();
                document.getElementById('serviceId').value = '';
                document.getElementById('serviceName').value = '';
                document.getElementById('serviceManager').value = '';
                document.getElementById('serviceDesc').value = '';
                document.getElementById('serviceStatus').value = 'actif';
                document.getElementById('serviceTags').value = '';
            }
            document.getElementById('serviceModal').classList.add('active');
        } else if (type === 'sanction') {
            document.getElementById('sanctionForm').reset();
            document.getElementById('sanctionStagiaire').value = '';
            document.getElementById('sanctionType').value = '';
            document.getElementById('sanctionMotif').value = '';
            document.getElementById('sanctionGravite').value = 'moyenne';
            document.getElementById('sanctionDuree').value = '';
            document.getElementById('sanctionModal').classList.add('active');
        }
    }
    
    function closeModal(type) {
        if (type === 'service') {
            document.getElementById('serviceModal').classList.remove('active');
        } else if (type === 'sanction') {
            document.getElementById('sanctionModal').classList.remove('active');
        }
    }
    
    // Soumission du formulaire de sanction
    const sanctionForm = document.getElementById('sanctionForm');
    if (sanctionForm) {
        sanctionForm.onsubmit = function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs
            const stagiaireId = document.getElementById('sanctionStagiaire').value;
            const type = document.getElementById('sanctionType').value;
            const motif = document.getElementById('sanctionMotif').value;
            const gravite = document.getElementById('sanctionGravite').value;
            const duree = document.getElementById('sanctionDuree').value;
            
            // Validation simple
            if (!stagiaireId) {
                showNotification('Erreur', 'Veuillez sélectionner un stagiaire', 'error');
                return;
            }
            if (!type) {
                showNotification('Erreur', 'Veuillez sélectionner un type de sanction', 'error');
                return;
            }
            if (!motif || motif.length < 10) {
                showNotification('Erreur', 'Le motif doit contenir au moins 10 caractères', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('stagiaire_id', stagiaireId);
            formData.append('type', type);
            formData.append('motif', motif);
            formData.append('gravite', gravite);
            formData.append('duree', duree);
            formData.append('_token', '{{ csrf_token() }}');
            
            // Afficher un message de chargement
            const submitBtn = sanctionForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Chargement...';
            submitBtn.disabled = true;
            
            fetch('{{ route("chef-service.sanctions.store") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    closeModal('sanction');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Erreur lors de l\'application';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    showNotification('Erreur', errorMsg, 'error');
                }
            })
            .catch(error => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue. Vérifiez votre connexion.', 'error');
            });
        };
    }
    
    // Soumission du formulaire de service
    const serviceFormElem = document.getElementById('serviceForm');
    if (serviceFormElem) {
        serviceFormElem.onsubmit = function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs
            const nom = document.getElementById('serviceName').value;
            const responsableId = document.getElementById('serviceManager').value;
            const description = document.getElementById('serviceDesc').value;
            const statut = document.getElementById('serviceStatus').value;
            const tags = document.getElementById('serviceTags').value;
            const serviceId = document.getElementById('serviceId').value;
            
            // Validation simple
            if (!nom) {
                showNotification('Erreur', 'Veuillez saisir un nom de service', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('nom', nom);
            formData.append('responsable_id', responsableId);
            formData.append('description', description);
            formData.append('statut', statut);
            formData.append('tags', tags);
            formData.append('_token', '{{ csrf_token() }}');
            
            if (serviceId) {
                formData.append('_method', 'PUT');
            }
            
            const url = serviceId ? `/chef-service/services/${serviceId}` : '{{ route("chef-service.services.store") }}';
            
            // Afficher un message de chargement
            const submitBtn = serviceFormElem.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Chargement...';
            submitBtn.disabled = true;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    closeModal('service');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Erreur lors de l\'enregistrement';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    showNotification('Erreur', errorMsg, 'error');
                }
            })
            .catch(error => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue. Vérifiez votre connexion.', 'error');
            });
        };
    }
    
    function viewService(id) {
        showNotification('Information', 'Affichage des détails du service', 'info');
    }
    
    function editService(id) {
        openModal('service', id);
    }
    
    function deleteService(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
            fetch(`/chef-service/services/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    location.reload();
                } else {
                    showNotification('Erreur', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function viewSanction(id) {
        showNotification('Information', 'Détails de la sanction #' + id, 'info');
    }
    
    function deleteSanction(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette sanction ?')) {
            fetch(`/chef-service/sanctions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    location.reload();
                } else {
                    showNotification('Erreur', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function viewBanni(id) {
        showNotification('Information', 'Détails du stagiaire banni #' + id, 'info');
    }
    
    function appealBanni(id) {
        if (confirm('Faire appel pour ce stagiaire banni ? Le compte sera réactivé en attendant la décision.')) {
            fetch(`/chef-service/bannis/${id}/appeal`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    location.reload();
                } else {
                    showNotification('Erreur', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function viewStageFini(id) {
        showNotification('Information', 'Détails du stage #' + id, 'info');
    }
    
    function validateBilan(id) {
        if (confirm('Valider le bilan de ce stage ?')) {
            fetch(`/chef-service/stages/${id}/validate-bilan`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    location.reload();
                } else {
                    showNotification('Erreur', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function archiveStage(id) {
        if (confirm('Archiver ce stage ?')) {
            fetch(`/chef-service/stages/${id}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', data.message, 'success');
                    location.reload();
                } else {
                    showNotification('Erreur', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function exportData(type) {
        showNotification('Information', `Export des ${type} en cours...`, 'info');
        window.location.href = `/chef-service/export/${type}`;
    }
    
    function dismissAlert(btn) {
        const alertCard = btn.closest('.alert-card');
        if (alertCard) alertCard.remove();
    }
    
    function showNotification(title, message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        if (!toast) {
            alert(title + ': ' + message);
            return;
        }
        
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toast.style.borderLeftColor = '#10b981';
            if (toastIcon) {
                toastIcon.className = 'fas fa-check-circle';
                toastIcon.style.color = '#10b981';
            }
        } else if (type === 'error') {
            toast.style.borderLeftColor = '#ef4444';
            if (toastIcon) {
                toastIcon.className = 'fas fa-times-circle';
                toastIcon.style.color = '#ef4444';
            }
        } else {
            toast.style.borderLeftColor = '#3b82f6';
            if (toastIcon) {
                toastIcon.className = 'fas fa-info-circle';
                toastIcon.style.color = '#3b82f6';
            }
        }
        
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Fermer les modals en cliquant en dehors
    const serviceModal = document.getElementById('serviceModal');
    if (serviceModal) {
        serviceModal.addEventListener('click', function(e) {
            if (e.target === this) closeModal('service');
        });
    }
    
    const sanctionModal = document.getElementById('sanctionModal');
    if (sanctionModal) {
        sanctionModal.addEventListener('click', function(e) {
            if (e.target === this) closeModal('sanction');
        });
    }
    
    // Filtres
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const activeTab = document.querySelector('.tab-btn.active');
            if (!activeTab) return;
            
            const tabText = activeTab.textContent.trim();
            
            if (tabText.includes('Services')) {
                document.querySelectorAll('.service-card').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
            } else {
                document.querySelectorAll('.sanctions-table-section table tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    }
    
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            const type = this.value;
            if (type) {
                const tabMap = {
                    'service': 'services',
                    'sanction': 'sanctions',
                    'banni': 'bannis',
                    'fini': 'finis'
                };
                if (tabMap[type]) {
                    const buttons = document.querySelectorAll('.tab-btn');
                    buttons.forEach(btn => {
                        if (btn.textContent.toLowerCase().includes(type)) {
                            btn.click();
                        }
                    });
                }
            }
        });
    }
    
    // Afficher les notifications de session
    @if(session('success'))
        showNotification('Succès', '{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('Erreur', '{{ session('error') }}', 'error');
    @endif
    
    // Animation des cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.service-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.05}s`;
        });
    });
</script>
@endsection
