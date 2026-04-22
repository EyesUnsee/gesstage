@extends('layouts.tuteur')

@section('title', 'Suivi des journaux - Tuteur')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'">
    </div>
    <div class="welcome-text">
        <h1 class="welcome-title">Suivi des <span>journaux</span> 📔</h1>
        <p class="welcome-subtitle">Consultez et validez les journaux de bord de vos stagiaires</p>
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

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">En attente</span>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $enAttente ?? 0 }}</div>
        <div class="stat-trend trend-down">
            <i class="fas fa-hourglass-half"></i> À valider
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Validés</span>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $valides ?? 0 }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-check"></i> Approuvés
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Rejetés</span>
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $rejetes ?? 0 }}</div>
        <div class="stat-trend trend-down">
            <i class="fas fa-undo-alt"></i> À corriger
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total</span>
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="stat-value">{{ $total ?? 0 }}</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-chart-line"></i> Entrées
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-section">
    <div class="filter-group">
        <select id="stagiaireFilter" onchange="filterJournaux()">
            <option value="all">Tous les stagiaires</option>
            @foreach($stagiaires ?? [] as $stagiaire)
                <option value="{{ $stagiaire->id }}">{{ $stagiaire->first_name }} {{ $stagiaire->last_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <select id="statutFilter" onchange="filterJournaux()">
            <option value="all">Tous les statuts</option>
            <option value="en_attente">En attente</option>
            <option value="valide">Validés</option>
            <option value="rejete">Rejetés</option>
        </select>
    </div>
    <div class="filter-group">
        <select id="semaineFilter" onchange="filterJournaux()">
            <option value="all">Toutes les semaines</option>
            @for($i = 1; $i <= 52; $i++)
                <option value="{{ $i }}">Semaine {{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="filter-group">
        <input type="text" id="searchInput" placeholder="Rechercher par titre..." onkeyup="filterJournaux()">
    </div>
</div>

<!-- Grille des journaux -->
<div class="journaux-grid" id="journauxGrid">
    @forelse($journaux ?? [] as $journal)
    <div class="journal-card" 
         data-statut="{{ $journal->statut }}"
         data-stagiaire="{{ $journal->user_id }}"
         data-semaine="{{ \Carbon\Carbon::parse($journal->created_at)->weekOfYear }}"
         data-titre="{{ strtolower($journal->titre) }}">
        <div class="journal-header">
            <div class="journal-avatar">
                @if($journal->user && $journal->user->avatar)
                    <img src="{{ asset('storage/' . $journal->user->avatar) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
            </div>
            <div class="journal-info">
                <h3>{{ $journal->user->first_name ?? '' }} {{ $journal->user->last_name ?? '' }}</h3>
                <div class="journal-meta">
                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($journal->created_at)->format('d/m/Y') }}</span>
                    <span><i class="fas fa-tag"></i> Semaine {{ \Carbon\Carbon::parse($journal->created_at)->weekOfYear }}</span>
                </div>
            </div>
            <div class="journal-status status-{{ $journal->statut }}">
                @if($journal->statut == 'en_attente')
                    <i class="fas fa-clock"></i> En attente
                @elseif($journal->statut == 'valide')
                    <i class="fas fa-check-circle"></i> Validé
                @else
                    <i class="fas fa-times-circle"></i> Rejeté
                @endif
            </div>
        </div>
        
        <div class="journal-content">
            <h4>{{ $journal->titre }}</h4>
            <p>{{ Str::limit($journal->contenu, 150) }}</p>
            @if($journal->commentaire_tuteur)
                <div class="tuteur-comment">
                    <i class="fas fa-comment"></i>
                    <strong>Votre commentaire :</strong> {{ $journal->commentaire_tuteur }}
                </div>
            @endif
        </div>
        
        <div class="journal-footer">
            <div class="journal-date">
                <i class="far fa-calendar-alt"></i>
                Créé le {{ \Carbon\Carbon::parse($journal->created_at)->format('d/m/Y à H:i') }}
            </div>
            <div class="journal-actions">
                <button class="btn-view" onclick="viewJournal({{ $journal->id }})">
                    <i class="fas fa-eye"></i> Voir
                </button>
                @if($journal->statut == 'en_attente')
                    <button class="btn-validate" onclick="validateJournal({{ $journal->id }})">
                        <i class="fas fa-check"></i> Valider
                    </button>
                    <button class="btn-reject" onclick="openRejectModal({{ $journal->id }})">
                        <i class="fas fa-times"></i> Rejeter
                    </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h3>Aucun journal</h3>
        <p>Aucun journal n'a été soumis pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Modal de visualisation -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-book"></i> Détail du journal</h2>
            <button class="modal-close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="viewModalBody">
            <div class="loading">Chargement...</div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeViewModal()">Fermer</button>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-times-circle"></i> Rejeter le journal</h2>
            <button class="modal-close" onclick="closeRejectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Raison du rejet <span class="required">*</span></label>
                <textarea id="rejectComment" class="form-control" rows="4" placeholder="Expliquez pourquoi ce journal est rejeté..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeRejectModal()">Annuler</button>
            <button class="btn-submit" onclick="submitReject()">Confirmer le rejet</button>
        </div>
    </div>
</div>

<!-- Notification toast -->
<div id="notificationToast" class="notification-toast">
    <div class="toast-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="toast-content">
        <div class="toast-title">Succès</div>
        <div class="toast-message">Action effectuée avec succès</div>
    </div>
</div>

<style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --bleu: #4361ee;
        --vert: #06ffa5;
        --rouge: #ef476f;
        --noir: #2b2d42;
        --gris: #6c757d;
        --gris-clair: #f8f9fa;
        --gris-fonce: #495057;
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
        display: flex;
        align-items: center;
        gap: 2rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        padding: 2rem;
        border-radius: 24px;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-10px, -10px); }
    }
    
    .welcome-logo img {
        height: 80px;
        width: auto;
        transition: transform 0.3s ease;
    }
    
    .welcome-logo img:hover {
        transform: scale(1.05) rotate(5deg);
    }
    
    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    
    .welcome-title span {
        color: #fcd34d;
    }
    
    .welcome-subtitle {
        opacity: 0.9;
        margin-top: 0.5rem;
        margin-bottom: 0;
    }
    
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
        border: 2px solid var(--gris-clair);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 80px;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card:hover::before {
        width: 100px;
        height: 100px;
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
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
    }
    
    .stat-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.5rem;
    }
    
    .stat-trend {
        font-size: 0.85rem;
        color: var(--gris);
    }
    
    .trend-up {
        color: var(--vert);
    }
    
    .trend-down {
        color: var(--rouge);
    }
    
    .filters-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        border: 2px solid var(--gris-clair);
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
        background: white;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
    }
    
    .journaux-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .journal-card {
        background: white;
        border-radius: 20px;
        border: 2px solid var(--gris-clair);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .journal-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.1);
    }
    
    .journal-header {
        padding: 1.2rem 1.5rem;
        background: linear-gradient(135deg, var(--gris-clair), white);
        border-bottom: 2px solid var(--gris-clair);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .journal-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.4rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    
    .journal-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .journal-info {
        flex: 1;
    }
    
    .journal-info h3 {
        color: var(--noir);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    
    .journal-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--gris);
        flex-wrap: wrap;
    }
    
    .journal-meta i {
        color: var(--bleu);
        margin-right: 0.3rem;
    }
    
    .journal-status {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .status-en_attente {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    
    .status-valide {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .status-rejete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }
    
    .journal-content {
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .journal-content h4 {
        color: var(--noir);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .journal-content p {
        color: var(--gris-fonce);
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .tuteur-comment {
        margin-top: 1rem;
        padding: 0.8rem;
        background: var(--gris-clair);
        border-radius: 12px;
        font-size: 0.85rem;
        color: var(--gris-fonce);
        border-left: 3px solid var(--bleu);
    }
    
    .tuteur-comment i {
        color: var(--bleu);
        margin-right: 0.3rem;
    }
    
    .journal-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .journal-date {
        font-size: 0.8rem;
        color: var(--gris);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .journal-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-view, .btn-validate, .btn-reject {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-view {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }
    
    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .btn-validate {
        background: var(--vert);
        color: white;
    }
    
    .btn-validate:hover {
        background: #059669;
        transform: translateY(-2px);
    }
    
    .btn-reject {
        background: var(--rouge);
        color: white;
    }
    
    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
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
    
    /* Modal styles */
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
        background: white;
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }
    
    .modal-large {
        max-width: 700px;
    }
    
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.2rem 1.5rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    
    .modal-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: var(--rouge);
        color: white;
        transform: rotate(90deg);
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
        margin-bottom: 1.2rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .required {
        color: var(--rouge);
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s;
        font-size: 0.9rem;
        font-family: inherit;
        resize: vertical;
    }
    
    .form-control:focus {
        border-color: var(--bleu);
        background: white;
    }
    
    .btn-cancel, .btn-submit {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-cancel {
        background: var(--gris-clair);
        color: var(--gris-fonce);
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }
    
    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        border-left: 4px solid var(--vert);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 2000;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }
    
    .notification-toast.show {
        transform: translateX(0);
    }
    
    .loading {
        text-align: center;
        padding: 2rem;
        color: var(--gris);
    }
    
    /* Scrollbar */
    .journal-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .journal-content::-webkit-scrollbar-track {
        background: var(--gris-clair);
        border-radius: 10px;
    }
    
    .journal-content::-webkit-scrollbar-thumb {
        background: var(--bleu);
        border-radius: 10px;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .journaux-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .welcome-section {
            flex-direction: column;
            text-align: center;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .journaux-grid {
            grid-template-columns: 1fr;
        }
        
        .filters-section {
            flex-direction: column;
        }
        
        .journal-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .journal-actions {
            width: 100%;
        }
        
        .btn-view, .btn-validate, .btn-reject {
            flex: 1;
            justify-content: center;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer button {
            width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .stat-value {
            font-size: 1.8rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .welcome-logo img {
            height: 60px;
        }
        
        .stat-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .journal-header {
            flex-direction: column;
            text-align: center;
        }
        
        .journal-status {
            margin-left: 0;
        }
        
        .journal-meta {
            justify-content: center;
        }
    }
</style>

<script>
    let toastTimeout;
    let currentJournalId = null;
    
    function filterJournaux() {
        const stagiaire = document.getElementById('stagiaireFilter').value;
        const statut = document.getElementById('statutFilter').value;
        const semaine = document.getElementById('semaineFilter').value;
        const search = document.getElementById('searchInput').value.toLowerCase();
        
        const cards = document.querySelectorAll('.journal-card');
        
        cards.forEach(card => {
            const cardStagiaire = card.dataset.stagiaire;
            const cardStatut = card.dataset.statut;
            const cardSemaine = card.dataset.semaine;
            const cardTitre = card.dataset.titre;
            
            let show = true;
            if (stagiaire !== 'all' && cardStagiaire != stagiaire) show = false;
            if (statut !== 'all' && cardStatut !== statut) show = false;
            if (semaine !== 'all' && cardSemaine != semaine) show = false;
            if (search && !cardTitre.includes(search)) show = false;
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    function viewJournal(id) {
        window.location.href = `/tuteur/journaux/${id}`;
    }
    
    function validateJournal(id) {
        if (confirm('Êtes-vous sûr de vouloir valider ce journal ?')) {
            fetch(`/tuteur/journaux/${id}/valider`, {
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
                    showNotification('Succès', data.message || 'Journal validé avec succès', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Erreur', data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        }
    }
    
    function openRejectModal(id) {
        currentJournalId = id;
        document.getElementById('rejectModal').style.display = 'flex';
        document.getElementById('rejectModal').classList.add('active');
        document.getElementById('rejectComment').value = '';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.getElementById('rejectModal').classList.remove('active');
        currentJournalId = null;
    }
    
    function submitReject() {
        const commentaire = document.getElementById('rejectComment').value.trim();
        if (!commentaire) {
            showNotification('Erreur', 'Veuillez indiquer une raison pour le rejet', 'error');
            return;
        }
        
        fetch(`/tuteur/journaux/${currentJournalId}/rejeter`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ commentaire: commentaire })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeRejectModal();
                showNotification('Succès', data.message || 'Journal rejeté', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur', data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    }
    
    function closeViewModal() {
        document.getElementById('viewModal').style.display = 'none';
        document.getElementById('viewModal').classList.remove('active');
    }
    
    function showNotification(title, message, type) {
        const toast = document.getElementById('notificationToast');
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toast.style.borderLeftColor = 'var(--vert)';
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = 'var(--vert)';
        } else if (type === 'error') {
            toast.style.borderLeftColor = 'var(--rouge)';
            toastIcon.className = 'fas fa-times-circle';
            toastIcon.style.color = 'var(--rouge)';
        } else {
            toast.style.borderLeftColor = 'var(--bleu)';
            toastIcon.className = 'fas fa-info-circle';
            toastIcon.style.color = 'var(--bleu)';
        }
        
        toast.classList.add('show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des clics sur les modales
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target === rejectModal) closeRejectModal();
            if (event.target === viewModal) closeViewModal();
        };
        
        // Animation d'entrée des cartes
        const cards = document.querySelectorAll('.journal-card');
        cards.forEach((card, index) => {
            card.style.animation = `slideIn 0.3s ease ${index * 0.05}s backwards`;
        });
    });
</script>
@endsection
