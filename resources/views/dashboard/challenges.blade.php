@extends('layouts.dashboard')
@section('title', 'Retos')
@section('page-title', '🏅 Retos')

@section('styles')
<style>
.challenges-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.ch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.ch-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 20px; display: flex; flex-direction: column; gap: 12px; transition: border-color .2s; }
.ch-card:hover { border-color: var(--primary); }
.ch-type { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: 3px 10px; border-radius: 20px; display: inline-block; }
.type-semanal  { background: rgba(59,130,246,.15); color: #60a5fa; }
.type-mensual  { background: rgba(168,85,247,.15); color: #c084fc; }
.ch-title { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
.ch-desc  { font-size: 13px; color: var(--muted); margin: 0; }
.ch-meta  { display: flex; gap: 16px; font-size: 12px; color: var(--muted); }
.ch-meta span { display: flex; align-items: center; gap: 4px; }
.progress-bar-wrap { background: var(--surface2); border-radius: 8px; height: 8px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, var(--primary), #ff6347); border-radius: 8px; transition: width .4s ease; }
.ch-actions { display: flex; gap: 8px; margin-top: auto; padding-top: 8px; border-top: 1px solid var(--border); }
.btn-join { flex: 1; padding: 9px; border: none; border-radius: 8px; background: linear-gradient(90deg, var(--primary), #ff6347); color: #fff; font-weight: 600; font-size: 13px; cursor: pointer; }
.btn-join:disabled { opacity: .5; cursor: default; }
.btn-progress { flex: 1; padding: 9px; border: 1px solid var(--primary); border-radius: 8px; background: transparent; color: var(--primary); font-weight: 600; font-size: 13px; cursor: pointer; }
.badge-done { display: inline-flex; align-items: center; gap: 6px; background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); color: #22c55e; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; }
/* Entrenador: formulario */
.form-section { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 24px; margin-bottom: 28px; }
.form-section h3 { color: #fff; margin: 0 0 16px; font-size: 16px; }
.form-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg label { font-size: 12px; color: var(--muted); }
.fg input, .fg select, .fg textarea { padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: #fff; font-size: 13px; font-family: inherit; }
.fg textarea { resize: vertical; min-height: 70px; }
.btn-create { padding: 10px 28px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; margin-top: 4px; }
.btn-del { padding: 7px 12px; background: rgba(239,68,68,.15); border: none; border-radius: 8px; color: #dc3545; cursor: pointer; font-size: 12px; }
@media(max-width:600px){ .form-2col{ grid-template-columns:1fr; } .ch-grid{ grid-template-columns:1fr; } }
</style>
@endsection

@section('content')
@if(session('success'))
<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#22c55e;font-size:13px;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- ===== VISTA ENTRENADOR ===== --}}
@if(auth()->user()->role === 4)

<div class="form-section">
    <h3><i class="bi bi-plus-circle"></i> Crear Nuevo Reto</h3>
    <form action="{{ route('dashboard.challenges.store') }}" method="POST">
        @csrf
        <div class="form-2col">
            <div class="fg">
                <label>Título del Reto *</label>
                <input type="text" name="title" required placeholder="Ej: 30 días de sentadillas">
            </div>
            <div class="fg">
                <label>Tipo *</label>
                <select name="type" required>
                    <option value="semanal">Semanal</option>
                    <option value="mensual">Mensual</option>
                </select>
            </div>
            <div class="fg">
                <label>Tipo de Meta *</label>
                <input type="text" name="goal_type" required placeholder="Ej: días, repeticiones, km">
            </div>
            <div class="fg">
                <label>Valor de la Meta *</label>
                <input type="number" name="goal_value" required min="1" placeholder="Ej: 30">
            </div>
            <div class="fg">
                <label>Fecha Inicio *</label>
                <input type="date" name="start_date" required value="{{ now()->toDateString() }}">
            </div>
            <div class="fg">
                <label>Fecha Fin *</label>
                <input type="date" name="end_date" required>
            </div>
        </div>
        <div class="fg" style="margin-top:10px;">
            <label>Descripción</label>
            <textarea name="description" placeholder="Describe el reto (opcional)..."></textarea>
        </div>
        <div style="margin-top:14px;">
            <button type="submit" class="btn-create"><i class="bi bi-trophy"></i> Publicar Reto</button>
        </div>
    </form>
</div>

<h3 style="color:#fff;margin-bottom:16px;">Mis Retos Publicados</h3>
<div class="ch-grid">
    @forelse($challenges as $ch)
    <div class="ch-card">
        <div>
            <span class="ch-type type-{{ $ch->type }}">{{ ucfirst($ch->type) }}</span>
            <span style="font-size:11px;color:var(--muted);float:right;">{{ $ch->participants }} participantes</span>
        </div>
        <p class="ch-title">{{ $ch->title }}</p>
        <p class="ch-desc">{{ $ch->description ?: 'Sin descripción.' }}</p>
        <div class="ch-meta">
            <span><i class="bi bi-bullseye"></i> Meta: {{ $ch->goal_value }} {{ $ch->goal_type }}</span>
            <span><i class="bi bi-calendar3"></i> Hasta {{ \Carbon\Carbon::parse($ch->end_date)->format('d/m/Y') }}</span>
        </div>
        <div class="ch-actions">
            <form action="{{ route('dashboard.challenges.destroy', $ch->id) }}" method="POST" style="flex:1">
                @csrf @method('DELETE')
                <button type="submit" class="btn-del" style="width:100%;" onclick="return confirm('¿Eliminar este reto?')">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="color:var(--muted);padding:32px;text-align:center;grid-column:1/-1;">Aún no has creado ningún reto.</div>
    @endforelse
</div>

{{-- ===== VISTA USUARIO ===== --}}
@else

<div class="challenges-header">
    <h2 style="color:#fff;margin:0;">Retos Disponibles</h2>
    <span style="color:var(--muted);font-size:13px;">Inscríbete y gana insignias 🏆</span>
</div>

<div class="ch-grid">
    @forelse($challenges as $ch)
    @php $enrolled = in_array($ch->id, $enrolledIds ?? []); $pivot = $user->challenges()->where('challenge_id',$ch->id)->first(); @endphp
    <div class="ch-card" id="ch-{{ $ch->id }}">
        <div>
            <span class="ch-type type-{{ $ch->type }}">{{ ucfirst($ch->type) }}</span>
            @if($enrolled && $pivot?->pivot?->completed)
                <span class="badge-done" style="float:right;">🏆 Completado</span>
            @endif
        </div>
        <p class="ch-title">{{ $ch->title }}</p>
        <p class="ch-desc">{{ $ch->description ?: 'Sin descripción.' }}</p>
        <div class="ch-meta">
            <span><i class="bi bi-bullseye"></i> Meta: {{ $ch->goal_value }} {{ $ch->goal_type }}</span>
            <span><i class="bi bi-calendar3"></i> Hasta {{ \Carbon\Carbon::parse($ch->end_date)->format('d/m/Y') }}</span>
            <span><i class="bi bi-person"></i> {{ $ch->trainer?->name ?? '—' }}</span>
        </div>

        @if($enrolled)
        @php $prog = $pivot?->pivot?->current_progress ?? 0; $pct = min(100, round($prog / $ch->goal_value * 100)); @endphp
        <div>
            <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:4px;">
                <span>Progreso</span><span>{{ $prog }} / {{ $ch->goal_value }} {{ $ch->goal_type }} ({{ $pct }}%)</span>
            </div>
            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ $pct }}%"></div></div>
        </div>
        @endif

        <div class="ch-actions">
            @if(!$enrolled)
                <button class="btn-join" onclick="joinChallenge({{ $ch->id }}, this)">
                    <i class="bi bi-lightning"></i> Inscribirse
                </button>
            @elseif(!($pivot?->pivot?->completed))
                <div style="display:flex;gap:8px;align-items:center;flex:1;">
                    <input type="number" id="prog-{{ $ch->id }}" class="fg input" style="flex:1;padding:8px 10px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:#fff;font-size:13px;" placeholder="Nuevo progreso..." min="0" max="{{ $ch->goal_value }}" step="0.5">
                    <button class="btn-progress" onclick="updateProgress({{ $ch->id }}, {{ $ch->goal_value }}, this)">Actualizar</button>
                </div>
            @else
                <span class="badge-done" style="width:100%;justify-content:center;">🏅 ¡Insignia ganada!</span>
            @endif
        </div>
    </div>
    @empty
    <div style="color:var(--muted);padding:48px;text-align:center;grid-column:1/-1;">
        <div style="font-size:48px;margin-bottom:12px;">🏅</div>
        <p>No hay retos activos en este momento.</p>
    </div>
    @endforelse
</div>
@endif
@endsection

@section('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function joinChallenge(id, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Inscribiendo...';
    const r = await fetch(`/dashboard/retos/${id}/join`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
    });
    const d = await r.json();
    if (d.success) {
        showToast('¡Inscripción exitosa! 🎉');
        setTimeout(() => location.reload(), 1000);
    } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning"></i> Inscribirse';
        showToast(d.message, 'error');
    }
}

async function updateProgress(id, goal, btn) {
    const val = parseFloat(document.getElementById(`prog-${id}`).value);
    if (isNaN(val) || val < 0) { showToast('Ingresa un valor válido.', 'error'); return; }
    btn.disabled = true;
    const r = await fetch(`/dashboard/retos/${id}/progress`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ progress: val })
    });
    const d = await r.json();
    showToast(d.message, d.completed ? 'success' : 'info');
    setTimeout(() => location.reload(), 1200);
}

function showToast(msg, type='success') {
    const t = document.createElement('div');
    const colors = { success: '#22c55e', error: '#ef4444', info: '#60a5fa' };
    t.style.cssText = `position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:10px;background:var(--surface);border:1px solid ${colors[type]||colors.success};color:${colors[type]||colors.success};font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.4);`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
@endsection
