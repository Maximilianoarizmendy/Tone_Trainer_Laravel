<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Tone Trainer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #FF4500; --bg: #0f0f0f; --surface: #1a1a1a; --surface2: #242424; --border: #2a2a2a; --text: #e0e0e0; --muted: #888; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; color: var(--text); }
        .card { background: var(--surface); border-radius: 20px; padding: 50px 45px; width: 100%; max-width: 420px; box-shadow: 0 25px 60px rgba(0,0,0,0.5); }
        .icon { font-size: 48px; text-align: center; margin-bottom: 18px; }
        h2 { font-size: 22px; font-weight: 700; color: #fff; text-align: center; margin-bottom: 8px; }
        p.sub { color: var(--muted); font-size: 13px; text-align: center; margin-bottom: 28px; }
        label { display: block; color: #ccc; font-size: 13px; font-weight: 500; margin-bottom: 7px; }
        input { width: 100%; padding: 12px 16px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif; transition: border-color .2s; }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,69,0,0.12); }
        input::placeholder { color: #555; }
        .form-group { margin-bottom: 18px; }
        .btn { width: 100%; padding: 13px; background: linear-gradient(90deg, var(--primary), #ff6347); color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: transform .2s, box-shadow .2s; letter-spacing: .5px; margin-top: 4px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,69,0,0.35); }
        .back-link { text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted); }
        .back-link a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .alert-error { background: rgba(220,53,69,.12); border: 1px solid rgba(220,53,69,.3); border-radius: 8px; padding: 12px 16px; color: #f87171; font-size: 13px; margin-bottom: 18px; }
        .alert-success { background: rgba(40,167,69,.12); border: 1px solid rgba(40,167,69,.3); border-radius: 8px; padding: 12px 16px; color: #6ee7a0; font-size: 13px; margin-bottom: 18px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🔐</div>
    <h2>Recuperar Contraseña</h2>
    <p class="sub">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>

    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert-error">@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="tu@correo.com"
                   value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn">Enviar Enlace de Recuperación</button>
    </form>

    <div class="back-link">
        <a href="{{ route('login') }}">← Volver al inicio de sesión</a>
    </div>
</div>
</body>
</html>
