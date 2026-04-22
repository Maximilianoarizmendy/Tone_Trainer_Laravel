<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciando sesión... - Tone Trainer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; height: 100vh;
            display: flex; justify-content: center; align-items: center;
            background: #121212; color: #fff;
            font-family: 'Poppins', sans-serif; flex-direction: column;
        }
        .loader {
            border: 6px solid #1E1E1E; border-top: 6px solid #FF3C00;
            border-radius: 50%; width: 60px; height: 60px;
            animation: spin 1s linear infinite; margin-bottom: 20px;
            box-shadow: 0 0 25px #FF3C00;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { font-weight: 600; color: #FF3C00; text-shadow: 0 0 15px #FF3C00; }
        p  { color: #aaa; font-size: 0.95em; }
        .welcome { color: #fff; margin-top: 10px; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="loader"></div>
    <h2>Iniciando sesión...</h2>
    <p>🏋️ Preparando tu dashboard</p>
    <p class="welcome">¡Bienvenido, {{ $userName }}!</p>
    <script>
        setTimeout(() => { window.location.href = '{{ $redirectUrl }}'; }, 2000);
    </script>
</body>
</html>
