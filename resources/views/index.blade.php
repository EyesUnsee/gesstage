<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GesStage - Accueil | Plateforme de gestion de stages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            backdrop-filter: blur(10px);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .btn-login {
            background: white;
            color: #667eea !important;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-login:hover {
            opacity: 1 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Menu burger mobile */
        .burger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .burger-menu span {
            width: 25px;
            height: 3px;
            background: white;
            margin: 3px 0;
            transition: 0.3s;
        }

        /* Hero section */
        .hero {
            padding: 120px 5% 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
        }

        /* Style pour le logo dans hero */
        .hero-logo {
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease;
        }

        .hero-logo img {
            max-width: 180px;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .hero-logo img:hover {
            transform: scale(1.05);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary, .btn-secondary {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-primary:hover, .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* Features */
        .features {
            padding: 80px 5%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px 50px 0 0;
        }

        .features h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            padding: 2rem;
            border-radius: 20px;
            color: white;
            text-align: center;
            transition: transform 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.25);
        }

        .feature-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .burger-menu {
                display: flex;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 2rem;
                gap: 1.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .nav-links.active {
                display: flex;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .features h2 {
                font-size: 2rem;
            }

            .hero-logo img {
                max-width: 140px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
            }

            .hero-logo img {
                max-width: 120px;
            }
        }

        /* Ajout d'un style pour le cas où l'image ne charge pas */
        .hero-logo img:error {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <span>GesStage</span>
            </div>
            <div class="burger-menu" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-links" id="navLinks">
                <a href="#">Accueil</a>
                <a href="#features">Fonctionnalités</a>
                <a href="#contact">Contact</a>
                <a href="login" class="btn-login"><i class="fas fa-user"></i> Connexion</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="hero-content">
                <!-- Logo corrigé et bien positionné -->
                <div class="hero-logo">
                    <img src="assets/images/logo.png" alt="GesStage Logo" onerror="this.style.display='none'">
                </div>
                <h1>Gérez les stages en toute simplicité</h1>
                <p>Une plateforme complète pour les candidats, tuteurs, responsables et chefs de service</p>
                <div class="cta-buttons">
                    <a href="register.html" class="btn-primary"><i class="fas fa-user-plus"></i> Commencer maintenant</a>
                    <a href="#features" class="btn-secondary"><i class="fas fa-play"></i> Découvrir</a>
                </div>
            </div>
        </section>

        <section class="features" id="features">
            <h2>Une solution pour tous</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Candidats</h3>
                    <p>Gérez vos candidatures, documents et suivez votre progression</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Tuteurs</h3>
                    <p>Suivez vos stagiaires et évaluez leurs performances</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-user-tie"></i>
                    <h3>Responsables</h3>
                    <p>Supervisez les candidatures et gérez les équipes</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Chefs de service</h3>
                    <p>Analysez les indicateurs et validez les bilans</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 GesStage - Tous droits réservés</p>
    </footer>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }

        // Gestionnaire d'erreur pour l'image
        document.addEventListener('DOMContentLoaded', function() {
            const logo = document.querySelector('.hero-logo img');
            if (logo) {
                logo.onerror = function() {
                    this.style.display = 'none';
                    console.log('Logo non trouvé, vérifiez le chemin assets/images/logo.png');
                };
            }
        });
    </script>
</body>
</html>
