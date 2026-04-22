

@extends('layouts.candidat-new')

@section('title', 'Compléter mon dossier - Candidat')

@section('content')
<div class="dashboard-container">
    {{-- En-tête Hero avec Glassmorphism --}}
    <div class="hero-section">
        <div class="hero-background">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-pattern"></div>
        </div>
        
        <div class="hero-content">
            <div class="welcome-section">
                <div class="avatar-pulse">
                    <div class="avatar-ring"></div>
                    <div class="avatar-ring"></div>
                    <span class="wave-emoji">👋</span>
                </div>
                <div class="welcome-text">
                    <h1>Bienvenue, <span class="gradient-text">{{ auth()->user()->first_name ?? 'Candidat' }}</span></h1>
                    <p class="hero-subtitle">Finalisez votre inscription pour débuter votre stage</p>
                </div>
            </div>
            
            {{-- Progress Timeline Modernisé --}}
            <div class="timeline-container">
                @php
                    $steps = [
                        ['name' => 'CV', 'icon' => 'file-alt', 'desc' => 'Curriculum'],
                        ['name' => 'Lettre', 'icon' => 'envelope', 'desc' => 'Motivation'],
                        ['name' => 'Diplôme', 'icon' => 'graduation-cap', 'desc' => 'Certificat'],
                        ['name' => 'Infos', 'icon' => 'user-check', 'desc' => 'Profil'],
                        ['name' => 'Token', 'icon' => 'key', 'desc' => 'Activation'],
                    ];
                    $currentStep = 0;
                    foreach($steps as $index => $step) {
                        if($step['name'] == 'CV' && $documents->where('type', 'cv')->count() > 0) $currentStep = $index + 1;
                        if($step['name'] == 'Lettre' && $documents->where('type', 'lettre_motivation')->count() > 0) $currentStep = max($currentStep, $index + 1);
                        if($step['name'] == 'Diplôme' && $documents->where('type', 'diplome')->count() > 0) $currentStep = max($currentStep, $index + 1);
                        if($step['name'] == 'Infos' && auth()->user()->phone && auth()->user()->address) $currentStep = max($currentStep, $index + 1);
                        if($step['name'] == 'Token' && (auth()->user()->token_acces ?? false)) $currentStep = max($currentStep, $index + 1);
                    }
                @endphp
                
                <div class="progress-timeline">
                    @foreach($steps as $index => $step)
                        @php
                            $isCompleted = $currentStep > $index;
                            $isCurrent = $currentStep == $index;
                            $status = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                        @endphp
                        <div class="timeline-step {{ $status }}">
                            <div class="step-node">
                                <div class="node-bg"></div>
                                <div class="node-icon">
                                    @if($isCompleted)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-{{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                @if($isCurrent)
                                    <div class="node-pulse"></div>
                                @endif
                            </div>
                            <div class="step-info">
                                <span class="step-title">{{ $step['name'] }}</span>
                                <span class="step-desc">{{ $step['desc'] }}</span>
                            </div>
                            @if($index < count($steps) - 1)
                                <div class="step-line {{ $isCompleted ? 'completed' : '' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications Toast --}}
    <div class="toast-container">
        @if(session('success'))
            <div class="toast toast-success">
                <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                <div class="toast-content">
                    <strong>Succès</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
                <div class="toast-progress"></div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast toast-error">
                <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="toast-content">
                    <strong>Erreur</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
                <div class="toast-progress"></div>
            </div>
        @endif
    </div>

    @php
        $totalDocs = 3;
        $docsUploaded = $documents->whereIn('type', ['cv', 'lettre_motivation', 'diplome'])->count();
        $progress = round(($docsUploaded / $totalDocs) * 100);
        $profileCompleted = (auth()->user()->phone && auth()->user()->address) ? 25 : 0;
        $totalProgress = min(100, $progress + $profileCompleted);
        $isDossierComplet = $totalProgress == 100;
        $hasToken = auth()->user()->token_acces ?? false;
        $isDossierValide = auth()->user()->dossier_valide ?? false;
    @endphp

    {{-- Dashboard Grid --}}
    <div class="dashboard-grid">
        {{-- Carte Profil --}}
        <div class="glass-card profile-card">
            <div class="card-glow"></div>
            <div class="card-header-modern">
                <div class="header-icon gradient-blue">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                <div class="header-text">
                    <h2>Informations personnelles</h2>
                    <span>Complétez votre profil</span>
                </div>
                <div class="header-badge" id="profile-badge">
                    <i class="fas fa-pen"></i>
                </div>
            </div>
            
            <form method="POST" action="{{ route('candidat.new.profile') }}" class="modern-form">
                @csrf
                <div class="form-grid">
                    <div class="input-group floating">
                        <input type="tel" name="phone" id="phone" class="form-control-modern" 
                               value="{{ old('phone', auth()->user()->phone) }}" placeholder=" " required>
                        <label for="phone">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Téléphone</span>
                        </label>
                        <div class="input-focus-border"></div>
                    </div>
                    
                    <div class="input-group floating">
                        <input type="date" name="birth_date" id="birth_date" class="form-control-modern" 
                               value="{{ old('birth_date', auth()->user()->birth_date) }}" placeholder=" ">
                        <label for="birth_date">
                            <i class="fas fa-birthday-cake"></i>
                            <span>Date de naissance</span>
                        </label>
                        <div class="input-focus-border"></div>
                    </div>
                </div>
                
                <div class="input-group floating textarea-group">
                    <textarea name="address" id="address" class="form-control-modern" rows="2" placeholder=" ">{{ old('address', auth()->user()->address) }}</textarea>
                    <label for="address">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Adresse complète</span>
                    </label>
                    <div class="input-focus-border"></div>
                </div>
                
                <div class="form-grid">
                    <div class="input-group floating">
                        <input type="text" name="formation" id="formation" class="form-control-modern" 
                               value="{{ old('formation', auth()->user()->formation) }}" placeholder=" ">
                        <label for="formation">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Formation</span>
                        </label>
                        <div class="input-focus-border"></div>
                    </div>
                    
                    <div class="input-group floating">
                        <input type="text" name="universite" id="universite" class="form-control-modern" 
                               value="{{ old('universite', auth()->user()->universite) }}" placeholder=" ">
                        <label for="universite">
                            <i class="fas fa-university"></i>
                            <span>Université</span>
                        </label>
                        <div class="input-focus-border"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-gradient btn-modern">
                    <span class="btn-text">Mettre à jour le profil</span>
                    <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>
                    <div class="btn-shine"></div>
                </button>
            </form>
        </div>

        {{-- Carte Documents --}}
        <div class="glass-card documents-card">
            <div class="card-glow"></div>
            <div class="card-header-modern">
                <div class="header-icon gradient-purple">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="header-text">
                    <h2>Documents requis</h2>
                    <span>Formats acceptés : PDF, DOC, DOCX</span>
                </div>
            </div>
            
            <form method="POST" action="{{ route('candidat.new.documents') }}" enctype="multipart/form-data" class="documents-form">
                @csrf
                
                @foreach([
                    'cv' => ['label' => 'Curriculum Vitae', 'icon' => 'file-pdf', 'required' => true, 'color' => 'rose'],
                    'lettre_motivation' => ['label' => 'Lettre de motivation', 'icon' => 'file-alt', 'required' => true, 'color' => 'amber'],
                    'diplome' => ['label' => 'Diplôme (optionnel)', 'icon' => 'certificate', 'required' => false, 'color' => 'emerald']
                ] as $type => $config)
                    @php
                        $existingDoc = $documents->where('type', $type)->first();
                        $hasFile = $existingDoc !== null;
                    @endphp
                    <div class="upload-zone {{ $hasFile ? 'has-file' : '' }}" data-type="{{ $type }}">
                        <div class="upload-glow {{ $config['color'] }}"></div>
                        <input type="file" name="{{ $type }}" id="{{ $type }}" class="file-input-modern" 
                               accept=".pdf,.doc,.docx" {{ $hasFile ? '' : ($config['required'] ? 'required' : '') }}>
                        
                        <div class="upload-content">
                            <div class="upload-icon-wrapper {{ $config['color'] }}">
                                <i class="fas fa-{{ $config['icon'] }}"></i>
                                @if($hasFile)
                                    <div class="check-badge"><i class="fas fa-check"></i></div>
                                @endif
                            </div>
                            
                            <div class="upload-text">
                                <span class="upload-title">{{ $config['label'] }}</span>
                                <span class="upload-hint">
                                    {{ $hasFile ? 'Fichier téléchargé' : 'Glissez-déposez ou cliquez' }}
                                </span>
                            </div>
                            
                            @if($hasFile)
                                <a href="{{ asset('storage/' . $existingDoc->fichier_path) }}" target="_blank" class="file-action view">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @else
                                <div class="upload-arrow">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="upload-progress">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                @endforeach
                
                <button type="submit" class="btn-gradient btn-modern btn-purple">
                    <span class="btn-text">Envoyer les documents</span>
                    <span class="btn-icon"><i class="fas fa-paper-plane"></i></span>
                    <div class="btn-shine"></div>
                </button>
            </form>
        </div>
    </div>

    {{-- Section Token --}}
    @if(!$isDossierValide && !$hasToken)
    <div class="token-section">
        <div class="glass-card token-card-modern">
            <div class="card-glow"></div>
            <div class="token-visual">
                <div class="token-lock">
                    <i class="fas fa-lock"></i>
                    <div class="lock-shine"></div>
                </div>
                <div class="token-particles"></div>
            </div>
            
            <div class="token-content">
                <h2><i class="fas fa-key"></i> Activation du compte</h2>
                <p>Saisissez le token fourni par votre responsable pour débloquer l'accès complet à votre dashboard.</p>
                
                <form method="POST" action="{{ route('candidat.verifier-token') }}" class="token-form-modern">
                    @csrf
                    <div class="token-input-wrapper">
                        <div class="input-decoration left"><i class="fas fa-shield-alt"></i></div>
                        <input type="text" name="token" class="token-input-modern" 
                               placeholder="ABC-123-DEF-456" maxlength="15" autocomplete="off" required>
                        <div class="input-decoration right">
                            <button type="submit" class="token-submit">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                        <div class="token-input-bg"></div>
                    </div>
                    <span class="token-hint">Format : 12 caractères alphanumériques</span>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Carte Statut --}}
    <div class="status-section">
        <div class="glass-card status-card-modern">
            <div class="card-glow"></div>
            
            <div class="status-header">
                <div class="status-icon-wrapper {{ $isDossierValide ? 'success' : ($hasToken ? 'info' : ($isDossierComplet ? 'warning' : 'error')) }}">
                    <i class="fas fa-{{ $isDossierValide ? 'check-circle' : ($hasToken ? 'key' : ($isDossierComplet ? 'hourglass-half' : 'exclamation-triangle')) }}"></i>
                </div>
                <div class="status-title-group">
                    <h2>État du dossier</h2>
                    <span class="status-pill {{ $isDossierValide ? 'success' : ($hasToken ? 'info' : ($isDossierComplet ? 'warning' : 'error')) }}">
                        {{ $isDossierValide ? 'Validé' : ($hasToken ? 'En cours' : ($isDossierComplet ? 'Complet' : 'Incomplet')) }}
                    </span>
                </div>
            </div>
            
            {{-- Progress Circle --}}
            <div class="progress-circle-container">
                <div class="progress-circle" data-progress="{{ $totalProgress }}">
                    <svg viewBox="0 0 100 100">
                        <circle class="progress-bg" cx="50" cy="50" r="45"/>
                        <circle class="progress-bar-circular" cx="50" cy="50" r="45"/>
                    </svg>
                    <div class="progress-text">
                        <span class="progress-percent">{{ $totalProgress }}</span>
                        <span class="progress-label">%</span>
                    </div>
                </div>
                <div class="progress-info">
                    <span class="info-label">Progression globale</span>
                    <span class="info-value">{{ $docsUploaded }}/3 documents</span>
                </div>
            </div>
            
            {{-- Checklist Grid --}}
            <div class="checklist-grid">
                @php
                    $checks = [
                        ['label' => 'CV', 'icon' => 'file-pdf', 'completed' => $documents->where('type', 'cv')->count() > 0],
                        ['label' => 'Lettre', 'icon' => 'envelope', 'completed' => $documents->where('type', 'lettre_motivation')->count() > 0],
                        ['label' => 'Téléphone', 'icon' => 'phone', 'completed' => auth()->user()->phone != null],
                        ['label' => 'Adresse', 'icon' => 'home', 'completed' => auth()->user()->address != null],
                        ['label' => 'Token', 'icon' => 'key', 'completed' => $hasToken],
                    ];
                @endphp
                @foreach($checks as $check)
                    <div class="check-item {{ $check['completed'] ? 'completed' : '' }}">
                        <div class="check-box">
                            <i class="fas fa-{{ $check['completed'] ? 'check' : 'minus' }}"></i>
                        </div>
                        <i class="fas fa-{{ $check['icon'] }}"></i>
                        <span>{{ $check['label'] }}</span>
                    </div>
                @endforeach
            </div>
            
            {{-- Action Button --}}
            <div class="status-action">
                @if($isDossierValide)
                    <a href="{{ route('candidat.dashboard') }}" class="btn-gradient btn-success btn-large">
                        <span class="btn-text">Accéder au Dashboard</span>
                        <span class="btn-icon"><i class="fas fa-rocket"></i></span>
                        <div class="btn-particles"></div>
                    </a>
                @elseif($isDossierComplet && $hasToken)
                    <div class="waiting-message">
                        <div class="spinner"></div>
                        <span>Validation en cours par l'administration...</span>
                    </div>
                @elseif($isDossierComplet)
                    <div class="alert-modern alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <span>Dossier complet ! En attente du token d'activation.</span>
                    </div>
                @else
                    <div class="alert-modern alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Complétez tous les champs obligatoires pour continuer.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animations d'entrée
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.glass-card').forEach(card => observer.observe(card));
    
    // Upload zones interaction
    document.querySelectorAll('.upload-zone').forEach(zone => {
        const input = zone.querySelector('.file-input-modern');
        const fileNameSpan = zone.querySelector('.upload-hint');
        
        zone.addEventListener('click', () => input.click());
        
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        
        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dragover');
        });
        
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                updateFileDisplay(zone, e.dataTransfer.files[0].name);
            }
        });
        
        input.addEventListener('change', (e) => {
            if (e.target.files.length) {
                updateFileDisplay(zone, e.target.files[0].name);
            }
        });
    });
    
    function updateFileDisplay(zone, fileName) {
        const hint = zone.querySelector('.upload-hint');
        const icon = zone.querySelector('.upload-icon-wrapper');
        hint.textContent = fileName;
        hint.style.color = 'var(--success)';
        icon.classList.add('pulse');
        zone.classList.add('has-file');
        
        // Simuler la progression
        const progress = zone.querySelector('.progress-fill');
        progress.style.width = '100%';
        setTimeout(() => {
            progress.style.width = '0%';
        }, 1000);
    }
    
    // Formatage token
    const tokenInput = document.querySelector('.token-input-modern');
    if (tokenInput) {
        tokenInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            // Ajouter des tirets automatiquement
            if (value.length > 3) value = value.slice(0,3) + '-' + value.slice(3);
            if (value.length > 7) value = value.slice(0,7) + '-' + value.slice(7);
            if (value.length > 11) value = value.slice(0,11) + '-' + value.slice(11);
            e.target.value = value;
        });
    }
    
    // Progress circle animation
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const progress = progressCircle.dataset.progress;
        const circle = progressCircle.querySelector('.progress-bar-circular');
        const circumference = 2 * Math.PI * 45;
        const offset = circumference - (progress / 100) * circumference;
        
        setTimeout(() => {
            circle.style.strokeDashoffset = offset;
        }, 500);
    }
    
    // Fermeture des toasts
    document.querySelectorAll('.toast-close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.toast').classList.add('hiding');
            setTimeout(() => this.closest('.toast').remove(), 300);
        });
    });
    
    // Auto-hide toasts après 5s
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach(toast => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        });
    }, 5000);
});
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #ec4899;
        --accent: #06b6d4;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --dark: #0f172a;
        --gray-900: #1e293b;
        --gray-700: #334155;
        --gray-500: #64748b;
        --gray-300: #cbd5e1;
        --gray-100: #f1f5f9;
        --white: #ffffff;
        
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.5);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        
        --radius-sm: 12px;
        --radius-md: 20px;
        --radius-lg: 28px;
        --radius-full: 9999px;
        
        --transition-fast: 0.15s ease;
        --transition-normal: 0.3s ease;
        --transition-slow: 0.5s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-attachment: fixed;
        min-height: 100vh;
        color: var(--gray-900);
    }

    /* Hero Section avec Glassmorphism */
    .hero-section {
        position: relative;
        border-radius: var(--radius-lg);
        padding: 3rem 2.5rem;
        margin-bottom: 2rem;
        overflow: hidden;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--glass-shadow);
    }

    .hero-background {
        position: absolute;
        inset: 0;
        overflow: hidden;
        z-index: 0;
    }

    .gradient-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.6;
        animation: float 20s infinite ease-in-out;
    }

    .orb-1 {
        width: 300px;
        height: 300px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        top: -100px;
        right: -50px;
        animation-delay: 0s;
    }

    .orb-2 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #f093fb, #f5576c);
        bottom: -50px;
        left: 10%;
        animation-delay: -5s;
    }

    .orb-3 {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        top: 50%;
        left: 50%;
        animation-delay: -10s;
    }

    .grid-pattern {
        position: absolute;
        inset: 0;
        background-image: 
            linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        opacity: 0.3;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .welcome-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .avatar-pulse {
        position: relative;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-ring {
        position: absolute;
        border: 2px solid rgba(99, 102, 241, 0.3);
        border-radius: 50%;
        animation: pulse-ring 2s infinite;
    }

    .avatar-ring:nth-child(1) {
        width: 100%;
        height: 100%;
    }

    .avatar-ring:nth-child(2) {
        width: 140%;
        height: 140%;
        animation-delay: 0.5s;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(1.2); opacity: 0; }
    }

    .wave-emoji {
        font-size: 2.5rem;
        animation: wave 2s infinite;
        transform-origin: 70% 70%;
        display: inline-block;
        z-index: 2;
    }

    @keyframes wave {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(14deg); }
        20% { transform: rotate(-8deg); }
        30% { transform: rotate(14deg); }
        40% { transform: rotate(-4deg); }
        50% { transform: rotate(10deg); }
        60% { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }

    .welcome-text h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        color: var(--gray-500);
        font-size: 1.1rem;
        font-weight: 500;
    }

    /* Timeline Progress */
    .timeline-container {
        background: rgba(255, 255, 255, 0.5);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .progress-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .step-node {
        position: relative;
        width: 50px;
        height: 50px;
        margin-bottom: 0.75rem;
    }

    .node-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--gray-300), var(--gray-100));
        border-radius: 50%;
        transition: var(--transition-normal);
    }

    .timeline-step.completed .node-bg {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .timeline-step.current .node-bg {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        animation: node-glow 2s infinite;
    }

    @keyframes node-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); }
        50% { box-shadow: 0 0 0 15px rgba(99, 102, 241, 0); }
    }

    .node-icon {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        z-index: 2;
    }

    .timeline-step.pending .node-icon {
        color: var(--gray-500);
    }

    .node-pulse {
        position: absolute;
        inset: -5px;
        border: 2px solid var(--primary);
        border-radius: 50%;
        animation: node-pulse 2s infinite;
    }

    @keyframes node-pulse {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.3); opacity: 0; }
    }

    .step-info {
        text-align: center;
    }

    .step-title {
        display: block;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--gray-900);
        margin-bottom: 0.2rem;
    }

    .step-desc {
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    .timeline-step.pending .step-title,
    .timeline-step.pending .step-desc {
        color: var(--gray-400);
    }

    .step-line {
        position: absolute;
        top: 25px;
        left: 60%;
        width: 80%;
        height: 3px;
        background: var(--gray-300);
        border-radius: 2px;
        transition: var(--transition-slow);
    }

    .step-line.completed {
        background: linear-gradient(90deg, var(--success), #059669);
    }

    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 2rem;
        right: 2rem;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .toast {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-md);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        min-width: 350px;
        animation: slideIn 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .toast.hiding {
        animation: slideOut 0.3s ease forwards;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        to { transform: translateX(100%); opacity: 0; }
    }

    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .toast-success .toast-icon { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .toast-error .toast-icon { background: rgba(239, 68, 68, 0.1); color: var(--error); }

    .toast-content {
        flex: 1;
    }

    .toast-content strong {
        display: block;
        font-size: 0.95rem;
        color: var(--gray-900);
        margin-bottom: 0.2rem;
    }

    .toast-content span {
        font-size: 0.85rem;
        color: var(--gray-500);
    }

    .toast-close {
        background: none;
        border: none;
        color: var(--gray-400);
        cursor: pointer;
        padding: 0.5rem;
        transition: var(--transition-fast);
    }

    .toast-close:hover { color: var(--gray-700); }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: currentColor;
        opacity: 0.3;
        animation: progress 5s linear forwards;
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Glass Cards */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--glass-shadow);
        position: relative;
        overflow: hidden;
        transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        opacity: 0;
        transform: translateY(30px);
    }

    .glass-card.animate-in {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .card-glow {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%);
        opacity: 0;
        transition: opacity var(--transition-normal);
        pointer-events: none;
    }

    .glass-card:hover .card-glow {
        opacity: 1;
    }

    .card-header-modern {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .header-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
    }

    .gradient-blue { background: linear-gradient(135deg, #667eea, #764ba2); }
    .gradient-purple { background: linear-gradient(135deg, #f093fb, #f5576c); }

    .header-text h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.2rem;
    }

    .header-text span {
        font-size: 0.85rem;
        color: var(--gray-500);
    }

    .header-badge {
        margin-left: auto;
        width: 36px;
        height: 36px;
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .header-badge:hover {
        background: var(--primary);
        color: white;
        transform: rotate(15deg);
    }

    /* Modern Form Inputs */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .input-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .input-group.floating {
        margin-bottom: 1.5rem;
    }

    .form-control-modern {
        width: 100%;
        padding: 1rem 1rem 1rem 2.8rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-sm);
        font-size: 0.95rem;
        font-family: inherit;
        background: var(--white);
        transition: var(--transition-fast);
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group.floating .form-control-modern {
        padding: 1.2rem 1rem 0.8rem 2.8rem;
    }

    .input-group.floating label {
        position: absolute;
        left: 2.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
        font-size: 0.95rem;
        pointer-events: none;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .input-group.floating label i {
        color: var(--primary);
    }

    .input-group.floating .form-control-modern:focus ~ label,
    .input-group.floating .form-control-modern:not(:placeholder-shown) ~ label {
        top: 0.6rem;
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 600;
    }

    .input-focus-border {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        transition: var(--transition-normal);
        transform: translateX(-50%);
    }

    .form-control-modern:focus ~ .input-focus-border {
        width: 100%;
    }

    .textarea-group .form-control-modern {
        padding-top: 1.5rem;
        min-height: 100px;
        resize: vertical;
    }

    /* Upload Zones */
    .documents-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .upload-zone {
        position: relative;
        background: var(--gray-100);
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        cursor: pointer;
        transition: var(--transition-normal);
        overflow: hidden;
    }

    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.05);
        transform: translateY(-2px);
    }

    .upload-zone.has-file {
        border-color: var(--success);
        background: rgba(16, 185, 129, 0.05);
    }

    .upload-glow {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity var(--transition-normal);
    }

    .upload-glow.rose { background: radial-gradient(circle at center, rgba(244, 63, 94, 0.1), transparent); }
    .upload-glow.amber { background: radial-gradient(circle at center, rgba(245, 158, 11, 0.1), transparent); }
    .upload-glow.emerald { background: radial-gradient(circle at center, rgba(16, 185, 129, 0.1), transparent); }

    .upload-zone:hover .upload-glow {
        opacity: 1;
    }

    .file-input-modern {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .upload-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        z-index: 5;
    }

    .upload-icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
        position: relative;
        transition: var(--transition-normal);
    }

    .upload-icon-wrapper.rose { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .upload-icon-wrapper.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .upload-icon-wrapper.emerald { background: linear-gradient(135deg, #10b981, #059669); }

    .upload-icon-wrapper.pulse {
        animation: icon-pulse 0.5s ease;
    }

    @keyframes icon-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .check-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        width: 22px;
        height: 22px;
        background: var(--success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        border: 2px solid white;
    }

    .upload-text {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .upload-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.95rem;
    }

    .upload-hint {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin-top: 0.2rem;
    }

    .upload-arrow,
    .file-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--white);
        color: var(--gray-500);
        transition: var(--transition-fast);
    }

    .upload-zone:hover .upload-arrow {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
    }

    .file-action.view {
        background: var(--success);
        color: white;
        text-decoration: none;
    }

    .file-action.view:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    .upload-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--gray-200);
        opacity: 0;
        transition: opacity var(--transition-fast);
    }

    .upload-zone:hover .upload-progress {
        opacity: 1;
    }

    .progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        transition: width 0.3s ease;
    }

    /* Buttons */
    .btn-gradient {
        position: relative;
        padding: 1rem 2rem;
        border: none;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.95rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: var(--transition-normal);
    }

    .btn-modern {
        width: 100%;
        margin-top: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .btn-modern.btn-purple {
        background: linear-gradient(135deg, #f093fb, #f5576c);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .btn-large {
        padding: 1.2rem 2.5rem;
        font-size: 1.1rem;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .btn-gradient:hover .btn-icon {
        transform: translateX(5px);
    }

    .btn-icon {
        transition: transform var(--transition-fast);
    }

    .btn-shine {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255,255,255,0.3) 50%,
            transparent 70%
        );
        transform: rotate(45deg) translateX(-100%);
        transition: transform 0.6s;
    }

    .btn-gradient:hover .btn-shine {
        transform: rotate(45deg) translateX(100%);
    }

    /* Token Section */
    .token-section {
        margin-bottom: 2rem;
    }

    .token-card-modern {
        display: flex;
        align-items: center;
        gap: 3rem;
        padding: 3rem;
    }

    .token-visual {
        position: relative;
        flex-shrink: 0;
    }

    .token-lock {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--gray-700), var(--dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        position: relative;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .lock-shine {
        position: absolute;
        top: 20%;
        left: 20%;
        width: 30%;
        height: 30%;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        filter: blur(10px);
    }

    .token-particles {
        position: absolute;
        inset: -20px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);
        animation: particle-rotate 10s linear infinite;
    }

    @keyframes particle-rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .token-content {
        flex: 1;
    }

    .token-content h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .token-content h2 i {
        color: var(--primary);
    }

    .token-content p {
        color: var(--gray-500);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .token-form-modern {
        max-width: 500px;
    }

    .token-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-decoration {
        position: absolute;
        z-index: 10;
        color: var(--gray-400);
    }

    .input-decoration.left {
        left: 1.2rem;
        font-size: 1.2rem;
    }

    .input-decoration.right {
        right: 0.5rem;
    }

    .token-input-modern {
        width: 100%;
        padding: 1.2rem 4rem 1.2rem 3.5rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-full);
        font-size: 1.1rem;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        background: var(--white);
        transition: var(--transition-fast);
    }

    .token-input-modern:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 10px 30px rgba(99, 102, 241, 0.1);
    }

    .token-submit {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        cursor: pointer;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .token-submit:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
    }

    .token-hint {
        display: block;
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: var(--gray-400);
        margin-left: 1.5rem;
    }

    /* Status Section */
    .status-section {
        margin-bottom: 2rem;
    }

    .status-card-modern {
        padding: 2.5rem;
    }

    .status-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .status-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .status-icon-wrapper.success { background: linear-gradient(135deg, var(--success), #059669); }
    .status-icon-wrapper.info { background: linear-gradient(135deg, var(--info), #2563eb); }
    .status-icon-wrapper.warning { background: linear-gradient(135deg, var(--warning), #d97706); }
    .status-icon-wrapper.error { background: linear-gradient(135deg, var(--error), #dc2626); }

    .status-title-group {
        flex: 1;
    }

    .status-title-group h2 {
        font-size: 1.4rem;
        margin-bottom: 0.3rem;
    }

    .status-pill {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: var(--radius-full);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-pill.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-pill.info { background: rgba(59, 130, 246, 0.1); color: var(--info); }
    .status-pill.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .status-pill.error { background: rgba(239, 68, 68, 0.1); color: var(--error); }

    /* Circular Progress */
    .progress-circle-container {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding: 2rem;
        background: var(--gray-100);
        border-radius: var(--radius-md);
    }

    .progress-circle {
        position: relative;
        width: 150px;
        height: 150px;
        flex-shrink: 0;
    }

    .progress-circle svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }

    .progress-bg {
        fill: none;
        stroke: var(--gray-300);
        stroke-width: 8;
    }

    .progress-bar-circular {
        fill: none;
        stroke: url(#gradient);
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 283;
        stroke-dashoffset: 283;
        transition: stroke-dashoffset 1s ease;
    }

    .progress-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .progress-percent {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .progress-label {
        font-size: 1.2rem;
        color: var(--gray-400);
        font-weight: 600;
    }

    .progress-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        font-size: 0.9rem;
        color: var(--gray-500);
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    /* Checklist Grid */
    .checklist-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .check-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        background: var(--gray-100);
        border-radius: var(--radius-sm);
        transition: var(--transition-fast);
        cursor: default;
    }

    .check-item.completed {
        background: rgba(16, 185, 129, 0.1);
    }

    .check-box {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--gray-300);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        transition: var(--transition-fast);
    }

    .check-item.completed .check-box {
        background: var(--success);
        animation: check-pop 0.3s ease;
    }

    @keyframes check-pop {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .check-item i:not(.check-box i) {
        font-size: 1.3rem;
        color: var(--gray-400);
    }

    .check-item.completed i:not(.check-box i) {
        color: var(--success);
    }

    .check-item span {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-500);
    }

    .check-item.completed span {
        color: var(--success);
    }

    /* Status Action */
    .status-action {
        display: flex;
        justify-content: center;
    }

    .waiting-message {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 2rem;
        background: rgba(59, 130, 246, 0.1);
        border-radius: var(--radius-full);
        color: var(--info);
        font-weight: 500;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid currentColor;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .alert-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: var(--error);
    }

    /* SVG Gradient Definition */
    .dashboard-container::before {
        content: '';
        position: fixed;
        width: 0;
        height: 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .token-card-modern {
            flex-direction: column;
            text-align: center;
        }
        
        .checklist-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        
        .hero-section {
            padding: 2rem 1.5rem;
        }
        
        .welcome-section {
            flex-direction: column;
            text-align: center;
        }
        
        .welcome-text h1 {
            font-size: 1.6rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .timeline-container {
            overflow-x: auto;
        }
        
        .progress-timeline {
            min-width: 600px;
        }
        
        .checklist-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .progress-circle-container {
            flex-direction: column;
            text-align: center;
        }
        
        .toast-container {
            left: 1rem;
            right: 1rem;
            top: 1rem;
        }
        
        .toast {
            min-width: auto;
        }
    }

    /* SVG Definition for gradient */
    .dashboard-container {
        position: relative;
    }
</style>

<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#ec4899;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>
@endsection


