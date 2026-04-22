@extends('layouts.dashboard')
@section('title', 'Progreso')
@section('page-title', '📈 Mi Progreso')

@section('styles')
<style>
.metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
.metric-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; text-align: center; transition: border-color .2s; }
.metric-card:hover { border-color: var(--primary); }
.metric-icon { font-size: 28px; margin-bottom: 8px; }
.metric-val { font-size: 24px; font-weight: 700; color: var(--primary); }
.metric-lbl { font-size: 11px; color: var(--muted); margin-top: 4px; }
.metric-trend { font-size: 11px; margin-top: 6px; }
.trend-up   { color: #22c55e; }
.trend-down { color: #ef4444; }
.chart-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; margin-bottom: 20px; }
.chart-card h3 { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.chart-card h3 i { color: var(--primary); }
.update-form { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; max-width: 560px; margin-bottom: 24px; }
.update-form h3 { color: var(--primary); font-size: 15px; font-weight: 600; margin-bottom: 18px; }
.form-row { display: flex; gap: 12px; flex-wrap: wrap; }
.form-group { flex: 1; min-width: 130px; margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
.form-group input { width: 100%; padding: 10px 14px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-size: 13px; font-family: 'Poppins', sans-serif; }
.form-group input:focus { outline: none; border-color: var(--primary); }
.btn-primary { padding: 11px 24px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
canvas { max-height: 220px; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
<div class="metrics-grid" id="metricsGrid">
    @foreach([['⚖️','Peso','weight','kg'],['💪','Músculo','muscle_mass','%'],['🔥','Grasa','body_fat','%'],['📊','IMC','bmi',''],['💧','Agua','water_intake','L'],['🥩','Proteína','protein_intake','g']] as [$icon,$label,$key,$unit])
    <div class="metric-card">
        <div class="metric-icon">{{ $icon }}</div>
        <div class="metric-val" id="val_{{ $key }}">—</div>
        <div class="metric-lbl">{{ $label }}{{ $unit ? " ($unit)" : '' }}</div>
        <div class="metric-trend" id="trend_{{ $key }}"></div>
    </div>
    @endforeach
</div>

{{-- Gráficas --}}
<div class="chart-card">
    <h3><i class="bi bi-graph-up-arrow"></i> Evolución del Peso</h3>
    <canvas id="weightChart"></canvas>
</div>
<div class="chart-card">
    <h3><i class="bi bi-bar-chart-fill"></i> Body Fat vs Masa Muscular</h3>
    <canvas id="compositionChart"></canvas>
</div>

{{-- Formulario actualización --}}
<div class="update-form">
    <h3>📋 Registrar Métricas</h3>
    <div class="form-row">
        <div class="form-group"><label>Peso (kg)</label><input type="number" id="inp_weight" step="0.1" placeholder="70.5"></div>
        <div class="form-group"><label>Grasa corporal (%)</label><input type="number" id="inp_fat" step="0.1" placeholder="15.0"></div>
        <div class="form-group"><label>Músculo (%)</label><input type="number" id="inp_muscle" step="0.1" placeholder="40.0"></div>
    </div>
    <div class="form-row">
        <div class="form-group"><label>IMC</label><input type="number" id="inp_bmi" step="0.01" placeholder="22.5"></div>
        <div class="form-group"><label>Agua (L)</label><input type="number" id="inp_water" step="0.1" placeholder="2.5"></div>
        <div class="form-group"><label>Proteína diaria (g)</label><input type="number" id="inp_protein" step="1" placeholder="120"></div>
    </div>
    <button class="btn-primary" onclick="saveMetrics()">Guardar Métricas</button>
</div>
@endsection

@section('scripts')
<script>
let weightChart, compositionChart;

async function loadMetrics() {
    const r = await fetch('/api/progress/metrics');
    const d = await r.json();
    if (!d.success || !d.data.length) return;

    const latest = d.data[d.data.length - 1];
    const prev   = d.data.length > 1 ? d.data[d.data.length - 2] : null;

    const fields = {weight:'weight', muscle_mass:'muscle_mass', body_fat:'body_fat', bmi:'bmi', water_intake:'water_intake', protein_intake:'protein_intake'};
    Object.entries(fields).forEach(([key, src]) => {
        const el = document.getElementById('val_' + key);
        if (el) el.textContent = parseFloat(latest[src] || 0).toFixed(1);
        if (prev) {
            const diff = (parseFloat(latest[src]) - parseFloat(prev[src])).toFixed(1);
            const tEl = document.getElementById('trend_' + key);
            if (tEl && diff != 0) {
                tEl.textContent = (diff > 0 ? '↑ +' : '↓ ') + diff;
                tEl.className   = 'metric-trend ' + (diff > 0 ? 'trend-up' : 'trend-down');
            }
        }
    });

    // Gráficas
    const labels  = d.data.map(p => p.date.substring(0,10));
    const weights = d.data.map(p => parseFloat(p.weight));
    const fats    = d.data.map(p => parseFloat(p.body_fat));
    const muscles = d.data.map(p => parseFloat(p.muscle_mass));

    const chartOpts = { responsive: true, plugins: { legend: { labels: { color: '#aaa' } } }, scales: { x: { ticks: { color: '#666' }, grid: { color: '#222' } }, y: { ticks: { color: '#666' }, grid: { color: '#222' } } } };

    weightChart = new Chart(document.getElementById('weightChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Peso (kg)', data: weights, borderColor: '#FF4500', backgroundColor: 'rgba(255,69,0,.1)', tension: 0.4, fill: true, pointBackgroundColor: '#FF4500' }] },
        options: chartOpts
    });

    compositionChart = new Chart(document.getElementById('compositionChart'), {
        type: 'bar',
        data: { labels, datasets: [
            { label: 'Grasa (%)', data: fats,    backgroundColor: 'rgba(239,68,68,.6)' },
            { label: 'Músculo (%)', data: muscles, backgroundColor: 'rgba(34,197,94,.6)' }
        ]},
        options: chartOpts
    });
}

async function saveMetrics() {
    const body = {
        weight: document.getElementById('inp_weight').value,
        fat:    document.getElementById('inp_fat').value,
        muscle: document.getElementById('inp_muscle').value,
        bmi:    document.getElementById('inp_bmi').value,
        water:  document.getElementById('inp_water').value,
        protein:document.getElementById('inp_protein').value,
    };
    if (Object.values(body).some(v => !v)) { showToast('⚠️ Completa todos los campos'); return; }
    const r = await fetch('/api/progress/metrics', {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body)
    });
    const d = await r.json();
    if (d.success) {
        showToast('✅ Métricas guardadas');
        weightChart?.destroy(); compositionChart?.destroy();
        loadMetrics();
    } else showToast('❌ ' + (d.error || 'Error'));
}

function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#FF4500;color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;font-size:13px;';
    document.body.appendChild(t); setTimeout(() => t.remove(), 2500);
}

loadMetrics();
</script>
@endsection
