<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GesStage - @yield('title', 'Compléter mon dossier')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ========================================
           VARIABLES & RESET
        ======================================== */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #ec4899;
            --accent: #06b6d4;
            --success: #10b981;
            --success-dark: #059669;
            --warning: #f59e0b;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --info: #3b82f6;
            --dark: #0f172a;
            --gray-900: #1e293b;
            --gray-700: #334155;
            --gray-500: #64748b;
            --gray-300: #cbd5e1;
            --gray-100: #f1f5f9;
            --white: #ffffff;
            
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 28px;
            --radius-full: 9999px;
            
            --transition-fast: 0.15s ease;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.5s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 210, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(6, 182, 212, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Floating Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: float 20s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, #667eea, transparent);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, #f093fb, transparent);
            bottom: -50px;
            left: 10%;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, #4facfe, transparent);
            top: 50%;
            left: 50%;
            animation-delay: -10s;
        }

        .orb-4 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #43e97b, transparent);
            bottom: 20%;
            right: 15%;
            animation-delay: -7s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Grid Pattern Overlay */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        /* ========================================
           HEADER STYLES - GLASSMORPHISM
        ======================================== */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-sm);
            padding: 0.3rem 0.8rem;
            transition: var(--transition);
        }

        .logo::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
            border-radius: var(--radius-sm);
        }

        .logo:hover::before {
            opacity: 0.1;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.3rem;
            box-shadow: 0 10px 20px -8px var(--primary);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .logo-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: rotate(45deg) translateX(-100%);
            transition: transform 0.6s;
        }

        .logo:hover .logo-icon::after {
            transform: rotate(45deg) translateX(100%);
        }

        .logo-icon:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .welcome-logo {
            display: flex;
            align-items: center;
        }

        .welcome-logo img {
            height: 45px;
            width: auto;
            transition: var(--transition);
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.1));
        }

        .welcome-logo img:hover {
            transform: scale(1.05);
        }

        .logo-text {
            font-weight: 800;
            font-size: 1.4rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        /* User Info - Glass Card Style */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            padding: 0.3rem 0.3rem 0.3rem 1.2rem;
            border-radius: var(--radius-full);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
        }

        .user-info:hover {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            background: linear-gradient(135deg, var(--dark), var(--gray-700));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .user-avatar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            border-radius: 50%;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        /* ========================================
           MAIN CONTENT
        ======================================== */
        .main-content {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           NOTIFICATION TOAST - GLASS STYLE
        ======================================== */
        .notification-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-left: 4px solid var(--success);
            border-radius: var(--radius-md);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(450px);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 2000;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
            min-width: 350px;
            cursor: pointer;
        }

        .notification-toast.show {
            transform: translateX(0);
        }

        .notification-toast:hover {
            transform: translateX(0) translateY(-3px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .toast-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            font-size: 1.2rem;
            backdrop-filter: blur(5px);
        }

        .toast-icon.error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }

        .toast-icon.warning {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .toast-icon.info {
            background: rgba(59, 130, 246, 0.15);
            color: var(--info);
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .toast-message {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--gray-300);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition-fast);
        }

        .toast-close:hover {
            color: var(--gray-700);
            background: rgba(0, 0, 0, 0.05);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 100%;
            animation: toastProgress 4s linear forwards;
            border-radius: 0 0 0 var(--radius-md);
        }

        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* ========================================
           LOADING SPINNER
        ======================================== */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-spinner::before {
            content: '';
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            filter: blur(20px);
            opacity: 0.5;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ========================================
           RESPONSIVE
        ======================================== */
        @media (max-width: 768px) {
            .header-container {
                padding: 0.75rem 1rem;
            }
            
            .main-content {
                margin: 1rem auto;
                padding: 0 1rem;
            }
            
            .logo-text {
                font-size: 1.1rem;
            }
            
            .logo-icon {
                width: 38px;
                height: 38px;
                font-size: 1rem;
            }
            
            .welcome-logo img {
                height: 35px;
            }
            
            .user-name {
                display: none;
            }
            
            .user-info {
                padding: 0.2rem;
                gap: 0;
            }
            
            .user-avatar {
                width: 38px;
                height: 38px;
                font-size: 0.9rem;
            }
            
            .notification-toast {
                bottom: 20px;
                right: 20px;
                left: 20px;
                min-width: auto;
                padding: 0.8rem 1rem;
            }
        }

        @media (max-width: 480px) {
            .logo-text {
                display: none;
            }
            
            .welcome-logo img {
                height: 30px;
            }
            
            .logo-icon {
                width: 35px;
                height: 35px;
            }
        }

        /* ========================================
           SCROLLBAR - Gradient Style
        ======================================== */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
        }

        /* ========================================
           UTILITY CLASSES
        ======================================== */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: var(--glass-shadow);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Animated Background Elements -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
    <div class="grid-overlay"></div>

    <header class="header">
        <div class="header-container">
            <a href="{{ route('candidat.new.dashboard') }}" class="logo">
                <div class="logo-icon">
                    <div class="welcome-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'">
                    </div>
                </div>
                <span class="logo-text">GesStage</span>
            </a>
            
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->first_name ?? 'Candidat' }} {{ auth()->user()->last_name ?? '' }}</span>
                <div class="user-avatar">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </header>
    
    <main class="main-content">
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
        <button class="toast-close" onclick="closeToast()">
            <i class="fas fa-times"></i>
        </button>
        <div class="toast-progress"></div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <script>
        // Notification Toast System
        let toastTimeout;
        let toastProgressTimeout;
        
        function showNotification(title, message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            if (!toast) return;
            
            const toastIcon = toast.querySelector('.toast-icon i');
            const toastIconParent = toast.querySelector('.toast-icon');
            const toastTitle = toast.querySelector('.toast-title');
            const toastMessage = toast.querySelector('.toast-message');
            const toastProgress = toast.querySelector('.toast-progress');
            
            // Reset animation
            toastProgress.style.animation = 'none';
            toastProgress.offsetHeight; // Force reflow
            toastProgress.style.animation = 'toastProgress 4s linear forwards';
            
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            
            // Reset classes
            toastIconParent.classList.remove('error', 'warning', 'info');
            
            if (type === 'success') {
                toast.style.borderLeftColor = '#10b981';
                toastIcon.className = 'fas fa-check-circle';
                toastIcon.style.color = '#10b981';
                toastIconParent.classList.add('success');
            } else if (type === 'error') {
                toast.style.borderLeftColor = '#ef4444';
                toastIcon.className = 'fas fa-exclamation-circle';
                toastIcon.style.color = '#ef4444';
                toastIconParent.classList.add('error');
            } else if (type === 'warning') {
                toast.style.borderLeftColor = '#f59e0b';
                toastIcon.className = 'fas fa-exclamation-triangle';
                toastIcon.style.color = '#f59e0b';
                toastIconParent.classList.add('warning');
            } else if (type === 'info') {
                toast.style.borderLeftColor = '#3b82f6';
                toastIcon.className = 'fas fa-info-circle';
                toastIcon.style.color = '#3b82f6';
                toastIconParent.classList.add('info');
            }
            
            toast.classList.add('show');
            
            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }
            if (toastProgressTimeout) {
                clearTimeout(toastProgressTimeout);
            }
            
            toastTimeout = setTimeout(() => {
                closeToast();
            }, 4000);
        }
        
        function closeToast() {
            const toast = document.getElementById('notificationToast');
            if (toast) {
                toast.classList.remove('show');
            }
            if (toastTimeout) {
                clearTimeout(toastTimeout);
            }
        }
        
        // Loading overlay functions
        function showLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('active');
            }
        }
        
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.remove('active');
            }
        }
        
        // Auto-hide loading after page load
        window.addEventListener('load', function() {
            hideLoading();
        });
        
        // Show loading on form submissions
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM' && !e.target.hasAttribute('data-no-loading')) {
                showLoading();
            }
        });
        
        // Session flash messages
        @if(session('success'))
            showNotification('Succès', '{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('Erreur', '{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showNotification('Attention', '{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showNotification('Information', '{{ session('info') }}', 'info');
        @endif
        
        // Add animation to main content on page load
        document.querySelector('.main-content').style.animation = 'fadeInUp 0.6s ease-out';
    </script>
    
    @stack('scripts')
</body>
</html>
