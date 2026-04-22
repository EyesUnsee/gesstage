<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GesStage - Tuteur')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1e293b;
            --light: #f8fafc;
            --gris: #94a3b8;
            --blanc: #ffffff;
            --blanc-casse: #f8fafc;
            --rouge: #ef4444;
            --rouge-fonce: #dc2626;
            --bleu: #3b82f6;
            --bleu-fonce: #2563eb;
            --vert: #10b981;
            --vert-fonce: #059669;
            --gris-clair: #f1f5f9;
            --gris-fonce: #334155;
            --noir: #0f172a;
            --shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar-header .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
        }

        .sidebar-header .logo img {
            height: 40px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(37, 99, 235, 0.2));
            transition: transform 0.3s ease;
        }

        .sidebar-header .logo img:hover {
            transform: scale(1.05);
        }

        .sidebar-header .logo span {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .user-info {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--bleu);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .user-details h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--dark);
        }

        .user-details p {
            font-size: 0.8rem;
            color: var(--gris);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .user-details p i {
            color: var(--vert);
            font-size: 0.7rem;
        }

        .nav-menu {
            padding: 1.5rem 0;
        }

        .nav-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-item i {
            width: 20px;
            font-size: 1.1rem;
        }

        .nav-item:hover, .nav-item.active {
            background: #eef2ff;
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .nav-item.active {
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Burger Menu */
        .burger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 10px;
            background: var(--gris-clair);
            border-radius: 12px;
            margin-left: 10px;
        }

        .burger-menu span {
            width: 22px;
            height: 3px;
            background: var(--gris-fonce);
            margin: 2px 0;
            transition: 0.3s;
            border-radius: 3px;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
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
        }

        .welcome-logo {
            flex-shrink: 0;
            animation: float 3s ease-in-out infinite;
        }

        .welcome-logo img {
            height: 80px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .welcome-logo img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-title span {
            color: #fcd34d;
        }

        .welcome-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
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
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-trend {
            font-size: 0.85rem;
            color: var(--gris);
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        /* Section Titles */
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .section-title h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--noir);
        }

        .section-title i {
            font-size: 1.5rem;
            color: var(--bleu);
        }

        /* Stagiaires Grid */
        .stagiaires-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stagiaire-card {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 2px solid var(--gris-clair);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stagiaire-card:hover {
            transform: translateY(-5px);
            border-color: var(--bleu);
        }

        .stagiaire-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanc);
            font-size: 1.8rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .stagiaire-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .stagiaire-info {
            flex: 1;
        }

        .stagiaire-info h3 {
            color: var(--noir);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .stagiaire-info p {
            color: var(--gris);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .stagiaire-info p i {
            color: var(--bleu);
        }

        .stagiaire-progress {
            margin-top: 0.5rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--gris-fonce);
            margin-bottom: 0.3rem;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--gris-clair);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--bleu), var(--vert));
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .btn-view {
            padding: 0.5rem 1.2rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: var(--blanc);
            text-decoration: none;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px var(--bleu);
            color: var(--blanc);
        }

        /* Table Container */
        .table-container {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 2px solid var(--gris-clair);
            box-shadow: var(--shadow);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            color: var(--gris);
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid var(--gris-clair);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--gris-clair);
            color: var(--gris-fonce);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--gris-clair);
        }

        .btn-table {
            padding: 0.4rem 1rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: var(--blanc);
            text-decoration: none;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-table:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px var(--bleu);
            color: var(--blanc);
        }

        /* Empty States */
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

        .empty-state-small {
            text-align: center;
            padding: 2rem;
        }

        .empty-state-small i {
            font-size: 2rem;
            color: var(--vert);
            margin-bottom: 0.5rem;
        }

        .empty-state-small p {
            color: var(--gris);
        }

        .text-center {
            text-align: center;
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

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .top-bar-actions {
                gap: 15px;
            }
        }

        @media (max-width: 850px) {
            .page-title {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .burger-menu {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stagiaires-grid {
                grid-template-columns: 1fr;
            }
            
            .stagiaire-card {
                flex-wrap: wrap;
            }
            
            .btn-view {
                width: 100%;
                text-align: center;
            }
            
            .welcome-section {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .welcome-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 580px) {
            .top-bar-actions {
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 1rem;
            }
            
            .top-bar {
                padding: 1rem;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
            
            .welcome-section {
                padding: 1.5rem;
            }
            
            .welcome-logo img {
                height: 60px;
            }
            
            .stat-value {
                font-size: 1.8rem;
            }
            
            .section-title h2 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 360px) {
            .sidebar-header .logo span {
                font-size: 1.2rem;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
            }
            
            .user-details h4 {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('tuteur.dashboard') }}" class="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage" onerror="this.style.display='none'">
                <span>GesStage</span>
            </a>
        </div>

        <div class="user-info">
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/images/avatar-default.png') }}" 
                 alt="Avatar" 
                 class="user-avatar"
                 onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar-default.png') }}';">
            <div class="user-details">
                <h4>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                <p><i class="fas fa-circle"></i> Tuteur</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('tuteur.dashboard') }}" class="nav-item {{ request()->routeIs('tuteur.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('tuteur.profil') }}" class="nav-item {{ request()->routeIs('tuteur.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="{{ route('tuteur.stagiaires') }}" class="nav-item {{ request()->routeIs('tuteur.stagiaires*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Stagiaires</span>
            </a>
            <a href="{{ route('tuteur.evaluations') }}" class="nav-item {{ request()->routeIs('tuteur.evaluations*') ? 'active' : '' }}">
                <i class="fas fa-star-half-alt"></i>
                <span>Évaluations</span>
            </a>
            <a href="{{ route('tuteur.journaux') }}" class="nav-item {{ request()->routeIs('tuteur.journaux*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>
                <span>Suivi journal</span>
            </a>
            <hr style="margin: 1rem 1.5rem; border-color: #e2e8f0;">
            <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>@yield('title', 'Tableau de bord')</h1>
            </div>
            <div class="top-bar-actions">
                <div class="burger-menu" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Fermer la sidebar en cliquant à l'extérieur sur mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const burgerMenu = document.querySelector('.burger-menu');
            
            if (window.innerWidth <= 768) {
                if (sidebar && burgerMenu && !sidebar.contains(event.target) && !burgerMenu.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Gestionnaire d'erreur pour les images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
