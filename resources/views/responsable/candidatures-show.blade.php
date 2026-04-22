@extends('layouts.responsable')

@section('title', 'Détails de la candidature')

@section('content')
<div class="container">
    <!-- En-tête -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h1 class="welcome-title">Détails de la <span>candidature</span> 📄</h1>
            <p class="welcome-subtitle">Consultez toutes les informations de la candidature et les documents du candidat</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('responsable.candidatures.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('token_generated'))
        <div class="alert alert-token">
            <i class="fas fa-key"></i>
            <div>
                <strong>Token d'accès généré avec succès !</strong><br>
                <span>Code: <code style="font-size: 1.2rem; background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 8px;">{{ session('token_generated') }}</code></span><br>
                <small>Ce token a été envoyé au candidat par email. Il lui permettra d'accéder à son dashboard.</small>
            </div>
        </div>
    @endif

    <div class="candidature-detail">
        <!-- En-tête de la candidature -->
        <div class="detail-header">
            <div class="header-left">
                <h2>{{ $candidature->titre }}</h2>
                <div class="entreprise-info">
                    <i class="fas fa-building"></i>
                    <span>{{ $candidature->entreprise ?? 'Entreprise non spécifiée' }}</span>
                </div>
            </div>
            <div class="header-right">
                <span class="status-badge status-{{ $candidature->statut }}">
                    @if($candidature->statut == 'en_attente')
                        <i class="fas fa-clock"></i> En attente
                    @elseif($candidature->statut == 'acceptee')
                        <i class="fas fa-check-circle"></i> Acceptée
                    @elseif($candidature->statut == 'refusee')
                        <i class="fas fa-times-circle"></i> Refusée
                    @elseif($candidature->statut == 'en_cours')
                        <i class="fas fa-spinner"></i> En cours
                    @endif
                </span>
            </div>
        </div>

        <!-- Grille d'informations -->
        <div class="detail-grid">
            <!-- Colonne gauche - Infos candidat -->
            <div class="detail-card">
                <h3><i class="fas fa-user-graduate"></i> Informations candidat</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Nom complet :</span>
                        <span class="info-value">{{ $candidature->candidat->first_name ?? '' }} {{ $candidature->candidat->last_name ?? '' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email :</span>
                        <span class="info-value">{{ $candidature->candidat->email ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Téléphone :</span>
                        <span class="info-value">{{ $candidature->candidat->phone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Adresse :</span>
                        <span class="info-value">{{ $candidature->candidat->address ?? 'Non renseignée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de naissance :</span>
                        <span class="info-value">{{ $candidature->candidat->birth_date ? \Carbon\Carbon::parse($candidature->candidat->birth_date)->format('d/m/Y') : 'Non renseignée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Formation :</span>
                        <span class="info-value">{{ $candidature->candidat->formation ?? 'Non renseignée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Université :</span>
                        <span class="info-value">{{ $candidature->candidat->universite ?? 'Non renseignée' }}</span>
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Infos stage -->
            <div class="detail-card">
                <h3><i class="fas fa-briefcase"></i> Informations stage</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Type de stage :</span>
                        <span class="info-value">{{ $candidature->typeLabel }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de début :</span>
                        <span class="info-value">{{ $candidature->date_debut ? \Carbon\Carbon::parse($candidature->date_debut)->format('d/m/Y') : 'Non spécifiée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de fin :</span>
                        <span class="info-value">{{ $candidature->date_fin ? \Carbon\Carbon::parse($candidature->date_fin)->format('d/m/Y') : 'Non spécifiée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de candidature :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($candidature->created_at)->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if($candidature->date_reponse)
                    <div class="info-item">
                        <span class="info-label">Date de réponse :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($candidature->date_reponse)->format('d/m/Y à H:i') }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Statut dossier :</span>
                        <span class="info-value">
                            @if($candidature->candidat->dossier_valide)
                                <span class="badge-valid">✅ Validé</span>
                            @else
                                <span class="badge-pending">⏳ En attente de validation</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="detail-card full-width">
            <h3><i class="fas fa-align-left"></i> Description</h3>
            <div class="description-content">
                {{ $candidature->description ?? 'Aucune description fournie.' }}
            </div>
        </div>

        <!-- ========== SECTION DOCUMENTS UPLOADÉS PAR LE CANDIDAT ========== -->
        @php
            $documents = App\Models\Document::where('user_id', $candidature->candidat_id)->get();
        @endphp
        
        @if($documents->count() > 0)
        <div class="detail-card full-width">
            <h3><i class="fas fa-folder-open"></i> Documents du candidat</h3>
            <div class="documents-grid">
                @foreach($documents as $document)
                    @php
                        $icon = match($document->type) {
                            'cv' => 'fa-file-pdf',
                            'lettre_motivation' => 'fa-file-alt',
                            'diplome' => 'fa-graduation-cap',
                            default => 'fa-file'
                        };
                        $color = match($document->type) {
                            'cv' => '#ef4444',
                            'lettre_motivation' => '#3b82f6',
                            'diplome' => '#10b981',
                            default => '#64748b'
                        };
                        $label = match($document->type) {
                            'cv' => 'CV',
                            'lettre_motivation' => 'Lettre de motivation',
                            'diplome' => 'Diplôme',
                            default => ucfirst($document->type)
                        };
                    @endphp
                    <div class="document-card">
                        <div class="document-icon" style="background: {{ $color }}20;">
                            <i class="fas {{ $icon }}" style="color: {{ $color }};"></i>
                        </div>
                        <div class="document-info">
                            <h4>{{ $label }}</h4>
                            <p class="document-name">{{ $document->titre ?? $document->fichier_nom ?? 'Document' }}</p>
                            <p class="document-meta">
                                <i class="fas fa-calendar-alt"></i> 
                                {{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y') }}
                                @if($document->taille)
                                    • <i class="fas fa-database"></i> {{ round($document->taille, 2) }} KB
                                @endif
                            </p>
                            <span class="document-status status-{{ $document->statut }}">
                                @if($document->statut == 'valide')
                                    <i class="fas fa-check-circle"></i> Validé
                                @elseif($document->statut == 'rejete')
                                    <i class="fas fa-times-circle"></i> Rejeté
                                @else
                                    <i class="fas fa-clock"></i> En attente
                                @endif
                            </span>
                        </div>
                        <div class="document-actions">
                            <a href="{{ asset('storage/' . $document->fichier_path) }}" target="_blank" class="btn-view" title="Voir le document">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ asset('storage/' . $document->fichier_path) }}" download="{{ $document->fichier_nom }}" class="btn-download" title="Télécharger">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="detail-card full-width">
            <h3><i class="fas fa-folder-open"></i> Documents du candidat</h3>
            <div class="empty-documents">
                <i class="fas fa-file-alt"></i>
                <p>Aucun document n'a encore été téléchargé par ce candidat.</p>
            </div>
        </div>
        @endif

        <!-- ========== SECTION GÉNÉRATION DE TOKEN ========== -->
        @if($candidature->statut == 'acceptee' && !$candidature->candidat->dossier_valide)
        <div class="detail-card full-width token-section">
            <h3><i class="fas fa-key"></i> Génération du token d'accès</h3>
            <div class="token-info">
                <div class="token-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="token-content">
                    <p>Après validation de la candidature, un token unique sera généré pour permettre au candidat d'accéder à son tableau de bord.</p>
                    <p class="token-note">
                        <i class="fas fa-info-circle"></i>
                        Ce token sera envoyé automatiquement par email au candidat.
                    </p>
                </div>
            </div>
            <div class="token-actions">
                <form action="{{ route('responsable.candidatures.generer-token', $candidature->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-generate-token" onclick="return confirm('Générer un token d\'accès pour ce candidat ? Un email lui sera envoyé automatiquement.')">
                        <i class="fas fa-key"></i>
                        Générer le token d'accès
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($candidature->candidat->dossier_valide && $candidature->candidat->token_acces)
        <div class="detail-card full-width token-section token-displayed">
            <h3><i class="fas fa-check-circle"></i> Token d'accès généré</h3>
            <div class="token-info">
                <div class="token-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <div class="token-content">
                    <p>Un token d'accès a été généré pour ce candidat.</p>
                    <div class="token-code">
                        <span>Code d'accès :</span>
                        <code>{{ $candidature->candidat->token_acces }}</code>
                        <button class="btn-copy" onclick="copyToken('{{ $candidature->candidat->token_acces }}')" title="Copier le token">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="token-note">
                        <i class="fas fa-envelope"></i>
                        Ce token a été envoyé à <strong>{{ $candidature->candidat->email }}</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        @if($candidature->statut == 'en_attente')
        <div class="detail-card actions-card">
            <h3><i class="fas fa-cog"></i> Actions</h3>
            <div class="actions-buttons">
                <form action="{{ route('responsable.candidatures.accepter', $candidature->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-accept" onclick="return confirm('Accepter cette candidature ?')">
                        <i class="fas fa-check-circle"></i>
                        Accepter la candidature
                    </button>
                </form>
                <form action="{{ route('responsable.candidatures.refuser', $candidature->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-reject" onclick="return confirm('Refuser cette candidature ?')">
                        <i class="fas fa-times-circle"></i>
                        Refuser la candidature
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .welcome-section {
        background: linear-gradient(135deg, #3b82f6, #10b981);
        padding: 2rem;
        border-radius: 24px;
        margin-bottom: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
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

    .btn-back {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 40px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.3);
        color: white;
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
        border: 1px solid #10b981;
        color: #10b981;
    }

    .alert-token {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid #3b82f6;
        color: #3b82f6;
    }
    
    .alert-token code {
        background: #f1f5f9;
        padding: 0.2rem 0.5rem;
        border-radius: 8px;
        font-weight: bold;
        color: #1e40af;
    }

    .candidature-detail {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .detail-header {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .header-left h2 {
        font-size: 1.5rem;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .entreprise-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
    }

    .status-badge {
        padding: 0.5rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-en_attente {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-acceptee {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-refusee {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-en_cours {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid #3b82f6;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .detail-card.full-width {
        grid-column: 1 / -1;
    }

    .detail-card h3 {
        font-size: 1.2rem;
        color: #0f172a;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-card h3 i {
        color: #3b82f6;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .info-item {
        display: flex;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.8rem;
    }

    .info-label {
        width: 140px;
        font-weight: 600;
        color: #64748b;
    }

    .info-value {
        flex: 1;
        color: #0f172a;
    }

    .badge-valid {
        background: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .badge-pending {
        background: #fed7aa;
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .description-content {
        color: #334155;
        line-height: 1.6;
    }

    /* ========== STYLES DOCUMENTS ========== */
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1rem;
    }

    .document-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);
        border-color: #3b82f6;
    }

    .document-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(59, 130, 246, 0.1);
    }

    .document-icon i {
        font-size: 1.8rem;
    }

    .document-info {
        flex: 1;
    }

    .document-info h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .document-name {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.25rem;
        word-break: break-all;
    }

    .document-meta {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }

    .document-status {
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .status-valide {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejete {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-en_attente {
        background: #fed7aa;
        color: #92400e;
    }

    .document-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-view, .btn-download {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-view:hover {
        background: #2563eb;
        transform: translateY(-2px);
    }

    .btn-download {
        background: #10b981;
        color: white;
    }

    .btn-download:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    .empty-documents {
        text-align: center;
        padding: 2rem;
        color: #64748b;
    }

    .empty-documents i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* ========== STYLES SECTION TOKEN ========== */
    .token-section {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
        border: 2px solid #e2e8f0;
    }
    
    .token-section.token-displayed {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4, #ffffff);
    }
    
    .token-info {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    
    .token-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6, #10b981);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
    }
    
    .token-icon.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .token-content {
        flex: 1;
    }
    
    .token-content p {
        color: #334155;
        margin-bottom: 0.5rem;
    }
    
    .token-note {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .token-code {
        background: #f1f5f9;
        padding: 1rem;
        border-radius: 12px;
        margin: 1rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .token-code span {
        font-weight: 600;
        color: #334155;
    }
    
    .token-code code {
        font-size: 1.2rem;
        font-weight: bold;
        background: #ffffff;
        padding: 0.3rem 0.8rem;
        border-radius: 8px;
        color: #3b82f6;
        letter-spacing: 1px;
        font-family: monospace;
    }
    
    .btn-copy {
        background: #64748b;
        border: none;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-copy:hover {
        background: #3b82f6;
        transform: scale(1.05);
    }
    
    .token-actions {
        text-align: center;
        margin-top: 1rem;
    }
    
    .btn-generate-token {
        background: linear-gradient(135deg, #3b82f6, #10b981);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-generate-token:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px #3b82f6;
    }

    .actions-card {
        text-align: center;
    }

    .actions-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-accept {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px #10b981;
    }

    .btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px #ef4444;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
        
        .info-item {
            flex-direction: column;
            gap: 0.3rem;
        }
        
        .info-label {
            width: 100%;
        }
        
        .welcome-section {
            flex-direction: column;
            text-align: center;
        }
        
        .detail-header {
            flex-direction: column;
            text-align: center;
        }
        
        .actions-buttons {
            flex-direction: column;
        }
        
        .btn-accept, .btn-reject, .btn-generate-token {
            width: 100%;
            justify-content: center;
        }
        
        .documents-grid {
            grid-template-columns: 1fr;
        }
        
        .document-card {
            flex-wrap: wrap;
        }
        
        .document-actions {
            width: 100%;
            justify-content: flex-end;
        }
        
        .token-info {
            flex-direction: column;
            text-align: center;
        }
        
        .token-code {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<script>
    function copyToken(token) {
        navigator.clipboard.writeText(token).then(function() {
            // Afficher une notification
            const toast = document.createElement('div');
            toast.className = 'alert alert-success';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.animation = 'fadeInUp 0.3s ease';
            toast.innerHTML = '<i class="fas fa-check-circle"></i> Token copié dans le presse-papier !';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    }
</script>
@endsection
