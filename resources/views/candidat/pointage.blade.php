@extends('layouts.candidat')

@section('title', 'Pointage - Candidat')

@section('content')
<div class="pointage-container">
    <!-- Welcome section -->
    <div class="welcome-section">
        <div class="welcome-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'">
        </div>
        <div class="welcome-text">
            <h1 class="welcome-title">Pointage <span>journalier</span> ⏰</h1>
            <p class="welcome-subtitle">Enregistrez vos heures de présence et suivez votre assiduité</p>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $error }}
        </div>
    @endif

    <!-- Info stage -->
    @if(isset($stage) && $stage)
    <div class="stage-info-card">
        <div class="stage-info-header">
            <i class="fas fa-briefcase"></i>
            <h3>Stage en cours</h3>
        </div>
        <div class="stage-info-content">
            <div class="info-item">
                <span class="info-label">Titre :</span>
                <span class="info-value">{{ $stage->titre ?? 'Stage' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Période :</span>
                <span class="info-value">{{ isset($stage->date_debut) ? Carbon\Carbon::parse($stage->date_debut)->format('d/m/Y') : '-' }} - {{ isset($stage->date_fin) ? Carbon\Carbon::parse($stage->date_fin)->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Heures/semaine :</span>
                <span class="info-value">{{ $stage->heures_semaine ?? '35' }}h</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Carte de pointage du jour -->
    <div class="pointage-card">
        <div class="pointage-header">
            <h2><i class="fas fa-calendar-day"></i> Pointage du {{ Carbon\Carbon::now()->format('d/m/Y') }}</h2>
            <div class="current-time" id="currentTime">
                {{ Carbon\Carbon::now()->format('H:i:s') }}
            </div>
        </div>

        <div class="pointage-status">
            @if(isset($presenceAujourdhui) && $presenceAujourdhui)
                @if($presenceAujourdhui->heure_arrivee && !$presenceAujourdhui->heure_depart)
                    <div class="status-badge status-arrived">
                        <i class="fas fa-sign-in-alt"></i>
                        Arrivée enregistrée à {{ Carbon\Carbon::parse($presenceAujourdhui->heure_arrivee)->format('H:i') }}
                    </div>
                @elseif($presenceAujourdhui->heure_arrivee && $presenceAujourdhui->heure_depart)
                    <div class="status-badge status-completed">
                        <i class="fas fa-check-circle"></i>
                        Journée complétée ({{ Carbon\Carbon::parse($presenceAujourdhui->heure_arrivee)->format('H:i') }} - {{ Carbon\Carbon::parse($presenceAujourdhui->heure_depart)->format('H:i') }})
                    </div>
                @elseif(!$presenceAujourdhui->est_present && $presenceAujourdhui->est_justifie)
                    <div class="status-badge status-justified">
                        <i class="fas fa-file-medical-alt"></i>
                        Absence justifiée : {{ $presenceAujourdhui->motif_absence }}
                    </div>
                @elseif(!$presenceAujourdhui->est_present)
                    <div class="status-badge status-absent">
                        <i class="fas fa-times-circle"></i>
                        Absence non justifiée
                    </div>
                @endif
            @else
                <div class="status-badge status-pending">
                    <i class="fas fa-hourglass-half"></i>
                    En attente de pointage
                </div>
            @endif
        </div>

        <div class="pointage-actions">
            @if(!isset($presenceAujourdhui) || !$presenceAujourdhui || !$presenceAujourdhui->heure_arrivee)
                <button class="btn-pointage btn-arrival" onclick="pointageArrival()">
                    <i class="fas fa-sign-in-alt"></i>
                    Pointer l'arrivée
                </button>
            @endif

            @if(isset($presenceAujourdhui) && $presenceAujourdhui && $presenceAujourdhui->heure_arrivee && !$presenceAujourdhui->heure_depart)
                <button class="btn-pointage btn-departure" onclick="pointageDeparture()">
                    <i class="fas fa-sign-out-alt"></i>
                    Pointer le départ
                </button>
            @endif

            @if(!isset($presenceAujourdhui) || !$presenceAujourdhui || (!$presenceAujourdhui->heure_arrivee && !$presenceAujourdhui->est_justifie))
                <button class="btn-pointage btn-absence" onclick="openAbsenceModal()">
                    <i class="fas fa-file-medical-alt"></i>
                    Absence justifiée
                </button>
            @endif
        </div>
    </div>

    <!-- Statistiques -->
    @if(isset($stats) && $stats)
    <div class="stats-section">
        <h2><i class="fas fa-chart-line"></i> Statistiques (30 derniers jours)</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Taux de présence</h3>
                    <div class="stat-value">{{ $stats['taux_presence'] ?? 0 }}%</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Heures totales</h3>
                    <div class="stat-value">{{ $stats['total_heures'] ?? 0 }}h</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-simple"></i>
                </div>
                <div class="stat-info">
                    <h3>Moyenne/jour</h3>
                    <div class="stat-value">{{ $stats['moyenne_heures'] ?? 0 }}h</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3>Heures restantes</h3>
                    <div class="stat-value">{{ $stats['heures_restantes'] ?? 0 }}h</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des présences -->
    <div class="historique-section">
        <div class="historique-header">
            <h2><i class="fas fa-history"></i> Historique des pointages</h2>
            <span class="historique-count">{{ isset($historique) ? $historique->count() : 0 }} enregistrements</span>
        </div>

        <div class="table-container">
            <table class="historique-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Heures</th>
                        <th>Statut</th>
                        <th>Motif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($historique ?? collect()) as $presence)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($presence->date)->format('d/m/Y') }}</td>
                        <td>{{ $presence->heure_arrivee ? Carbon\Carbon::parse($presence->heure_arrivee)->format('H:i') : '-' }}</td>
                        <td>{{ $presence->heure_depart ? Carbon\Carbon::parse($presence->heure_depart)->format('H:i') : '-' }}</td>
                        <td>{{ $presence->heures_travaillees ? $presence->heures_travaillees . 'h' : '-' }}</td>
                        <td>
                            @if($presence->est_present)
                                <span class="badge badge-present">Présent</span>
                            @elseif($presence->est_justifie)
                                <span class="badge badge-justified">Justifié</span>
                            @else
                                <span class="badge badge-absent">Absent</span>
                            @endif
                        </td>
                        <td>{{ $presence->motif_absence ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun enregistrement trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Modal Absence -->
<div id="absenceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-file-medical-alt"></i> Justifier une absence</h2>
            <button class="modal-close" onclick="closeAbsenceModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="motifAbsence">Motif de l'absence</label>
                <textarea id="motifAbsence" 
                          class="form-control" 
                          rows="5"
                          placeholder="Veuillez expliquer le motif de votre absence..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeAbsenceModal()">Annuler</button>
            <button class="btn-submit" onclick="submitAbsence()">Enregistrer</button>
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

    .pointage-container {
        max-width: 1400px;
        margin: 0 auto;
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
    }
    
    .welcome-logo img {
        height: 80px;
        width: auto;
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
    
    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--rouge);
        color: var(--rouge);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stage-info-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 2px solid var(--gris-clair);
        transition: all 0.3s;
    }
    
    .stage-info-card:hover {
        transform: translateY(-2px);
        border-color: var(--bleu);
    }
    
    .stage-info-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: var(--bleu);
    }
    
    .stage-info-header h3 {
        color: var(--noir);
        margin: 0;
    }
    
    .stage-info-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .info-item {
        display: flex;
        gap: 0.5rem;
        font-size: 0.9rem;
    }
    
    .info-label {
        color: var(--gris);
        font-weight: 500;
    }
    
    .info-value {
        color: var(--noir);
        font-weight: 600;
    }
    
    .pointage-card {
        background: linear-gradient(135deg, white, var(--gris-clair));
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 2px solid var(--gris-clair);
        transition: all 0.3s;
    }
    
    .pointage-card:hover {
        border-color: var(--bleu);
        box-shadow: 0 10px 30px -12px rgba(0, 0, 0, 0.1);
    }
    
    .pointage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .pointage-header h2 {
        color: var(--noir);
        font-size: 1.5rem;
        margin: 0;
    }
    
    .current-time {
        font-size: 2rem;
        font-weight: 700;
        color: var(--bleu);
        font-family: monospace;
        background: rgba(67, 97, 238, 0.1);
        padding: 0.3rem 1rem;
        border-radius: 12px;
    }
    
    .pointage-status {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
    }
    
    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    
    .status-arrived {
        background: rgba(59, 130, 246, 0.1);
        color: var(--bleu);
        border: 1px solid var(--bleu);
    }
    
    .status-completed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
        border: 1px solid var(--vert);
    }
    
    .status-justified {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    
    .status-absent {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
        border: 1px solid var(--rouge);
    }
    
    .pointage-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .btn-pointage {
        padding: 1rem 2rem;
        border: none;
        border-radius: 14px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-arrival {
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
    }
    
    .btn-departure {
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: white;
    }
    
    .btn-absence {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }
    
    .btn-pointage:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.2);
    }
    
    .stats-section {
        margin-bottom: 2rem;
    }
    
    .stats-section h2 {
        margin-bottom: 1rem;
        color: var(--noir);
        font-size: 1.3rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--bleu);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }
    
    .stat-info {
        flex: 1;
    }
    
    .stat-info h3 {
        color: var(--gris);
        font-size: 0.8rem;
        margin-bottom: 0.3rem;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--noir);
    }
    
    .historique-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid var(--gris-clair);
    }
    
    .historique-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .historique-header h2 {
        color: var(--noir);
        margin: 0;
        font-size: 1.3rem;
    }
    
    .historique-count {
        background: var(--gris-clair);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        color: var(--gris-fonce);
        font-size: 0.9rem;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .historique-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .historique-table th {
        text-align: left;
        padding: 1rem;
        background: var(--gris-clair);
        color: var(--gris-fonce);
        font-weight: 600;
    }
    
    .historique-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gris-clair);
        color: var(--gris-fonce);
    }
    
    .historique-table tr:hover td {
        background: var(--gris-clair);
    }
    
    .badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-present {
        background: rgba(16, 185, 129, 0.1);
        color: var(--vert);
    }
    
    .badge-justified {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .badge-absent {
        background: rgba(239, 68, 68, 0.1);
        color: var(--rouge);
    }
    
    .text-center {
        text-align: center;
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
        background: white;
        border-radius: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
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
        color: var(--noir);
        font-size: 1.2rem;
        margin: 0;
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
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gris-fonce);
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 2px solid var(--gris-clair);
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: inherit;
        resize: vertical;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--bleu);
    }
    
    .btn-cancel {
        padding: 0.6rem 1.5rem;
        background: var(--gris-clair);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-cancel:hover {
        background: #e2e8f0;
    }
    
    .btn-submit {
        padding: 0.6rem 1.5rem;
        background: linear-gradient(135deg, var(--bleu), var(--vert));
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
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
    
    .toast-title {
        font-weight: 700;
        color: var(--noir);
        margin-bottom: 0.2rem;
    }
    
    .toast-message {
        color: var(--gris);
        font-size: 0.85rem;
    }
    
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .welcome-section {
            flex-direction: column;
            text-align: center;
        }
        
        .pointage-header {
            flex-direction: column;
            text-align: center;
        }
        
        .pointage-actions {
            flex-direction: column;
        }
        
        .btn-pointage {
            width: 100%;
            justify-content: center;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .historique-table {
            font-size: 0.85rem;
        }
        
        .historique-table th,
        .historique-table td {
            padding: 0.8rem 0.5rem;
        }
        
        .stage-info-content {
            grid-template-columns: 1fr;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer button {
            width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .welcome-logo img {
            height: 60px;
        }
        
        .current-time {
            font-size: 1.3rem;
        }
        
        .stat-value {
            font-size: 1.4rem;
        }
    }
</style>

<script>
    let toastTimeout;
    
    // Mettre à jour l'heure en temps réel
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('fr-FR');
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    setInterval(updateTime, 1000);
    
    // Pointage arrivée
    function pointageArrival() {
        fetch('{{ route("candidat.pointage.arrival") }}', {
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
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur', data.error || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    }
    
    // Pointage départ
    function pointageDeparture() {
        fetch('{{ route("candidat.pointage.departure") }}', {
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
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur', data.error || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    }
    
    // Modal absence
    function openAbsenceModal() {
        document.getElementById('absenceModal').classList.add('active');
    }
    
    function closeAbsenceModal() {
        document.getElementById('absenceModal').classList.remove('active');
        document.getElementById('motifAbsence').value = '';
    }
    
    function submitAbsence() {
        const motif = document.getElementById('motifAbsence').value.trim();
        
        if (!motif) {
            showNotification('Erreur', 'Veuillez entrer un motif', 'error');
            return;
        }
        
        fetch('{{ route("candidat.pointage.absence") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ motif: motif })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAbsenceModal();
                showNotification('Succès', data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur', data.error || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur', 'Une erreur est survenue', 'error');
        });
    }
    
    // Notification
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
        }
        
        toast.classList.add('show');
        
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }
        
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // Fermer modal en cliquant à l'extérieur
    document.addEventListener('DOMContentLoaded', function() {
        window.onclick = function(event) {
            const modal = document.getElementById('absenceModal');
            if (event.target === modal) {
                closeAbsenceModal();
            }
        };
    });
</script>
@endsection
