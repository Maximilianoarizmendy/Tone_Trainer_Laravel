@extends('layouts.dashboard')
@section('title', 'Progreso Físico')
@section('page-title', '📈 Progreso Físico')

@section('styles')
<style>
    /* ── Header Banner ── */
    .progress-header-banner {
        background: linear-gradient(135deg, rgba(255, 69, 0, 0.12), rgba(0, 0, 0, 0.4));
        border: 1px solid rgba(255, 69, 0, 0.25);
        border-radius: var(--radius);
        padding: 20px 24px; margin-bottom: 28px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px;
    }
    .progress-header-banner .user-info h2 {
        color: #fff; font-size: 20px; font-weight: 700; margin: 0 0 4px;
        display: flex; align-items: center; gap: 10px;
    }
    .progress-header-banner .user-info p {
        color: var(--muted); font-size: 13px; margin: 0;
    }

    /* ── Metrics Grid ── */
    .metrics-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px; margin-bottom: 28px;
    }
    .metric-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 20px 16px; text-align: center; transition: transform .2s, border-color .2s;
        box-shadow: var(--shadow);
    }
    .metric-card:hover { transform: translateY(-3px); border-color: var(--primary); }
    .metric-icon { font-size: 30px; margin-bottom: 8px; }
    .metric-val { font-size: 26px; font-weight: 800; color: #fff; line-height: 1.1; }
    .metric-lbl { font-size: 12px; color: var(--muted); margin-top: 6px; font-weight: 500; }
    .metric-trend { font-size: 11px; margin-top: 6px; font-weight: 600; }
    .trend-up   { color: #22c55e; }
    .trend-down { color: #ef4444; }
    .trend-neutral { color: var(--muted); }

    /* ── Chart Cards ── */
    .chart-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow);
    }
    .chart-card h3 {
        font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 18px;
        display: flex; align-items: center; gap: 10px;
    }
    .chart-card h3 i { color: var(--primary); }
    canvas { max-height: 250px; width: 100% !important; }

    /* ── Form Section ── */
    .update-form {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 28px; margin-bottom: 28px; box-shadow: var(--shadow);
    }
    .update-form h3 {
        color: var(--primary); font-size: 16px; font-weight: 700; margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px;
    }
    .form-row { display: flex; gap: 16px; flex-wrap: wrap; }
    .form-group { flex: 1; min-width: 140px; margin-bottom: 16px; }
    .form-group label {
        display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .form-group input, .form-group textarea {
        width: 100%; padding: 12px 14px; background: var(--surface2);
        border: 1px solid var(--border); color: #fff; border-radius: 10px;
        font-size: 13px; font-family: 'Poppins', sans-serif; transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
    .btn-primary {
        padding: 12px 28px; background: linear-gradient(135deg, var(--primary), #ff6347);
        border: none; border-radius: 10px; color: #fff; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: transform .15s, box-shadow .15s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 69, 0, 0.3); }

    /* ── Empty State ── */
    .empty-progress-state {
        background: var(--surface); border: 1px dashed var(--border); border-radius: var(--radius);
        padding: 40px 20px; text-align: center; color: var(--muted); margin-bottom: 28px;
    }
    .empty-progress-state i { font-size: 42px; color: var(--border); margin-bottom: 12px; display: block; }
    .empty-progress-state p { font-size: 14px; margin: 0; }

    /* ── Table Historial ── */
    .history-table-wrap {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        overflow-x: auto; box-shadow: var(--shadow); margin-bottom: 28px;
    }
    .history-table { width: 100%; border-collapse: collapse; text-align: left; }
    .history-table thead { background: var(--surface2); }
    .history-table th {
        padding: 14px 16px; font-size: 11px; text-transform: uppercase;
        letter-spacing: 1px; color: var(--muted); font-weight: 600;
    }
    .history-table td { padding: 14px 16px; font-size: 13px; border-top: 1px solid var(--border); color: #fff; }
    .history-table tbody tr:hover { background: var(--surface2); }

    .badge-val {
        display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
    }
    .badge-val.ok { background: rgba(34,197,94,.15); color: #22c55e; border: 1px solid rgba(34,197,94,.3); }
    .badge-val.pending { background: rgba(250,204,21,.15); color: #facc15; border: 1px solid rgba(250,204,21,.3); }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')

@php
    $target = $targetUser ?? $user;
    $isViewingClient = ($target->id !== $user->id);
@endphp

<!-- Header Banner -->
<div class="progress-header-banner">
    <div class="user-info">
        <h2>
            <i class="bi bi-graph-up-arrow"></i>
            {{ $isViewingClient ? 'Progreso de ' . $target->name : 'Mi Progreso Físico' }}
        </h2>
        <p>
            {{ $isViewingClient ? 'Monitoreo y evaluación del desempeño de ' . $target->name : 'Haz seguimiento a tus métricas corporales y observa tu evolución día a día.' }}
        </p>
    </div>
    @if($isViewingClient)
        <a href="{{ route('dashboard.users') }}" class="btn-primary" style="background: var(--surface2); border: 1px solid var(--border);">
            <i class="bi bi-arrow-left"></i> Volver a Clientes
        </a>
    @endif
</div>

<!-- Métricas Principales -->
<div class="metrics-grid" id="metricsGrid">
    @foreach([
        ['⚖️','Peso','weight','kg'],
        ['💪','Músculo','muscle_mass','%'],
        ['🔥','Grasa','body_fat','%'],
        ['📊','IMC','bmi',''],
        ['💧','Agua','water_intake','L'],
        ['🥩','Proteína','protein_intake','g']
    ] as [$icon,$label,$key,$unit])
    <div class="metric-card">
        <div class="metric-icon">{{ $icon }}</div>
        <div class="metric-val" id="val_{{ $key }}">—</div>
        <div class="metric-lbl">{{ $label }}{{ $unit ? " ($unit)" : '' }}</div>
        <div class="metric-trend" id="trend_{{ $key }}"></div>
    </div>
    @endforeach
</div>

<!-- Alerta de estado vacío -->
<div id="emptyStateNotice" class="empty-progress-state" style="display: none;">
    <i class="bi bi-clipboard-data"></i>
    <p>Aún no hay registros de progreso almacenados. Completa el formulario para ingresar la primera medición.</p>
</div>

<!-- Feedback de Validación de Entrenador -->
<div id="validationFeedback" style="display:none; margin-bottom: 24px; padding: 18px 24px; background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); border-radius: var(--radius);">
    <h4 style="color:#22c55e; margin:0 0 6px 0; font-size: 15px; font-weight: 700; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-patch-check-fill"></i> Validado por el entrenador
    </h4>
    <p id="validationCommentText" style="margin:0; font-size:13px; color:#e2e8f0; line-height: 1.5;"></p>
</div>

<!-- Gráficas -->
<div class="chart-card">
    <h3><i class="bi bi-graph-up"></i> Evolución del Peso</h3>
    <canvas id="weightChart"></canvas>
</div>
<div class="chart-card">
    <h3><i class="bi bi-bar-chart-fill"></i> Composición Corporal (Grasa vs Músculo)</h3>
    <canvas id="compositionChart"></canvas>
</div>

<!-- Comparación de Períodos -->
<div class="chart-card" id="compareSection">
    <h3><i class="bi bi-calendar-range"></i> Comparar Períodos de Progreso</h3>
    <div class="form-row" style="align-items: flex-end;">
        <div class="form-group"><label>Desde</label><input type="date" id="compFrom"></div>
        <div class="form-group"><label>Hasta</label><input type="date" id="compTo"></div>
        <div class="form-group"><button class="btn-primary" onclick="compareProgress()"><i class="bi bi-search"></i> Comparar</button></div>
    </div>
    <div id="compareResults" style="display:none; margin-top: 24px;">
        <canvas id="compareChart"></canvas>
    </div>
</div>

<!-- Formulario para Registrar Métricas -->
<div class="update-form">
    <h3><i class="bi bi-journal-plus"></i> Registrar Métricas Físicas</h3>
    <div class="form-row">
        <div class="form-group"><label>Peso (kg) *</label><input type="number" id="inp_weight" step="0.1" placeholder="Ej: 72.5" required></div>
        <div class="form-group"><label>Grasa corporal (%)</label><input type="number" id="inp_fat" step="0.1" placeholder="Ej: 14.5"></div>
        <div class="form-group"><label>Masa muscular (%)</label><input type="number" id="inp_muscle" step="0.1" placeholder="Ej: 42.0"></div>
    </div>
    <div class="form-row">
        <div class="form-group"><label>IMC (opcional u auto)</label><input type="number" id="inp_bmi" step="0.01" placeholder="Ej: 22.4"></div>
        <div class="form-group"><label>Agua diaria (L)</label><input type="number" id="inp_water" step="0.1" placeholder="Ej: 2.5"></div>
        <div class="form-group"><label>Proteína diaria (g)</label><input type="number" id="inp_protein" step="1" placeholder="Ej: 130"></div>
    </div>
    <button class="btn-primary" onclick="saveMetrics()"><i class="bi bi-save-fill"></i> Guardar Métricas</button>
</div>

<!-- Validación por Staff (Entrenador / Nutricionista / Admin) -->
@if($user->isStaff())
<div class="update-form" style="border-color: rgba(59,130,246,0.3); background: linear-gradient(135deg, rgba(59,130,246,0.05), var(--surface));">
    <h3 style="color: #60a5fa;"><i class="bi bi-award-fill"></i> Validar Progreso y Comentar</h3>
    <div class="form-group">
        <label>Comentario / Retroalimentación para el Cliente</label>
        <textarea id="trainerComment" rows="3" placeholder="Escribe tus observaciones y recomendaciones técnicas sobre el progreso registrado..."></textarea>
    </div>
    <button class="btn-primary" style="background: linear-gradient(135deg, #3b82f6, #2563eb);" onclick="validateProgress()">
        <i class="bi bi-check-circle-fill"></i> Enviar Validación
    </button>
</div>
@endif

<!-- Tabla Historial -->
<div class="chart-card">
    <h3><i class="bi bi-table"></i> Historial de Registros</h3>
    <div class="history-table-wrap">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Peso</th>
                    <th>Grasa</th>
                    <th>Músculo</th>
                    <th>IMC</th>
                    <th>Agua</th>
                    <th>Proteína</th>
                    <th>Estado / Comentario</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                <tr>
                    <td colspan="8" style="text-align:center; color:var(--muted); padding:20px;">Cargando historial…</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
let weightChart = null;
let compositionChart = null;
let compareChartInstance = null;
const targetUserId = @json($target->id);

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

async function loadMetrics() {
    try {
        const url = `/api/progress/metrics?user_id=${targetUserId}`;
        const r = await fetch(url);
        const d = await r.json();

        const emptyNotice = document.getElementById('emptyStateNotice');

        if (!d.success || !d.data || d.data.length === 0) {
            if (emptyNotice) emptyNotice.style.display = 'block';
            renderEmptyHistoryTable();
            return;
        }

        if (emptyNotice) emptyNotice.style.display = 'none';

        const latest = d.data[d.data.length - 1];
        const prev   = d.data.length > 1 ? d.data[d.data.length - 2] : null;

        // Rellenar métricas actuales
        const fields = {
            weight: 'weight',
            muscle_mass: 'muscle_mass',
            body_fat: 'body_fat',
            bmi: 'bmi',
            water_intake: 'water_intake',
            protein_intake: 'protein_intake'
        };

        Object.entries(fields).forEach(([key, src]) => {
            const el = document.getElementById('val_' + key);
            if (el) {
                const val = latest[src];
                el.textContent = (val !== null && val !== undefined && val !== '') ? parseFloat(val).toFixed(1) : '—';
            }
            if (prev && prev[src] !== null && latest[src] !== null) {
                const diff = (parseFloat(latest[src]) - parseFloat(prev[src])).toFixed(1);
                const tEl = document.getElementById('trend_' + key);
                if (tEl) {
                    if (diff > 0) {
                        tEl.textContent = '↑ +' + diff;
                        tEl.className = 'metric-trend trend-up';
                    } else if (diff < 0) {
                        tEl.textContent = '↓ ' + diff;
                        tEl.className = 'metric-trend trend-down';
                    } else {
                        tEl.textContent = '= 0.0';
                        tEl.className = 'metric-trend trend-neutral';
                    }
                }
            }
        });

        // Re-crear Gráficas destruyendo instancias existentes
        const labels  = d.data.map(p => p.date.substring(0, 10));
        const weights = d.data.map(p => p.weight !== null ? parseFloat(p.weight) : null);
        const fats    = d.data.map(p => p.body_fat !== null ? parseFloat(p.body_fat) : null);
        const muscles = d.data.map(p => p.muscle_mass !== null ? parseFloat(p.muscle_mass) : null);

        const chartOpts = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#aaa', font: { family: 'Poppins' } } } },
            scales: {
                x: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        };

        if (weightChart) weightChart.destroy();
        weightChart = new Chart(document.getElementById('weightChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Peso (kg)',
                    data: weights,
                    borderColor: '#FF4500',
                    backgroundColor: 'rgba(255,69,0,0.12)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#FF4500',
                    pointRadius: 4
                }]
            },
            options: chartOpts
        });

        if (compositionChart) compositionChart.destroy();
        compositionChart = new Chart(document.getElementById('compositionChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Grasa (%)', data: fats, backgroundColor: 'rgba(239,68,68,0.7)', borderRadius: 6 },
                    { label: 'Músculo (%)', data: muscles, backgroundColor: 'rgba(34,197,94,0.7)', borderRadius: 6 }
                ]
            },
            options: chartOpts
        });

        // Mostrar comentario de validación si existe
        if (latest.is_validated && latest.trainer_comment) {
            document.getElementById('validationFeedback').style.display = 'block';
            document.getElementById('validationCommentText').textContent = latest.trainer_comment;
        } else {
            document.getElementById('validationFeedback').style.display = 'none';
        }

        window.latestProgressId = latest.id;

        // Renderizar Tabla de Historial
        renderHistoryTable(d.data);

    } catch (err) {
        console.error('Error cargando métricas:', err);
    }
}

function renderHistoryTable(data) {
    const tbody = document.getElementById('historyTableBody');
    if (!tbody) return;

    if (!data || data.length === 0) {
        renderEmptyHistoryTable();
        return;
    }

    // Orden invertido para mostrar los más recientes arriba
    const reversed = [...data].reverse();

    tbody.innerHTML = reversed.map(p => `
        <tr>
            <td style="color: var(--muted); font-size:12px;">${p.date.substring(0, 16)}</td>
            <td><strong>${p.weight ? p.weight + ' kg' : '—'}</strong></td>
            <td>${p.body_fat ? p.body_fat + ' %' : '—'}</td>
            <td>${p.muscle_mass ? p.muscle_mass + ' %' : '—'}</td>
            <td>${p.bmi ? p.bmi : '—'}</td>
            <td>${p.water_intake ? p.water_intake + ' L' : '—'}</td>
            <td>${p.protein_intake ? p.protein_intake + ' g' : '—'}</td>
            <td>
                ${p.is_validated
                    ? `<span class="badge-val ok" title="${p.trainer_comment || ''}"><i class="bi bi-check-circle-fill"></i> Validado</span>`
                    : `<span class="badge-val pending"><i class="bi bi-clock"></i> Pendiente</span>`}
                ${p.trainer_comment ? `<div style="font-size:11px; color:#aaa; margin-top:4px;">"${p.trainer_comment}"</div>` : ''}
            </td>
        </tr>
    `).join('');
}

function renderEmptyHistoryTable() {
    const tbody = document.getElementById('historyTableBody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:var(--muted); padding:24px;">No hay registros de progreso guardados todavía.</td></tr>';
    }
}

async function saveMetrics() {
    const weightVal = document.getElementById('inp_weight').value;

    if (!weightVal || parseFloat(weightVal) <= 0) {
        showToast('⚠️ Ingresa un peso válido (en kg)');
        return;
    }

    const body = {
        user_id: targetUserId,
        weight:  parseFloat(weightVal),
        fat:     document.getElementById('inp_fat').value ? parseFloat(document.getElementById('inp_fat').value) : null,
        muscle:  document.getElementById('inp_muscle').value ? parseFloat(document.getElementById('inp_muscle').value) : null,
        bmi:     document.getElementById('inp_bmi').value ? parseFloat(document.getElementById('inp_bmi').value) : null,
        water:   document.getElementById('inp_water').value ? parseFloat(document.getElementById('inp_water').value) : null,
        protein: document.getElementById('inp_protein').value ? parseFloat(document.getElementById('inp_protein').value) : null,
    };

    try {
        const r = await fetch('/api/progress/metrics', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(body)
        });

        const d = await r.json();

        if (d.success) {
            showToast('✅ Métricas guardadas exitosamente');
            // Limpiar inputs
            ['inp_weight','inp_fat','inp_muscle','inp_bmi','inp_water','inp_protein'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            loadMetrics();
        } else {
            showToast('❌ ' + (d.message || d.error || 'Error al guardar métricas'));
        }
    } catch (err) {
        showToast('❌ Error de conexión al servidor');
    }
}

async function compareProgress() {
    const from = document.getElementById('compFrom').value;
    const to   = document.getElementById('compTo').value;
    if (!from || !to) return showToast('⚠️ Selecciona ambas fechas para comparar');

    const query = `?from=${from}&to=${to}&user_id=${targetUserId}`;

    try {
        const r = await fetch('/api/progress/compare' + query);
        const d = await r.json();

        if (d.success && d.data.length > 0) {
            document.getElementById('compareResults').style.display = 'block';
            const labels = d.data.map(p => p.created_at.substring(0, 10));
            const weights = d.data.map(p => p.weight);

            if (compareChartInstance) compareChartInstance.destroy();
            compareChartInstance = new Chart(document.getElementById('compareChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Evolución del Peso en el Período',
                        data: weights,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.12)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else {
            showToast('⚠️ No se encontraron registros en el rango seleccionado');
        }
    } catch (err) {
        showToast('❌ Error al realizar la comparación');
    }
}

async function validateProgress() {
    if (!window.latestProgressId) return showToast('⚠️ No hay registros de progreso para validar');

    const comment = document.getElementById('trainerComment').value.trim();
    if (!comment) return showToast('⚠️ Escribe un comentario o retroalimentación');

    try {
        const r = await fetch(`/api/progress/metrics/${window.latestProgressId}/validate`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ trainer_comment: comment })
        });

        const d = await r.json();

        if (d.success) {
            showToast('✅ Validación y comentario enviados');
            document.getElementById('trainerComment').value = '';
            loadMetrics();
        } else {
            showToast('❌ ' + (d.message || 'Error al enviar validación'));
        }
    } catch (err) {
        showToast('❌ Error de conexión al servidor');
    }
}

function showToast(msg) {
    const existing = document.getElementById('toastNotification');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.id = 'toastNotification';
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:linear-gradient(135deg, #FF4500, #ff6347);color:#fff;padding:12px 24px;border-radius:10px;z-index:9999;font-size:13px;font-weight:600;box-shadow:0 10px 30px rgba(0,0,0,0.5);font-family:Poppins,sans-serif;';
    document.body.appendChild(t);
    setTimeout(() => { if (t) t.remove(); }, 3000);
}

document.addEventListener('DOMContentLoaded', loadMetrics);
</script>
@endsection
