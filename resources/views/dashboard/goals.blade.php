@extends('layouts.dashboard')
@section('title', 'Metas')
@section('page-title', '🏆 Mis Metas')

@section('styles')
<style>
.goals-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.btn-primary { padding: 10px 20px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 7px 16px; border: 1px solid var(--border); border-radius: 20px; background: none; color: var(--muted); font-size: 12px; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
.filter-tab.active { background: var(--primary); border-color: var(--primary); color: #fff; }
.goals-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px; }
.goal-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; transition: border-color .2s, transform .2s; }
.goal-card:hover { border-color: var(--primary); transform: translateY(-2px); }
.goal-card.completed { border-color: #22c55e; }
.goal-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.goal-title { font-size: 14px; font-weight: 600; color: #fff; }
.goal-category { font-size: 10px; padding: 2px 8px; border-radius: 10px; background: var(--primary-glow); color: var(--primary); border: 1px solid rgba(255,69,0,.25); font-weight: 600; margin-top: 3px; }
.goal-badge-status { font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
.status-active    { background: rgba(59,130,246,.12); color: #60a5fa; }
.status-completed { background: rgba(34,197,94,.12);  color: #4ade80; }
.status-failed    { background: rgba(239,68,68,.12);  color: #f87171; }
.goal-progress-bar { height: 8px; background: var(--surface3); border-radius: 4px; overflow: hidden; margin: 10px 0 6px; }
.goal-progress-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--primary), #ff6347); transition: width .5s; }
.goal-progress-fill.completed-fill { background: linear-gradient(90deg, #22c55e, #4ade80); }
.goal-values { display: flex; justify-content: space-between; font-size: 11px; color: var(--muted); margin-bottom: 12px; }
.goal-deadline { font-size: 11px; color: var(--muted); margin-bottom: 12px; }
.goal-actions { display: flex; gap: 8px; }
.btn-sm { padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; border: 1px solid; font-family: 'Poppins', sans-serif; }
.btn-sm-progress { background: rgba(255,69,0,.1); border-color: rgba(255,69,0,.3); color: var(--primary); }
.btn-sm-delete  { background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.2); color: #f87171; }
.btn-sm-edit    { background: rgba(59,130,246,.08); border-color: rgba(59,130,246,.2); color: #60a5fa; }
/* Achievements */
.achievements-section { margin-top: 8px; }
.achievements-section h3 { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 14px; }
.achievements-grid { display: flex; flex-wrap: wrap; gap: 12px; }
.achievement-badge { display: flex; align-items: center; gap: 10px; background: var(--surface); border: 1px solid #2a2a2a; border-radius: 10px; padding: 12px 16px; }
.achiev-icon { font-size: 28px; }
.achiev-name { font-size: 13px; font-weight: 600; color: #fff; }
.achiev-desc { font-size: 11px; color: var(--muted); }
/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 200; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal-box { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 30px; width: 100%; max-width: 440px; }
.modal-box h3 { color: var(--primary); font-size: 16px; font-weight: 600; margin-bottom: 18px; }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
.form-group input, .form-group select { width: 100%; padding: 10px 14px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-size: 13px; font-family: 'Poppins', sans-serif; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
.modal-actions { display: flex; gap: 10px; margin-top: 18px; }
.btn-cancel { flex: 1; padding: 11px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: var(--muted); cursor: pointer; font-family: 'Poppins', sans-serif; }
.btn-save   { flex: 2; padding: 11px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; }
.empty-state { text-align: center; padding: 50px; color: var(--muted); }
</style>
@endsection

@section('content')
<div class="goals-toolbar">
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterGoals('all', this)">Todas</button>
        <button class="filter-tab" onclick="filterGoals('active', this)">Activas</button>
        <button class="filter-tab" onclick="filterGoals('completed', this)">Completadas</button>
    </div>
    <button class="btn-primary" onclick="openModal()">+ Nueva Meta</button>
</div>

<div class="goals-grid" id="goalsGrid"></div>

<div class="achievements-section">
    <h3>🏅 Logros Desbloqueados</h3>
    <div class="achievements-grid" id="achievementsGrid">
        <p style="color:var(--muted);font-size:13px;">Cargando logros...</p>
    </div>
</div>

{{-- Modal nueva meta --}}
<div class="modal-overlay" id="goalModal">
    <div class="modal-box">
        <h3 id="modalTitle">Nueva Meta</h3>
        <input type="hidden" id="editGoalId">
        <div class="form-group"><label>Título</label><input type="text" id="goalTitle" placeholder="Ej: Perder 5kg"></div>
        <div class="form-group"><label>Descripción</label><input type="text" id="goalDesc" placeholder="Descripción opcional"></div>
        <div style="display:flex; gap:12px;">
            <div class="form-group" style="flex:1;"><label>Categoría</label>
                <select id="goalCategory">
                    <option value="peso">⚖️ Peso</option><option value="musculo">💪 Músculo</option>
                    <option value="grasa">🔥 Grasa</option><option value="fuerza">🏋️ Fuerza</option>
                    <option value="resistencia">🏃 Resistencia</option><option value="nutricion">🥗 Nutrición</option>
                    <option value="habitos">✨ Hábitos</option><option value="otro">🌟 Otro</option>
                </select>
            </div>
            <div class="form-group" style="flex:1;"><label>Unidad</label><input type="text" id="goalUnit" placeholder="kg, %, km..."></div>
        </div>
        <div style="display:flex; gap:12px;">
            <div class="form-group" style="flex:1;"><label>Valor objetivo</label><input type="number" id="goalTarget" step="0.1"></div>
            <div class="form-group" style="flex:1;"><label>Fecha límite</label><input type="date" id="goalDeadline"></div>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
            <button class="btn-save" onclick="saveGoal()">Guardar Meta</button>
        </div>
    </div>
</div>

{{-- Modal progreso --}}
<div class="modal-overlay" id="progressModal">
    <div class="modal-box">
        <h3>Actualizar Progreso</h3>
        <input type="hidden" id="progressGoalId">
        <p id="progressGoalName" style="color:var(--muted);font-size:13px;margin-bottom:14px;"></p>
        <div class="form-group"><label>Valor actual</label><input type="number" id="progressValue" step="0.1" placeholder="0"></div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeProgressModal()">Cancelar</button>
            <button class="btn-save" onclick="saveProgress()">Actualizar</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allGoals = [], currentFilter = 'all';

async function loadGoals() {
    const [gr, ar] = await Promise.all([fetch('/api/goals'), fetch('/api/goals/achievements')]);
    const gd = await gr.json(), ad = await ar.json();
    if (gd.success) { allGoals = gd.data; renderGoals(); }
    if (ad.success) renderAchievements(ad.data);
}

function filterGoals(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderGoals();
}

function renderGoals() {
    const filtered = currentFilter === 'all' ? allGoals : allGoals.filter(g => g.status === currentFilter);
    const grid = document.getElementById('goalsGrid');
    if (!filtered.length) { grid.innerHTML = '<div class="empty-state"><div style="font-size:40px">🎯</div><p>No hay metas en esta categoría.</p></div>'; return; }
    grid.innerHTML = filtered.map(g => {
        const pct = g.target_value > 0 ? Math.min(100, Math.round((g.current_value / g.target_value) * 100)) : 0;
        const statusClass = { active: 'status-active', completed: 'status-completed', failed: 'status-failed' }[g.status] || 'status-active';
        const statusLabel = { active: 'Activa', completed: '✅ Completada', failed: 'Fallida' }[g.status] || g.status;
        return `<div class="goal-card ${g.status === 'completed' ? 'completed' : ''}">
            <div class="goal-header">
                <div>
                    <div class="goal-title">${g.title}</div>
                    <span class="goal-category">${g.category || 'otro'}</span>
                </div>
                <span class="goal-badge-status ${statusClass}">${statusLabel}</span>
            </div>
            ${g.description ? `<p style="font-size:12px;color:var(--muted);margin-bottom:10px;">${g.description}</p>` : ''}
            <div class="goal-progress-bar">
                <div class="goal-progress-fill ${g.status === 'completed' ? 'completed-fill' : ''}" style="width:${pct}%"></div>
            </div>
            <div class="goal-values"><span>${g.current_value} ${g.unit}</span><span>${g.target_value} ${g.unit} (${pct}%)</span></div>
            ${g.deadline ? `<div class="goal-deadline">📅 Fecha límite: ${g.deadline}</div>` : ''}
            <div class="goal-actions">
                ${g.status === 'active' ? `<button class="btn-sm btn-sm-progress" onclick="openProgressModal(${g.id}, '${g.title}', ${g.current_value})">📊 Progreso</button>` : ''}
                <button class="btn-sm btn-sm-edit"   onclick="openEditModal(${g.id})">✏️</button>
                <button class="btn-sm btn-sm-delete" onclick="deleteGoal(${g.id})">🗑️</button>
            </div>
        </div>`;
    }).join('');
}

function renderAchievements(list) {
    const grid = document.getElementById('achievementsGrid');
    if (!list.length) { grid.innerHTML = '<p style="color:var(--muted);font-size:13px;">Aún no has desbloqueado logros. ¡Completa tus metas!</p>'; return; }
    grid.innerHTML = list.map(a => `<div class="achievement-badge">
        <div class="achiev-icon">${a.badge_icon}</div>
        <div><div class="achiev-name">${a.badge_name}</div><div class="achiev-desc">${a.description}</div></div>
    </div>`).join('');
}

function openModal()   { document.getElementById('editGoalId').value = ''; document.getElementById('modalTitle').textContent = 'Nueva Meta'; ['goalTitle','goalDesc','goalUnit','goalTarget','goalDeadline'].forEach(id => document.getElementById(id).value = ''); document.getElementById('goalModal').classList.add('open'); }
function closeModal()  { document.getElementById('goalModal').classList.remove('open'); }
function openEditModal(id) {
    const g = allGoals.find(g => g.id == id); if (!g) return;
    document.getElementById('editGoalId').value  = g.id;
    document.getElementById('goalTitle').value   = g.title;
    document.getElementById('goalDesc').value    = g.description || '';
    document.getElementById('goalCategory').value= g.category || 'otro';
    document.getElementById('goalUnit').value    = g.unit;
    document.getElementById('goalTarget').value  = g.target_value;
    document.getElementById('goalDeadline').value= g.deadline || '';
    document.getElementById('modalTitle').textContent = 'Editar Meta';
    document.getElementById('goalModal').classList.add('open');
}
function openProgressModal(id, title, current) {
    document.getElementById('progressGoalId').value  = id;
    document.getElementById('progressGoalName').textContent = title;
    document.getElementById('progressValue').value   = current;
    document.getElementById('progressModal').classList.add('open');
}
function closeProgressModal() { document.getElementById('progressModal').classList.remove('open'); }

async function saveGoal() {
    const id   = document.getElementById('editGoalId').value;
    const body = {
        title:        document.getElementById('goalTitle').value,
        description:  document.getElementById('goalDesc').value,
        category:     document.getElementById('goalCategory').value,
        unit:         document.getElementById('goalUnit').value,
        target_value: document.getElementById('goalTarget').value,
        deadline:     document.getElementById('goalDeadline').value,
    };
    if (!body.title || !body.target_value || !body.unit) { showToast('⚠️ Título, objetivo y unidad son obligatorios'); return; }
    const method = id ? 'PUT' : 'POST', url = id ? `/api/goals/${id}` : '/api/goals';
    const r = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const d = await r.json();
    if (d.success) { showToast('✅ Meta guardada'); closeModal(); loadGoals(); }
    else showToast('❌ Error');
}

async function saveProgress() {
    const id  = document.getElementById('progressGoalId').value;
    const val = document.getElementById('progressValue').value;
    const r = await fetch(`/api/goals/${id}/progress`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({current_value: val}) });
    const d = await r.json();
    if (d.success) {
        showToast('✅ Progreso actualizado' + (d.achievement ? ` 🏅 ¡Lograste "${d.achievement}"!` : ''));
        closeProgressModal(); loadGoals();
    }
}

async function deleteGoal(id) {
    if (!confirm('¿Eliminar esta meta?')) return;
    await fetch(`/api/goals/${id}`, { method:'DELETE' });
    showToast('🗑️ Meta eliminada'); loadGoals();
}

function showToast(msg) {
    const t = document.createElement('div'); t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#FF4500;color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;font-size:13px;';
    document.body.appendChild(t); setTimeout(() => t.remove(), 3000);
}

loadGoals();
</script>
@endsection
