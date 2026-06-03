@extends('layouts.dashboard')
@section('title', 'Verificación de Entrenadores')
@section('page-title', '🛡️ Verificación de Entrenadores')

@section('styles')
<style>
.table-container { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: auto; padding: 20px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 14px 20px; text-align: left; font-size: 14px; border-bottom: 1px solid var(--surface2); }
th { color: var(--muted); font-weight: 500; font-size: 13px; text-transform: uppercase; }
.btn-approve { background: rgba(34,197,94,.15); color: #22c55e; border: 1px solid rgba(34,197,94,.3); padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px; }
.btn-approve:hover { background: rgba(34,197,94,.25); }
.btn-reject { background: rgba(239,68,68,.15); color: #ef4444; border: 1px solid rgba(239,68,68,.3); padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; }
.btn-reject:hover { background: rgba(239,68,68,.25); }
.empty-state { text-align: center; padding: 40px; color: var(--muted); }
.doc-link { color: var(--primary); text-decoration: none; font-weight: 500; }
.doc-link:hover { text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="table-container">
    @if($pendingTrainers->isEmpty())
        <div class="empty-state">
            <span style="font-size: 40px; display:block; margin-bottom:15px;">✅</span>
            <p>No hay solicitudes de entrenadores pendientes de verificación.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Documento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingTrainers as $trainer)
                <tr>
                    <td>{{ $trainer->name }}</td>
                    <td>{{ $trainer->email }}</td>
                    <td>
                        @if($trainer->verification_document)
                            <a href="{{ asset('storage/' . $trainer->verification_document) }}" target="_blank" class="doc-link">Ver Documento</a>
                        @else
                            <span class="text-muted" style="color:#666;">Sin documento</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.trainers.verify') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $trainer->id }}">
                            <input type="hidden" name="action" value="approve">
                            <button class="btn-approve" type="submit" onclick="return confirm('¿Aprobar solicitud de {{ $trainer->name }}?')">Aprobar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.trainers.verify') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $trainer->id }}">
                            <input type="hidden" name="action" value="reject">
                            <button class="btn-reject" type="submit" onclick="return confirm('¿Rechazar solicitud de {{ $trainer->name }}?')">Rechazar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
