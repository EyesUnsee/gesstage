@extends('layouts.candidat')

@section('title', 'Détails du document - Candidat')

@section('content')
<div class="document-detail-container">
    <div class="welcome-section">
        <h1 class="welcome-title">Détails du <span>document</span> 📄</h1>
        <p class="welcome-subtitle">Informations détaillées du document</p>
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

    <div class="document-card">
        <div class="document-header">
            <div class="document-icon">
                @if($document->type === 'convention')
                    <i class="fas fa-file-signature"></i>
                @elseif($document->type === 'rapport')
                    <i class="fas fa-file-pdf"></i>
                @elseif($document->type === 'attestation')
                    <i class="fas fa-award"></i>
                @else
                    <i class="fas fa-file-alt"></i>
                @endif
            </div>
            <div class="document-title">
                <h2>{{ $document->titre }}</h2>
                <span class="badge-type badge-{{ $document->type }}">
                    {{ $types[$document->type] ?? $document->type }}
                </span>
            </div>
            <div class="document-status">
                @if($document->statut === 'valide')
                    <span class="badge badge-success">
                        <i class="fas fa-check-circle"></i> Validé
                    </span>
                @elseif($document->statut === 'rejete')
                    <span class="badge badge-danger">
                        <i class="fas fa-times-circle"></i> Rejeté
                    </span>
                @else
                    <span class="badge badge-warning">
                        <i class="fas fa-clock"></i> En attente
                    </span>
                @endif
            </div>
        </div>

        <div class="document-info-grid">
            <div class="info-group">
                <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                <div class="info-row">
                    <span class="info-label">Titre :</span>
                    <span class="info-value">{{ $document->titre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Type :</span>
                    <span class="info-value">{{ $types[$document->type] ?? $document->type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nom du fichier :</span>
                    <span class="info-value">{{ $document->fichier_nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Taille :</span>
                    <span class="info-value">{{ $document->taille }} Ko</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date d'upload :</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y à H:i') }}</span>
                </div>
                @if($document->description)
                <div class="info-row">
                    <span class="info-label">Description :</span>
                    <span class="info-value">{{ $document->description }}</span>
                </div>
                @endif
            </div>

            @if($document->commentaire)
            <div class="info-group">
                <h3><i class="fas fa-comment"></i> Commentaire</h3>
                <div class="comment-box">
                    <i class="fas fa-quote-left"></i>
                    <p>{{ $document->commentaire }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="document-actions">
            <a href="{{ route('candidat.documents.download', $document->id) }}" class="btn-primary">
                <i class="fas fa-download"></i> Télécharger le document
            </a>
            <a href="{{ route('candidat.documents.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button onclick="deleteDocument({{ $document->id }}, '{{ addslashes($document->titre) }}')" class="btn-danger">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>
</div>

<style>
    .document-detail-container {
        max-width: 900px;
        margin: 0 auto;
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
    
    .document-card {
        background: var(--blanc);
        border-radius: 24px;
        border: 2px solid var(--gris-clair);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    
    .document-header {
        background: linear-gradient(135deg, var(--gris-clair), var(--blanc));
        padding: 2rem;
        border-bottom: 2px solid var(--gris-clair);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    
    .document-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
    }
    
    .document-title {
        flex: 1;
    }
    
    .document-title h2 {
        color: var(--noir);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .badge-type {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-convention {
        background: rgba(59, 130, 246, 0.1);
        color: var(--bleu);
    }
    
    .badge-rapport {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }
    
    .badge-attestation {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .badge-autre {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gris-fonce);
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }
    
    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }
    
    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .document-info-grid {
        padding: 2rem;
        display: grid;
        gap: 2rem;
    }
    
    .info-group h3 {
        color: var(--noir);
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-group h3 i {
        color: var(--bleu);
    }
    
    .info-row {
        display: flex;
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--gris-clair);
    }
    
    .info-label {
        width: 150px;
        color: var(--gris);
        font-weight: 500;
    }
    
    .info-value {
        flex: 1;
        color: var(--noir);
    }
    
    .comment-box {
        background: var(--gris-clair);
        padding: 1rem;
        border-radius: 12px;
        position: relative;
    }
    
    .comment-box i {
        position: absolute;
        top: -10px;
        left: 10px;
        color: var(--bleu);
        font-size: 1.2rem;
        background: var(--blanc);
        padding: 0 0.5rem;
    }
    
    .comment-box p {
        margin-top: 0.5rem;
        color: var(--gris-fonce);
        line-height: 1.5;
    }
    
    .document-actions {
        padding: 1.5rem 2rem 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        border-top: 2px solid var(--gris-clair);
    }
    
    .btn-primary, .btn-secondary, .btn-danger {
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--bleu);
    }
    
    .btn-secondary {
        background: var(--gris-clair);
        color: var(--gris-fonce);
        border: 2px solid var(--gris);
    }
    
    .btn-secondary:hover {
        background: var(--blanc);
        border-color: var(--bleu);
        color: var(--bleu);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, var(--rouge), #dc2626);
        color: white;
        border: none;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px var(--rouge);
    }
    
    @media (max-width: 768px) {
        .document-header {
            flex-direction: column;
            text-align: center;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 0.3rem;
        }
        
        .document-actions {
            flex-direction: column;
        }
        
        .btn-primary, .btn-secondary, .btn-danger {
            justify-content: center;
        }
    }
</style>

<script>
    function deleteDocument(id, titre) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer le document "${titre}" ? Cette action est irréversible.`)) {
            fetch(`/candidat/documents/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("candidat.documents.index") }}';
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible de supprimer le document'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la suppression');
            });
        }
    }
</script>
@endsection
