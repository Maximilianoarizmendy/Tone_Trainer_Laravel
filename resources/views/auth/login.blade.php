<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Autenticación | Tone Trainer</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap");

:root {
  --primary: #FF3C00;
  --accent: #008300;
  --dark: #121212;
  --light: #ffffff;
  --box-bg: #1E1E1E;
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

body {
  display: flex; justify-content: center; align-items: center;
  min-height: 100vh; background: var(--dark); color: var(--light); overflow: hidden;
}

/* ========== CAJA PRINCIPAL ========== */
.box {
  position: relative;
  width: 350px; height: 200px;
  border-radius: 20px;
  animation: rotate 4s linear infinite;
  background: repeating-conic-gradient(from var(--a), var(--primary) 0%, var(--primary) 5%, transparent 5%, transparent 40%, var(--primary) 50%);
  filter: drop-shadow(0 0 45px var(--primary));
  transition: 0.6s ease;
  display: flex; justify-content: center; align-items: center;
}

@property --a { syntax: "<angle>"; inherits: false; initial-value: 0deg; }
@keyframes rotate { 0% { --a: 0deg; } 100% { --a: 360deg; } }

.box::before {
  content: ""; position: absolute; width: 100%; height: 100%; border-radius: 20px;
  background: repeating-conic-gradient(from var(--a), var(--accent) 0%, var(--accent) 5%, transparent 5%, transparent 40%, var(--accent) 50%);
  animation: rotate 4s linear infinite; animation-delay: -1s;
  filter: drop-shadow(0 0 40px var(--accent));
}

.box::after {
  content: ""; position: absolute; inset: 5px; background: var(--box-bg); border-radius: 15px; border: 8px solid var(--dark);
}

.subbox {
  position: absolute; top: 50px; width: 70%;
  background: linear-gradient(180deg, #181818 0%, #111 100%);
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(255, 60, 0, 0.5), 0 0 25px rgba(255, 60, 0, 0.3), inset 0 2px 6px rgba(255,255,255,0.1);
  padding: 20px; text-align: center; transition: 0.5s; z-index: 2;
}
.subbox h2 { color: #fff; font-size: 1.4rem; letter-spacing: 0.15em; text-shadow: 0 0 15px var(--primary); }
.subbox h2 i { color: var(--primary); margin-right: 8px; }

/* ========== FORMS ========== */
.loginBx {
  position: absolute; bottom: 40px; width: 70%;
  display: flex; flex-direction: column; align-items: center;
  opacity: 0; transform: translateY(100px); transition: 0.6s ease; z-index: 1;
}

.box:hover .loginBx, .box.has-errors .loginBx { opacity: 1; transform: translateY(0); }
.box.state-login:hover, .box.state-login.has-errors { width: 450px; height: 460px; }
.box.state-register:hover, .box.state-register.has-errors { width: 450px; height: 560px; }

.loginBx input {
  width: 100%; padding: 12px 20px; margin-bottom: 12px; border-radius: 30px;
  border: 2px solid var(--accent); background: rgba(255,255,255,0.05);
  color: var(--light); outline: none; transition: 0.3s;
}
.loginBx input::placeholder { color: #999; }
.loginBx input:focus { border-color: var(--primary); box-shadow: 0 0 10px var(--primary); }

.loginBx input[type="submit"] {
  background: var(--primary); border: none; color: white; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 0 15px var(--primary); margin-top: 5px; margin-bottom: 5px;
}
.loginBx input[type="submit"]:hover { background: var(--accent); color: #000; box-shadow: 0 0 20px var(--accent); }

.group { display: flex; justify-content: space-between; width: 100%; font-size: 0.9em; margin-top: 8px; }
.group.center { justify-content: center; }
.group a { color: var(--accent); text-decoration: none; transition: 0.3s; cursor: pointer; }
.group a:hover { color: var(--primary); }

/* ========== ALERTS ========== */
.alert-messages { position: absolute; top: 125px; z-index: 2; width: 80%; text-align: center; }
.state-register .alert-messages { top: 110px; }
.error, .success {
  padding: 8px 12px; border-radius: 8px; font-size: 0.85em; margin-bottom: 5px; animation: fadeIn 0.5s ease;
}
.error { background: rgba(255, 60, 0, 0.1); color: var(--primary); box-shadow: 0 0 10px rgba(255, 60, 0, 0.3); }
.success { background: rgba(0, 212, 255, 0.1); color: var(--accent); box-shadow: 0 0 10px rgba(0, 212, 255, 0.3); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

/* Mobile y pantallas pequeñas (Responsive) */
@media (hover: none), (max-width: 500px) {
  body { overflow-y: auto; padding: 30px 0; }
  .box { width: 92vw !important; height: auto !important; padding: 40px 0 30px; flex-direction: column; justify-content: flex-start; gap: 20px; }
  .box.state-login, .box.state-login:hover { min-height: 470px; }
  .box.state-register, .box.state-register:hover { min-height: 580px; }
  .subbox { position: relative; top: 0 !important; width: 85%; margin-bottom: 0; }
  .alert-messages { position: relative; top: 0 !important; width: 85%; }
  .loginBx { position: relative; bottom: 0 !important; opacity: 1 !important; transform: none !important; width: 85%; margin-top: auto; }
  .loginBx input { font-size: 14px; padding: 12px 16px; margin-bottom: 12px; }
}
</style>
</head>
<body>
  <div class="box state-login {{ $errors->any() || session('success') || session('register_success') ? 'has-errors' : '' }}" id="mainBox">
    
    <!-- SUBBOX LOGIN -->
    <div class="subbox" id="subboxLogin">
      <h2><i class="fa-solid fa-dumbbell"></i> TONE <br> TRAINER</h2>
    </div>

    <!-- SUBBOX REGISTER -->
    <div class="subbox" id="subboxRegister" style="display:none; top: 30px;">
      <h2><i class="fa-solid fa-user-plus"></i> CREAR CUENTA</h2>
    </div>

    <!-- ERRORES GLOBALES -->
    <div class="alert-messages">
      @if (session('success') || session('register_success'))
        <div class="success">{{ session('success') ?? session('register_success') }}</div>
      @endif
      @if ($errors->any())
        <div class="error">
          @foreach ($errors->all() as $error) {{ $error }}<br> @endforeach
        </div>
      @endif
    </div>

    <!-- LOGIN FORM -->
    <form method="POST" class="loginBx" action="{{ route('login.post') }}" id="formLogin">
      @csrf
      <input type="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">
      <input type="password" name="password" placeholder="Contraseña" required>
      <input type="submit" value="Iniciar sesión">
      <div class="group">
        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
        <a onclick="switchTab('register')">Crear cuenta</a>
      </div>
    </form>

    <!-- REGISTER FORM -->
    <form method="POST" class="loginBx" action="{{ route('register.post') }}" id="formRegister" style="display:none; bottom: 20px;">
      @csrf
      <input type="text" name="name" placeholder="Nombre completo" required value="{{ old('name') }}">
      <input type="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">
      <input type="password" name="password" placeholder="Contraseña (mín. 8 caracteres)" required>
      <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>
      <input type="date" name="birthdate" value="{{ old('birthdate') }}" title="Fecha de nacimiento">
      <input type="tel" name="phone" placeholder="Teléfono" value="{{ old('phone') }}">
      <input type="text" name="location" placeholder="Ciudad" value="{{ old('location') }}">
      <select name="level" style="width:100%;padding:12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:#fff;font-size:13px;">
        <option value="">Nivel...</option>
        <option value="Principiante">Principiante</option>
        <option value="Intermedio">Intermedio</option>
        <option value="Avanzado">Avanzado</option>
      </select>
      <select name="goal" style="width:100%;padding:12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:#fff;font-size:13px;">
        <option value="">Objetivo...</option>
        <option value="Pérdida de peso">Pérdida de peso</option>
        <option value="Ganar músculo">Ganar músculo</option>
        <option value="Tonificar">Tonificar</option>
        <option value="Mejorar resistencia">Mejorar resistencia</option>
      </select>
      <input type="number" name="weight" placeholder="Peso (kg)" step="0.1" min="0" value="{{ old('weight') }}">
      <input type="number" name="height" placeholder="Altura (cm)" step="0.1" min="0" value="{{ old('height') }}">
      <input type="submit" value="CREAR CUENTA">
      <div class="group center">
        <a onclick="switchTab('login')"><i class="fa-solid fa-arrow-left"></i> Volver al login</a>
      </div>
    </form>

  </div>

<script>
  function switchTab(tab) {
      const box = document.getElementById('mainBox');
      const formLogin = document.getElementById('formLogin');
      const formRegister = document.getElementById('formRegister');
      const subboxLogin = document.getElementById('subboxLogin');
      const subboxRegister = document.getElementById('subboxRegister');
      const alertMsgs = document.querySelector('.alert-messages');
      
      // Clear errors when switching tabs so box shrinks if needed
      if (alertMsgs) {
          alertMsgs.innerHTML = '';
          box.classList.remove('has-errors');
      }
      
      if (tab === 'register') {
          box.classList.remove('state-login');
          box.classList.add('state-register');
          formLogin.style.display = 'none';
          subboxLogin.style.display = 'none';
          
          formRegister.style.display = 'flex';
          subboxRegister.style.display = 'block';
      } else {
          box.classList.remove('state-register');
          box.classList.add('state-login');
          formRegister.style.display = 'none';
          subboxRegister.style.display = 'none';
          
          formLogin.style.display = 'flex';
          subboxLogin.style.display = 'block';
      }
  }

  // Si hay errores de registro o se pasó el parámetro tab=register, abrir en registro
  @if(session('_tab') === 'register' || old('name') || $errors->hasBag('register'))
      switchTab('register');
  @endif
</script>
</body>
</html>
