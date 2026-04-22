@extends('layouts.candidat')

@section('title', 'Mes documents - Candidat')

@section('content')
<div class="documents-container">
    <!-- Welcome section - Plus visible -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1 class="welcome-title">Mes <span>documents</span> <span class="emoji">📄</span></h1>
            <p class="welcome-subtitle">Gérez vos documents de stage (convention, rapport, attestation...)</p>
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
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3>Total documents</h3>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-signature"></i>
            </div>
            <div class="stat-info">
                <h3>Conventions</h3>
                <div class="stat-value">{{ $stats['conventions'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="stat-info">
                <h3>Rapports</h3>
                <div class="stat-value">{{ $stats['rapports'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-info">
                <h3>Attestations</h3>
                <div class="stat-value">{{ $stats['attestations'] }}</div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="header-actions-row">
        <h2>Liste des documents</h2>
        <a href="{{ route('candidat.documents.upload') }}" class="btn-primary">
            <i class="fas fa-upload"></i>
            Uploader un document
        </a>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <div class="filter-group">
            <select id="typeFilter">
                <option value="all">Tous les types</option>
                @foreach($types as $key => $label)
                    @if(!in_array($key, ['lettre_motivation']))
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <select id="statutFilter">
                <option value="all">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="valide">Validé</option>
                <option value="rejete">Rejeté</option>
            </select>
        </div>
        <div class="filter-group">
            <input type="text" id="searchInput" placeholder="Rechercher par titre...">
        </div>
    </div>

    <!-- Table des documents -->
    <div class="table-container">
        <table class="documents-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Fichier</th>
                    <th>Taille</th>
                    <th>Statut</th>
                    <th>Date d'upload</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="documentsTableBody">
                @forelse($documents as $document)
                    @if(!in_array($document->type, ['lettre_motivation']))
                    <tr class="document-row" data-type="{{ $document->type }}" data-statut="{{ $document->statut }}" data-titre="{{ strtolower($document->titre) }}">
                        <td data-label="Titre">
                            <div class="document-info">
                                <i class="fas fa-file-{{ $document->type === 'convention' ? 'signature' : ($document->type === 'rapport' ? 'file-pdf' : ($document->type === 'attestation' ? 'award' : 'file')) }}"></i>
                                <span class="document-titre">{{ $document->titre }}</span>
                                @if($document->description)
                                    <span class="tooltip" title="{{ $document->description }}">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Type">
                            <span class="badge badge-{{ $document->type }}">
                                {{ $types[$document->type] ?? $document->type }}
                            </span>
                        </td>
                        <td data-label="Fichier">
                            <div class="file-name">
                                <i class="fas fa-paperclip"></i>
                                {{ strlen($document->fichier_nom) > 40 ? substr($document->fichier_nom, 0, 40) . '...' : $document->fichier_nom }}
                            </div>
                        </td>
                        <td data-label="Taille">{{ $document->taille }} Ko</td>
                        <td data-label="Statut">
                            <div class="status-badge">
                                @if($document->statut === 'valide')
                                    <span class="badge status-valid">
                                        <i class="fas fa-check-circle"></i> Validé
                                    </span>
                                @elseif($document->statut === 'rejete')
                                    <span class="badge status-rejected">
                                        <i class="fas fa-times-circle"></i> Rejeté
                                    </span>
                                @else
                                    <span class="badge status-pending">
                                        <i class="fas fa-clock"></i> En attente
                                    </span>
                                @endif
                                
                                @if($document->commentaire)
                                    <span class="tooltip" title="{{ $document->commentaire }}">
                                        <i class="fas fa-comment"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Date">
                            <div class="date-info">
                                {{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') }}
                                <span class="time">{{ \Carbon\Carbon::parse($document->created_at)->format('H:i') }}</span>
                            </div>
                        </td>
                        <td data-label="Actions">
                            <div class="action-buttons">
                                <a href="{{ route('candidat.documents.download', $document->id) }}" class="btn-icon" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="{{ route('candidat.documents.show', $document->id) }}" class="btn-icon" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('candidat.documents.destroy', $document->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>Aucun document pour le moment</p>
                            <a href="{{ route('candidat.documents.upload') }}" class="btn-primary" style="display: inline-flex;">
                                <i class="fas fa-upload"></i>
                                Uploader un document
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($documents->count() > 10)
    <div class="pagination">
        <button class="page-btn" onclick="previousPage()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div id="paginationNumbers" class="pagination-numbers"></div>
        <button class="page-btn" onclick="nextPage()">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    @endif
</div>

<style>
    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .documents-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Welcome section - Plus visible */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        margin-bottom: 2rem;
        padding: 2rem;
        text-align: center;
        box-shadow: var(--shadow-lg);
    }

    .welcome-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.75rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .welcome-title span {
        background: linear-gradient(135deg, #fff, #e0d4ff);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        text-shadow: none;
    }

    .welcome-title .emoji {
        background: none;
        -webkit-background-clip: unset;
        background-clip: unset;
        color: white;
    }

    .welcome-subtitle {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    /* Alerts */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        border-color: var(--gray-300);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #e0e7ff, #d1fae5);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.25rem;
    }

    .stat-info h3 {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-600);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-800);
        line-height: 1;
    }

    /* Header actions */
    .header-actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-actions-row h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    /* Filters */
    .filters {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 160px;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-700);
        background: white;
        transition: all 0.2s ease;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    /* Table */
    .table-container {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid var(--gray-200);
        overflow-x: auto;
    }

    .documents-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .documents-table th {
        text-align: left;
        padding: 0.875rem 1rem;
        background: var(--gray-100);
        color: var(--gray-700);
        font-weight: 600;
        border-bottom: 1px solid var(--gray-200);
    }

    .documents-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--gray-200);
        color: var(--gray-700);
        vertical-align: middle;
    }

    .documents-table tr:last-child td {
        border-bottom: none;
    }

    .documents-table tr:hover td {
        background: var(--gray-100);
    }

    /* Document info */
    .document-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .document-info > i {
        color: var(--primary);
        font-size: 1rem;
    }

    .document-titre {
        font-weight: 500;
    }

    .tooltip {
        position: relative;
        display: inline-flex;
        cursor: help;
    }

    .tooltip i {
        color: var(--gray-400);
        font-size: 0.75rem;
    }

    .tooltip:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--gray-800);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 10;
        margin-bottom: 0.25rem;
    }

    /* File name */
    .file-name {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.813rem;
    }

    .file-name i {
        color: var(--success);
    }

    /* Date info */
    .date-info {
        font-size: 0.813rem;
    }

    .date-info .time {
        font-size: 0.688rem;
        color: var(--gray-500);
        display: block;
        margin-top: 0.125rem;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-convention {
        background: #e0e7ff;
        color: #4338ca;
    }

    .badge-rapport {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-attestation {
        background: #fed7aa;
        color: #92400e;
    }

    .badge-cv {
        background: #e0e7ff;
        color: #4338ca;
    }

    .badge-diplome {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-autre {
        background: #f1f5f9;
        color: #475569;
    }

    .status-valid {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-pending {
        background: #fed7aa;
        color: #92400e;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 0.375rem;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 0.375rem;
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
        color: var(--gray-800);
    }

    .btn-delete {
        background: none;
        border: 1px solid var(--gray-300);
    }

    .btn-delete:hover {
        background: #fef2f2;
        border-color: var(--danger);
        color: var(--danger);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state p {
        margin-bottom: 1rem;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .page-btn {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .page-btn:hover {
        background: var(--gray-100);
        border-color: var(--gray-400);
    }

    .pagination-numbers {
        display: flex;
        gap: 0.25rem;
    }

    .pagination-numbers .page-item {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pagination-numbers .page-item:hover {
        background: var(--gray-100);
    }

    .pagination-numbers .page-item.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .documents-table {
            display: block;
        }

        .documents-table thead {
            display: none;
        }

        .documents-table tbody,
        .documents-table tr,
        .documents-table td {
            display: block;
        }

        .documents-table tr {
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .documents-table td {
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--gray-200);
        }

        .documents-table td:last-child {
            border-bottom: none;
        }

        .documents-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--gray-600);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-card {
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .welcome-title {
            font-size: 1.75rem;
        }

        .welcome-subtitle {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Filtres
    document.getElementById('typeFilter').addEventListener('change', filterDocuments);
    document.getElementById('statutFilter').addEventListener('change', filterDocuments);
    document.getElementById('searchInput').addEventListener('keyup', filterDocuments);
    
    function filterDocuments() {
        const type = document.getElementById('typeFilter').value;
        const statut = document.getElementById('statutFilter').value;
        const search = document.getElementById('searchInput').value.toLowerCase();
        
        const rows = document.querySelectorAll('.document-row');
        
        rows.forEach(row => {
            const rowType = row.dataset.type;
            const rowStatut = row.dataset.statut;
            const rowTitre = row.dataset.titre;
            
            let show = true;
            
            if (type !== 'all' && rowType !== type) show = false;
            if (statut !== 'all' && rowStatut !== statut) show = false;
            if (search && !rowTitre.includes(search)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
        
        currentPage = 1;
        updatePagination();
    }
    
    // Pagination
    let currentPage = 1;
    const rowsPerPage = 10;
    
    function updatePagination() {
        const rows = Array.from(document.querySelectorAll('.document-row')).filter(row => row.style.display !== 'none');
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        
        rows.forEach((row, index) => {
            const page = Math.floor(index / rowsPerPage) + 1;
            row.style.display = page === currentPage ? '' : 'none';
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
        const rows = Array.from(document.querySelectorAll('.document-row')).filter(row => row.style.display !== 'none');
        const totalPages = Math.ceil(rows.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updatePagination();
    });
</script>
@endsection
