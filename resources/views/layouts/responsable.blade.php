<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GesStage - Responsable')</title>
    
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
            --orange: #f59e0b;
            --violet: #8b5cf6;
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

        /* Button Add */
        .btn-add {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: var(--blanc);
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            box-shadow: 0 10px 20px -8px var(--bleu);
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -8px var(--bleu);
            color: var(--blanc);
        }

        /* Search Section */
        .search-section {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 2px solid var(--gris-clair);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            flex: 1;
            display: flex;
            align-items: center;
            background: var(--gris-clair);
            border-radius: 50px;
            padding: 0.5rem 1rem 0.5rem 1.5rem;
            gap: 0.8rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .search-input:focus-within {
            border-color: var(--bleu);
            background: var(--blanc);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
        }

        .search-input i {
            color: var(--gris);
            font-size: 1rem;
        }

        .search-input input {
            background: transparent;
            border: none;
            color: var(--noir);
            font-size: 1rem;
            width: 100%;
            outline: none;
        }

        .btn-search {
            padding: 0 2rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: var(--blanc);
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px var(--bleu);
        }

        .filters-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 0.8rem 1.5rem;
            background: var(--gris-clair);
            border: 2px solid transparent;
            border-radius: 40px;
            color: var(--noir);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
            min-width: 180px;
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: var(--bleu);
            background: var(--blanc);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab {
            padding: 0.8rem 2rem;
            background: var(--blanc);
            border: 2px solid var(--gris-clair);
            border-radius: 40px;
            color: var(--gris-fonce);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .tab:hover {
            border-color: var(--bleu);
            color: var(--bleu);
        }

        .tab.active {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: var(--blanc);
            border-color: transparent;
        }

        /* Cards Grid */
        .stagiaires-grid, .tuteurs-grid, .responsables-grid, .candidatures-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stagiaire-card, .tuteur-card, .responsable-card, .candidature-card {
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

        .stagiaire-card:hover, .tuteur-card:hover, .responsable-card:hover, .candidature-card:hover {
            transform: translateY(-5px);
            border-color: var(--bleu);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
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
            color: var(--bleu);
            font-size: 1rem;
        }

        .detail-item strong {
            color: var(--noir);
            font-weight: 600;
            margin-right: 0.3rem;
        }

        .card-stats {
            display: flex;
            justify-content: space-around;
            margin: 1rem 0;
            padding: 0.5rem;
            background: var(--gris-clair);
            border-radius: 16px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--noir);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--gris);
            font-weight: 500;
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

        .status-busy {
            color: var(--orange);
        }

        .status-available {
            color: var(--vert);
        }

        .status-inactive {
            color: var(--rouge);
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
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
            background: var(--bleu);
            color: var(--blanc);
        }

        .btn-icon.delete:hover {
            background: var(--rouge);
        }

        /* Progress Bar */
        .progress-section {
            margin: 1rem 0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            color: var(--gris-fonce);
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: var(--gris-clair);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--bleu), var(--vert));
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* Pagination */
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
            padding: 0.8rem 1rem;
            background: var(--gris-clair);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--bleu);
            background: var(--blanc);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--rouge), #dc2626);
        }

        .btn-danger:hover {
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
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

        .text-warning {
            color: var(--orange);
            margin-top: 0.5rem;
        }

        .text-danger {
            color: var(--rouge);
            margin-top: 0.5rem;
            font-weight: 600;
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
            
            .stagiaires-grid, .tuteurs-grid, .responsables-grid, .candidatures-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-section {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .welcome-title {
                font-size: 1.8rem;
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
            
            .tabs {
                justify-content: center;
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
            
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            
            .card-stats {
                flex-direction: column;
                gap: 0.5rem;
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
            <a href="{{ route('responsable.dashboard') }}" class="logo">
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
                <p><i class="fas fa-circle"></i> Responsable</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('responsable.dashboard') }}" class="nav-item {{ request()->routeIs('responsable.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('responsable.profil') }}" class="nav-item {{ request()->routeIs('responsable.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="{{ route('responsable.candidatures.index') }}" class="nav-item {{ request()->routeIs('responsable.candidatures*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Candidatures</span>
            </a>
            <a href="{{ route('responsable.stagiaires') }}" class="nav-item {{ request()->routeIs('responsable.stagiaires*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Stagiaires</span>
            </a>
            <a href="{{ route('responsable.tuteurs') }}" class="nav-item {{ request()->routeIs('responsable.tuteurs*') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Tuteurs</span>
            </a>
            <a href="{{ route('responsable.responsables.index') }}" class="nav-item {{ request()->routeIs('responsable.responsables*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span>Responsables</span>
            </a>
            <a href="{{ route('responsable.statistiques') }}" class="nav-item {{ request()->routeIs('responsable.statistiques') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Statistiques</span>
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
