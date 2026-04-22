<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GesStage - Chef de service')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--blanc) 0%, #fefefe 100%);
            box-shadow: var(--shadow-lg);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--gris-clair);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--bleu);
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid var(--gris-clair);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(16, 185, 129, 0.05));
        }

        .sidebar-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .sidebar-header .logo:hover {
            transform: translateX(5px);
        }

        .sidebar-header .logo img {
            height: 45px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(37, 99, 235, 0.2));
            transition: transform 0.3s ease;
        }

        .sidebar-header .logo img:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .sidebar-header .logo span {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .user-info {
            padding: 1.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 2px solid var(--gris-clair);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.03), rgba(16, 185, 129, 0.03));
        }

        .user-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--bleu);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: transform 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-details h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .user-details p {
            font-size: 0.8rem;
            color: var(--gris);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-details p i {
            color: var(--vert);
            font-size: 0.7rem;
        }

        .nav-menu {
            padding: 1.5rem 0;
        }

        .nav-item {
            padding: 0.85rem 1.5rem;
            margin: 0.2rem 0.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            font-weight: 500;
        }

        .nav-item i {
            width: 22px;
            font-size: 1.2rem;
        }

        .nav-item:hover {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(16, 185, 129, 0.1));
            color: var(--primary);
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .nav-item.active i {
            color: white;
        }

        hr {
            margin: 1rem 1.5rem;
            border: none;
            height: 2px;
            background: linear-gradient(90deg, var(--gris-clair), var(--bleu), var(--gris-clair));
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        .top-bar {
            background: var(--blanc);
            padding: 1rem 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gris-clair);
            backdrop-filter: blur(10px);
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .notification-badge:hover {
            transform: scale(1.1);
        }

        .notification-badge i {
            font-size: 1.4rem;
            color: var(--secondary);
        }

        .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, var(--rouge), var(--rouge-fonce));
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 20px;
            font-weight: 700;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        /* Burger Menu */
        .burger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 12px;
            background: var(--gris-clair);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .burger-menu:hover {
            background: var(--bleu);
        }

        .burger-menu:hover span {
            background: white;
        }

        .burger-menu span {
            width: 24px;
            height: 3px;
            background: var(--gris-fonce);
            margin: 2px 0;
            transition: 0.3s;
            border-radius: 3px;
        }

        /* ===== STYLES GLOBAUX ===== */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2rem;
            border-radius: 24px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
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
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-20px, -20px);
            }
        }

        .welcome-logo {
            flex-shrink: 0;
            animation: bounce 3s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .welcome-logo img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 800;
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
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--gris-clair);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
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
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--bleu);
        }

        .stat-card:hover::before {
            width: 120px;
            height: 120px;
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
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .stat-value {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--noir);
            margin-bottom: 0.5rem;
        }

        .stat-trend {
            font-size: 0.85rem;
            color: var(--gris);
        }

        .trend-up {
            color: var(--vert);
        }

        .trend-down {
            color: var(--rouge);
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .progress-section, .validations-section, .activity-section, .services-section {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--gris-clair);
            transition: all 0.3s ease;
        }

        .progress-section:hover, .validations-section:hover, .activity-section:hover, .services-section:hover {
            box-shadow: var(--shadow-md);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--noir);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header h2 i {
            color: var(--bleu);
        }

        .view-all {
            color: var(--bleu);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }

        .view-all:hover {
            color: var(--vert);
            gap: 0.5rem;
        }

        .progress-badge {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .progress-overall {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--gris-clair);
            flex-wrap: wrap;
        }

        .progress-circle {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .circle-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .circle-text .number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--bleu);
            line-height: 1;
        }

        .circle-text .label {
            font-size: 0.7rem;
            color: var(--gris);
        }

        .progress-stats {
            flex: 1;
        }

        .progress-stats h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--noir);
            margin-bottom: 0.5rem;
        }

        .progress-stats p {
            color: var(--gris);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .progress-meta {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--gris-fonce);
        }

        .meta-item i {
            color: var(--bleu);
        }

        .progress-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .progress-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--gris-fonce);
        }

        .progress-bar-bg {
            height: 8px;
            background: var(--gris-clair);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--bleu), var(--vert));
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .presence-evolution {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--gris-clair);
        }

        .presence-evolution h4 {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: var(--noir);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .presence-evolution h4 i {
            color: var(--bleu);
        }

        .presence-bars {
            display: flex;
            gap: 0.3rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .presence-bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 40px;
        }

        .presence-bar {
            width: 30px;
            border-radius: 4px 4px 0 0;
            transition: height 0.3s ease;
            margin-bottom: 0.3rem;
        }

        .presence-label {
            font-size: 0.6rem;
            color: var(--gris);
            transform: rotate(-45deg);
            white-space: nowrap;
        }

        .validations-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .pending-count {
            background: var(--rouge);
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .validations-list, .activity-list {
            list-style: none;
            margin-bottom: 1rem;
            max-height: 350px;
            overflow-y: auto;
        }

        .validation-item, .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            padding: 1rem;
            border-bottom: 1px solid var(--gris-clair);
            transition: background 0.2s ease;
        }

        .validation-item:hover, .activity-item:hover {
            background: var(--gris-clair);
            border-radius: 12px;
        }

        .validation-avatar, .activity-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .validation-avatar {
            background: linear-gradient(135deg, var(--rouge), #fca5a5);
        }

        .activity-icon {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
        }

        .validation-content, .activity-content {
            flex: 1;
        }

        .validation-title, .activity-title {
            font-weight: 600;
            color: var(--noir);
            margin-bottom: 0.3rem;
        }

        .validation-meta, .activity-meta {
            display: flex;
            gap: 0.8rem;
            font-size: 0.7rem;
            flex-wrap: wrap;
            color: var(--gris);
        }

        .validation-meta i, .activity-meta i {
            margin-right: 0.2rem;
        }

        .validation-action {
            display: flex;
            gap: 0.3rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .validation-item:hover .validation-action {
            opacity: 1;
        }

        .validation-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: white;
            border: 2px solid var(--gris-clair);
            color: var(--gris);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .validation-btn:hover {
            background: var(--vert);
            border-color: var(--vert);
            color: white;
            transform: scale(1.1);
        }

        .validation-btn.reject:hover {
            background: var(--rouge);
            border-color: var(--rouge);
        }

        .activity-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--vert);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.1);
            color: var(--bleu);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .service-card {
            background: var(--gris-clair);
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-3px);
            border-color: var(--bleu);
            box-shadow: 0 10px 20px -10px var(--bleu);
        }

        .service-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin: 0 auto 0.5rem;
        }

        .service-name {
            font-weight: 700;
            color: var(--noir);
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .service-count {
            color: var(--gris);
            font-size: 0.7rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gris);
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--vert);
        }

        .empty-state p {
            margin-bottom: 0.3rem;
        }

        .empty-state small {
            font-size: 0.7rem;
        }

        /* Notification Toast */
        .notification-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            border-left: 4px solid var(--vert);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(400px);
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 2000;
            box-shadow: var(--shadow-lg);
            max-width: 380px;
        }

        .notification-toast.show {
            transform: translateX(0);
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vert);
            font-size: 1.2rem;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            color: var(--noir);
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .toast-message {
            color: var(--gris);
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .dashboard-grid, .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .burger-menu {
                display: flex;
            }
            
            .progress-overall {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .progress-meta {
                justify-content: center;
            }
            
            .validation-action {
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            .stat-value {
                font-size: 1.8rem;
            }
            
            .welcome-title {
                font-size: 1.5rem;
            }
            
            .welcome-logo img {
                height: 60px;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .notification-toast {
                left: 20px;
                right: 20px;
                max-width: calc(100% - 40px);
            }
            
            .presence-bar-container {
                min-width: 30px;
            }
            
            .presence-bar {
                width: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('chef-service.dashboard') }}" class="logo">
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
                <h4>{{ auth()->user()->first_name ?? auth()->user()->name ?? 'Chef' }} {{ auth()->user()->last_name ?? '' }}</h4>
                <p><i class="fas fa-circle"></i> Chef de service</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('chef-service.dashboard') }}" class="nav-item {{ request()->routeIs('chef-service.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('chef-service.profil') }}" class="nav-item {{ request()->routeIs('chef-service.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="{{ route('chef-service.indicateurs') }}" class="nav-item {{ request()->routeIs('chef-service.indicateurs*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Indicateurs</span>
            </a>
            <a href="{{ route('chef-service.services') }}" class="nav-item {{ request()->routeIs('chef-service.services*') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Services</span>
            </a>
            <a href="{{ route('chef-service.equipe') }}" class="nav-item {{ request()->routeIs('chef-service.equipe*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Équipe</span>
            </a>
            <a href="{{ route('chef-service.bilans') }}" class="nav-item {{ request()->routeIs('chef-service.bilans*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Bilans</span>
            </a>
            <a href="{{ route('chef-service.validations') }}" class="nav-item {{ request()->routeIs('chef-service.validations*') ? 'active' : '' }}">
                <i class="fas fa-check-double"></i>
                <span>Validations</span>
            </a>
            <a href="{{ route('chef-service.rapports') }}" class="nav-item {{ request()->routeIs('chef-service.rapports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Rapports</span>
            </a>
            <a href="{{ route('chef-service.pointages') }}" class="nav-item {{ request()->routeIs('chef-service.pointages*') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Pointages</span>
            </a>
            <hr>
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
                <div class="notification-badge" onclick="window.location.href='{{ route('chef-service.notifications') }}'">
                    <i class="far fa-bell"></i>
                    <span class="badge">{{ $notificationsCount ?? 0 }}</span>
                </div>
                <div class="burger-menu" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <!-- Notification Toast -->
    <div id="notificationToast" class="notification-toast">
        <div class="toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">Succès</div>
            <div class="toast-message">Action effectuée avec succès</div>
        </div>
    </div>

    <!-- Scripts -->
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

        let toastTimeout;

        function showNotification(title, message, type = 'success') {
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
            } else if (type === 'warning') {
                toast.style.borderLeftColor = '#f59e0b';
                toastIcon.className = 'fas fa-exclamation-triangle';
                toastIcon.style.color = '#f59e0b';
            }
            
            toast.classList.add('show');
            
            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }
            
            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
    @stack('scripts')
</body>
</html>
