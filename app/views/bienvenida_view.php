<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido — ClariFi</title>
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="app/views/assets/style.css">
    <style>
        /* --- Background Dinámico --- */
        .welcome-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
            overflow: hidden;
            background: var(--bg-body);
        }

        /* Orbs animados de fondo */
        .welcome-page::before,
        .welcome-page::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.35;
            animation: float 12s ease-in-out infinite;
            pointer-events: none;
        }

        .welcome-page::before {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #6366f1, transparent 70%);
            top: -10%;
            left: -5%;
            animation-delay: 0s;
        }

        .welcome-page::after {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #06b6d4, transparent 70%);
            bottom: -10%;
            right: -5%;
            animation-delay: -6s;
        }

        .orb-3 {
            position: absolute;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, #a78bfa, transparent 70%);
            filter: blur(120px);
            opacity: 0.25;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 8s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -40px) scale(1.05); }
            66% { transform: translate(-20px, 25px) scale(0.95); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.2; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.35; transform: translate(-50%, -50%) scale(1.15); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        /* --- Contenido --- */
        .welcome-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 720px;
            width: 100%;
        }

        /* Logo */
        .welcome-logo {
            animation: fadeInScale 0.8s ease-out;
        }

        .welcome-logo h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #a78bfa, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
            margin-bottom: 4px;
        }

        .welcome-logo .tagline {
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* Saludo */
        .welcome-greeting {
            margin-top: 36px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .welcome-greeting h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 14px;
        }

        .welcome-greeting p {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.7;
            max-width: 560px;
            margin: 0 auto;
        }

        /* Separador */
        .welcome-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #06b6d4);
            border-radius: 99px;
            margin: 36px auto;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        /* CTA */
        .welcome-cta {
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .welcome-cta h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 24px;
        }

        /* Grid de Módulos */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            max-width: 580px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .module-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 22px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .module-btn:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
            color: #fff;
        }

        .module-btn .module-icon {
            font-size: 1.8rem;
            line-height: 1;
        }

        .module-btn .module-label {
            font-size: 0.82rem;
            color: var(--text-secondary);
            transition: color 0.25s ease;
        }

        .module-btn:hover .module-label {
            color: var(--text-primary);
        }

        /* Footer mínimo */
        .welcome-footer {
            margin-top: 44px;
            animation: fadeInUp 0.8s ease-out 0.8s both;
        }

        .welcome-footer a {
            font-size: 0.85rem;
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        .welcome-footer a:hover {
            color: var(--red);
        }

        /* Responsive */
        @media (max-width: 600px) {
            .welcome-logo h1 { font-size: 2.5rem; }
            .modules-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* --- Carousel Premium --- */
        .welcome-carousel {
            position: relative;
            width: 100%;
            max-width: 440px;
            margin: 32px auto;
            border-radius: var(--radius);
            box-shadow: 0 20px 45px rgba(0,0,0,0.5);
            border: 1px solid var(--border);
            overflow: hidden;
            background: var(--bg-card);
        }

        .carousel-track-container {
            width: 100%;
            height: 250px;
            position: relative;
        }

        .carousel-track {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .carousel-slide.active {
            opacity: 1;
            z-index: 2;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.85) contrast(1.1);
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(15,17,23,0.95));
            padding: 24px 16px 14px;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .carousel-nav {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .carousel-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-indicator.active {
            background: var(--accent);
            transform: scale(1.25);
            box-shadow: 0 0 8px var(--accent);
        }
    </style>
</head>
<body>
    <div class="welcome-page">
        <div class="orb-3"></div>

        <div class="welcome-content">
            <!-- Logo -->
            <div class="welcome-logo">
                <img src="app/views/assets/img/logo.png" alt="ClariFi Logo" style="width: 72px; height: 72px; object-fit: contain; margin-bottom: 12px; border-radius: 16px; box-shadow: 0 10px 25px rgba(99,102,241,0.25);">
                <h1>ClariFi</h1>
                <div class="tagline">Claridad Financiera</div>
            </div>

            <!-- Saludo y Explicación -->
            <div class="welcome-greeting">
                <h2>Hola, <?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></h2>
                <p>
                    ClariFi es tu asistente de finanzas personales. Te ayuda a organizar tus ingresos, 
                    priorizar el pago de deudas con el <strong>Método Avalancha</strong>, automatizar gastos fijos, 
                    proyectar inversiones y planificar metas de ahorro. Todo en un solo lugar, para que puedas 
                    tomar el control de tu economía y salir de los ahogos financieros.
                </p>
                <!-- Hero Illustration Carousel -->
                <div class="welcome-carousel">
                    <div class="carousel-track-container">
                        <div class="carousel-track">
                            <div class="carousel-slide active">
                                <img src="app/views/assets/img/hero.jpg" alt="Organización Financiera">
                                <div class="carousel-caption">Organización Inteligente</div>
                            </div>
                            <div class="carousel-slide">
                                <img src="app/views/assets/img/carousel_2.jpg" alt="Ahorro y Metas">
                                <div class="carousel-caption">Planificá tus Metas</div>
                            </div>
                            <div class="carousel-slide">
                                <img src="app/views/assets/img/carousel_3.jpg" alt="Inversiones Inteligentes">
                                <div class="carousel-caption">Multiplicá tus Ahorros</div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-nav">
                        <button class="carousel-indicator active"></button>
                        <button class="carousel-indicator"></button>
                        <button class="carousel-indicator"></button>
                    </div>
                </div>
            </div>

            <div class="welcome-divider"></div>

            <!-- Call to Action -->
            <div class="welcome-cta">
                <h3>¿Cómo te querés organizar hoy?</h3>
            </div>

            <!-- Módulos -->
            <div class="modules-grid">
                <a href="index.php?action=dashboard" class="module-btn">
                    <span class="module-icon" style="color: var(--accent);"><i class="fa-solid fa-chart-pie"></i></span>
                    <span>Dashboard</span>
                    <span class="module-label">Resumen general</span>
                </a>
                <a href="index.php?action=gastos_recurrentes" class="module-btn">
                    <span class="module-icon" style="color: var(--cyan);"><i class="fa-solid fa-calendar-days"></i></span>
                    <span>Gastos Fijos</span>
                    <span class="module-label">Automatización</span>
                </a>
                <a href="index.php?action=deudas" class="module-btn">
                    <span class="module-icon" style="color: var(--red);"><i class="fa-solid fa-credit-card"></i></span>
                    <span>Deudas</span>
                    <span class="module-label">Método Avalancha</span>
                </a>
                <a href="index.php?action=metas" class="module-btn">
                    <span class="module-icon" style="color: var(--green);"><i class="fa-solid fa-bullseye"></i></span>
                    <span>Metas</span>
                    <span class="module-label">Fondo de ahorro</span>
                </a>
                <a href="index.php?action=inversiones" class="module-btn">
                    <span class="module-icon" style="color: var(--yellow);"><i class="fa-solid fa-chart-line"></i></span>
                    <span>Inversiones</span>
                    <span class="module-label">Interés compuesto</span>
                </a>
                <a href="index.php?action=impuestos" class="module-btn">
                    <span class="module-icon" style="color: var(--text-secondary);"><i class="fa-solid fa-receipt"></i></span>
                    <span>Fiscal</span>
                    <span class="module-label">Retenciones</span>
                </a>
            </div>

            <!-- Footer -->
            <div class="welcome-footer">
                <a href="index.php?action=logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar Sesión</a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.carousel-slide');
            const indicators = document.querySelectorAll('.carousel-indicator');
            let currentIndex = 0;
            let slideInterval;

            function showSlide(index) {
                slides[currentIndex].classList.remove('active');
                indicators[currentIndex].classList.remove('active');
                
                currentIndex = index;
                
                slides[currentIndex].classList.add('active');
                indicators[currentIndex].classList.add('active');
            }

            function nextSlide() {
                let nextIndex = (currentIndex + 1) % slides.length;
                showSlide(nextIndex);
            }

            function startAutoPlay() {
                slideInterval = setInterval(nextSlide, 4000);
            }

            function stopAutoPlay() {
                clearInterval(slideInterval);
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    stopAutoPlay();
                    showSlide(index);
                    startAutoPlay();
                });
            });

            startAutoPlay();
        });
    </script>
</body>
</html>
