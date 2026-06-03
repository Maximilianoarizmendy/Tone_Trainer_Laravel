@extends('layouts.dashboard')
@section('title', 'Reportes del Gimnasio')
@section('page-title', '📊 Reportes de Desempeño')

@section('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 20px; text-align: center; }
.stat-icon { font-size: 32px; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 800; color: #fff; }
.stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; }
.reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.report-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 20px; }
.report-card h4 { color: #fff; margin: 0 0 16px; font-size: 14px; font-weight: 600; }
canvas { max-height: 220px; }
.recent-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.recent-table th { text-align: left; color: var(--muted); font-weight: 600; padding: 8px 0; border-bottom: 1px solid var(--border); font-size: 11px; text-transform: uppercase; }
.recent-table td { padding: 10px 0; border-bottom: 1px solid var(--border); color: #fff; }
.recent-table tr:last-child td { border-bottom: none; }
.broadcast-form { background: var(--surface); border: 1px solid rgba(250,204,21,.3); border-radius: 14px; padding: 20px; margin-top: 20px; }
.broadcast-form h4 { color: #facc15; margin: 0 0 12px; font-size: 14px; }
.broadcast-form textarea { width: 100%; padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: #fff; font-size: 13px; resize: vertical; min-height: 70px; font-family: inherit; box-sizing: border-box; }
.btn-broadcast { margin-top: 10px; padding: 10px 24px; background: #facc15; color: #000; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; }
@media(max-width:700px){ .reports-grid{ grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@if(session('success'))
<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#22c55e;font-size:13px;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- ESTADÍSTICAS PRINCIPALES --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ $totalUsers }}</div>
        <div class="stat-label">Total Usuarios</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $activeUsers }}</div>
        <div class="stat-label">Usuarios Activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏋️</div>
        <div class="stat-value">{{ $totalTrainers }}</div>
        <div class="stat-label">Entrenadores</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-label">Ingresos Totales (USD)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏅</div>
        <div class="stat-value">{{ $totalChallenges }}</div>
        <div class="stat-label">Retos Creados</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🔥</div>
        <div class="stat-value">{{ $activeChallenges }}</div>
        <div class="stat-label">Retos Activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎯</div>
        <div class="stat-value">{{ $completedRetos }}</div>
        <div class="stat-label">Retos Completados</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-value">{{ $totalBadges }}</div>
        <div class="stat-label">Insignias Otorgadas</div>
    </div>
</div>

{{-- GRÁFICOS --}}
<div class="reports-grid">
    <div class="report-card">
        <h4>📈 Nuevos Usuarios (últimos 6 meses)</h4>
        <canvas id="chartUsers"></canvas>
    </div>
    <div class="report-card">
        <h4>💳 Últimos Pagos</h4>
        <table class="recent-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lastPayments as $p)
                <tr>
                    <td style="color:var(--muted);font-size:11px;">#{{ $p->id }}</td>
                    <td style="color:#22c55e;font-weight:700;">
                        ${{ $p->amount_cents ? number_format($p->amount_cents/100, 2) : number_format($p->amount ?? 0, 2) }}
                    </td>
                    <td>
                        <span style="background:rgba(34,197,94,.15);color:#22c55e;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">
                            {{ $p->status ?? 'paid' }}
                        </span>
                    </td>
                    <td style="color:var(--muted);font-size:11px;">{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="color:var(--muted);text-align:center;padding:20px 0;">Sin pagos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- COMUNICADO GENERAL (Req 33) --}}
<div class="broadcast-form">
    <h4>📢 Enviar Comunicado a Todos los Usuarios</h4>
    <form action="{{ route('dashboard.admin.broadcast') }}" method="POST">
        @csrf
        <textarea name="message" required placeholder="Escribe aquí el comunicado para todos los usuarios (máximo 500 caracteres)..." maxlength="500"></textarea>
        <div>
            <button type="submit" class="btn-broadcast">📣 Enviar Comunicado</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const rawData = @json($newUsersPerMonth);
const labels = rawData.map(d => months[d.month - 1] + ' ' + d.year);
const values = rawData.map(d => d.total);

new Chart(document.getElementById('chartUsers'), {
    type: 'line',
    data: {
        labels: labels.length ? labels : ['Sin datos'],
        datasets: [{
            label: 'Nuevos Usuarios',
            data: values.length ? values : [0],
            borderColor: '#ff4500',
            backgroundColor: 'rgba(255,69,0,.12)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ff4500',
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#9ca3af' } } },
        scales: {
            x: { ticks: { color: '#9ca3af' }, grid: { color: '#1e1e1e' } },
            y: { ticks: { color: '#9ca3af', stepSize: 1 }, grid: { color: '#1e1e1e' }, beginAtZero: true }
        }
    }
});
</script>
@endsection