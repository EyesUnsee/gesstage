@extends('layouts.candidat')

@section('title', 'Journal de bord - Candidat')

@section('content')
<div class="journal-container">
    <!-- Welcome section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1 class="welcome-title">Journal de <span>bord</span> 📔</h1>
            <p class="welcome-subtitle">Organisez vos tâches par semaine et suivez votre progression</p>
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
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                <h3>Total tâches</h3>
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <span class="stat-label">Toutes semaines</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>Tâches terminées</h3>
                <div class="stat-value">{{ $stats['terminees'] ?? 0 }}</div>
                <span class="stat-label">Accomplies</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
                <h3>En cours</h3>
                <div class="stat-value">{{ $stats['en_cours'] ?? 0 }}</div>
                <span class="stat-label">Restantes</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>Progression</h3>
                <div class="stat-value">{{ $progressionSemaine ?? 0 }}%</div>
                <span class="stat-label">Cette semaine</span>
            </div>
        </div>
    </div>

    <!-- Sélecteur de semaine -->
    <div class="week-selector">
        <div class="month-info">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ $currentMonthName ?? Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="week-nav">
            <button class="week-nav-btn" onclick="changeWeek(-1)">
                <i class="fas fa-chevron-left"></i> Semaine précédente
            </button>
            <button class="week-nav-btn" onclick="changeWeek(1)">
                Semaine suivante <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="week-selector-wrapper">
            <select id="weekSelect" class="week-select" onchange="goToWeek()">
                <option value="">Sélectionner une semaine</option>
                @foreach($semainesDisponibles ?? [] as $semaine)
                    <option value="{{ $semaine['annee'] }}_{{ $semaine['semaine'] }}" {{ ($semaine['active'] ?? false) ? 'selected' : '' }}>
                        {{ $semaine['libelle'] }}
                    </option>
                @endforeach
            </select>
            <button class="week-current" onclick="goToCurrentWeek()">
                <i class="fas fa-calendar-week"></i> Cette semaine
            </button>
        </div>
    </div>

    <!-- Semaine courante avec validation -->
    <div class="current-week">
        <div class="week-range">
            <i class="fas fa-calendar-week"></i>
            Semaine {{ $semaineActuelle }} : 
            <strong>{{ $dateDebutSemaine->format('d/m/Y') }}</strong> - 
            <strong>{{ $dateFinSemaine->format('d/m/Y') }}</strong>
        </div>
        <div class="week-progress">
            <div class="progress-label">
                <span>Progression de la semaine</span>
                <span>{{ $progressionSemaine ?? 0 }}%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $progressionSemaine ?? 0 }}%;"></div>
            </div>
        </div>
        
        @if(isset($tachesTermineesSemaine) && isset($totalTachesSemaine) && $tachesTermineesSemaine == $totalTachesSemaine && $totalTachesSemaine > 0 && isset($semaineValidee) && !$semaineValidee)
        <div class="week-validation">
            <button class="btn-validate-week" onclick="validerSemaine()">
                <i class="fas fa-check-circle"></i> Valider la semaine {{ $semaineActuelle }}
            </button>
            <p class="validation-info">Toutes les tâches de cette semaine sont terminées !</p>
        </div>
        @elseif(isset($semaineValidee) && $semaineValidee)
        <div class="week-validated">
            <i class="fas fa-star"></i>
            <span>Semaine {{ $semaineActuelle }} validée !</span>
            <i class="fas fa-star"></i>
        </div>
        @elseif(isset($totalTachesSemaine) && $totalTachesSemaine == 0)
        <div class="week-empty">
            <i class="fas fa-info-circle"></i>
            <span>Aucune tâche pour cette semaine. Ajoutez des tâches pour valider la semaine.</span>
        </div>
        @elseif(isset($tachesTermineesSemaine) && isset($totalTachesSemaine))
        <div class="week-pending">
            <i class="fas fa-clock"></i>
            <span>Terminez toutes les tâches ({{ $tachesTermineesSemaine }}/{{ $totalTachesSemaine }}) pour valider la semaine</span>
        </div>
        @endif
    </div>

    <!-- Grille hebdomadaire (Lundi à Dimanche) -->
    <div class="weekly-grid">
        @foreach($joursSemaine as $index => $jour)
        <div class="day-card" data-jour="{{ $jour['nom'] }}">
            <div class="day-header">
                <h3>{{ $jour['nom'] }}</h3>
                <span class="day-date">{{ $jour['date']->format('d/m') }}</span>
            </div>
            <div class="day-tasks" id="tasks-{{ $jour['nom'] }}">
                @forelse($jour['taches'] as $tache)
                <div class="task-item {{ $tache->terminee ? 'completed' : '' }}" 
                     data-id="{{ $tache->id }}"
                     data-priority="{{ $tache->priorite ?? 'medium' }}">
                    <div class="task-checkbox {{ $tache->terminee ? 'completed' : '' }}" 
                         onclick="toggleTask({{ $tache->id }}, this)">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="task-content">
                        <span class="task-title">{{ $tache->titre }}</span>
                        <div class="task-meta">
                            <span class="task-priority priority-{{ $tache->priorite ?? 'medium' }}">
                                {{ $tache->priorite == 'high' ? 'Haute' : ($tache->priorite == 'low' ? 'Basse' : 'Moyenne') }}
                            </span>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn-icon-small" onclick="editTask({{ $tache->id }}, '{{ addslashes($tache->titre) }}', '{{ addslashes($tache->description ?? '') }}', '{{ $tache->priorite ?? 'medium' }}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon-small" onclick="deleteTask({{ $tache->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="empty-day" onclick="openAddTaskModal('{{ $jour['nom'] }}', '{{ $jour['date']->format('Y-m-d') }}')">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajouter une tâche</span>
                </div>
                @endforelse
            </div>
            <button class="add-task-day" onclick="openAddTaskModal('{{ $jour['nom'] }}', '{{ $jour['date']->format('Y-m-d') }}')">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </div>
        @endforeach
    </div>

    <!-- Calendrier mensuel des semaines -->
    <div class="month-calendar">
        <div class="month-header">
            <h3><i class="fas fa-calendar-alt"></i> Calendrier des semaines</h3>
        </div>
        <div class="weeks-calendar">
            @php
                $semainesTriees = collect($semainesDuMois)->sortBy('numero');
            @endphp
            @foreach($semainesTriees as $semaine)
            <div class="week-card {{ $semaine['numero'] == $semaineActuelle ? 'current' : '' }} {{ $semaine['validee'] ? 'validated' : '' }}" 
                 onclick="goToWeekByNumber({{ $semaine['numero'] }}, {{ $semaine['annee'] }})">
                <div class="week-number">
                    @if($semaine['validee'])
                        <i class="fas fa-check-circle"></i>
                    @endif
                    Semaine {{ $semaine['numero'] }}
                </div>
                <div class="week-dates">{{ $semaine['debut']->format('d/m') }} - {{ $semaine['fin']->format('d/m') }}</div>
                <div class="week-tasks-count">
                    {{ $semaine['totalTaches'] }} tâches • 
                    @if($semaine['totalTaches'] > 0)
                        {{ $semaine['terminees'] }}/{{ $semaine['totalTaches'] }} terminées
                    @else
                        Aucune tâche
                    @endif
                </div>
                @if($semaine['validee'])
                    <div class="week-validated-badge">✓ Validée</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal d'ajout de tâche -->
<div id="addTaskModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Ajouter une tâche</h2>
            <button class="modal-close" onclick="closeAddTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="taskJour" value="">
            <input type="hidden" id="taskDate" value="">
            <div class="form-group">
                <label>Titre de la tâche *</label>
                <input type="text" id="taskTitre" class="form-control" placeholder="Ex: Rédiger le rapport de stage">
            </div>
            <div class="form-group">
                <label>Priorité</label>
                <div class="priority-select">
                    <label class="priority-option">
                        <input type="radio" name="priorite" value="low">
                        <span class="priority-badge low">🟢 Basse</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priorite" value="medium" checked>
                        <span class="priority-badge medium">🟡 Moyenne</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priorite" value="high">
                        <span class="priority-badge high">🔴 Haute</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="taskDescription" class="form-control" rows="3" placeholder="Description détaillée..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeAddTaskModal()">Annuler</button>
            <button class="btn-submit" onclick="ajouterTacheJour()">Ajouter</button>
        </div>
    </div>
</div>

<!-- Modal d'édition -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier la tâche</h2>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editTaskId">
            <div class="form-group">
                <label>Titre</label>
                <input type="text" id="editTaskTitle" class="form-control">
            </div>
            <div class="form-group">
                <label>Priorité</label>
                <div class="priority-select">
                    <label class="priority-option">
                        <input type="radio" name="edit_priorite" value="low">
                        <span class="priority-badge low">🟢 Basse</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="edit_priorite" value="medium">
                        <span class="priority-badge medium">🟡 Moyenne</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="edit_priorite" value="high">
                        <span class="priority-badge high">🔴 Haute</span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="editTaskDescription" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeEditModal()">Annuler</button>
            <button class="btn-submit" onclick="saveEditTask()">Enregistrer</button>
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
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .journal-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Welcome section */
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

    .stat-label {
        font-size: 0.688rem;
        color: var(--gray-500);
    }

    /* Week selector */
    .week-selector {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        border: 1px solid var(--gray-200);
    }

    .month-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--primary);
        background: rgba(67, 97, 238, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
    }

    .week-nav {
        display: flex;
        gap: 0.5rem;
    }

    .week-nav-btn {
        padding: 0.5rem 1rem;
        background: var(--gray-100);
        border: 1px solid var(--gray-200);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .week-nav-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .week-selector-wrapper {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .week-select {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        background: white;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .week-current {
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .week-current:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Current week */
    .current-week {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 2rem;
        color: white;
    }

    .week-range {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .week-progress {
        margin-top: 0.5rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .progress-bar-bg {
        height: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: #fcd34d;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .week-validation, .week-validated, .week-empty, .week-pending {
        margin-top: 1rem;
        text-align: center;
    }

    .btn-validate-week {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid white;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 2rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-validate-week:hover {
        background: white;
        color: var(--primary);
        transform: scale(1.02);
    }

    .week-validated {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem;
        border-radius: 2rem;
        display: inline-block;
        width: auto;
        margin-left: auto;
        margin-right: auto;
    }

    .week-validated i {
        color: #fcd34d;
        margin: 0 0.5rem;
    }

    /* Weekly grid */
    .weekly-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.75rem;
        margin-bottom: 2rem;
    }

    .day-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
        transition: all 0.2s;
    }

    .day-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
    }

    .day-header {
        background: var(--gray-100);
        padding: 0.75rem;
        text-align: center;
        border-bottom: 1px solid var(--gray-200);
    }

    .day-header h3 {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0 0 0.25rem 0;
    }

    .day-date {
        font-size: 0.688rem;
        color: var(--gray-500);
    }

    .day-tasks {
        min-height: 280px;
        max-height: 350px;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .task-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        background: var(--gray-100);
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .task-item:hover {
        transform: translateX(2px);
        background: #eef2ff;
    }

    .task-item.completed {
        opacity: 0.6;
        background: #e2e8f0;
    }

    .task-item.completed .task-title {
        text-decoration: line-through;
        color: var(--gray-600);
    }

    .task-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid var(--gray-400);
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        background: white;
        transition: all 0.2s;
    }

    .task-checkbox.completed {
        background: var(--success);
        border-color: var(--success);
    }

    .task-checkbox i {
        font-size: 0.625rem;
        color: white;
    }

    .task-content {
        flex: 1;
    }

    .task-title {
        font-size: 0.813rem;
        font-weight: 500;
        display: block;
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .task-priority {
        font-size: 0.625rem;
        padding: 0.125rem 0.5rem;
        border-radius: 1rem;
    }

    .priority-high {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .priority-medium {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .priority-low {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .task-actions {
        display: flex;
        gap: 0.25rem;
    }

    .btn-icon-small {
        width: 26px;
        height: 26px;
        border-radius: 0.375rem;
        background: white;
        border: 1px solid var(--gray-200);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon-small:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .empty-day {
        text-align: center;
        padding: 1.5rem;
        color: var(--gray-500);
        cursor: pointer;
        transition: all 0.2s;
    }

    .empty-day:hover {
        background: var(--gray-100);
        border-radius: 0.5rem;
    }

    .empty-day i {
        font-size: 1.25rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .empty-day span {
        font-size: 0.75rem;
    }

    .add-task-day {
        width: 100%;
        padding: 0.5rem;
        background: var(--gray-100);
        border: none;
        border-top: 1px solid var(--gray-200);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--gray-700);
    }

    .add-task-day:hover {
        background: var(--primary);
        color: white;
    }

    /* Month calendar */
    .month-calendar {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
    }

    .month-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-800);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .weeks-calendar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
    }

    .week-card {
        background: var(--gray-100);
        border-radius: 0.5rem;
        padding: 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        position: relative;
    }

    .week-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        background: white;
        box-shadow: var(--shadow);
    }

    .week-card.current {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
    }

    .week-card.current .week-tasks-count {
        color: rgba(255, 255, 255, 0.9);
    }

    .week-card.validated {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .week-number {
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .week-dates {
        font-size: 0.688rem;
        margin-bottom: 0.5rem;
    }

    .week-tasks-count {
        font-size: 0.688rem;
        color: var(--gray-600);
    }

    .week-validated-badge {
        margin-top: 0.5rem;
        font-size: 0.625rem;
        font-weight: bold;
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
        backdrop-filter: blur(4px);
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-30px);
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
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .modal-header h2 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        background: var(--gray-100);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: var(--gray-200);
    }

    .modal-body {
        padding: 1.25rem;
    }

    .modal-footer {
        padding: 1rem 1.25rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        border-top: 1px solid var(--gray-200);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        color: var(--gray-700);
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .priority-select {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .priority-option {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .priority-option input {
        display: none;
    }

    .priority-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 2rem;
        font-size: 0.813rem;
        font-weight: 500;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .priority-option input:checked + .priority-badge {
        border-color: currentColor;
        transform: scale(1.02);
    }

    .btn-cancel, .btn-submit {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-cancel {
        background: var(--gray-100);
        color: var(--gray-700);
    }

    .btn-cancel:hover {
        background: var(--gray-200);
    }

    .btn-submit {
        background: var(--primary);
        color: white;
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Notification toast */
    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        border-left: 4px solid var(--success);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 2000;
        box-shadow: var(--shadow-lg);
    }

    .notification-toast.show {
        transform: translateX(0);
    }

    .toast-icon i {
        font-size: 1.25rem;
    }

    .toast-title {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .toast-message {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    /* Scrollbar */
    .day-tasks::-webkit-scrollbar {
        width: 4px;
    }

    .day-tasks::-webkit-scrollbar-track {
        background: var(--gray-200);
        border-radius: 4px;
    }

    .day-tasks::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .weekly-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .weekly-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .weekly-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .week-selector {
            flex-direction: column;
            align-items: stretch;
        }
        
        .week-nav {
            justify-content: center;
        }
        
        .week-selector-wrapper {
            flex-direction: column;
        }
        
        .welcome-title {
            font-size: 1.75rem;
        }
        
        .welcome-subtitle {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .weekly-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 1.25rem;
        }
        
        .modal-footer {
            flex-direction: column;
        }
        
        .modal-footer button {
            width: 100%;
        }
    }
</style>

<script>
    let toastTimeout;
    let currentEditId = null;
    
    function showNotification(title, message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        const toastTitle = toast.querySelector('.toast-title');
        const toastMessage = toast.querySelector('.toast-message');
        const toastIcon = toast.querySelector('.toast-icon i');
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toast.style.borderLeftColor = '#10b981';
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = '#10b981';
        } else if (type === 'error') {
            toast.style.borderLeftColor = '#ef4444';
            toastIcon.className = 'fas fa-exclamation-circle';
            toastIcon.style.color = '#ef4444';
        } else {
            toast.style.borderLeftColor = '#4361ee';
            toastIcon.className = 'fas fa-info-circle';
            toastIcon.style.color = '#4361ee';
        }
        
        toast.classList.add('show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    function changeWeek(offset) {
        showNotification('Info', 'Chargement...', 'info');
        setTimeout(() => {
            window.location.href = '{{ route("candidat.journal") }}?offset=' + offset;
        }, 300);
    }
    
    function goToWeek() {
        const select = document.getElementById('weekSelect');
        const value = select.value;
        if (value) {
            const [annee, semaine] = value.split('_');
            window.location.href = '{{ route("candidat.journal") }}?semaine=' + semaine + '&annee=' + annee;
        }
    }
    
    function goToCurrentWeek() {
        window.location.href = '{{ route("candidat.journal") }}';
    }
    
    function goToWeekByNumber(semaine, annee) {
        window.location.href = '{{ route("candidat.journal") }}?semaine=' + semaine + '&annee=' + annee;
    }
    
    function validerSemaine() {
        if (confirm('Valider la semaine {{ $semaineActuelle }} ? Cette action marquera la semaine comme terminée.')) {
            fetch('{{ route("candidat.journal.valider") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    semaine: {{ $semaineActuelle }},
                    annee: {{ $anneeActuelle }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', 'Semaine validée avec succès !', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification('Erreur', data.message || 'Erreur lors de la validation', 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur', 'Erreur réseau', 'error');
            });
        }
    }
    
    function openAddTaskModal(jour, date) {
        document.getElementById('taskJour').value = jour;
        document.getElementById('taskDate').value = date;
        document.getElementById('taskTitre').value = '';
        document.getElementById('taskDescription').value = '';
        document.querySelector('input[name="priorite"][value="medium"]').checked = true;
        document.getElementById('addTaskModal').style.display = 'flex';
        document.getElementById('addTaskModal').classList.add('active');
    }
    
    function closeAddTaskModal() {
        document.getElementById('addTaskModal').style.display = 'none';
        document.getElementById('addTaskModal').classList.remove('active');
    }
    
    function ajouterTacheJour() {
        const titre = document.getElementById('taskTitre').value.trim();
        const priorite = document.querySelector('input[name="priorite"]:checked').value;
        const description = document.getElementById('taskDescription').value;
        const date = document.getElementById('taskDate').value;
        
        if (!titre) {
            showNotification('Erreur', 'Veuillez entrer un titre', 'error');
            return;
        }
        
        fetch('{{ route("candidat.journal.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                titre: titre,
                priorite: priorite,
                description: description,
                date: date
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAddTaskModal();
                showNotification('Succès', 'Tâche ajoutée', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('Erreur', data.message || 'Erreur', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur', 'Erreur réseau', 'error');
        });
    }
    
    function toggleTask(id, element) {
        fetch(`/candidat/taches/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const taskItem = element.closest('.task-item');
                taskItem.classList.toggle('completed');
                element.classList.toggle('completed');
                showNotification('Succès', data.message, 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('Erreur', data.message || 'Erreur', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur', 'Erreur réseau', 'error');
        });
    }
    
    function editTask(id, titre, description, priorite) {
        currentEditId = id;
        document.getElementById('editTaskId').value = id;
        document.getElementById('editTaskTitle').value = titre;
        document.getElementById('editTaskDescription').value = description || '';
        
        document.querySelectorAll('input[name="edit_priorite"]').forEach(radio => {
            if (radio.value === priorite) radio.checked = true;
        });
        
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('editModal').classList.add('active');
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('editModal').classList.remove('active');
    }
    
    function saveEditTask() {
        const id = document.getElementById('editTaskId').value;
        const titre = document.getElementById('editTaskTitle').value.trim();
        const description = document.getElementById('editTaskDescription').value;
        const priorite = document.querySelector('input[name="edit_priorite"]:checked').value;
        
        if (!titre) {
            showNotification('Erreur', 'Le titre est requis', 'error');
            return;
        }
        
        fetch(`/candidat/taches/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ titre, description, priorite })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                showNotification('Succès', 'Tâche modifiée', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('Erreur', data.message || 'Erreur', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur', 'Erreur réseau', 'error');
        });
    }
    
    function deleteTask(id) {
        if (confirm('Supprimer cette tâche ?')) {
            fetch(`/candidat/taches/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Succès', 'Tâche supprimée', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showNotification('Erreur', data.message || 'Erreur', 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur', 'Erreur réseau', 'error');
            });
        }
    }
    
    // Fermer les modals en cliquant en dehors
    window.onclick = function(event) {
        const addModal = document.getElementById('addTaskModal');
        const editModal = document.getElementById('editModal');
        if (event.target === addModal) closeAddTaskModal();
        if (event.target === editModal) closeEditModal();
    };
</script>
@endsection
