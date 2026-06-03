@extends('layouts.dashboard')
@section('title', 'Ranking de Usuarios')
@section('page-title', '🏆 Ranking de Logros')

@section('styles')
<style>
.ranking-wrap { max-width: 760px; margin: 0 auto; }
.rank-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.rank-table { width: 100%; border-collapse: collapse; }
.rank-table thead tr { border-bottom: 1px solid var(--border); }
.rank-table th { text-align: left; padding: 12px 16px; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
.rank-row { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 10px; display: flex; align-items: center; padding: 14px 18px; gap: 16px; transition: border-color .2s; }
.rank-row:hover { border-color: var(--primary); }
.rank-pos { font-size: 22px; font-weight: 900; min-width: 40px; text-align: center; }
.pos-1 { color: #facc15; }
.pos-2 { color: #94a3b8; }
.pos-3 { color: #d97706; }
.pos-n { color: var(--muted); font-size: 16px; }
.rank-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--surface2); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: var(--primary); flex-shrink: 0; }
.rank-name  { font-size: 15px; font-weight: 700; color: #fff; }
.rank-email { font-size: 11px; color: var(--muted); }
.badge-count { margin-left: auto; display: flex; align-items: center; gap: 8px; }
.badge-pill { background: rgba(250,204,21,.12); border: 1px solid rgba(250,204,21,.3); color: #facc15; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; }
.empty-rank { text-align: center; padding: 60px 0; color: var(--muted); }
.empty-rank .emo { font-size: 52px; display: block; margin-bottom: 12px; }
</style>
@endsection

@section('content')
<div class="ranking-wrap">
    <div class="rank-header">
        <div>
            <h2 style="color:#fff;margin:0;">Tabla de Posiciones</h2>
            <p style="color:var(--muted);margin:4px 0 0;font-size:13px;">Los usuarios con más insignias y retos completados</p>
        </div>
        <span style="font-size:32px;">🏆</span>
    </div>

    @if($ranking->isEmpty())
        <div class="empty-rank">
            <span class="emo">🏅</span>
            <p>Aún no hay logros registrados.<br>¡Completa retos para aparecer aquí!</p>
        </div>
    @else
        @foreach($ranking as $i => $u)
        <div class="rank-row">
            <div class="rank-pos {{ $i === 0 ? 'pos-1' : ($i === 1 ? 'pos-2' : ($i === 2 ? 'pos-3' : 'pos-n')) }}">
                @if($i === 0) 🥇
                @elseif($i === 1) 🥈
                @elseif($i === 2) 🥉
                @else {{ $i + 1 }}
                @endif
            </div>
            <div class="rank-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
            <div>
                <div class="rank-name">{{ $u->name }}</div>
                <div class="rank-email">{{ $u->email }}</div>
            </div>
            <div class="badge-count">
                <span class="badge-pill">🏆 {{ $u->achievements_count }} {{ $u->achievements_count === 1 ? 'insignia' : 'insignias' }}</span>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
