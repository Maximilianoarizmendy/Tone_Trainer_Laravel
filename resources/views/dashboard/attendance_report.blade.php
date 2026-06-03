@extends('layouts.dashboard')
@section('title', 'Asistencia a Rutinas')
@section('page-title', '📅 Reporte de Asistencia')

@section('styles')
<style>
.controls-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 20px; margin-bottom: 24px;
    display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;
}
.form-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 150px; }
.form-group label { font-size: 13px; color: var(--muted); font-weight: 500; }
.form-group input, .form-group select {
    padding: 10px 14px; background: var(--surface2); border: 1px solid var(--border);
    color: var(--text); border-radius: 8px; font-size: 14px; outline: none;
}
.form-group input:focus, .form-group select:focus { border-color: var(--primary); }
.btn-primary {
    padding: 11px 24px; background: linear-gradient(90deg, var(--primary), #ff6347);
    border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer;
}
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px; }
.summary-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; text-align: center; }
.summary-val { font-size: 28px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
.summary-lbl { font-size: 13px; color: var(--muted); }
.table-container { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 14px 20px; text-align: left; font-size: 14px; border-bottom: 1px solid var(--surface2); }
th { color: var(--muted); font-weight: 500; font-size: 13px; text-transform: uppercase; }
.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-present { background: rgba(34,197,94,.15); color: #22c55e; }
.status-absent { background: rgba(239,68,68,.15); color: #ef4444; }
</style>
@endsection

@section('content')
<div class="controls-card">
    <div class="form-group">
        <label>Desde</label>
        <input type="date" id="filterFrom">
    </div>
    <div class="form-group">
        <label>Hasta</label>
        <input type="date" id="filterTo">
    </div>
    <div class="form-group">
        <label>Usuario (ID opcional)</label>
        <input type="number" id="filterUserId" placeholder="Ej: 5">
    </div>
    <button class="btn-primary" onclick="loadAttendance()">Generar Reporte</button>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-val" id="valTotal">0</div>
        <div class="summary-lbl">Total Registros</div>
    </div>
    <div class="summary-card">
        <div class="summary-val" id="valPresent" style="color:#22c55e">0</div>
        <div class="summary-lbl">Asistencias</div>
    </div>
    <div class="summary-card">
        <div class="summary-val" id="valAbsent" style="color:#ef4444">0</div>
        <div class="summary-lbl">Ausencias</div>
    </div>
    <div class="summary-card">
        <div class="summary-val" id="valAdherence">0%</div>
        <div class="summary-lbl">Adherencia</div>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>ID Plan</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody id="attendanceTableBody">
            <tr><td colspan="4" style="text-align:center; padding:30px; color:#666;">Seleccione filtros y genere el reporte</td></tr>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
async function loadAttendance() {
    const from = document.getElementById('filterFrom').value;
    const to = document.getElementById('filterTo').value;
    const userId = document.getElementById('filterUserId').value;

    let query = '?';
    if(from) query += `from=${from}&`;
    if(to) query += `to=${to}&`;
    if(userId) query += `user_id=${userId}`;

    const r = await fetch('/api/attendance' + query);
    const d = await r.json();

    if(d.success) {
        // Update summary
        document.getElementById('valTotal').textContent = d.data.summary.total;
        document.getElementById('valPresent').textContent = d.data.summary.present;
        document.getElementById('valAbsent').textContent = d.data.summary.absent;
        document.getElementById('valAdherence').textContent = d.data.summary.adherence + '%';

        // Update table
        const tbody = document.getElementById('attendanceTableBody');
        tbody.innerHTML = '';
        
        if(d.data.records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:30px; color:#666;">No hay registros</td></tr>';
            return;
        }

        d.data.records.forEach(rec => {
            const tr = document.createElement('tr');
            const isPresent = rec.status === 'present';
            tr.innerHTML = `
                <td>${rec.date}</td>
                <td>${rec.user ? rec.user.name : 'ID '+rec.user_id}</td>
                <td>${rec.training_plan_id}</td>
                <td><span class="status-badge ${isPresent ? 'status-present' : 'status-absent'}">${isPresent ? 'Presente' : 'Ausente'}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }
}
</script>
@endsection
