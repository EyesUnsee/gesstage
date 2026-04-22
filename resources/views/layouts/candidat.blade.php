<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GesStage - Candidat')</title>
    
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

        /* Week Selector */
        .week-selector {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border: 1px solid var(--gris-clair);
        }

        .month-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--bleu);
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 12px;
        }

        .week-nav {
            display: flex;
            gap: 0.8rem;
        }

        .week-nav-btn {
            padding: 0.5rem 1.2rem;
            background: var(--gris-clair);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .week-nav-btn:hover {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
            transform: translateY(-2px);
        }

        .week-selector-wrapper {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .week-select {
            padding: 0.5rem 1rem;
            border: 2px solid var(--gris-clair);
            border-radius: 12px;
            background: white;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .week-select:focus {
            outline: none;
            border-color: var(--bleu);
        }

        .week-current {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            font-weight: 500;
        }

        .week-current:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Current Week */
        .current-week {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .current-week::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .week-range {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .week-progress {
            margin-top: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
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
            transition: width 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        /* Weekly Grid */
        .weekly-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .day-card {
            background: var(--blanc);
            border-radius: 20px;
            border: 1px solid var(--gris-clair);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .day-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--bleu);
        }

        .day-header {
            background: linear-gradient(135deg, var(--gris-clair), white);
            padding: 1rem;
            text-align: center;
            border-bottom: 2px solid var(--gris-clair);
        }

        .day-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--noir);
            margin: 0 0 0.3rem 0;
        }

        .day-date {
            font-size: 0.75rem;
            color: var(--gris);
        }

        .day-tasks {
            min-height: 300px;
            max-height: 400px;
            overflow-y: auto;
            padding: 0.8rem;
        }

        .day-tasks::-webkit-scrollbar {
            width: 4px;
        }

        .day-tasks::-webkit-scrollbar-track {
            background: var(--gris-clair);
            border-radius: 10px;
        }

        .day-tasks::-webkit-scrollbar-thumb {
            background: var(--bleu);
            border-radius: 10px;
        }

        .task-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 0.6rem;
            margin-bottom: 0.6rem;
            background: var(--gris-clair);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .task-item:hover {
            transform: translateX(3px);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
        }

        .task-item.completed {
            opacity: 0.6;
            background: #e2e8f0;
        }

        .task-item.completed .task-title {
            text-decoration: line-through;
            color: var(--gris);
        }

        .task-checkbox {
            width: 22px;
            height: 22px;
            border: 2px solid var(--gris);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            background: white;
            transition: all 0.2s ease;
        }

        .task-checkbox:hover {
            transform: scale(1.1);
        }

        .task-checkbox.completed {
            background: var(--vert);
            border-color: var(--vert);
        }

        .task-checkbox i {
            font-size: 0.7rem;
            color: white;
        }

        .task-content {
            flex: 1;
        }

        .task-title {
            font-size: 0.85rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.3rem;
            word-break: break-word;
        }

        .task-meta {
            display: flex;
            align-items: center;
        }

        .task-priority {
            font-size: 0.65rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
        }

        .priority-high {
            background: rgba(239, 68, 68, 0.1);
            color: var(--rouge);
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .priority-low {
            background: rgba(16, 185, 129, 0.1);
            color: var(--vert);
        }

        .task-actions {
            display: flex;
            gap: 0.3rem;
        }

        .btn-icon-small {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: white;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon-small:hover {
            background: var(--bleu);
            color: white;
            transform: scale(1.05);
        }

        .empty-day {
            text-align: center;
            padding: 1.5rem;
            color: var(--gris);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .empty-day:hover {
            background: var(--gris-clair);
            border-radius: 12px;
            transform: scale(1.02);
        }

        .empty-day i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.3rem;
        }

        .add-task-day {
            width: 100%;
            padding: 0.7rem;
            background: var(--gris-clair);
            border: none;
            border-top: 2px solid var(--gris-clair);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .add-task-day:hover {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
        }

        /* Month Calendar */
        .month-calendar {
            background: var(--blanc);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--gris-clair);
            margin-bottom: 2rem;
        }

        .month-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--noir);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .weeks-calendar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .week-card {
            background: var(--gris-clair);
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .week-card:hover {
            transform: translateY(-3px);
            border-color: var(--bleu);
            background: white;
            box-shadow: var(--shadow-md);
        }

        .week-card.current {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
        }

        .week-card.current .week-tasks-count {
            color: rgba(255, 255, 255, 0.9);
        }

        .week-card.validated {
            background: linear-gradient(135deg, var(--vert), #10b981);
            color: white;
        }

        .week-number {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }

        .week-dates {
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .week-tasks-count {
            font-size: 0.7rem;
            color: var(--gris);
        }

        .week-validated-badge {
            margin-top: 0.5rem;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .week-card .week-number i {
            margin-right: 0.3rem;
            font-size: 0.8rem;
        }

        /* Validation Styles */
        .week-validation {
            margin-top: 1rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .btn-validate-week {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 0.6rem 1.8rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-validate-week:hover {
            background: white;
            color: var(--bleu);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .validation-info {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        .week-validated {
            margin-top: 1rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.6rem;
            border-radius: 50px;
            display: inline-block;
            width: auto;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
        }

        .week-validated i {
            color: #fcd34d;
            margin: 0 0.5rem;
            animation: starPulse 1s ease-in-out infinite;
        }

        @keyframes starPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        .week-empty, .week-pending {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 28px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
            box-shadow: var(--shadow-lg);
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
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
            background: linear-gradient(135deg, var(--gris-clair), white);
            border-radius: 28px 28px 0 0;
        }

        .modal-header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-close {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--gris-clair);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .modal-close:hover {
            background: var(--rouge);
            color: white;
            transform: rotate(90deg);
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
            margin-bottom: 1.2rem;
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
            border-radius: 14px;
            outline: none;
            transition: all 0.2s;
            font-size: 0.9rem;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: var(--bleu);
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .priority-select {
            display: flex;
            gap: 1rem;
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
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .priority-option input:checked + .priority-badge {
            border-color: currentColor;
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-cancel, .btn-submit {
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-cancel {
            background: var(--gris-clair);
            color: var(--gris-fonce);
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--bleu), var(--vert));
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
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
            .weekly-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .weekly-grid {
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
            
            .weekly-grid {
                grid-template-columns: 1fr;
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
            
            .weeks-calendar {
                grid-template-columns: 1fr;
            }
            
            .priority-select {
                flex-direction: column;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer button {
                width: 100%;
            }
            
            .week-range {
                flex-direction: column;
                text-align: center;
            }
            
            .burger-menu {
                display: flex;
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
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('candidat.dashboard') }}" class="logo">
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
                <p><i class="fas fa-circle"></i> Candidat</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('candidat.dashboard') }}" class="nav-item {{ request()->routeIs('candidat.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('candidat.profil') }}" class="nav-item {{ request()->routeIs('candidat.profil') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="{{ route('candidat.documents.index') }}" class="nav-item {{ request()->routeIs('candidat.documents*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i>
                <span>Mes documents</span>
            </a>
            <a href="{{ route('candidat.pointage') }}" class="nav-item {{ request()->routeIs('candidat.pointage') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>Pointage</span>
            </a>
            <a href="{{ route('candidat.journal') }}" class="nav-item {{ request()->routeIs('candidat.journal*') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                <span>Journal de bord</span>
            </a>
            <a href="{{ route('candidat.evaluations.index') }}" class="nav-item {{ request()->routeIs('candidat.evaluations*') ? 'active' : '' }}">
                <i class="fas fa-star"></i>
                <span>Mes évaluations</span>
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
                <div class="notification-badge">
                    <i class="far fa-bell"></i>
                    <span class="badge">3</span>
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
