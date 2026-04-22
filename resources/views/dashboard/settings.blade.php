@extends('layouts.dashboard')
@section('title', 'Configuración')
@section('page-title', '⚙️ Configuración')

@section('content')
<div id="settingsAlert"></div>

<div class="settings-section">
    <h2 class="section-title"><i class="bi bi-gear"></i> Notificaciones</h2>
    <div class="settings-item">
        <div class="settings-item-info">
            <span class="settings-item-title">Notificaciones por email</span>
            <span class="settings-item-desc">Recibe actualizaciones de rutinas y nutrición</span>
        </div>
        <label class="switch">
            <input type="checkbox" id="email_notifications" checked>
            <span class="slider"></span>
        </label>
    </div>
    <div class="settings-item">
        <div class="settings-item-info">
            <span class="settings-item-title">Recordatorios de entrenamiento</span>
            <span class="settings-item-desc">Notificaciones diarias de tu rutina</span>
        </div>
        <label class="switch">
            <input type="checkbox" id="workout_reminders" checked>
            <span class="slider"></span>
        </label>
    </div>
    <div class="settings-item last">
        <div class="settings-item-info">
            <span class="settings-item-title">Notificaciones push</span>
            <span class="settings-item-desc">Notificaciones en el navegador</span>
        </div>
        <label class="switch">
            <input type="checkbox" id="push_notifications" checked>
            <span class="slider"></span>
        </label>
    </div>
</div>



<div class="settings-section">
    <h2 class="section-title"><i class="bi bi-lock"></i> Cambiar Contraseña</h2>
    <div class="form-group">
        <label>Contraseña actual</label>
        <input type="password" id="current_password" class="form-control" placeholder="Ingresa tu contraseña actual">
    </div>
    <div class="form-group">
        <label>Nueva contraseña</label>
        <input type="password" id="new_password" class="form-control" placeholder="Mín. 8 caracteres">
    </div>
    <div class="form-group">
        <label>Confirmar nueva contraseña</label>
        <input type="password" id="new_password_confirmation" class="form-control" placeholder="Repite la nueva contraseña">
    </div>
</div>

<div class="settings-section">
    <h2 class="section-title"><i class="bi bi-info-circle"></i> Información de la Cuenta</h2>
    <div class="info-grid">
        <div class="info-card"><span class="info-label">Usuario</span><span class="info-value">{{ auth()->user()->name }}</span></div>
        <div class="info-card"><span class="info-label">Correo</span><span class="info-value">{{ auth()->user()->email }}</span></div>
        <div class="info-card"><span class="info-label">Rol</span><span class="info-value primary">{{ auth()->user()->roleName }}</span></div>
        <div class="info-card"><span class="info-label">Desde</span><span class="info-value">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('d/m/Y') }}</span></div>
    </div>
</div>

<div class="settings-section">
    <button onclick="saveSettings()" class="btn-save">Guardar Cambios</button>
</div>



<style>
.settings-section { background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px; }
.section-title { font-size:14px;font-weight:600;color:#fff;margin-bottom:16px;display:flex;align-items:center;gap:8px; }
.settings-item { display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--surface2); }
.settings-item.last { border-bottom:none; }
.settings-item-title { font-size:13px;color:#fff;display:block; }
.settings-item-desc { font-size:11px;color:var(--muted);display:block;margin-top:2px; }

.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
.info-card { background:var(--surface2);border-radius:8px;padding:12px; }
.info-label { font-size:10px;color:var(--muted);display:block; }
.info-value { font-size:13px;color:#fff;font-weight:600;display:block;margin-top:2px; }
.info-value.primary { color:var(--primary); }
.danger { background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.2); }
.danger-text { color:#f87171; }
.danger-desc { font-size:12px;color:var(--muted);margin-bottom:12px; }
.danger-btns { display:flex;gap:10px;flex-wrap:wrap; }
.btn-save { width:100%;padding:12px;background:var(--primary);border:none;border-radius:8px;color:#fff;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px; }
.form-group { margin-bottom:14px; }
.form-group:last-child { margin-bottom:0; }
.form-group label { font-size:11px;color:var(--muted);display:block;margin-bottom:4px; }
.form-control { width:100%;padding:10px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:#fff;font-size:13px; }
.form-control:focus { border-color:var(--primary);outline:none; }

.switch { position:relative;display:inline-block;width:44px;height:24px; }
.switch input { opacity:0;width:0;height:0; }
.slider { position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:var(--surface2);transition:.3s;border-radius:24px; }
.slider:before { position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background:#fff;transition:.3s;border-radius:50%; }
input:checked + .slider { background:var(--primary); }
input:checked + .slider:before { transform:translateX(20px); }

@media (max-width:600px) {
    .settings-section { padding:16px; }
    .form-row { grid-template-columns:1fr; }
    .info-grid { grid-template-columns:1fr; }
    .danger-btns { flex-direction:column; }
    .btn-logout, .btn-deactivate { width:100%;text-align:center; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', loadSettings);

function loadSettings() {
    fetch('/api/settings', { credentials: 'include', headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(r => { if (r.success && r.data) {
        const d = r.data;
        document.getElementById('email_notifications').checked = d.email_notifications !== false;
        document.getElementById('workout_reminders').checked = d.workout_reminders !== false;
        document.getElementById('push_notifications').checked = d.push_notifications !== false;
        document.getElementById('training_level').value = d.training_level || 'intermediate';
        document.getElementById('weekly_frequency').value = d.weekly_frequency || 3;
        document.getElementById('preferred_schedule').value = d.preferred_schedule || '';
        document.getElementById('goal').value = d.goal || '';
    }});
}

function saveSettings() {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('new_password_confirmation').value;
    
    if (newPass && newPass !== confirmPass) {
        showAlert('Las contraseñas nuevas no coinciden.', 'error');
        return;
    }
    if (newPass && newPass.length < 8) {
        showAlert('La nueva contraseña debe tener al menos 8 caracteres.', 'error');
        return;
    }

    const data = {
        email_notifications: document.getElementById('email_notifications').checked,
        workout_reminders: document.getElementById('workout_reminders').checked,
        push_notifications: document.getElementById('push_notifications').checked,
        training_level: document.getElementById('training_level').value,
        weekly_frequency: parseInt(document.getElementById('weekly_frequency').value),
        preferred_schedule: document.getElementById('preferred_schedule').value,
        goal: document.getElementById('goal').value,
    };

    if (newPass) {
        data.current_password = document.getElementById('current_password').value;
        data.new_password = newPass;
        data.new_password_confirmation = confirmPass;
    }

    fetch('/api/settings', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(r => {
        if (r.success) {
            showAlert('Configuración guardada.', 'success');
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        } else {
            showAlert(r.message, 'error');
        }
    })
    .catch(() => showAlert('Error de conexión', 'error'));
}



function showAlert(msg, type) {
    const a = document.getElementById('settingsAlert');
    a.innerHTML = `<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;background:${type==='success'?'rgba(34,197,94,.12)':'rgba(239,68,68,.12)'};border:1px solid ${type==='success'?'rgba(34,197,94,.3)':'rgba(239,68,68,.3)'};color:${type==='success'?'#22c55e':'#ef4444'}">${msg}</div>`;
    setTimeout(() => a.innerHTML = '', 4000);
}
</script>
@endsection