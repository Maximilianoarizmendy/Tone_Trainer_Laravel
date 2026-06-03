@extends('layouts.dashboard')
@section('title', 'Gestión de Usuarios')
@section('page-title', '👥 Gestión de Usuarios')

@section('content')
<div id="usersAlert"></div>

@if($errors->any())
    <div style="padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#ef4444;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($errors->any())
    <div style="padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); color:#ef4444;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($myRole !== 'user')
<div class="toolbar">
    <button onclick="showModal()" class="btn-add"><i class="bi bi-plus-circle"></i> Nuevo Usuario</button>
    @if($myRole === 'admin')
        <button onclick="showTrainerModal()" class="btn-add" style="background: var(--surface2); border: 1px solid var(--border); margin-left: 8px;"><i class="bi bi-person-badge"></i> Registrar Entrenador</button>
        <button onclick="showNutritionistModal()" class="btn-add" style="background: var(--surface2); border: 1px solid var(--border); margin-left: 8px;"><i class="bi bi-person-hearts"></i> Registrar Nutricionista</button>
    @endif
</div>
@endif

@if($myRole === 'admin')
    @php
        $unverifiedTrainers = $users->where('role', 4)->where('is_verified', false);
    @endphp
    @if($unverifiedTrainers->count() > 0)
        <div style="background: rgba(250,204,21,.1); border: 1px solid rgba(250,204,21,.3); padding: 16px; border-radius: 12px; margin-bottom: 24px;">
            <h4 style="color: #facc15; margin-top: 0; margin-bottom: 12px;"><i class="bi bi-exclamation-triangle"></i> Entrenadores pendientes de verificación</h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($unverifiedTrainers as $ut)
                    <div style="display: flex; align-items: center; justify-content: space-between; background: var(--surface); padding: 10px 16px; border-radius: 8px;">
                        <div>
                            <strong style="color: #fff;">{{ $ut->name }}</strong> 
                            <span style="color: var(--muted); font-size: 12px;">({{ $ut->email }})</span>
                        </div>
                        <form action="{{ route('dashboard.admin.trainers.verify') }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $ut->id }}">
                            <button type="submit" style="padding: 6px 12px; background: #22c55e; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                <i class="bi bi-check-circle"></i> Aprobar Entrenador
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif

<div class="users-grid">
    @forelse($users as $u)
    <div class="user-card">
        <div class="user-header">
            @if($u->profile_photo)
                <img src="{{ asset('storage/' . $u->profile_photo) }}" class="user-avatar-img" alt="Foto de {{ $u->name }}">
            @else
                <div class="user-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
            @endif
            <div class="user-info">
                <span class="user-name">{{ $u->name }}</span>
                <span class="user-email">{{ $u->email }}</span>
            </div>
            <span class="user-role role-{{ $u->role }}">{{ $u->roleName }}</span>
        </div>
        <div class="user-body">
            <div class="user-detail"><i class="bi bi-telephone"></i> {{ $u->phone ?: '—' }}</div>
            <div class="user-detail"><i class="bi bi-geo-alt"></i> {{ $u->location ?: '—' }}</div>
            <div class="user-detail"><i class="bi bi-bullseye"></i> {{ $u->goal ?: '—' }}</div>
            <div class="user-detail"><i class="bi bi-bar-chart"></i> {{ $u->level ?: '—' }}</div>
            <div class="user-detail"><i class="bi bi-ruler"></i> {{ $u->weight ? $u->weight . ' kg' : '—' }} / {{ $u->height ? $u->height . ' cm' : '—' }}</div>
            <div class="user-detail"><i class="bi bi-calendar3"></i> {{ $u->birthdate ? \Carbon\Carbon::parse($u->birthdate)->format('d/m/Y') : '—' }}</div>
        </div>
        @if($myRole !== 'user')
        <div class="user-actions">
            <button onclick="editUser({{ $u->id }})" class="btn-edit"><i class="bi bi-pencil"></i> Editar</button>
            @if($myRole === 'admin' || $myRole === 'nutritionist')
            <button onclick="deleteUser({{ $u->id }})" class="btn-delete"><i class="bi bi-trash"></i></button>
            @endif
            @if($myRole === 'admin' || $myRole === 'nutritionist')
            <button onclick="showNoteModal({{ $u->id }}, '{{ $u->nutritionist_notes ?? '' }}')" class="btn-note">📝 Nota</button>
            @endif
        </div>
        
        @if($myRole === 'admin' && $u->role === 1)
            <div style="margin-top: 12px; border-top: 1px solid var(--border); padding-top: 12px;">
                <form action="{{ route('dashboard.admin.trainers.assign') }}" method="POST" style="display: flex; gap: 8px; margin: 0;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                    <select name="trainer_id" class="form-control" style="padding: 6px; font-size: 11px;">
                        <option value="">Sin entrenador</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ $u->trainer_id === $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-save" style="padding: 6px 12px; font-size: 11px; flex: none;">Asignar</button>
                </form>
            </div>
        @endif
        @endif
    </div>
    @empty
    <div class="empty-state">No hay usuarios registrados.</div>
    @endforelse
</div>

<div id="userModal" class="modal-hidden">
    <div class="modal-content">
        <h3 id="modalTitle">Nuevo Usuario</h3>
        <form id="userForm">
            <input type="hidden" id="userId">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="email" class="form-control" required>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" id="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Ciudad</label>
                    <input type="text" id="location" class="form-control">
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nivel</label>
                    <select id="level" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="Principiante">Principiante</option>
                        <option value="Intermedio">Intermedio</option>
                        <option value="Avanzado">Avanzado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Objetivo</label>
                    <select id="goal" class="form-control">
                        <option value="">Seleccionar...</option>
                        <option value="Pérdida de peso">Pérdida de peso</option>
                        <option value="Ganar músculo">Ganar músculo</option>
                        <option value="Tonificar">Tonificar</option>
                        <option value="Mejorar resistencia">Mejorar resistencia</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Peso (kg)</label>
                    <input type="number" id="weight" class="form-control" step="0.1" min="0">
                </div>
                <div class="form-group">
                    <label>Altura (cm)</label>
                    <input type="number" id="height" class="form-control" step="0.1" min="0">
                </div>
            </div>
            @if($myRole === 'admin')
            <div class="form-group">
                <label>Rol</label>
                <select id="role" class="form-control">
                    <option value="1">Usuario</option>
                    <option value="3">Nutricionista</option>
                    <option value="4">Entrenador</option>
                </select>
            </div>
            @endif
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Registrar Entrenador (Manual) -->
<div id="trainerModal" class="modal-hidden">
    <div class="modal-content">
        <h3><i class="bi bi-person-badge"></i> Registrar Entrenador (Verificado)</h3>
        <p style="font-size: 12px; color: var(--muted); margin-bottom: 16px;">
            Al registrar un entrenador desde aquí, quedará automáticamente verificado y podrá comenzar a aceptar clientes.
        </p>
        <form action="{{ route('dashboard.admin.trainers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombre Completo *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeTrainerModal()" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Entrenador</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Registrar Nutricionista (Manual) -->
<div id="nutritionistModal" class="modal-hidden">
    <div class="modal-content">
        <h3><i class="bi bi-person-hearts"></i> Registrar Nutricionista</h3>
        <p style="font-size: 12px; color: var(--muted); margin-bottom: 16px;">
            Al registrar un nutricionista desde aquí, quedará automáticamente verificado.
        </p>
        <form action="{{ route('dashboard.admin.nutritionists.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombre Completo *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeNutritionistModal()" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Nutricionista</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nota Nutricionista -->
<div id="noteModal" class="modal-hidden">
    <div class="modal-content">
        <h3><i class="bi bi-pencil-square"></i> Nota Nutricionista</h3>
        <form action="{{ route('dashboard.admin.nutritionist.notes') }}" method="POST" id="noteForm">
            @csrf
            <input type="hidden" name="target_user_id" id="noteUserId">
            <div class="form-group">
                <label>Nota</label>
                <textarea id="noteTextarea" name="note" rows="5" class="form-control" placeholder="Escribe recomendaciones..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeNoteModal()" class="btn-cancel">Cancelar</button>
                <button type="button" onclick="generateAINote()" class="btn-ai">🧠 Generar IA</button>
                <button type="submit" class="btn-save">Guardar Nota</button>
            </div>
        </form>
    </div>
</div>

<style>
.toolbar { margin-bottom: 16px; }
.btn-add { padding: 10px 20px; background: var(--primary); border: none; border-radius: 8px; color: #fff; cursor: pointer; font-size: 13px; }
.users-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.user-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; }
.user-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.user-avatar { width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 16px; flex-shrink: 0; }
.user-avatar-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
.user-info { flex: 1; min-width: 0; }
.user-name { display: block; font-size: 14px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { display: block; font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; flex-shrink: 0; }
.role-1 { background: rgba(59,130,246,.15); color: #60a5fa; }
.role-2 { background: rgba(168,85,247,.15); color: #a78bfa; }
.role-3 { background: rgba(34,197,94,.15); color: #4ade80; }
.role-4 { background: rgba(249,115,22,.15); color: #fb923c; }
.user-body { margin-bottom: 12px; }
.user-detail { font-size: 12px; color: var(--muted); padding: 4px 0; display: flex; gap: 8px; }
.user-actions { display: flex; gap: 8px; border-top: 1px solid var(--border); padding-top: 12px; }
.btn-edit { flex: 1; padding: 8px; background: var(--surface2); border: none; border-radius: 6px; color: #fff; cursor: pointer; font-size: 12px; }
.btn-delete { padding: 8px 12px; background: rgba(239,68,68,.15); border: none; border-radius: 6px; color: #dc3545; cursor: pointer; }
.btn-note { flex: 1; padding: 8px; background: var(--surface2); border: none; border-radius: 6px; color: #fff; cursor: pointer; font-size: 12px; }
.empty-state { text-align: center; color: var(--muted); padding: 40px; grid-column: 1 / -1; }
.modal-hidden { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,.7); z-index: 1000; align-items: center; justify-content: center; }
.modal-content { background: var(--surface); border-radius: 12px; padding: 24px; max-width: 480px; width: 95%; max-height: 90vh; overflow-y: auto; }
.modal-content h3 { color: #fff; font-size: 16px; margin-bottom: 16px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-group { margin-bottom: 12px; }
.form-group label { font-size: 12px; color: var(--muted); display: block; margin-bottom: 4px; }
.form-control { width: 100%; padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: #fff; font-size: 13px; }
.modal-actions { display: flex; gap: 10px; margin-top: 20px; }
.btn-cancel { flex: 1; padding: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; color: #fff; cursor: pointer; }
.btn-save { flex: 1; padding: 10px; background: var(--primary); border: none; border-radius: 8px; color: #fff; cursor: pointer; }
.btn-ai { flex: 1; padding: 10px; background: var(--primary); border: none; border-radius: 8px; color: #fff; cursor: pointer; font-size: 12px; margin-left: 4px; }
@media (max-width: 600px) {
    .users-grid { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: 1fr; }
}
</style>

<script>
let isEdit = false;

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    saveUser();
});

function showModal(user = null) {
    isEdit = !!user;
    document.getElementById('modalTitle').textContent = user ? 'Editar Usuario' : 'Nuevo Usuario';
    document.getElementById('userId').value = user?.id || '';
    document.getElementById('name').value = user?.name || '';
    document.getElementById('email').value = user?.email || '';
    document.getElementById('email').disabled = isEdit;
    document.getElementById('phone').value = user?.phone || '';
    document.getElementById('location').value = user?.location || '';
    document.getElementById('level').value = user?.level || '';
    document.getElementById('goal').value = user?.goal || '';
    document.getElementById('weight').value = user?.weight || '';
    document.getElementById('height').value = user?.height || '';
    const roleElem = document.getElementById('role');
    if (roleElem) {
        roleElem.value = user?.role || 1;
    }
    document.getElementById('userModal').style.display = 'flex';
}

function showTrainerModal() {
    document.getElementById('trainerModal').style.display = 'flex';
}

function closeTrainerModal() {
    document.getElementById('trainerModal').style.display = 'none';
}

function showNutritionistModal() {
    document.getElementById('nutritionistModal').style.display = 'flex';
}

function closeNutritionistModal() {
    document.getElementById('nutritionistModal').style.display = 'none';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
    document.getElementById('userForm').reset();
    document.getElementById('email').disabled = false;
}

function editUser(id) {
    fetch(`/api/users/${id}`, { credentials: 'include', headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(r => { if (r.success) showModal(r.data); });
}

function saveUser() {
    const id = document.getElementById('userId').value;
    const data = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        location: document.getElementById('location').value,
        level: document.getElementById('level').value,
        goal: document.getElementById('goal').value,
        weight: document.getElementById('weight').value,
        height: document.getElementById('height').value,
    };
    if (!id && document.getElementById('role')) {
        data.role = document.getElementById('role').value;
    }

    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/users/${id}` : '/api/users';

    fetch(url, {
        method,
        credentials: 'include',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(r => {
        if (r.success) {
            closeModal();
            location.reload();
        } else {
            showAlert(r.message, 'error');
        }
    });
}

function deleteUser(id) {
    if (!confirm('¿Desactivar este usuario?')) return;
    fetch(`/api/users/${id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(r => { if (r.success) location.reload(); else showAlert(r.message, 'error'); });
}

function showAlert(msg, type) {
    const a = document.getElementById('usersAlert');
    a.innerHTML = `<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;background:${type==='success'?'rgba(34,197,94,.12)':'rgba(239,68,68,.12)'};border:1px solid ${type==='success'?'rgba(34,197,94,.3)':'rgba(239,68,68,.3)'};color:${type==='success'?'#22c55e':'#ef4444'}">${msg}</div>`;
    setTimeout(() => a.innerHTML = '', 4000);
}
function showNoteModal(userId, existingNote) {
    document.getElementById('noteUserId').value = userId;
    document.getElementById('noteTextarea').value = existingNote;
    document.getElementById('noteModal').style.display = 'flex';
}

function generateAINote() {
    const userId = document.getElementById('noteUserId').value;
    if (!userId) { showAlert('Usuario no seleccionado.', 'error'); return; }
    fetch(`{{ route('dashboard.admin.nutritionist.ai_notes') }}`, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ target_user_id: userId })
    })
    .then(r => r.json())
    .then(r => {
        if (r.success) {
            document.getElementById('noteTextarea').value = r.note;
            showAlert('Nota generada por IA.', 'success');
        } else {
            showAlert(r.message || 'Error al generar la nota.', 'error');
        }
    })
    .catch(() => showAlert('Error de red al contactar IA.', 'error'));
}

    document.getElementById('noteModal').style.display = 'none';
    document.getElementById('noteForm').reset();
}
</script>
@endsection