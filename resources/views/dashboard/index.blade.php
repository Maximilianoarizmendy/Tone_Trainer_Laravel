@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', '🏠 Dashboard')

@section('styles')
<style>
/* ── Saludo ───────────────────────────────────────── */
.greeting { margin-bottom: 28px; }
.greeting h1 { font-size: 24px; font-weight: 700; color: #fff; }
.greeting p  { color: var(--muted); font-size: 13px; margin-top: 4px; }

/* ── Stats Cards ──────────────────────────────────── */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
.stat-card {
    background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
    border-radius: 14px; padding: 22px; text-align: center;
    border: 1px solid #333; transition: transform .3s, box-shadow .3s;
    position: relative; overflow: hidden;
}
.stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff4500, #ff6347, #ff8c00);
    border-radius: 14px 14px 0 0;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(255,69,0,.15); }
.stat-icon  { font-size: 32px; margin-bottom: 8px; display: block; }
.stat-value { font-size: 28px; font-weight: 700; color: #ff6347; display: block; margin-bottom: 4px; }
.stat-label { font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; }

/* ── Layout dos columnas ──────────────────────────── */
.dashboard-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
@media (max-width: 900px) { .dashboard-row { grid-template-columns: 1fr; } }

/* ── Cards genéricas ──────────────────────────────── */
.card {
    background: linear-gradient(135deg, #1e1e1e 0%, #252525 100%);
    border: 1px solid #333; border-radius: 14px; padding: 22px;
}
.card-title { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.card-title i { color: var(--primary); }

/* ── Calendario ───────────────────────────────────── */
.calendar-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.calendar-nav h3 { margin: 0; font-size: 16px; color: #fff; font-weight: 600; }
.cal-nav-btn {
    background: #333; border: 1px solid #444; color: #fff;
    width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
    font-size: 14px; display: flex; align-items: center; justify-content: center;
    transition: background .2s, border-color .2s;
}
.cal-nav-btn:hover { background: var(--primary); border-color: var(--primary); }
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
.cal-day-header { text-align: center; font-size: 11px; color: #888; font-weight: 600; padding: 6px 0; text-transform: uppercase; }
.cal-day {
    aspect-ratio: 1; display: flex; flex-direction: column; align-items: center;
    justify-content: flex-start; padding-top: 8px; border-radius: 8px;
    font-size: 13px; color: #ccc; cursor: pointer; background: #222;
    transition: all .2s; position: relative; border: 1px solid transparent;
}
.cal-day:hover:not(.cal-day--empty) { background: #2a2a2a; border-color: var(--primary); }
.cal-day--empty { background: transparent; cursor: default; border: none; }
.cal-day--today { background: rgba(255,69,0,.08); color: #fff; font-weight: 700; border-color: var(--primary) !important; }
.cal-event-indicators { display: flex; gap: 3px; margin-top: auto; margin-bottom: 14%; flex-wrap: wrap; justify-content: center; padding: 0 3px; }
.cal-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--primary); }
.cal-dot.done { background: #22c55e; box-shadow: 0 0 5px rgba(34,197,94,.5); }

/* ── Accesos rápidos ──────────────────────────────── */
.quick-links { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.quick-link {
    display: flex; align-items: center; gap: 10px; padding: 14px;
    background: #222; border: 1px solid #333; border-radius: 10px;
    color: var(--text); font-size: 13px; font-weight: 500;
    transition: border-color .2s, color .2s, background .2s;
}
.quick-link:hover { border-color: var(--primary); color: var(--primary); background: rgba(255,69,0,.06); }

/* ── Progreso de hoy ──────────────────────────────── */
.progress-item { margin-bottom: 12px; }
.progress-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
.progress-bar { height: 8px; background: #333; border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--primary), #ff6347); transition: width 1s ease; }

/* ════════════════════════════════════════════════════
   MODAL DE EVENTOS DEL DÍA
   ════════════════════════════════════════════════════ */
.day-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.75); z-index: 2000;
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.day-modal-overlay.active { display: flex; }
.day-modal {
    background: #1e1e1e; border-radius: 16px;
    width: 95%; max-width: 500px;
    border: 1px solid #333; overflow: hidden;
    animation: modalIn .25s ease;
}
@keyframes modalIn { from { transform: scale(.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.day-header {
    background: #252525; padding: 18px 22px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid #444;
}
.day-header h3 { margin: 0; color: #fff; font-size: 17px; }
.modal-close-btn {
    background: #333; border: none; color: #aaa;
    width: 30px; height: 30px; border-radius: 50%; cursor: pointer;
    font-size: 16px; display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
.modal-close-btn:hover { background: #ef4444; color: #fff; }

/* Event list */
.event-list { max-height: 230px; overflow-y: auto; padding: 16px 20px; }
.event-item {
    background: #2a2a2a; border-radius: 8px; padding: 12px 14px;
    margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;
    border-left: 3px solid var(--primary);
}
.event-item.done { border-left-color: #22c55e; opacity: .8; }
.event-item-info strong { display: block; color: #fff; font-size: 13px; }
.event-item-info span   { color: #aaa; font-size: 11px; }
.event-actions button { background: none; border: none; cursor: pointer; font-size: 16px; padding: 4px; opacity: .7; transition: opacity .2s; }
.event-actions button:hover { opacity: 1; }
.empty-events { text-align: center; color: #666; font-size: 13px; font-style: italic; padding: 16px 0; }

/* Add event form */
.add-event-form { padding: 18px 20px; background: #222; border-top: 1px solid #333; }
.add-event-form h4 { margin: 0 0 14px 0; color: var(--primary); font-size: 14px; font-weight: 600; }
.modal-form-group { margin-bottom: 10px; }
.modal-form-group input, .modal-form-group select {
    width: 100%; padding: 9px 12px;
    background: #181818; border: 1px solid #333;
    color: #fff; border-radius: 6px; font-size: 13px;
    box-sizing: border-box; transition: border-color .2s;
}
.modal-form-group input:focus { border-color: var(--primary); outline: none; }
.modal-form-row { display: flex; gap: 8px; }
.modal-form-row .modal-form-group { flex: 1; }
.btn-add-event {
    width: 100%; background: linear-gradient(90deg, #ff4500, #ff6347);
    color: #fff; border: none; padding: 11px; border-radius: 6px;
    font-weight: 600; cursor: pointer; font-size: 13px;
    transition: transform .2s, box-shadow .2s;
}
.btn-add-event:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255,69,0,.35); }

/* ── AI Fab ───────────────────────────────────────── */
.ai-fab {
    position: fixed; bottom: 28px; right: 28px;
    width: 58px; height: 58px; border-radius: 50%;
    background: linear-gradient(135deg, #ff4500, #ff6347);
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 26px;
    box-shadow: 0 6px 25px rgba(255,69,0,.45);
    transition: all .3s; z-index: 1000;
}
.ai-fab:hover { transform: scale(1.12); box-shadow: 0 8px 35px rgba(255,69,0,.6); }
</style>
@endsection

@section('content')

{{-- Saludo ─────────────────────────────────────────── --}}
<div class="greeting">
    <h1>¡Hola, {{ explode(' ', $user->name)[0] }}! 👋</h1>
    <p>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
</div>

{{-- Stats ─────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon">🔥</span>
        <span class="stat-value" id="statStreak">0</span>
        <span class="stat-label">Racha Días</span>
    </div>
    <div class="stat-card">
        <span class="stat-icon">📅</span>
        <span class="stat-value" id="statMonth">0</span>
        <span class="stat-label">Completados Mes</span>
    </div>
    <div class="stat-card">
        <span class="stat-icon">⏱️</span>
        <span class="stat-value" id="statMinutes">0</span>
        <span class="stat-label">Minutos Totales</span>
    </div>
    <div class="stat-card">
        <span class="stat-icon">🏆</span>
        <span class="stat-value" id="statTotal">0</span>
        <span class="stat-label">Historial Sesiones</span>
    </div>
</div>

<div class="dashboard-row">
    {{-- ── Calendario ──────────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-calendar3"></i> Mi Agenda Fitness</div>
        <div class="calendar-nav">
            <button class="cal-nav-btn" id="calPrev">◀</button>
            <h3 id="calTitle">Cargando...</h3>
            <button class="cal-nav-btn" id="calNext">▶</button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
    </div>

    {{-- ── Accesos rápidos + Progreso ──────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-lightning-charge-fill"></i> Accesos Rápidos</div>
        <div class="quick-links">
            @if($user->isUser())
                <a href="{{ route('dashboard.training') }}" class="quick-link">💪 Entrenamiento</a>
                <a href="{{ route('dashboard.nutrition') }}" class="quick-link">🥗 Nutrición</a>
                <a href="{{ route('dashboard.progress') }}"  class="quick-link">📈 Progreso</a>
                <a href="{{ route('dashboard.goals') }}"    class="quick-link">🏆 Metas</a>
            @else
                <a href="{{ route('dashboard.users') }}"   class="quick-link">👥 Usuarios</a>
            @endif
            <a href="{{ route('dashboard.messages') }}" class="quick-link">💬 Mensajes</a>
            <a href="{{ route('dashboard.profile') }}"  class="quick-link">👤 Mi Perfil</a>
        </div>

        @if($user->isUser())
        <div style="margin-top: 22px;">
            <div class="card-title" style="margin-bottom:12px;"><i class="bi bi-check2-circle"></i> Progreso de Hoy</div>
            <div class="progress-item">
                <div class="progress-label">
                    <span>Ejercicios completados</span>
                    <span id="exerciseProgress">—</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="exerciseBar" style="width:0%"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ════════ MODAL DE EVENTOS DEL DÍA ════════ --}}
<div class="day-modal-overlay" id="dayModal">
    <div class="day-modal">
        <div class="day-header">
            <h3 id="modalDateTitle">—</h3>
            <button class="modal-close-btn" onclick="closeModal()">✕</button>
        </div>

        <div class="event-list" id="modalEventList"></div>

        <div class="add-event-form">
            <h4>➕ Agregar Sesión</h4>
            <form id="addEventForm" onsubmit="submitEvent(event)">
                <input type="hidden" id="eventDate">
                <div class="modal-form-group">
                    <input type="text" id="eventTitle" placeholder="Ej. Pecho y Tríceps" required>
                </div>
                <div class="modal-form-row">
                    <div class="modal-form-group">
                        <select id="eventType">
                            <option value="Fuerza">Fuerza</option>
                            <option value="Cardio">Cardio</option>
                            <option value="HIIT">HIIT</option>
                            <option value="Movilidad">Movilidad / Yoga</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <input type="number" id="eventDuration" placeholder="Minutos" required min="1">
                    </div>
                </div>
                <div class="modal-form-group">
                    <input type="number" id="eventCalories" placeholder="Calorías quemadas (opcional)" min="0">
                </div>
                <button type="submit" class="btn-add-event">Guardar Sesión</button>
            </form>
        </div>
    </div>
</div>

{{-- ── AI Fab (botón flotante) ──────────────────── --}}
<button class="ai-fab" id="aiFab" title="Asistente Tone Trainer">🤖</button>

@endsection

@section('scripts')
<script>
// ════════════════════════════════════════════════
//  STATS con animación de conteo
// ════════════════════════════════════════════════
async function loadStats() {
    try {
        const r = await fetch('/api/calendar/stats');
        const d = await r.json();
        if (d.success) {
            animateValue('statStreak',  d.data.current_streak);
            animateValue('statMonth',   d.data.this_month);
            animateValue('statMinutes', d.data.total_minutes);
            animateValue('statTotal',   d.data.total_workouts);
        }
    } catch(e) {}
}

function animateValue(id, end) {
    const el  = document.getElementById(id);
    const val = parseInt(end) || 0;
    if (val === 0) { el.textContent = '0'; return; }
    let cur  = 0;
    const step = Math.max(1, Math.ceil(val / 30));
    const iv = setInterval(() => {
        cur += step;
        if (cur >= val) { cur = val; clearInterval(iv); }
        el.textContent = cur;
    }, 40);
}

// ════════════════════════════════════════════════
//  CALENDARIO INTERACTIVO
// ════════════════════════════════════════════════
let calYear, calMonth;
let workoutEvents = [];
const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

function initCalendar() {
    const now = new Date();
    calYear   = now.getFullYear();
    calMonth  = now.getMonth();
    refreshAll();
}

async function refreshAll() {
    await loadWorkoutEvents();
    loadStats();
    @if(auth()->user()->isUser()) loadTodayProgress(); @endif
}

async function loadWorkoutEvents() {
    try {
        const r = await fetch('/api/calendar/workouts');
        const d = await r.json();
        if (d.success) workoutEvents = d.data;
    } catch(e) {}
    renderCalendar();
}

function renderCalendar() {
    document.getElementById('calTitle').textContent = `${monthNames[calMonth]} ${calYear}`;
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    // Cabeceras
    ['Lu','Ma','Mi','Ju','Vi','Sa','Do'].forEach(d => {
        const h = document.createElement('div');
        h.className = 'cal-day-header';
        h.textContent = d;
        grid.appendChild(h);
    });

    const firstDay    = new Date(calYear, calMonth, 1);
    let   startCol    = firstDay.getDay() - 1;
    if (startCol < 0) startCol = 6;
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
    const today       = new Date();

    // Celdas vacías
    for (let i = 0; i < startCol; i++) {
        const el = document.createElement('div');
        el.className = 'cal-day cal-day--empty';
        grid.appendChild(el);
    }

    // Días
    for (let d = 1; d <= daysInMonth; d++) {
        const el      = document.createElement('div');
        el.className  = 'cal-day';
        el.textContent = d;

        const dateStr = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const isToday = today.getFullYear() === calYear && today.getMonth() === calMonth && today.getDate() === d;
        if (isToday) el.classList.add('cal-day--today');

        const dayEvts = workoutEvents.filter(w => w.workout_date === dateStr);
        if (dayEvts.length > 0) {
            const ind = document.createElement('div');
            ind.className = 'cal-event-indicators';
            dayEvts.forEach(w => {
                const dot = document.createElement('div');
                dot.className = `cal-dot${w.completed == 1 ? ' done' : ''}`;
                ind.appendChild(dot);
            });
            el.appendChild(ind);
        }

        el.addEventListener('click', () => openDayModal(d, dateStr, dayEvts));
        grid.appendChild(el);
    }
}

// Navegación
document.getElementById('calPrev').addEventListener('click', () => {
    calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCalendar();
});
document.getElementById('calNext').addEventListener('click', () => {
    calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderCalendar();
});

// ════════════════════════════════════════════════
//  MODAL CRUD DE EVENTOS
// ════════════════════════════════════════════════
const dayModal = document.getElementById('dayModal');

function openDayModal(day, dateStr, evts) {
    document.getElementById('modalDateTitle').textContent = `${day} de ${monthNames[calMonth]} ${calYear}`;
    document.getElementById('eventDate').value = dateStr;
    document.getElementById('addEventForm').reset();
    document.getElementById('eventDate').value = dateStr;

    const list = document.getElementById('modalEventList');
    list.innerHTML = '';

    if (!evts.length) {
        list.innerHTML = '<p class="empty-events">No hay eventos para este día.</p>';
    } else {
        evts.forEach(ev => {
            const done = ev.completed == 1;
            const item = document.createElement('div');
            item.className = `event-item${done ? ' done' : ''}`;
            item.innerHTML = `
                <div class="event-item-info">
                    <strong>${ev.title || ev.workout_type}</strong>
                    <span>${ev.workout_type} • ${ev.duration_minutes} min${ev.calories_burned ? ' • ' + ev.calories_burned + ' kcal' : ''}</span>
                </div>
                <div class="event-actions">
                    ${!done ? `<button title="Completar" onclick="markComplete(${ev.id})">✔️</button>` : ''}
                    <button title="Eliminar" onclick="deleteEvent(${ev.id})">🗑️</button>
                </div>`;
            list.appendChild(item);
        });
    }

    dayModal.classList.add('active');
}

function closeModal() { dayModal.classList.remove('active'); }
dayModal.addEventListener('click', e => { if (e.target === dayModal) closeModal(); });

async function submitEvent(e) {
    e.preventDefault();
    const payload = {
        workout_date:     document.getElementById('eventDate').value,
        title:            document.getElementById('eventTitle').value,
        workout_type:     document.getElementById('eventType').value,
        duration_minutes: document.getElementById('eventDuration').value,
        calories_burned:  document.getElementById('eventCalories').value || 0,
    };
    try {
        const r = await fetch('/api/calendar/workouts', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const d = await r.json();
        if (d.success) { closeModal(); refreshAll(); showToast('✅ Sesión guardada'); }
        else showToast('❌ ' + (d.message || 'Error'));
    } catch(err) { showToast('❌ Error de conexión'); }
}

async function markComplete(id) {
    await fetch('/api/calendar/workouts/complete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id })
    });
    closeModal(); refreshAll(); showToast('✅ ¡Sesión completada!');
}

async function deleteEvent(id) {
    if (!confirm('¿Eliminar este evento?')) return;
    await fetch(`/api/calendar/workouts/${id}`, { method: 'DELETE' });
    closeModal(); refreshAll(); showToast('🗑️ Evento eliminado');
}

// ════════════════════════════════════════════════
//  PROGRESO DEL DÍA (solo usuario normal)
// ════════════════════════════════════════════════
async function loadTodayProgress() {
    try {
        const r = await fetch('/api/training/stats');
        const d = await r.json();
        if (d.success) {
            const { totalExercises, completedToday, adherence } = d.data;
            document.getElementById('exerciseProgress').textContent = `${completedToday}/${totalExercises}`;
            document.getElementById('exerciseBar').style.width = adherence + '%';
        }
    } catch(e) {}
}

// ════════════════════════════════════════════════
//  AI FAB (botón flotante)
// ════════════════════════════════════════════════
document.getElementById('aiFab').addEventListener('click', () => {
    showToast('🤖 ¡Mantén la consistencia! Agenda tus sesiones desde el calendario.');
});

// ════════════════════════════════════════════════
//  TOAST
// ════════════════════════════════════════════════
function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#FF4500;color:#fff;padding:12px 22px;border-radius:10px;z-index:9999;box-shadow:0 6px 25px rgba(0,0,0,.4);font-size:13px;font-weight:500;transition:opacity .3s;';
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 2200);
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', initCalendar);
</script>
@endsection
