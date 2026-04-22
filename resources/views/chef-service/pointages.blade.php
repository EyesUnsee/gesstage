@extends('layouts.chef-service')

@section('title', 'Pointages & Présences - Chef de service')
@section('page-title', 'Pointages & Présences')
@section('active-pointages', 'active')

@section('content')
@php
    use Carbon\Carbon;
    
    $pointages = $pointages ?? collect();
    $stats = $stats ?? [
        'total_presents' => 0,
        'total_absents' => 0,
        'total_retards' => 0,
        'total_justifies' => 0,
        'taux_presence' => 0
    ];
    $services = $services ?? collect();
    $currentPeriod = $period ?? 'month';
    $currentDate = $date ?? Carbon::now();
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
    
    .period-selector {
        background: var(--blanc);
        border-radius: 50px;
        padding: 0.3rem;
        border: 2px solid var(--gris-clair);
        display: flex;
        gap: 0.3rem;
    }
    .period-btn {
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        background: transparent;
        border: none;
        color: var(--gris-fonce);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .period-btn:hover { color: var(--bleu); }
    .period-btn.active {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: var(--blanc);
    }
    
    .stats-grid {
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
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        opacity: 0.05;
        border-radius: 0 0 0 100px;
    }
    .stat-card:hover { transform: translateY(-5px); border-color: var(--bleu); }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .stat-title { color: var(--gris); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
    }
    .stat-icon.green { background: linear-gradient(135deg, var(--vert), #6ee7b7); }
    .stat-icon.red { background: linear-gradient(135deg, var(--rouge), #fca5a5); }
    .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #fcd34d); }
    .stat-icon.blue { background: linear-gradient(135deg, var(--bleu), #93c5fd); }
    .stat-value { font-size: 2rem; font-weight: 800; color: var(--noir); margin-bottom: 0.3rem; }
    .stat-detail { font-size: 0.75rem; color: var(--gris); margin-top: 0.5rem; }
    
    .filters-bar {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        border: 2px solid var(--gris-clair);
    }
    .search-box {
        flex: 2;
        min-width: 250px;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: var(--gris-clair);
        padding: 0.6rem 1rem;
        border-radius: 14px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .search-box:focus-within {
        border-color: var(--bleu);
        background: white;
    }
    .search-box i { color: var(--gris); }
    .search-box input {
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }
    .filter-select {
        padding: 0.6rem 1rem;
        background: var(--gris-clair);
        border: 2px solid transparent;
        border-radius: 14px;
        outline: none;
        cursor: pointer;
        font-size: 0.9rem;
        min-width: 150px;
        transition: all 0.2s ease;
    }
    .filter-select:focus {
        border-color: var(--bleu);
        background: white;
    }
    
    .pointages-table-section {
        background: var(--blanc);
        border-radius: 24px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
    }
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .table-header h2 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--noir);
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
    .export-btn:hover { background: var(--bleu); color: white; transform: translateY(-2px); }
    
    .pointages-table { width: 100%; border-collapse: collapse; }
    .pointages-table th {
        text-align: left;
        padding: 1rem 0.5rem;
        color: var(--gris);
        font-weight: 600;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--gris-clair);
    }
    .pointages-table td {
        padding: 1rem 0.5rem;
        color: var(--noir);
        font-weight: 500;
        border-bottom: 1px solid var(--gris-clair);
    }
    .pointages-table tr:last-child td { border-bottom: none; }
    .pointages-table tr:hover { background: var(--gris-clair); }
    
    .status-badge {
        padding: 0.3rem 1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .status-present { background: rgba(16, 185, 129, 0.1); color: var(--vert); }
    .status-absent { background: rgba(239, 68, 68, 0.1); color: var(--rouge); }
    .status-retard { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-justifie { background: rgba(59, 130, 246, 0.1); color: var(--bleu); }
    
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-btn:hover { background: var(--bleu); color: white; transform: scale(1.05); }
    
    .justify-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }
    .justify-modal.active { display: flex; }
    .modal-content {
        background: var(--blanc);
        border-radius: 30px;
        padding: 2rem;
        width: 90%;
        max-width: 500px;
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .modal-header h3 { font-size: 1.3rem; color: var(--noir); }
    .close-modal {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--gris-clair);
        border: none;
        cursor: pointer;
    }
    .close-modal:hover { background: var(--rouge); color: white; }
    .form-group { margin-bottom: 1.2rem; }
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
    }
    .form-control:focus { border-color: var(--bleu); background: white; }
    .btn-primary {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border: none;
        border-radius: 14px;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem;
        color: var(--gris);
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; color: var(--bleu); }
    
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid var(--gris-clair);
        border-top-color: var(--bleu);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .pointages-table { display: block; overflow-x: auto; }
        .filters-bar { flex-direction: column; align-items: stretch; }
        .period-selector { width: 100%; justify-content: center; }
    }
</style>

<!-- En-tête -->
<div class="page-header">
    <h1><i class="fas fa-clock"></i> Pointages & Présences</h1>
    <div class="period-selector">
        <button class="period-btn {{ $currentPeriod == 'week' ? 'active' : '' }}" data-period="week">Semaine</button>
        <button class="period-btn {{ $currentPeriod == 'month' ? 'active' : '' }}" data-period="month">Mois</button>
        <button class="period-btn {{ $currentPeriod == 'year' ? 'active' : '' }}" data-period="year">Année</button>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Présents</span>
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_presents'] }}</div>
        <div class="stat-detail">Taux: {{ $stats['taux_presence'] }}%</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Absents</span>
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_absents'] }}</div>
        <div class="stat-detail">Dont {{ $stats['total_justifies'] }} justifiés</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Retards</span>
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        </div>
        <div class="stat-value">{{ $stats['total_retards'] }}</div>
        <div class="stat-detail">Période en cours</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Taux présence</span>
            <div class="stat-icon blue"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="stat-value">{{ $stats['taux_presence'] }}%</div>
        <div class="stat-detail">Objectif: 95%</div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Rechercher un stagiaire...">
    </div>
    <select class="filter-select" id="serviceFilter">
        <option value="">Tous les services</option>
        @foreach($services as $service)
        <option value="{{ $service->id }}">{{ $service->nom }}</option>
        @endforeach
    </select>
    <select class="filter-select" id="statusFilter">
        <option value="">Tous les statuts</option>
        <option value="present">Présent</option>
        <option value="absent">Absent</option>
        <option value="retard">Retard</option>
        <option value="justifie">Justifié</option>
    </select>
</div>

<!-- Tableau des pointages -->
<div class="pointages-table-section">
    <div class="table-header">
        <h2><i class="fas fa-list"></i> Liste des pointages</h2>
       
    </div>

    <div style="overflow-x: auto;">
        <table class="pointages-table" id="pointagesTable">
            <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Arrivée</th>
                    <th>Départ</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pointagesTableBody">
                @forelse($pointages as $pointage)
                <tr data-stagiaire="{{ strtolower($pointage->stagiaire_nom) }}" data-service="{{ $pointage->service_id }}" data-status="{{ $pointage->statut }}">
                    <td><strong>{{ $pointage->stagiaire_nom }}</strong></td>
                    <td>{{ $pointage->service_nom }}</td>
                    <td>{{ Carbon::parse($pointage->date)->format('d/m/Y') }}</td>
                    <td>{{ $pointage->heure_arrivee ?? '--:--' }}</td>
                    <td>{{ $pointage->heure_depart ?? '--:--' }}</td>
                    <td>
                        <span class="status-badge status-{{ $pointage->statut }}">
                            {{ ucfirst($pointage->statut) }}
                        </span>
                    </td>
                    <td>
                        @if($pointage->statut == 'absent')
                        <button class="action-btn" onclick="openJustifyModal({{ $pointage->id }}, '{{ $pointage->stagiaire_nom }}')" title="Justifier">
                            <i class="fas fa-file-alt"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">Aucun pointage pour cette période</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de justification -->
<div class="justify-modal" id="justifyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-file-alt"></i> Justifier l'absence</h3>
            <button class="close-modal" onclick="closeJustifyModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="justifyForm">
            @csrf
            <input type="hidden" id="pointageId" name="pointage_id">
            <div class="form-group">
                <label>Stagiaire</label>
                <input type="text" id="stagiaireNom" class="form-control" readonly disabled>
            </div>
            <div class="form-group">
                <label>Motif de l'absence</label>
                <textarea name="motif" class="form-control" rows="4" placeholder="Détaillez le motif de l'absence..." required></textarea>
            </div>
            <button type="submit" class="btn-primary">Enregistrer la justification</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    let currentPointageId = null;
    
    function toggleMenu() { document.getElementById('sidebar')?.classList.toggle('active'); }
    
    function openJustifyModal(id, nom) {
        currentPointageId = id;
        document.getElementById('pointageId').value = id;
        document.getElementById('stagiaireNom').value = nom;
        document.getElementById('justifyModal').classList.add('active');
    }
    
    function closeJustifyModal() {
        document.getElementById('justifyModal').classList.remove('active');
        currentPointageId = null;
    }
    
    // Changement de période
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const period = this.dataset.period;
            const btnExport = document.getElementById('exportExcelBtn');
            if (btnExport) btnExport.innerHTML = '<i class="fas fa-spinner loading"></i> Chargement...';
            
            fetch(`{{ route('chef-service.pointages') }}?period=${period}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `{{ route('chef-service.pointages') }}?period=${period}`;
                } else {
                    showNotification('Erreur', data.message || 'Erreur de chargement', 'error');
                    if (btnExport) btnExport.innerHTML = '<i class="fas fa-file-excel"></i> Exporter Excel';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur', 'Erreur de connexion', 'error');
                if (btnExport) btnExport.innerHTML = '<i class="fas fa-file-excel"></i> Exporter Excel';
            });
        });
    });
    
    // Export Excel
    document.getElementById('exportExcelBtn')?.addEventListener('click', function() {
        const table = document.getElementById('pointagesTable');
        const rows = table.querySelectorAll('tr');
        const data = [];
        
        // En-têtes
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.innerText.trim());
        });
        data.push(headers);
        
        // Données
        table.querySelectorAll('tbody tr').forEach(row => {
            const rowData = [];
            row.querySelectorAll('td').forEach((td, index) => {
                if (index === 5) {
                    const badge = td.querySelector('.status-badge');
                    rowData.push(badge ? badge.innerText.trim() : td.innerText.trim());
                } else if (index !== 6) {
                    rowData.push(td.innerText.trim());
                }
            });
            if (rowData.length > 0) data.push(rowData);
        });
        
        if (data.length <= 1) {
            showNotification('Information', 'Aucune donnée à exporter', 'info');
            return;
        }
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Pointages');
        
        const fileName = `pointages_${new Date().toISOString().slice(0,19)}.xlsx`;
        XLSX.writeFile(wb, fileName);
        
        showNotification('Succès', 'Export Excel effectué avec succès', 'success');
    });
    
    // Justification
    document.getElementById('justifyForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Envoi...';
        submitBtn.disabled = true;
        
        fetch(`/chef-service/pointages/${currentPointageId}/justifier`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            if (data.success) {
                showNotification('Succès', data.message, 'success');
                closeJustifyModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur', data.message, 'error');
            }
        })
        .catch(error => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    });
    
    // Filtres
    const searchInput = document.getElementById('searchInput');
    const serviceFilter = document.getElementById('serviceFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterPointages() {
        const search = searchInput?.value.toLowerCase() || '';
        const service = serviceFilter?.value || '';
        const status = statusFilter?.value || '';
        
        document.querySelectorAll('#pointagesTableBody tr').forEach(row => {
            const stagiaire = row.dataset.stagiaire || '';
            const rowService = row.dataset.service || '';
            const rowStatus = row.dataset.status || '';
            
            let show = true;
            if (search && !stagiaire.includes(search)) show = false;
            if (service && rowService !== service) show = false;
            if (status && rowStatus !== status) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    searchInput?.addEventListener('keyup', filterPointages);
    serviceFilter?.addEventListener('change', filterPointages);
    statusFilter?.addEventListener('change', filterPointages);
    
    function showNotification(title, message, type) {
        const toast = document.getElementById('notificationToast');
        if (!toast) { alert(title + ': ' + message); return; }
        toast.querySelector('.toast-title').textContent = title;
        toast.querySelector('.toast-message').textContent = message;
        toast.style.borderLeftColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#3b82f6');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Fermer le modal en cliquant en dehors
    document.getElementById('justifyModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeJustifyModal();
    });
    
    @if(session('success')) showNotification('Succès', '{{ session('success') }}', 'success'); @endif
    @if(session('error')) showNotification('Erreur', '{{ session('error') }}', 'error'); @endif
</script>
@endsection
