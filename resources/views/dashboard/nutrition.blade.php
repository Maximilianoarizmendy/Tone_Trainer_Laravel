@extends('layouts.dashboard')
@section('title', 'Nutrición')
@section('page-title', '🥗 Plan de Nutrición')

@section('styles')
<style>
.days-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
.day-tab { padding: 8px 18px; border-radius: 20px; border: 1px solid var(--border); background: var(--surface); color: var(--muted); font-size: 13px; cursor: pointer; transition: all .15s; font-family: 'Poppins', sans-serif; }
.day-tab:hover { border-color: var(--primary); color: var(--primary); }
.day-tab.active { background: var(--primary); border-color: var(--primary); color: #fff; font-weight: 600; }
.meal-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; margin-bottom: 16px; }
.meal-type-header { font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.meal-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--surface2); gap: 16px; }
.meal-row:last-child { border-bottom: none; }
.meal-name { font-size: 14px; font-weight: 500; color: var(--text); }
.meal-macros { display: flex; gap: 10px; }
.macro-badge { font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
.macro-cal { background: rgba(255,69,0,.12); color: #ff7e5f; }
.macro-prot { background: rgba(59,130,246,.12); color: #60a5fa; }
.macro-carb { background: rgba(251,191,36,.12); color: #fbbf24; }
.macro-fat  { background: rgba(168,85,247,.12); color: #c084fc; }
.btn-del-meal { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; padding: 4px; border-radius: 4px; transition: background .15s; }
.btn-del-meal:hover { background: rgba(239,68,68,.12); }
.add-meal-form { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; max-width: 600px; margin-bottom: 24px; }
.add-meal-form h3 { color: var(--primary); font-size: 15px; font-weight: 600; margin-bottom: 18px; }
.form-row { display: flex; gap: 12px; flex-wrap: wrap; }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
.form-group input, .form-group select { width: 100%; padding: 10px 14px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-size: 13px; font-family: 'Poppins', sans-serif; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
.btn-primary { padding: 11px 24px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-danger { padding: 11px 24px; background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); border-radius: 8px; color: #f87171; font-size: 13px; font-weight: 500; cursor: pointer; margin-left: 10px; }
.targets-card { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
.target-badge { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 20px; text-align: center; flex: 1; min-width: 120px; }
.target-val { font-size: 22px; font-weight: 700; color: var(--primary); }
.target-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }
.empty-state { text-align: center; padding: 40px; color: var(--muted); }
</style>
@endsection

@section('content')
<div class="days-tabs" id="daysTabs">
    @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $day)
        <button class="day-tab {{ $loop->first ? 'active' : '' }}" onclick="selectDay('{{ $day }}', this)">{{ $day }}</button>
    @endforeach
</div>

{{-- Resumen de macros del día --}}
<div class="targets-card" id="dailyTotals">
    <div class="target-badge"><div class="target-val" id="totalCal">0</div><div class="target-lbl">Calorías</div></div>
    <div class="target-badge"><div class="target-val" id="totalProt">0g</div><div class="target-lbl">Proteínas</div></div>
    <div class="target-badge"><div class="target-val" id="totalCarb">0g</div><div class="target-lbl">Carbos</div></div>
    <div class="target-badge"><div class="target-val" id="totalFat">0g</div><div class="target-lbl">Grasas</div></div>
</div>


{{-- Formulario agregar (Admin/Nutri) --}}
@if($user->isAdmin() || $user->isNutritionist())
<div class="add-meal-form">
    <h3>➕ Agregar Comida</h3>
    <div class="form-row">
        <div class="form-group" style="flex:2; min-width:180px;">
            <label>Alimento</label>
            <input type="text" id="foodName" placeholder="Ej: Pechuga de pollo">
        </div>
        <div class="form-group" style="flex:1; min-width:130px;">
            <label>Tipo de comida</label>
            <select id="mealTime">
                @foreach(['Desayuno','Media Mañana','Almuerzo','Merienda','Cena'] as $m)
                    <option>{{ $m }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-row">
        @foreach(['calories' => 'Calorías (kcal)', 'protein' => 'Proteínas (g)', 'carbs' => 'Carbos (g)', 'fats' => 'Grasas (g)'] as $key => $label)
        <div class="form-group" style="flex:1; min-width:90px;">
            <label>{{ $label }}</label>
            <input type="number" id="{{ $key }}" placeholder="0" min="0">
        </div>
        @endforeach
    </div>
    <button class="btn-primary" onclick="addMeal()">Agregar Comida</button>
    <button class="btn-danger" onclick="resetPlan()">🗑️ Resetear Plan</button>
</div>
@endif

{{-- Diario de Consumo (solo para el usuario) --}}
@if($user->isUser())
<div class="section-header" style="margin-top: 30px;">
    <h2>🍽️ Diario de Consumo (Hoy)</h2>
</div>
<div class="add-meal-form">
    <div class="form-row">
        <div class="form-group" style="flex:2;">
            <input type="text" id="logFoodName" placeholder="¿Qué comiste hoy?">
        </div>
        <div class="form-group" style="flex:1;">
            <input type="number" id="logServings" placeholder="Porciones (ej: 1)" step="0.1" min="0.1">
        </div>
        <button class="btn-primary" onclick="logFood()">Registrar</button>
    </div>
</div>
<div id="foodLogsContainer"></div>
@endif

{{-- Lista de comidas del plan --}}
<div class="section-header" style="margin-top: 30px;">
    <h2>📅 Plan de la Semana</h2>
</div>
<div id="mealsContainer"></div>
@endsection

@section('scripts')
<script>
let currentDay = 'Lunes';
let allMeals   = [];
const targetUserId = new URLSearchParams(window.location.search).get('user_id') || '';

function selectDay(day, btn) {
    currentDay = day;
    document.querySelectorAll('.day-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderMeals();
}

async function loadMeals() {
    let url = '/api/nutrition/meals';
    if (targetUserId) {
        url += `?target_user_id=${targetUserId}`;
    }
    const res  = await fetch(url);
    const data = await res.json();
    if (data.success) { allMeals = data.data; renderMeals(); }
}

function renderMeals() {
    const container = document.getElementById('mealsContainer');
    const dayMeals  = allMeals.filter(m => m.day_of_week === currentDay);

    // Recalcular totales del día
    const tot = dayMeals.reduce((acc, m) => {
        acc.cal  += parseInt(m.calories || 0);
        acc.prot += parseInt(m.protein  || 0);
        acc.carb += parseInt(m.carbs    || 0);
        acc.fat  += parseInt(m.fats     || 0);
        return acc;
    }, {cal:0, prot:0, carb:0, fat:0});
    document.getElementById('totalCal').textContent  = tot.cal;
    document.getElementById('totalProt').textContent = tot.prot + 'g';
    document.getElementById('totalCarb').textContent = tot.carb + 'g';
    document.getElementById('totalFat').textContent  = tot.fat  + 'g';

    if (!dayMeals.length) {
        container.innerHTML = '<div class="empty-state"><div style="font-size:40px">🍽️</div><p>No hay comidas registradas para este día.</p></div>';
        return;
    }

    const mealTypes = ['Desayuno','Media Mañana','Almuerzo','Merienda','Cena'];
    let html = '';
    mealTypes.forEach(type => {
        const meals = dayMeals.filter(m => m.meal_type === type);
        if (!meals.length) return;
        html += `<div class="meal-card"><div class="meal-type-header">🍽️ ${type}</div>`;
        meals.forEach(m => {
            html += `<div class="meal-row">
                <div class="meal-name">${m.food_name}</div>
                <div class="meal-macros">
                    <span class="macro-badge macro-cal">${m.calories} kcal</span>
                    <span class="macro-badge macro-prot">P: ${m.protein}g</span>
                    <span class="macro-badge macro-carb">C: ${m.carbs}g</span>
                    <span class="macro-badge macro-fat">G: ${m.fats}g</span>
                </div>
                @if($user->isAdmin() || $user->isNutritionist())
                <button class="btn-del-meal" onclick="deleteMeal(${m.id})">🗑️</button>
                @endif
            </div>`;
        });
        html += '</div>';
    });
    container.innerHTML = html;
}

async function addMeal() {
    const body = {
        day:       currentDay,
        mealTime:  document.getElementById('mealTime').value,
        foodName:  document.getElementById('foodName').value,
        calories:  document.getElementById('calories').value,
        protein:   document.getElementById('protein').value,
        carbs:     document.getElementById('carbs').value,
        fats:      document.getElementById('fats').value,
        target_user_id: targetUserId || null,
    };
    if (!body.foodName || !body.calories) { showToast('⚠️ Nombre y calorías son obligatorios'); return; }
    const r = await fetch('/api/nutrition/meals', {
        method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body)
    });
    const d = await r.json();
    if (d.success) { showToast('✅ Comida añadida'); loadMeals(); ['foodName','calories','protein','carbs','fats'].forEach(id => document.getElementById(id).value = ''); }
    else showToast('❌ ' + (d.error || 'Error'));
}

async function deleteMeal(id) {
    if (!confirm('¿Eliminar esta comida?')) return;
    let url = `/api/nutrition/meals/${id}`;
    if (targetUserId) {
        url += `?target_user_id=${targetUserId}`;
    }
    await fetch(url, { method: 'DELETE' });
    showToast('🗑️ Comida eliminada'); loadMeals();
}

// === DIARIO DE CONSUMO ===
let foodLogs = [];

async function loadFoodLogs() {
    const res = await fetch('/api/food-logs');
    const data = await res.json();
    if (data.success) {
        foodLogs = data.data;
        renderFoodLogs();
    }
}

function renderFoodLogs() {
    const container = document.getElementById('foodLogsContainer');
    if (!container) return;
    
    if (!foodLogs.length) {
        container.innerHTML = '<div class="empty-state"><p>No has registrado comidas hoy.</p></div>';
        return;
    }

    let html = '<div class="meal-card">';
    foodLogs.forEach(log => {
        html += `<div class="meal-row">
            <div style="display:flex; align-items:center; gap: 10px;">
                <input type="checkbox" ${log.is_consumed ? 'checked' : ''} onchange="toggleFoodLog(${log.id}, this.checked)" style="accent-color: var(--primary); width:18px; height:18px; cursor:pointer;">
                <div class="meal-name" style="${log.is_consumed ? 'text-decoration: line-through; color: var(--muted);' : ''}">${log.food_name} <small>(${log.servings} porciones)</small></div>
            </div>
            <button class="btn-del-meal" onclick="deleteFoodLog(${log.id})">🗑️</button>
        </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

async function logFood() {
    const foodName = document.getElementById('logFoodName').value;
    const servings = document.getElementById('logServings').value || 1;
    if (!foodName) return showToast('⚠️ Ingresa el nombre del alimento');

    const res = await fetch('/api/food-logs', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ food_name: foodName, servings })
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('logFoodName').value = '';
        document.getElementById('logServings').value = '';
        loadFoodLogs();
        showToast('✅ Añadido a tu diario');
    }
}

async function toggleFoodLog(id, isConsumed) {
    await fetch(`/api/food-logs/${id}/consume`, {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ is_consumed: isConsumed })
    });
    loadFoodLogs();
}

async function deleteFoodLog(id) {
    if(!confirm('¿Eliminar registro?')) return;
    await fetch(`/api/food-logs/${id}`, { method: 'DELETE' });
    loadFoodLogs();
}

async function resetPlan() {
    if (!confirm('¿Resetear todo el plan nutricional?')) return;
    let url = '/api/nutrition/meals';
    if (targetUserId) {
        url += `?target_user_id=${targetUserId}`;
    }
    await fetch(url, { method: 'DELETE' });
    showToast('🔄 Plan reseteado'); loadMeals();
}

function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#FF4500;color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;font-size:13px;';
    document.body.appendChild(t); setTimeout(() => t.remove(), 2500);
}

loadMeals();
if(typeof loadFoodLogs === 'function') loadFoodLogs();
</script>
@endsection
