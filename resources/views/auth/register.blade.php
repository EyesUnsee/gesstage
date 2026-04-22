<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - {{ config('app.name', 'GesStage') }}</title>
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

        /* Animated background shapes */
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

        .register-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
            margin: auto;
        }

        .register-container {
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

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .register-header {
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

        .register-header h1 {
            color: white;
            font-size: clamp(1.3rem, 5vw, 1.875rem);
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .register-header p {
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

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

        .input-group input,
        .input-group select {
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

        .input-group select {
            cursor: pointer;
        }

        .input-group select option {
            background: #4f46e5;
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

        /* Password strength */
        .password-strength {
            margin-top: 10px;
            height: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }

        .password-strength-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: clamp(0.7rem, 2.5vw, 0.8rem);
            margin-top: 5px;
            text-align: right;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-medium { background: #f59e0b; width: 50%; }
        .strength-strong { background: #10b981; width: 75%; }
        .strength-very-strong { background: #10b981; width: 100%; }

        /* Terms checkbox */
        .terms-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .terms-checkbox input[type="checkbox"] {
            width: clamp(16px, 4vw, 18px);
            height: clamp(16px, 4vw, 18px);
            cursor: pointer;
            accent-color: white;
            border-radius: 4px;
        }

        .terms-checkbox label {
            cursor: pointer;
            font-size: clamp(0.8rem, 2.5vw, 0.9rem);
            font-weight: 500;
        }

        .terms-checkbox a {
            color: white;
            font-weight: 700;
            text-decoration: none;
            position: relative;
        }

        .terms-checkbox a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .terms-checkbox a:hover::after {
            width: 100%;
        }

        /* Button styles */
        .btn-register {
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

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-register:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .btn-register:hover:not(:disabled)::before {
            left: 100%;
        }

        .btn-register:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-register i {
            transition: transform 0.3s ease;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
        }

        .btn-register:hover i {
            transform: translateX(3px);
        }

        .btn-register.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .register-footer {
            text-align: center;
            margin-top: 24px;
        }

        .register-footer p {
            color: rgba(255, 255, 255, 0.8);
            font-size: clamp(0.85rem, 2.5vw, 0.9rem);
        }

        .register-footer a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .register-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        .register-footer a:hover::after {
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border-radius: clamp(16px, 4vw, 20px);
            padding: clamp(24px, 5vw, 30px);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-content h2 {
            color: white;
            margin-bottom: 20px;
            font-size: clamp(1.3rem, 4vw, 1.5rem);
        }

        .modal-content p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .modal-content button {
            margin-top: 20px;
            padding: 10px 20px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .modal-content button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 600px) {
            .register-container {
                padding: 24px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        @media (max-width: 480px) {
            .form-row {
                gap: 0;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 12px;
            }
            
            .register-container {
                padding: 20px 16px;
            }
        }

        /* Input autofill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        select:-webkit-autofill {
            -webkit-text-fill-color: white;
            -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.08) inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Optimisation tactile pour mobile */
        @media (max-width: 768px) {
            .btn-register,
            .terms-checkbox,
            .register-footer a {
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
            }
            
            .input-group input,
            .input-group select {
                font-size: 16px;
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

    <div class="register-wrapper">
        <div class="register-container">
            <div class="register-header">
                <div class="logo-wrapper">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="GesStage Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                    <i class="fas fa-briefcase" style="display: none;"></i>
                </div>
                <h1>Créer un compte 🚀</h1>
                <p>Inscrivez-vous pour commencer l'aventure</p>
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

            {{-- Formulaire d'inscription --}}
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-row">
                    {{-- Prénom --}}
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <div class="input-group @error('first_name') is-invalid @enderror">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="first_name"
                                   name="first_name" 
                                   value="{{ old('first_name') }}"
                                   placeholder="Votre prénom" 
                                   required 
                                   autofocus>
                        </div>
                        @error('first_name')
                            <span class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Nom --}}
                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <div class="input-group @error('last_name') is-invalid @enderror">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="last_name"
                                   name="last_name" 
                                   value="{{ old('last_name') }}"
                                   placeholder="Votre nom" 
                                   required>
                        </div>
                        @error('last_name')
                            <span class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

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
                               required>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Téléphone (optionnel) --}}
                <div class="form-group">
                    <label for="phone">Téléphone <span style="color: rgba(255,255,255,0.5);">(optionnel)</span></label>
                    <div class="input-group @error('phone') is-invalid @enderror">
                        <i class="fas fa-phone"></i>
                        <input type="tel" 
                               id="phone"
                               name="phone" 
                               value="{{ old('phone') }}"
                               placeholder="+261 00 00 000 00">
                    </div>
                    @error('phone')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Rôle --}}
                <div class="form-group">
                    <label for="role">Rôle</label>
                    <div class="input-group @error('role') is-invalid @enderror">
                        <i class="fas fa-user-tag"></i>
                        <select id="role" name="role" required>
                            <option value="">Sélectionnez votre rôle</option>
                            <option value="candidat" {{ old('role') == 'candidat' ? 'selected' : '' }}>Candidat</option>
                            <option value="tuteur" {{ old('role') == 'tuteur' ? 'selected' : '' }}>Encadreur</option>
                            <option value="responsable" {{ old('role') == 'responsable' ? 'selected' : '' }}>Responsable</option>
                            <option value="chef-service" {{ old('role') == 'chef-service' ? 'selected' : '' }}>Chef de service</option>
                        </select>
                    </div>
                    @error('role')
                        <span class="invalid-feedback">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-row">
                    {{-- Mot de passe --}}
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-group @error('password') is-invalid @enderror">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password"
                                   name="password" 
                                   placeholder="••••••••" 
                                   required>
                        </div>
                        @error('password')
                            <span class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Confirmation --}}
                    <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password_confirmation"
                                   name="password_confirmation" 
                                   placeholder="••••••••" 
                                   required>
                        </div>
                    </div>
                </div>

                {{-- Indicateur de force du mot de passe --}}
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="password-strength-text" id="passwordStrengthText"></div>

                {{-- Conditions d'utilisation --}}
                <div class="terms-checkbox">
                    <input type="checkbox" 
                           id="terms" 
                           name="terms" 
                           value="1" 
                           {{ old('terms') ? 'checked' : '' }}
                           required>
                    <label for="terms">
                        J'accepte les <a href="#" onclick="showTerms(event)">conditions d'utilisation</a>
                    </label>
                </div>
                @error('terms')
                    <span class="invalid-feedback">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </span>
                @enderror

                {{-- Bouton d'inscription --}}
                <button type="submit" class="btn-register" id="registerButton">
                    <i class="fas fa-user-plus"></i>
                    <span>S'inscrire</span>
                </button>
            </form>

            <div class="register-footer">
                <p>Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous</a></p>
            </div>

        </div>
    </div>

    {{-- Modal des conditions d'utilisation --}}
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <h2>📋 Conditions d'utilisation</h2>
            <p>1. Vous êtes responsable de la confidentialité de votre compte et de vos identifiants.</p>
            <p>2. Vous vous engagez à fournir des informations exactes et à jour.</p>
            <p>3. L'utilisation de la plateforme doit respecter les lois et règlements en vigueur.</p>
            <p>4. Les données personnelles sont traitées conformément au RGPD.</p>
            <p>5. Nous nous réservons le droit de modifier ces conditions à tout moment.</p>
            <p>6. Toute utilisation frauduleuse entraînera la suspension du compte.</p>
            <button onclick="closeTerms()">Fermer</button>
        </div>
    </div>

    <script>
        // Force du mot de passe
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            
            if (password.length === 0) {
                strengthBar.style.width = '0';
                strengthText.textContent = '';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = '🔒 Faible';
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = '⚠️ Moyen';
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = '✅ Fort';
            } else {
                strengthBar.classList.add('strength-very-strong');
                strengthText.textContent = '💪 Très fort';
            }
        });

        // Confirmation du mot de passe en temps réel
        const confirmInput = document.getElementById('password_confirmation');
        
        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    this.parentElement.style.borderColor = 'rgba(16, 185, 129, 0.5)';
                } else {
                    this.parentElement.style.borderColor = 'rgba(239, 68, 68, 0.5)';
                }
            } else {
                this.parentElement.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            }
        });

        // Gestion de la soumission du formulaire
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (password !== confirm) {
                e.preventDefault();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Les mots de passe ne correspondent pas !</span>';
                const container = document.querySelector('.register-container');
                const form = document.getElementById('registerForm');
                container.insertBefore(errorDiv, form);
                setTimeout(() => errorDiv.remove(), 3000);
                return;
            }
            
            const button = document.getElementById('registerButton');
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            button.disabled = true;
            button.classList.add('loading');
            icon.className = 'fas fa-circle-notch';
            text.textContent = 'Inscription en cours...';
        });

        // Afficher les conditions
        function showTerms(event) {
            event.preventDefault();
            document.getElementById('termsModal').style.display = 'flex';
        }

        function closeTerms() {
            document.getElementById('termsModal').style.display = 'none';
        }

        // Fermer le modal en cliquant dehors
        window.onclick = function(event) {
            const modal = document.getElementById('termsModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Animation des inputs
        document.querySelectorAll('.input-group input, .input-group select').forEach(input => {
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
