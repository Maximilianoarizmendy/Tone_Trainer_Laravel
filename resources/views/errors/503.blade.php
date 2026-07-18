<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tone Trainer - Mantenimiento Programado</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(255, 120, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 200, 83, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 165, 0, 0.03) 0%, transparent 70%);
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        .maintenance-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-image {
            width: 280px;
            height: 280px;
            object-fit: contain;
            margin: 0 auto 2rem;
            display: block;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(255, 120, 0, 0.3));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .maintenance-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 120, 0, 0.15);
            border: 1px solid rgba(255, 120, 0, 0.3);
            border-radius: 50px;
            padding: 8px 20px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ff7800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .maintenance-badge .dot {
            width: 8px;
            height: 8px;
            background: #ff7800;
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ff7800, #ff9a40, #00c853);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #888;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .subtitle strong {
            color: #00c853;
            font-weight: 600;
        }

        .progress-bar-container {
            width: 100%;
            max-width: 350px;
            margin: 0 auto 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            height: 6px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 60%;
            border-radius: 50px;
            background: linear-gradient(90deg, #ff7800, #00c853);
            animation: progressPulse 3s ease-in-out infinite;
        }

        @keyframes progressPulse {
            0% { width: 20%; opacity: 0.7; }
            50% { width: 80%; opacity: 1; }
            100% { width: 20%; opacity: 0.7; }
        }

        .info-cards {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            min-width: 150px;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: rgba(255, 120, 0, 0.3);
            background: rgba(255, 120, 0, 0.05);
            transform: translateY(-3px);
        }

        .info-card .icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .info-card .label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.3rem;
        }

        .info-card .value {
            font-size: 1rem;
            font-weight: 600;
            color: #ccc;
        }

        .footer-text {
            font-size: 0.8rem;
            color: #444;
            margin-top: 1rem;
        }

        .footer-text a {
            color: #ff7800;
            text-decoration: none;
        }

        /* Floating particles */
        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .particle:nth-child(1) {
            top: 15%;
            left: 10%;
            background: #ff7800;
            animation: drift 12s ease-in-out infinite;
            opacity: 0.4;
        }

        .particle:nth-child(2) {
            top: 70%;
            right: 15%;
            background: #00c853;
            animation: drift 15s ease-in-out infinite reverse;
            opacity: 0.3;
        }

        .particle:nth-child(3) {
            bottom: 20%;
            left: 25%;
            background: #ff9a40;
            animation: drift 10s ease-in-out infinite;
            opacity: 0.5;
        }

        .particle:nth-child(4) {
            top: 30%;
            right: 30%;
            background: #00c853;
            animation: drift 18s ease-in-out infinite reverse;
            opacity: 0.2;
        }

        @keyframes drift {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(30px, -40px) scale(1.5);
            }
            50% {
                transform: translate(-20px, 20px) scale(0.8);
            }
            75% {
                transform: translate(40px, 30px) scale(1.2);
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8rem;
            }

            .maintenance-image {
                width: 200px;
                height: 200px;
            }

            .info-cards {
                flex-direction: column;
                align-items: center;
            }

            .subtitle {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <!-- Floating particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="maintenance-container">
        <img src="/img/maintenance.png" alt="Mantenimiento Programado" class="maintenance-image">

        <div class="maintenance-badge">
            <span class="dot"></span>
            Mantenimiento en curso
        </div>

        <h1>Estamos mejorando tu experiencia</h1>

        <p class="subtitle">
            Tone Trainer está en <strong>mantenimiento programado</strong>.<br>
            Estamos trabajando para traerte nuevas funcionalidades y mejoras.
        </p>

        <div class="progress-bar-container">
            <div class="progress-bar"></div>
        </div>

        <div class="info-cards">
            <div class="info-card">
                <div class="icon">🔧</div>
                <div class="label">Estado</div>
                <div class="value">En progreso</div>
            </div>
            <div class="info-card">
                <div class="icon">⚡</div>
                <div class="label">Tipo</div>
                <div class="value">Actualización</div>
            </div>
            <div class="info-card">
                <div class="icon">💪</div>
                <div class="label">Volveremos</div>
                <div class="value">Pronto</div>
            </div>
        </div>

        <p class="footer-text">
            Tone Trainer &copy; {{ date('Y') }} — Volveremos más fuertes 🔥
        </p>
    </div>
</body>
</html>
