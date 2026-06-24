@extends('layouts.dashboard')

@section('title', 'Entrenamiento')
@section('page-title', '💪 Plan de Entrenamiento')

@section('styles')
<style>
.page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.page-header h1 { font-size: 20px; font-weight: 700; color: #fff; }

/* ── Progreso del día ─────────────────────────────── */
.progress-card {
    background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
    border: 1px solid #333; border-radius: 14px; padding: 20px; margin-bottom: 24px;
    position: relative; overflow: hidden;
}
.progress-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff4500, #ff6347, #ff8c00);
}
.progress-card h3 { color: var(--primary); font-size: 16px; font-weight: 600; margin-bottom: 12px; }
.progress-bar-bg { background: #333; border-radius: 10px; height: 28px; overflow: hidden; margin-bottom: 8px; }
.progress-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #ff4500, #ff6347); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; transition: width .5s ease; }
.progress-text { color: var(--muted); font-size: 13px; }

/* ── Stats badges ─────────────────────────────────── */
.stats-row { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
.stats-badge {
    background: linear-gradient(135deg, #1e1e1e 0%, #2a2a2a 100%);
    border: 1px solid #333; border-radius: 10px; padding: 12px 18px;
    font-size: 13px; color: var(--text);
}
.stats-badge strong { color: var(--primary); }

/* ── Tarjetas de ejercicios ───────────────────────── */
.training-cards { display: flex; flex-wrap: wrap; gap: 16px; }
.training-card {
    background: linear-gradient(135deg, #1e1e1e 0%, #252525 100%);
    border: 1px solid #333; border-radius: 14px; padding: 20px;
    width: 290px; flex-shrink: 0;
    position: relative; overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.training-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff4500, #ff6347, #ff8c00);
}
.training-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255,69,0,0.15); }
.training-card h4 { color: var(--primary); font-size: 15px; font-weight: 700; margin-bottom: 14px; border-bottom: 1px solid #333; padding-bottom: 8px; }
.exercise-item { display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #2a2a2a; gap: 10px; }
.exercise-item:last-child { border-bottom: none; }
.exercise-item.completed .exercise-name { text-decoration: line-through; color: var(--muted); }
.exercise-checkbox { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; flex-shrink: 0; }
.exercise-content { flex: 1; }
.exercise-name { font-size: 13px; font-weight: 500; color: var(--text); }
.exercise-meta { font-size: 11px; color: var(--muted); margin-top: 3px; }
.btn-delete-ex { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; padding: 4px; border-radius: 4px; transition: background .15s; }
.btn-delete-ex:hover { background: rgba(239,68,68,.15); }

/* ── Formulario agregar ───────────────────────────── */
.form-card {
    background: linear-gradient(135deg, #1e1e1e 0%, #252525 100%);
    border: 1px solid #333; border-radius: 14px; padding: 24px;
    max-width: 580px; margin-bottom: 28px;
    position: relative; overflow: hidden;
}
.form-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff4500, #ff6347, #ff8c00);
}
.form-card h3 { color: var(--primary); font-size: 15px; font-weight: 600; margin-bottom: 18px; }
.form-row { display: flex; gap: 12px; }
.form-group { margin-bottom: 14px; flex: 1; }
.form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%; padding: 10px 14px;
    background: #181818; border: 1px solid #333;
    color: var(--text); border-radius: 8px;
    font-size: 13px; font-family: 'Poppins', sans-serif;
    transition: border-color .2s;
}
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px rgba(255,69,0,.1); }
.btn-primary {
    padding: 12px 26px;
    background: linear-gradient(90deg, var(--primary), #ff6347);
    border: none; border-radius: 8px; color: #fff;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: transform .2s, box-shadow .2s;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,69,0,0.35); }

/* ── Empty state ──────────────────────────────────── */
.empty-state { text-align: center; padding: 60px; color: var(--muted); }
.empty-state .icon { font-size: 52px; margin-bottom: 14px; display: block; }

/* ── Section title ────────────────────────────────── */
.section-header {
    display: flex; align-items: center; gap: 10px;
    margin: 0 0 20px 0; padding-bottom: 10px;
    border-bottom: 2px solid #2a2a2a;
}
.section-header h2 { margin: 0; font-size: 18px; color: #fff; font-weight: 600; }
</style>
@endsection

@section('content')

{{-- Título con modo edición ─────────────────────────── --}}
<div class="page-header">
    <h1>
        @if(isset($targetUserName) && $targetUserName !== $user->name)
            <span style="font-size:22px">✏️</span> Rutina de: {{ $targetUserName }}
            <small style="font-size:13px; color:var(--muted);">(Modo Edición)</small>
        @else
            Mi Rutina de Entrenamiento
        @endif
    </h1>
    @if($user->isTrainer() || $user->isAdmin())
        <div style="background:var(--surface); padding:8px 12px; border-radius:8px; border:1px solid #333; display:flex; align-items:center; gap:10px;">
            <label style="font-size:13px; color:var(--muted); margin:0;">Seleccionar Alumno:</label>
            <select onchange="window.location.href='?user_id=' + this.value" style="padding:6px; border-radius:6px; background:#222; color:#fff; border:1px solid #444; font-size:13px;">
                <option value="{{ $user->id }}">Mi propia rutina</option>
                @if(isset($students))
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request()->query('user_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif
</div>

{{-- ── Barra de progreso (usuario normal) ───────────── --}}
@if($user->isUser())
<div class="progress-card">
    <h3>🎯 Progreso de Hoy</h3>
    <div class="progress-bar-bg">
        <div class="progress-bar-fill" id="dailyProgressBar" style="width:0%">0%</div>
    </div>
    <p class="progress-text"><span id="completedCount">0</span> de <span id="totalCount">0</span> ejercicios completados</p>
</div>
@endif

{{-- ── Stats entrenador/admin ─────────────────────── --}}
@if($user->isTrainer() || $user->isAdmin())
<div class="stats-row" id="trainerStatsRow" style="display:none;">
    <div class="stats-badge" id="statToday"></div>
    <div class="stats-badge" id="statWeek"></div>
    <div class="stats-badge" id="statStreak"></div>
</div>

{{-- Formulario para agregar ejercicio ─────────────────── --}}
<div class="form-card">
    <h3>➕ Agregar Ejercicio a la Rutina</h3>
    <div class="form-row">
        <div class="form-group">
            <label>Grupo / Día</label>
            <input type="text" id="newDayGroup" placeholder="Ej: Pecho, Lunes" list="dayOptions">
            <datalist id="dayOptions">
                <option value="Lunes"><option value="Martes"><option value="Miércoles">
                <option value="Jueves"><option value="Viernes"><option value="Sábado">
                <option value="Pecho"><option value="Espalda"><option value="Pierna"><option value="Brazos">
            </datalist>
        </div>
        <div class="form-group" style="position: relative;">
            <label>Ejercicio</label>
            <input type="text" id="newExercise" placeholder="Nombre del ejercicio" autocomplete="off" oninput="searchLibrary(this.value)" onfocus="searchLibrary(this.value)">
            <div id="exerciseSuggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #222; border: 1px solid #444; border-radius: 8px; z-index: 1000; max-height: 220px; overflow-y: auto; box-shadow: 0 8px 30px rgba(0,0,0,0.6); margin-top: 4px;"></div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Series</label>
            <input type="number" id="newSeries" placeholder="Ej: 3" min="1">
        </div>
        <div class="form-group">
            <label>Repeticiones (opcional)</label>
            <input type="number" id="newReps" placeholder="Ej: 12" min="1">
        </div>
        <div class="form-group">
            <label>Duración / Tiempo (opcional)</label>
            <input type="text" id="newDuration" placeholder="Ej: 60s, 2 min">
        </div>
    </div>
    <div class="form-group">
        <label>Descripción / Tips (opcional)</label>
        <input type="text" id="newDesc" placeholder="Nota adicional">
    </div>
    <button class="btn-primary" onclick="addExercise()">Agregar a la Rutina</button>
</div>
@endif

{{-- ── Área de tarjetas ──────────────────────────────── --}}
<div class="section-header">
    <span style="font-size:22px">📋</span>
    <h2>Plan de Entrenamiento</h2>
</div>

<div id="trainingArea">
    <div class="training-cards" id="trainingCards"></div>
    <div class="empty-state" id="emptyState" style="display:none;">
        <span class="icon">🏋️</span>
        <p>No hay rutina asignada todavía.</p>
        @if($user->isUser())<small style="color:#555;">Tu entrenador asignará ejercicios pronto.</small>@endif
    </div>
</div>

@endsection

@section('scripts')
<script>
const isTrainer    = {{ ($user->isTrainer() || $user->isAdmin()) ? 'true' : 'false' }};
const targetUserId = {{ request()->query('user_id', $user->id) }};
let completedSet   = new Set();
let allExercises   = [];

// ── Carga inicial ──────────────────────────────────────────
async function loadTraining() {
    // 1. Cargar plan de ejercicios
    const url = `/api/training-plan?user_id=${targetUserId}`;
    const [planRes, compRes, statsRes] = await Promise.all([
        fetch(url),
        fetch('/api/training/today' + (isTrainer ? `?user_id=${targetUserId}` : '')),
        fetch('/api/training/stats' + (isTrainer ? `?user_id=${targetUserId}` : ''))
    ]);

    const planData  = await planRes.json();
    const compData  = await compRes.json();
    const statsData = await statsRes.json();

    // 2. Marcar completados del día
    completedSet.clear();
    if (compData.success) {
        compData.data.forEach(c => completedSet.add(parseInt(c.exercise_id)));
    }

    // 3. Mostrar stats de entrenador
    if (statsData.success && isTrainer) {
        const d = statsData.data;
        document.getElementById('trainerStatsRow').style.display = 'flex';
        document.getElementById('statToday').innerHTML  = `Hoy: <strong>${d.completedToday}/${d.totalExercises}</strong> (${d.adherence}%)`;
        document.getElementById('statWeek').innerHTML   = `Semana: <strong>${d.completedWeek}</strong> ejercicios`;
        document.getElementById('statStreak').innerHTML = `Racha: <strong>${d.streak}</strong> días 🔥`;
    }

    // 4. Renderizar tarjetas
    allExercises = planData.success ? planData.data : [];
    renderCards(allExercises);
}

// ── Renderizar tarjetas agrupadas por día/grupo ──────────
function renderCards(exercises) {
    const container = document.getElementById('trainingCards');
    const empty     = document.getElementById('emptyState');
    container.innerHTML = '';

    if (!exercises.length) {
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';

    const groups = {};
    exercises.forEach(ex => {
        if (!groups[ex.day_group]) groups[ex.day_group] = [];
        groups[ex.day_group].push(ex);
    });

    Object.entries(groups).forEach(([group, exList]) => {
        const card = document.createElement('div');
        card.className = 'training-card';
        card.innerHTML = `<h4>${group}</h4><div class="exercise-list"></div>`;
        const list = card.querySelector('.exercise-list');

        exList.forEach(ex => {
            const done = completedSet.has(parseInt(ex.id));
            const item = document.createElement('div');
            item.className = `exercise-item${done ? ' completed' : ''}`;
            item.id = `ex-${ex.id}`;
            item.title = ex.description || '';

            let metaText = `${ex.series} series`;
            if (ex.reps && ex.duration) {
                metaText += ` × ${ex.reps} reps (${ex.duration})`;
            } else if (ex.reps) {
                metaText += ` × ${ex.reps} reps`;
            } else if (ex.duration) {
                metaText += ` × ${ex.duration}`;
            }

            item.innerHTML = `
                ${!isTrainer ? `<input type="checkbox" class="exercise-checkbox" ${done ? 'checked' : ''}
                    data-id="${ex.id}" onchange="toggleCompletion(this)">` : ''}
                <div class="exercise-content">
                    <div class="exercise-name">${ex.exercise}</div>
                    <div class="exercise-meta">${metaText}
                        ${ex.description ? ' — ' + ex.description : ''}</div>
                </div>
                ${isTrainer ? `<button class="btn-delete-ex" title="Eliminar" onclick="deleteExercise(${ex.id})">🗑️</button>` : ''}
            `;
            list.appendChild(item);
        });

        container.appendChild(card);
    });

    updateProgressBar();
}

// ── Toggle completado ──────────────────────────────────────
async function toggleCompletion(cb) {
    const id     = parseInt(cb.dataset.id);
    const action = cb.checked ? 'complete' : 'uncomplete';
    try {
        const r = await fetch(`/api/training/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ exercise_id: id })
        });
        const d = await r.json();
        if (d.success) {
            cb.checked ? completedSet.add(id) : completedSet.delete(id);
            cb.closest('.exercise-item').classList.toggle('completed', cb.checked);
            updateProgressBar();
            showToast(cb.checked ? '✅ Ejercicio completado' : '↩️ Ejercicio desmarcado');
        } else {
            cb.checked = !cb.checked;
            showToast('❌ Error: ' + (d.message || 'Inténtalo de nuevo'));
        }
    } catch (e) {
        cb.checked = !cb.checked;
        showToast('❌ Error de conexión al guardar el ejercicio.');
        console.error(e);
    }
}

// ── Agregar ejercicio ──────────────────────────────────────
async function addExercise() {
    const repsVal = document.getElementById('newReps').value;
    const body = {
        user_id:     targetUserId,
        day_group:   document.getElementById('newDayGroup').value.trim(),
        exercise:    document.getElementById('newExercise').value.trim(),
        series:      parseInt(document.getElementById('newSeries').value),
        reps:        repsVal ? parseInt(repsVal) : null,
        duration:    document.getElementById('newDuration').value.trim(),
        description: document.getElementById('newDesc').value.trim(),
    };

    if (!body.day_group || !body.exercise || !body.series) {
        showToast('⚠️ Completa los campos obligatorios (Día, Ejercicio, Series)'); return;
    }

    if (!body.reps && !body.duration) {
        showToast('⚠️ Debes ingresar Repeticiones o Duración/Tiempo'); return;
    }

    const r = await fetch('/api/training-plan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(body)
    });
    const d = await r.json();
    if (d.success) {
        showToast('✅ Ejercicio añadido');
        ['newDayGroup','newExercise','newSeries','newReps','newDuration','newDesc'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        loadTraining();
    } else {
        showToast('❌ ' + (d.message || d.errors?.day_group?.[0] || 'Error al guardar'));
    }
}

// ── Eliminar ejercicio ─────────────────────────────────────
async function deleteExercise(id) {
    if (!confirm('¿Eliminar este ejercicio de la rutina?')) return;
    const r = await fetch(`/api/training-plan/${id}?user_id=${targetUserId}`, { method: 'DELETE' });
    const d = await r.json();
    if (d.success) { showToast('🗑️ Ejercicio eliminado'); loadTraining(); }
    else showToast('❌ ' + (d.message || 'Error'));
}

// ── Barra de progreso ──────────────────────────────────────
function updateProgressBar() {
    const checks = document.querySelectorAll('.exercise-checkbox');
    const total  = checks.length;
    const done   = completedSet.size;
    const pct    = total > 0 ? Math.round((done / total) * 100) : 0;

    const bar = document.getElementById('dailyProgressBar');
    if (bar) { bar.style.width = pct + '%'; bar.textContent = pct + '%'; }

    const cc = document.getElementById('completedCount');
    const tc = document.getElementById('totalCount');
    if (cc) cc.textContent = done;
    if (tc) tc.textContent = total;
}

// ── Toast ──────────────────────────────────────────────────
function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#FF4500;color:#fff;padding:12px 22px;border-radius:10px;z-index:9999;box-shadow:0 6px 25px rgba(0,0,0,.4);font-size:13px;font-weight:500;';
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

// ── Autocomplete biblioteca de ejercicios ──────────────────
let libraryTimeout = null;
async function searchLibrary(val) {
    const suggContainer = document.getElementById('exerciseSuggestions');
    if (!val || val.length < 2) {
        suggContainer.innerHTML = '';
        suggContainer.style.display = 'none';
        return;
    }

    clearTimeout(libraryTimeout);
    libraryTimeout = setTimeout(async () => {
        try {
            const r = await fetch(`/api/exercises-library?search=${encodeURIComponent(val)}`);
            const d = await r.json();
            if (d.success && d.data.length > 0) {
                suggContainer.innerHTML = '';
                d.data.forEach(ex => {
                    const div = document.createElement('div');
                    div.style.cssText = 'padding: 10px 14px; border-bottom: 1px solid #333; cursor: pointer; transition: background .15s; font-size: 13px; color: #fff;';
                    div.innerHTML = `<strong>${ex.name}</strong> <span style="font-size:11px; color:var(--muted); float:right;">${ex.primaryMuscles ? ex.primaryMuscles.join(', ') : ''}</span>`;
                    
                    div.onmouseover = () => div.style.background = 'var(--primary)';
                    div.onmouseout = () => div.style.background = 'transparent';
                    
                    div.onclick = () => {
                        document.getElementById('newExercise').value = ex.name;
                        
                        let tip = '';
                        if (ex.primaryMuscles && ex.primaryMuscles.length > 0) {
                            tip += `Músculo: ${ex.primaryMuscles.join(', ')}. `;
                        }
                        if (ex.instructions && ex.instructions.length > 0) {
                            tip += ex.instructions[0];
                        }
                        document.getElementById('newDesc').value = tip.substring(0, 190);
                        
                        suggContainer.style.display = 'none';
                    };
                    suggContainer.appendChild(div);
                });
                suggContainer.style.display = 'block';
            } else {
                suggContainer.innerHTML = '<div style="padding: 10px 14px; color: var(--muted); font-size: 13px;">No se encontraron ejercicios.</div>';
                suggContainer.style.display = 'block';
            }
        } catch (e) {
            console.error('Error fetching exercises library', e);
        }
    }, 200);
}

// Cerrar sugerencias al hacer clic fuera
document.addEventListener('click', function(e) {
    const suggContainer = document.getElementById('exerciseSuggestions');
    if (suggContainer && e.target.id !== 'newExercise') {
        suggContainer.style.display = 'none';
    }
});

loadTraining();
</script>
@endsection
