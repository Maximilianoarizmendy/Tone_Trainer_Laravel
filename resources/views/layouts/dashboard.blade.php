<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Tone Trainer</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">

    <style>
        /* ──────────────────── VARIABLES ──────────────────── */
        :root {
            --primary: #FF4500; --primary-dark: #cc3700; --primary-glow: rgba(255,69,0,0.15);
            --bg: #0f0f0f; --bg2: #141414; --surface: #1a1a1a; --surface2: #222; --surface3: #2a2a2a;
            --border: #2a2a2a; --text: #e0e0e0; --muted: #777; --success: #22c55e;
            --sidebar-w: 240px; --topbar-h: 60px;
            --radius: 12px; --shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); font-family: 'Poppins', sans-serif; color: var(--text); min-height: 100vh; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }

        /* ──────────────────── SIDEBAR ──────────────────── */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w); background: var(--surface);
            border-right: 1px solid var(--border); display: flex; flex-direction: column;
            z-index: 100; transition: transform .3s ease;
        }
        .sidebar-logo {
            height: var(--topbar-h); display: flex; align-items: center;
            padding: 0 20px; border-bottom: 1px solid var(--border);
            font-size: 18px; font-weight: 700; letter-spacing: 1px;
            color: #fff; white-space: nowrap; overflow: hidden;
        }
        .sidebar-logo span { color: var(--primary); }
        .sidebar-logo i { font-size: 22px; color: var(--primary); margin-right: 10px; }
        .sidebar-user {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-avatar {
            width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--primary);
        }
        .sidebar-avatar-placeholder {
            width: 40px; height: 40px; border-radius: 50%; background: var(--primary-glow);
            border: 2px solid var(--primary); display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: var(--primary); flex-shrink: 0;
        }
        .sidebar-user-info .name { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
        .sidebar-user-info .role { font-size: 11px; color: var(--muted); }
        .sidebar-role-badge {
            display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px;
            font-weight: 600; background: var(--primary-glow); color: var(--primary);
            border: 1px solid rgba(255,69,0,0.3);
        }

        /* NAV */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 10px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .nav-section { padding: 16px 20px 6px; font-size: 10px; letter-spacing: 1.5px; color: var(--muted); text-transform: uppercase; font-weight: 600; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 20px; font-size: 13px; color: var(--muted); font-weight: 500;
            border-left: 3px solid transparent; transition: background .15s, color .15s, border-color .15s;
            cursor: pointer;
        }
        .nav-item:hover { background: var(--surface2); color: var(--text); }
        .nav-item.active { background: var(--primary-glow); color: var(--primary); border-left-color: var(--primary); }
        .nav-item i { width: 16px; text-align: center; font-size: 14px; flex-shrink: 0; }
        .nav-badge { margin-left: auto; background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; min-width: 18px; text-align: center; }
        .sidebar-bottom { padding: 14px 20px; border-top: 1px solid var(--border); }
        .sidebar-bottom form { margin: 0; }
        .btn-logout {
            width: 100%; display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; background: rgba(220,53,69,.08); border: 1px solid rgba(220,53,69,.2);
            border-radius: var(--radius); color: #f87171; font-size: 13px; font-weight: 500; cursor: pointer;
            transition: background .2s; font-family: 'Poppins', sans-serif; text-align: left;
        }
        .btn-logout:hover { background: rgba(220,53,69,.18); }

        /* ──────────────────── TOPBAR ──────────────────── */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0; height: var(--topbar-h);
            background: var(--surface); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 99; gap: 16px;
        }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: #fff; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-time { font-size: 13px; color: var(--muted); font-variant-numeric: tabular-nums; }
        .btn-mobile-menu { display: none; background: none; border: none; color: var(--text); font-size: 20px; cursor: pointer; padding: 4px; }

        /* ──────────────────── MAIN CONTENT ──────────────────── */
        .main-content { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); min-height: 100vh; }
        .content-inner { padding: 24px; }

        /* ──────────────────── FLASH MESSAGES ──────────────────── */
        .flash { padding: 12px 18px; border-radius: var(--radius); font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .flash-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.25); color: #86efac; }
        .flash-error   { background: rgba(220,53,69,.12); border: 1px solid rgba(220,53,69,.25); color: #f87171; }

        /* ──────────────────── RESPONSIVE ──────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: none; }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .btn-mobile-menu { display: block; }
            .content-inner { padding: 16px; }
        }

        /* ──────────────────── OVERLAY ──────────────────── */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.visible { display: block; }
    </style>

    {{-- Extra CSS de la página hija --}}
    @yield('styles')
</head>
<body>

{{-- Overlay móvil --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <i class="bi bi-lightning-charge-fill"></i>
        TONE<span>TRAINER</span>
    </div>

    {{-- Usuario --}}
    <div class="sidebar-user">
        @if(auth()->user()->profile_photo)
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                 alt="Foto" class="sidebar-avatar">
        @else
            <div class="sidebar-avatar-placeholder">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
        <div class="sidebar-user-info">
            <div class="name">{{ auth()->user()->name }}</div>
            <span class="sidebar-role-badge">{{ auth()->user()->roleName }}</span>
        </div>
    </div>

    {{-- Navegación --}}
    <nav class="sidebar-nav">

        {{-- USUARIO (rol 1) --}}
        @if(auth()->user()->isUser())
            <div class="nav-section">Principal</div>
            <a href="{{ route('dashboard.index') }}"
               class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('dashboard.training') }}"
               class="nav-item {{ request()->routeIs('dashboard.training') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> Entrenamiento
            </a>
            <a href="{{ route('dashboard.nutrition') }}"
               class="nav-item {{ request()->routeIs('dashboard.nutrition') ? 'active' : '' }}">
                <i class="bi bi-egg-fried"></i> Nutrición
            </a>
            <a href="{{ route('dashboard.progress') }}"
               class="nav-item {{ request()->routeIs('dashboard.progress') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Progreso
            </a>
            <a href="{{ route('dashboard.goals') }}"
               class="nav-item {{ request()->routeIs('dashboard.goals') ? 'active' : '' }}">
                <i class="bi bi-trophy-fill"></i> Metas
            </a>
        @endif

        {{-- ADMIN (rol 2) --}}
        @if(auth()->user()->isAdmin())
            <div class="nav-section">Administración</div>
            <a href="{{ route('dashboard.users') }}"
               class="nav-item {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Usuarios
            </a>
        @endif

        {{-- NUTRICIONISTA (rol 3) --}}
        @if(auth()->user()->isNutritionist())
            <div class="nav-section">Nutricionista</div>
            <a href="{{ route('dashboard.users') }}"
               class="nav-item {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Mis Pacientes
            </a>
        @endif

        {{-- ENTRENADOR (rol 4) --}}
        @if(auth()->user()->isTrainer())
            <div class="nav-section">Entrenador</div>
            <a href="{{ route('dashboard.users') }}"
               class="nav-item {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Mis Alumnos
            </a>
        @endif

        {{-- COMPARTIDO --}}
        <div class="nav-section">Comunicación</div>
        <a href="{{ route('dashboard.messages') }}"
           class="nav-item {{ request()->routeIs('dashboard.messages') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill"></i> Mensajes
            <span class="nav-badge" id="msgBadge" style="display:none;">0</span>
        </a>

        <div class="nav-section">Cuenta</div>
        <a href="{{ route('dashboard.profile') }}"
           class="nav-item {{ request()->routeIs('dashboard.profile') ? 'active' : '' }}">
            <i class="bi bi-person-fill"></i> Mi Perfil
        </a>
        <a href="{{ route('dashboard.settings') }}"
           class="nav-item {{ request()->routeIs('dashboard.settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Configuración
        </a>
    </nav>

    {{-- Logout --}}
    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
<header class="topbar">
    <div class="topbar-left">
        <button class="btn-mobile-menu" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-time" id="clock"></span>
    </div>
</header>

{{-- ═══════════════════ CONTENIDO ═══════════════════ --}}
<main class="main-content">
    <div class="content-inner">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<script>
// ─── Reloj en tiempo real ───────────────────────────────────
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    const days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    document.getElementById('clock').textContent =
        `${days[now.getDay()]} ${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

// ─── Badge mensajes no leídos ───────────────────────────────
async function refreshUnreadCount() {
    try {
        const res  = await fetch('/api/messages/unread-count', { credentials: 'same-origin' });
        const data = await res.json();
        const badge = document.getElementById('msgBadge');
        if (data.success && data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    } catch(e) {}
}
refreshUnreadCount();
setInterval(refreshUnreadCount, 30000);

// ─── Sidebar móvil ──────────────────────────────────────────
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('visible');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('visible');
}

// ─── CSRF header para todas las peticiones fetch ─────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const originalFetch = window.fetch;
window.fetch = function(url, opts = {}) {
    opts.headers = opts.headers || {};
    opts.headers['X-CSRF-TOKEN'] = csrfToken;
    return originalFetch(url, opts);
};
</script>

@yield('scripts')
</body>
</html>
