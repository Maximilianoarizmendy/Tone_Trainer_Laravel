@extends('layouts.dashboard')
@section('title', 'Mi Perfil')
@section('page-title', '👤 Mi Perfil')

@section('styles')
<style>
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; }
.profile-sidebar-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 30px 24px; text-align: center; }
.profile-photo-wrapper { position: relative; width: 100px; height: 100px; margin: 0 auto 16px; }
.profile-photo { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); }
.profile-initials { width: 100px; height: 100px; border-radius: 50%; background: var(--primary-glow); border: 3px solid var(--primary); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 700; color: var(--primary); }
.photo-upload-btn { position: absolute; bottom: 0; right: 0; width: 30px; height: 30px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--bg); font-size: 12px; color: #fff; }
.profile-name { font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.profile-email { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
.role-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; background: var(--primary-glow); color: var(--primary); border: 1px solid rgba(255,69,0,.3); margin-bottom: 20px; }
.profile-stat { display: flex; justify-content: space-around; margin-top: 8px; }
.pstat { text-align: center; }
.pstat-val { font-size: 20px; font-weight: 700; color: var(--primary); }
.pstat-lbl { font-size: 11px; color: var(--muted); }
.form-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; margin-bottom: 20px; }
.form-card h3 { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.form-card h3 i { color: var(--primary); }
.form-row { display: flex; gap: 14px; flex-wrap: wrap; }
.form-group { flex: 1; min-width: 200px; margin-bottom: 14px; }
.form-group label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; background: var(--surface2); border: 1px solid var(--border); color: var(--text); border-radius: 8px; font-size: 13px; font-family: 'Poppins', sans-serif; }
.form-group input:focus { outline: none; border-color: var(--primary); }
.btn-save { padding: 11px 28px; background: linear-gradient(90deg, var(--primary), #ff6347); border: none; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
@media (max-width: 860px) { .profile-layout { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="profile-layout">
    {{-- Sidebar --}}
    <div>
        <div class="profile-sidebar-card">
            <div class="profile-photo-wrapper">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto" class="profile-photo" id="profileImg">
                @else
                    <div class="profile-initials" id="profileInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <label for="photoInput" class="photo-upload-btn" title="Cambiar foto">📷</label>
                <input type="file" id="photoInput" accept="image/*" style="display:none" onchange="uploadPhoto(this)">
            </div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
            <div class="role-badge">{{ $user->roleName }}</div>
            @if($user->weight && $user->height)
            <div class="profile-stat">
                <div class="pstat"><div class="pstat-val">{{ $user->weight }}</div><div class="pstat-lbl">Peso (kg)</div></div>
                <div class="pstat"><div class="pstat-val">{{ $user->height }}</div><div class="pstat-lbl">Altura (cm)</div></div>
                @if($user->imc)<div class="pstat"><div class="pstat-val">{{ number_format($user->imc, 1) }}</div><div class="pstat-lbl">IMC</div></div>@endif
            </div>
            @endif
        </div>
    </div>

    {{-- Formulario --}}
    <div>
        <form method="POST" action="{{ route('dashboard.profile.update') }}">
            @csrf
            <div class="form-card">
                <h3><i class="bi bi-person-fill"></i> Información Personal</h3>
                <div class="form-row">
                    <div class="form-group"><label>Nombre completo</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
                    <div class="form-group"><label>Teléfono</label><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+57 300..."></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Ubicación</label><input type="text" name="location" value="{{ old('location', $user->location) }}" placeholder="Ciudad, País"></div>
                    <div class="form-group"><label>Objetivo</label><input type="text" name="goal" value="{{ old('goal', $user->goal) }}" placeholder="Perder peso, ganar músculo..."></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Nivel</label>
                        <select name="level">
                            @foreach(['Principiante','Intermedio','Avanzado'] as $lvl)
                                <option value="{{ $lvl }}" {{ $user->level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Peso (kg)</label><input type="number" name="weight" step="0.1" value="{{ old('weight', $user->weight) }}"></div>
                    <div class="form-group"><label>Altura (cm)</label><input type="number" name="height" step="0.1" value="{{ old('height', $user->height) }}"></div>
                </div>
            </div>

            <div class="form-card">
                <h3><i class="bi bi-shield-lock-fill"></i> Cambiar Contraseña <small style="font-size:11px;color:var(--muted);font-weight:400;">(dejar vacío para no cambiar)</small></h3>
                <div class="form-row">
                    <div class="form-group"><label>Nueva contraseña</label><input type="password" name="password" placeholder="Mín. 8 caracteres"></div>
                    <div class="form-group"><label>Confirmar contraseña</label><input type="password" name="password_confirmation" placeholder="Repite la contraseña"></div>
                </div>
            </div>

            <button type="submit" class="btn-save">Guardar Cambios</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function uploadPhoto(input) {
    const file = input.files[0]; if (!file) return;
    const form = new FormData(); form.append('profile_photo', file);
    const r = await fetch('/api/profile/photo', { method:'POST', body: form });
    const d = await r.json();
    if (d.success) {
        const img = document.getElementById('profileImg');
        const ini = document.getElementById('profileInitials');
        if (img) { img.src = d.path; }
        else if (ini) { ini.outerHTML = `<img src="${d.path}" alt="Foto" class="profile-photo" id="profileImg">`; }
        showToast('✅ Foto actualizada correctamente');
    } else showToast('❌ Error al subir la foto');
}
function showToast(msg) {
    const t = document.createElement('div'); t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#FF4500;color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;font-size:13px;';
    document.body.appendChild(t); setTimeout(() => t.remove(), 3000);
}
</script>
@endsection
