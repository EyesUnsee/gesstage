@extends('layouts.responsable')

@section('title', 'Gestion des responsables - Responsable')

@section('content')
<!-- Welcome section -->
<div class="welcome-section">
    <div class="welcome-text">
        <h1 class="welcome-title">Gestion des <span>responsables</span> 👨‍💼</h1>
        <p class="welcome-subtitle">Consultez et gérez tous les responsables de la plateforme</p>
    </div>
    <a href="{{ route('responsable.responsables.create') }}" class="btn-add">
        <i class="fas fa-user-plus"></i>
        Ajouter un responsable
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
            <span class="stat-title">Total responsables</span>
            <span class="stat-icon"><i class="fas fa-user-tie"></i></span>
        </div>
        <div class="stat-value">{{ $totalResponsables ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-arrow-up" style="color: var(--vert);"></i> +{{ $nouveauxResponsables ?? 0 }} ce mois
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Responsables actifs</span>
            <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $responsablesActifs ?? 0 }}</div>
        <div class="stat-trend">
            <i class="fas fa-user-check" style="color: var(--vert);"></i> {{ $pourcentageActifs ?? 0 }}%
        </div>
    </div>
</div>

<!-- Recherche et filtres -->
<div class="search-section">
    <form method="GET" action="{{ route('responsable.responsables.index') }}" id="filterForm">
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
            <select name="statut" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
            </select>
            <select name="sort" class="filter-select" onchange="this.form.submit()">
                <option value="nom" {{ request('sort') == 'nom' ? 'selected' : '' }}>Tri par nom</option>
                <option value="departement" {{ request('sort') == 'departement' ? 'selected' : '' }}>Tri par département</option>
            </select>
        </div>
    </form>
</div>

<!-- Liste des responsables -->
<div class="responsables-grid">
    @forelse($responsables ?? [] as $responsable)
    <div class="responsable-card">
        <div class="card-header">
            <div class="card-avatar">
                @if($responsable->avatar)
                    <img src="{{ Illuminate\Support\Facades\Storage::url($responsable->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user-tie"></i>
                @endif
            </div>
            <div class="card-info">
                <h3>{{ $responsable->first_name }} {{ $responsable->last_name }}</h3>
                <div class="card-role">
                    <i class="fas fa-building"></i>
                    {{ $responsable->departement ?? 'Département non défini' }}
                </div>
            </div>
        </div>
        <div class="card-details">
            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $responsable->email }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-phone"></i>
                <span>{{ $responsable->phone ?? 'Non renseigné' }}</span>
            </div>
            <div class="detail-item">
                <i class="fas fa-calendar-alt"></i>
                <span><strong>Inscrit le:</strong> {{ \Carbon\Carbon::parse($responsable->created_at)->format('d/m/Y') }}</span>
            </div>
            @if($responsable->poste)
            <div class="detail-item">
                <i class="fas fa-user-tag"></i>
                <span><strong>Poste:</strong> {{ $responsable->poste }}</span>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="card-status">
                @if($responsable->is_active)
                    <i class="fas fa-circle status-active"></i>
                    <span>Actif</span>
                @else
                    <i class="fas fa-circle status-inactive"></i>
                    <span>Inactif</span>
                @endif
            </div>
            <div class="card-actions">
                <a href="{{ route('responsable.responsables.edit', $responsable->id) }}" class="btn-icon edit" title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>
                @if(auth()->id() != $responsable->id)
                <button class="btn-icon delete" title="Supprimer" onclick="deleteResponsable({{ $responsable->id }}, '{{ addslashes($responsable->first_name . ' ' . $responsable->last_name) }}')">
                    <i class="fas fa-trash"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-user-tie"></i>
        <h3>Aucun responsable</h3>
        <p>Aucun responsable trouvé pour le moment</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if(isset($responsables) && $responsables->count() > 10)
<div class="pagination">
    {{ $responsables->appends(request()->query())->links() }}
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
            <p>Êtes-vous sûr de vouloir supprimer le responsable :</p>
            <p class="responsable-name-to-delete" style="font-weight: bold; color: var(--rouge); margin: 1rem 0;"></p>
            <p class="text-warning">Cette action est irréversible.</p>
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
        grid-template-columns: repeat(2, 1fr);
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
    
    .responsables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .responsable-card {
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
    
    .responsable-card::before {
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
    
    .responsable-card:hover {
        transform: translateY(-5px);
        border-color: #8b5cf6;
    }
    
    .card-avatar {
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
    
    .card-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .card-info h3 {
        color: var(--noir);
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }
    
    .card-role {
        color: var(--gris);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .card-role i {
        color: #8b5cf6;
        font-size: 0.8rem;
    }
    
    .card-details {
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
    
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 1rem;
    }
    
    .card-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }
    
    .status-active {
        color: var(--vert);
    }
    
    .status-inactive {
        color: var(--rouge);
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
        background: #8b5cf6;
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
        .responsables-grid {
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
        
        .responsables-grid {
            grid-template-columns: 1fr;
        }
        
        .card-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .card-header {
            flex-direction: column;
            text-align: center;
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
    }
</style>

<script>
    let deleteResponsableId = null;
    let deleteResponsableNom = null;
    
    function deleteResponsable(id, nom) {
        deleteResponsableId = id;
        deleteResponsableNom = nom;
        document.querySelector('.responsable-name-to-delete').textContent = nom;
        document.getElementById('deleteModal').classList.add('active');
        
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/responsable/responsables/${id}`;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        deleteResponsableId = null;
        deleteResponsableNom = null;
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
