@extends('layouts.chef-service')

@section('title', 'Gestion de l\'équipe - Chef de service')
@section('page-title', 'Gestion de l\'équipe')
@section('active-equipe', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    // Récupération des données depuis le contrôleur
    $membres = $membres ?? collect();
    $stats = $stats ?? [
        'total' => 0,
        'tuteurs' => 0,
        'stagiaires' => 0,
        'actifs' => 0
    ];
@endphp

<style>
    /* ===== STYLES SPÉCIFIQUES ÉQUIPE ===== */
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

    .add-member-btn {
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

    .add-member-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -8px var(--bleu);
    }

    /* Stats cards */
    .stats-equipe-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-equipe-card {
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

    .stat-equipe-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
    }

    .stat-equipe-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 1.3rem;
    }

    .stat-equipe-info h3 {
        color: var(--gris);
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .stat-equipe-info .number {
        font-size: 1.8rem;
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

    /* Team grid */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .member-card {
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

    .member-card::before {
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

    .member-card:hover {
        transform: translateY(-5px);
        border-color: var(--bleu);
        box-shadow: 0 20px 40px -15px var(--bleu);
    }

    .member-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .member-avatar {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanc);
        font-size: 2rem;
        box-shadow: 0 10px 20px -8px var(--bleu);
    }

    .member-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-tuteur {
        background: rgba(59, 130, 246, 0.1);
        color: var(--bleu);
    }

    .badge-stagiaire {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }

    .badge-inactif {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }

    .member-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--noir);
        margin-bottom: 0.3rem;
    }

    .member-email {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .member-email i {
        color: var(--bleu);
        width: 20px;
    }

    .member-phone {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gris);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .member-phone i {
        color: var(--vert);
        width: 20px;
    }

    .member-stats {
        display: flex;
        justify-content: space-around;
        padding: 0.8rem 0;
        margin: 1rem 0;
        border-top: 2px solid var(--gris-clair);
        border-bottom: 2px solid var(--gris-clair);
    }

    .member-stat {
        text-align: center;
    }

    .member-stat-value {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--noir);
    }

    .member-stat-label {
        font-size: 0.7rem;
        color: var(--gris);
    }

    .member-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .member-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--gris-clair);
        border: none;
        color: var(--gris-fonce);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .member-btn:hover {
        background: var(--bleu);
        color: var(--blanc);
        transform: scale(1.1);
    }

    .member-btn.danger:hover {
        background: var(--rouge);
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

    /* Modal */
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

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-equipe-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .team-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .team-grid {
            grid-template-columns: 1fr;
        }
        .stats-equipe-grid {
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
    }

    @media (max-width: 768px) {
        .modal-footer {
            flex-direction: column;
        }
        .modal-footer button {
            width: 100%;
        }
        .member-stats {
            flex-direction: column;
            gap: 0.5rem;
        }
        .member-footer {
            justify-content: center;
        }
    }
</style>

<!-- En-tête de page -->
<div class="page-header">
    <h1>
        <i class="fas fa-users"></i>
        Gestion de l'équipe
    </h1>
    <div class="action-buttons">
        <button class="add-member-btn" onclick="openModal()">
            <i class="fas fa-user-plus"></i> Ajouter un membre
        </button>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-equipe-grid">
    <div class="stat-equipe-card">
        <div class="stat-equipe-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-equipe-info">
            <h3>Total membres</h3>
            <div class="number">{{ $stats['total'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-equipe-card">
        <div class="stat-equipe-icon">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <div class="stat-equipe-info">
            <h3>Tuteurs</h3>
            <div class="number">{{ $stats['tuteurs'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-equipe-card">
        <div class="stat-equipe-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="stat-equipe-info">
            <h3>Stagiaires</h3>
            <div class="number">{{ $stats['stagiaires'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-equipe-card">
        <div class="stat-equipe-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-equipe-info">
            <h3>Actifs</h3>
            <div class="number">{{ $stats['actifs'] ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-section">
    <div class="search-filter">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Rechercher par nom, email, rôle...">
    </div>
    <select class="filter-select" id="roleFilter">
        <option value="">Tous les rôles</option>
        <option value="tuteur">Tuteurs</option>
        <option value="candidat">Stagiaires</option>
    </select>
    <select class="filter-select" id="statusFilter">
        <option value="">Tous les statuts</option>
        <option value="actif">Actifs</option>
        <option value="inactif">Inactifs</option>
    </select>
</div>

<!-- Grille des membres -->
<div class="team-grid" id="teamGrid">
    @forelse($membres as $membre)
    <div class="member-card" data-role="{{ $membre->role }}" data-status="{{ $membre->is_active ? 'actif' : 'inactif' }}" data-name="{{ strtolower($membre->first_name . ' ' . $membre->last_name) }}" data-email="{{ strtolower($membre->email) }}">
        <div class="member-header">
            <div class="member-avatar">
                @if($membre->avatar)
                    <img src="{{ asset('storage/' . $membre->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 20px; object-fit: cover;">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <span class="member-badge badge-{{ $membre->role }}">
                {{ $membre->role == 'tuteur' ? 'Tuteur' : 'Stagiaire' }}
            </span>
        </div>
        <div class="member-name">{{ $membre->first_name }} {{ $membre->last_name }}</div>
        <div class="member-email">
            <i class="fas fa-envelope"></i>
            <span>{{ $membre->email }}</span>
        </div>
        <div class="member-phone">
            <i class="fas fa-phone"></i>
            <span>{{ $membre->phone ?? 'Non renseigné' }}</span>
        </div>
        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-value">{{ $membre->stagiaires_count ?? 0 }}</div>
                <div class="member-stat-label">Stagiaires</div>
            </div>
            <div class="member-stat">
                <div class="member-stat-value">{{ $membre->evaluations_count ?? 0 }}</div>
                <div class="member-stat-label">Évaluations</div>
            </div>
            <div class="member-stat">
                <div class="member-stat-value">{{ $membre->presence_taux ?? 0 }}%</div>
                <div class="member-stat-label">Présence</div>
            </div>
        </div>
        <div class="member-footer">
            <button class="member-btn" onclick="editMembre({{ $membre->id }})" title="Modifier">
                <i class="fas fa-edit"></i>
            </button>
            <button class="member-btn" onclick="viewMembre({{ $membre->id }})" title="Voir">
                <i class="fas fa-eye"></i>
            </button>
            <button class="member-btn danger" onclick="deleteMembre({{ $membre->id }})" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>Aucun membre dans l'équipe</p>
        <small>Cliquez sur "Ajouter un membre" pour commencer</small>
    </div>
    @endforelse
</div>

<!-- Modal Ajout/Modification -->
<div class="modal" id="memberModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-user-plus"></i>
                <span id="modalTitle">Ajouter un membre</span>
            </h2>
            <button class="close-modal" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="memberForm" method="POST" action="{{ route('chef-service.equipe.store') }}">
            @csrf
            <input type="hidden" id="membreId" name="membre_id">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" id="firstName" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" id="lastName" name="last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="tel" id="phone" name="phone" class="form-control">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="tuteur">Tuteur</option>
                    <option value="candidat">Stagiaire</option>
                </select>
            </div>
            <div class="form-group" id="tuteurGroup" style="display: none;">
                <label>Tuteur assigné (pour stagiaire)</label>
                <select id="tuteurId" name="tuteur_id" class="form-control">
                    <option value="">Sélectionner un tuteur</option>
                    @foreach($tuteurs as $tuteur)
                    <option value="{{ $tuteur->id }}">{{ $tuteur->first_name }} {{ $tuteur->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Laisser vide pour conserver l'actuel">
            </div>
            <div class="form-group">
                <label>Confirmation</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentMembreId = null;
    
    function openModal(id = null) {
        currentMembreId = id;
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('memberForm');
        
        if (id) {
            modalTitle.textContent = 'Modifier un membre';
            fetch(`/chef-service/equipe/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('firstName').value = data.first_name;
                    document.getElementById('lastName').value = data.last_name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('phone').value = data.phone || '';
                    document.getElementById('role').value = data.role;
                    document.getElementById('tuteurId').value = data.tuteur_id || '';
                    document.getElementById('membreId').value = id;
                    
                    // Afficher/cacher le champ tuteur selon le rôle
                    document.getElementById('tuteurGroup').style.display = data.role === 'candidat' ? 'block' : 'none';
                    
                    // Supprimer l'obligation du mot de passe en modification
                    document.getElementById('password').required = false;
                    document.getElementById('password_confirmation').required = false;
                })
                .catch(error => console.error('Erreur:', error));
        } else {
            modalTitle.textContent = 'Ajouter un membre';
            form.reset();
            document.getElementById('membreId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password_confirmation').required = true;
            document.getElementById('tuteurGroup').style.display = 'none';
        }
        document.getElementById('memberModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('memberModal').classList.remove('active');
        currentMembreId = null;
    }
    
    // Afficher/cacher le champ tuteur selon le rôle
    document.getElementById('role')?.addEventListener('change', function() {
        const tuteurGroup = document.getElementById('tuteurGroup');
        if (this.value === 'candidat') {
            tuteurGroup.style.display = 'block';
        } else {
            tuteurGroup.style.display = 'none';
        }
    });
    
    // Soumission du formulaire via AJAX
    const memberForm = document.getElementById('memberForm');
    if (memberForm) {
        memberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const membreId = document.getElementById('membreId').value;
            const url = membreId ? `/chef-service/equipe/${membreId}` : this.action;
            const method = membreId ? 'POST' : 'POST';
            
            if (membreId) {
                formData.append('_method', 'PUT');
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Chargement...';
            submitBtn.disabled = true;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                    closeModal();
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
                showNotification('Erreur', 'Une erreur est survenue', 'error');
            });
        });
    }
    
    function editMembre(id) {
        openModal(id);
    }
    
    function viewMembre(id) {
        showNotification('Information', 'Affichage des détails du membre', 'info');
        window.location.href = `/chef-service/equipe/${id}`;
    }
    
    function deleteMembre(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')) {
            fetch(`/chef-service/equipe/${id}`, {
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
    
    // Filtres
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        filterMembers();
    });
    
    document.getElementById('roleFilter')?.addEventListener('change', function() {
        filterMembers();
    });
    
    document.getElementById('statusFilter')?.addEventListener('change', function() {
        filterMembers();
    });
    
    function filterMembers() {
        const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const roleFilter = document.getElementById('roleFilter')?.value || '';
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        
        document.querySelectorAll('.member-card').forEach(card => {
            const name = card.dataset.name || '';
            const email = card.dataset.email || '';
            const role = card.dataset.role || '';
            const status = card.dataset.status || '';
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleFilter || role === roleFilter;
            const matchesStatus = !statusFilter || status === statusFilter;
            
            card.style.display = (matchesSearch && matchesRole && matchesStatus) ? 'block' : 'none';
        });
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
    
    // Fermer le modal en cliquant en dehors
    const modal = document.getElementById('memberModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    }
    
    // Animation des cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.member-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.05}s`;
        });
    });
    
    // Afficher les notifications de session
    @if(session('success'))
        showNotification('Succès', '{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('Erreur', '{{ session('error') }}', 'error');
    @endif
</script>
@endsection
