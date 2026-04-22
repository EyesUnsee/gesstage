<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - {{ config('app.name', 'GesStage') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #ec4899;
            --success: #10b981;
            --error: #ef4444;
            --bg-dark: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 16px;
        }

        /* Animated background shapes - version responsive */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: min(400px, 80vw);
            height: min(400px, 80vw);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            top: -10%;
            left: -10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: min(300px, 60vw);
            height: min(300px, 60vw);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            bottom: -10%;
            right: -10%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: min(250px, 50vw);
            height: min(250px, 50vw);
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            margin: auto;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: clamp(20px, 6vw, 24px);
            padding: clamp(24px, 6vw, 48px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .login-header {
            text-align: center;
            margin-bottom: clamp(24px, 5vw, 32px);
        }

        .logo-wrapper {
            width: clamp(60px, 15vw, 80px);
            height: clamp(60px, 15vw, 80px);
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.05) 100%);
            border-radius: clamp(16px, 4vw, 20px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto clamp(16px, 4vw, 24px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .logo-wrapper img {
            max-width: 60%;
            max-height: 60%;
            object-fit: contain;
        }

        .logo-wrapper i {
            font-size: clamp(1.5rem, 5vw, 2rem);
            color: white;
        }

        .login-header h1 {
            color: white;
            font-size: clamp(1.3rem, 5vw, 1.875rem);
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: clamp(0.85rem, 3vw, 0.95rem);
            font-weight: 500;
        }

        /* Alert styles */
        .alert {
            padding: clamp(12px, 3vw, 16px);
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: clamp(0.85rem, 2.5vw, 0.9rem);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
            border: 1px solid;
            word-break: break-word;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fecaca;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #a7f3d0;
        }

        .alert i {
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
            font-weight: 600;
            font-size: clamp(0.8rem, 2.5vw, 0.875rem);
        }

        .input-wrapper {
            position: relative;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 4px 4px 4px clamp(12px, 3vw, 16px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
        }

        .input-group.is-invalid {
            border-color: rgba(239, 68, 68, 0.5);
            background: rgba(239, 68, 68, 0.08);
        }

        .input-group i {
            color: rgba(255, 255, 255, 0.6);
            margin-right: clamp(8px, 2vw, 12px);
            font-size: clamp(0.9rem, 3vw, 1rem);
            transition: color 0.3s ease;
        }

        .input-group:focus-within i {
            color: white;
        }

        .input-group input {
            width: 100%;
            padding: clamp(10px, 2.5vw, 12px) 0;
            background: none;
            border: none;
            color: white;
            font-size: clamp(0.85rem, 2.5vw, 0.95rem);
            outline: none;
            font-weight: 500;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        }

        .password-toggle {
            padding: clamp(6px, 2vw, 8px);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.3s ease;
            margin-right: clamp(4px, 1.5vw, 8px);
        }

        .password-toggle:hover {
            color: white;
        }

        .invalid-feedback {
            color: #fecaca;
            font-size: clamp(0.7rem, 2.5vw, 0.8rem);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: shake 0.3s ease;
            flex-wrap: wrap;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Options row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: clamp(16px, 4vw, 18px);
            height: clamp(16px, 4vw, 18px);
            cursor: pointer;
            accent-color: white;
            border-radius: 4px;
        }

        .remember-me label {
            color: rgba(255, 255, 255, 0.8);
            font-size: clamp(0.8rem, 2.5vw, 0.875rem);
            cursor: pointer;
            font-weight: 500;
        }

        .forgot-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: clamp(0.8rem, 2.5vw, 0.875rem);
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: white;
            transition: width 0.3s ease;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        /* Button styles */
        .btn-login {
            width: 100%;
            padding: clamp(12px, 3vw, 14px) clamp(20px, 5vw, 24px);
            background: white;
            border: none;
            border-radius: 12px;
            color: #667eea;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .btn-login:hover:not(:disabled)::before {
            left: 100%;
        }

        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-login i {
            transition: transform 0.3s ease;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
        }

        .btn-login:hover i {
            transform: translateX(3px);
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: clamp(0.8rem, 2.5vw, 0.875rem);
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .divider span {
            padding: 0 16px;
            white-space: nowrap;
        }

        /* Social login */
        .social-login {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .social-btn {
            flex: 1;
            min-width: clamp(50px, 20%, 80px);
            padding: clamp(10px, 2.5vw, 12px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(1rem, 3vw, 1.1rem);
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
        }

        .login-footer p {
            color: rgba(255, 255, 255, 0.8);
            font-size: clamp(0.85rem, 2.5vw, 0.9rem);
        }

        .login-footer a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .login-footer a:hover::after {
            width: 100%;
        }

        /* Security badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: clamp(0.7rem, 2.5vw, 0.8rem);
            text-align: center;
            flex-wrap: wrap;
        }

        .security-badge i {
            color: #10b981;
        }

        /* Responsive - petits écrans */
        @media (max-width: 480px) {
            .login-container {
                padding: 24px 20px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .divider span {
                padding: 0 12px;
            }
            
            .social-login {
                gap: 8px;
            }
        }

        /* Très petits écrans */
        @media (max-width: 360px) {
            body {
                padding: 12px;
            }
            
            .login-container {
                padding: 20px 16px;
            }
            
            .divider {
                margin: 20px 0;
            }
            
            .divider span {
                padding: 0 10px;
                font-size: 0.75rem;
            }
            
            .social-login {
                gap: 6px;
            }
            
            .social-btn {
                min-width: 45px;
            }
        }

        /* Grands écrans */
        @media (min-width: 1200px) {
            .login-wrapper {
                max-width: 520px;
            }
        }

        /* Input autofill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.08) inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Optimisation tactile pour mobile */
        @media (max-width: 768px) {
            .btn-login,
            .social-btn,
            .password-toggle,
            .remember-me,
            .forgot-link {
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
            }
            
            .input-group input {
                font-size: 16px; /* Empêche le zoom automatique sur iOS */
            }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo-wrapper">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    <i class="fas fa-briefcase" style="display: none;"></i>
                </div>
                <h1>Bon retour ! 👋</h1>
                <p>Connectez-vous pour accéder à votre espace</p>
            </div>

            {{-- Messages de session --}}
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Formulaire de connexion --}}
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <div class="input-group @error('email') is-invalid @enderror">
                        <i class="fas fa-envelope"></i>
                        <input type="email" 
                               id="email"
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="nom@exemple.com" 
                               required 
                               autofocus
                               autocomplete="username">
                    </div>
                    @error('email')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-group @error('password') is-invalid @enderror">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               id="password"
                               name="password" 
                               placeholder="••••••••" 
                               required
                               autocomplete="current-password">
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Options --}}
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" 
                               name="remember" 
                               id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Mot de passe oublié ?
                    </a>
                </div>

                {{-- Bouton de connexion --}}
                <button type="submit" class="btn-login" id="loginButton">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se connecter</span>
                </button>
            </form>

         >

       

            <div class="login-footer">
                <p>Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a></p>
            </div>

            
        </div>
    </div>

    <script>
        // Gestion de la soumission du formulaire
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            // Désactiver le bouton et afficher le chargement
            button.disabled = true;
            button.classList.add('loading');
            icon.className = 'fas fa-circle-notch';
            text.textContent = 'Connexion...';
        });

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Add ripple effect to inputs
        document.querySelectorAll('.input-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
