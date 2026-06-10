@extends('layouts.dashboard')

@section('title', 'Pagos y Membresías')
@section('page-title', '💳 Pagos y Membresías')

@section('styles')
<style>
    /* ── Header ── */
    .pay-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px; margin-bottom: 32px;
    }
    .pay-header h2 { color: #fff; font-size: 22px; font-weight: 700; margin: 0; }
    .pay-header p  { color: var(--muted); font-size: 13px; margin: 4px 0 0; }

    /* ── Admin stats ── */
    .payments-stats {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 32px;
    }
    .stat-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 20px; display: flex; align-items: center; gap: 14px;
        box-shadow: var(--shadow); transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.5); }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .stat-icon.green  { background: rgba(34,197,94,.12);  color: #22c55e; }
    .stat-icon.blue   { background: rgba(59,130,246,.12); color: #3b82f6; }
    .stat-icon.orange { background: rgba(255,69,0,.12);   color: var(--primary); }
    .stat-icon.purple { background: rgba(168,85,247,.12); color: #a855f7; }
    .stat-value { font-size: 24px; font-weight: 700; color: #fff; }
    .stat-label { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* ── Membresía activa banner ── */
    .membership-active-banner {
        background: linear-gradient(135deg, rgba(255,69,0,.12), rgba(255,106,51,.06));
        border: 1px solid rgba(255,69,0,.3); border-radius: var(--radius);
        padding: 20px 24px; margin-bottom: 32px;
        display: flex; align-items: center; gap: 16px;
    }
    .membership-active-banner .icon { font-size: 32px; }
    .membership-active-banner .info h3 { color: #fff; font-size: 16px; font-weight: 700; margin: 0 0 4px; }
    .membership-active-banner .info p  { color: var(--muted); font-size: 13px; margin: 0; }
    .badge-active { background: rgba(34,197,94,.15); color: #22c55e; border: 1px solid rgba(34,197,94,.3); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: auto; white-space: nowrap; }
    .badge-none   { background: rgba(239,68,68,.12);  color: #ef4444;  border: 1px solid rgba(239,68,68,.3);  padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: auto; white-space: nowrap; }

    /* ── Título de sección ── */
    .section-title {
        font-size: 18px; font-weight: 700; color: #fff;
        margin-bottom: 8px; display: flex; align-items: center; gap: 10px;
    }
    .section-subtitle { color: var(--muted); font-size: 13px; margin-bottom: 24px; }

    /* ── Tarjetas de membresía ── */
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .plan-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 28px 24px;
        display: flex; flex-direction: column;
        position: relative; overflow: hidden;
        transition: transform .25s, box-shadow .25s, border-color .25s;
    }
    .plan-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 48px rgba(0,0,0,.5);
    }
    .plan-card.featured {
        border-color: rgba(255,69,0,.5);
        box-shadow: 0 0 0 1px rgba(255,69,0,.2), var(--shadow);
    }
    .plan-card.featured::before {
        content: '★ MÁS POPULAR';
        position: absolute; top: 0; right: 0;
        background: var(--primary); color: #fff;
        font-size: 10px; font-weight: 700; letter-spacing: 1px;
        padding: 6px 16px; border-bottom-left-radius: 12px;
    }
    .plan-card.ultimate {
        background: linear-gradient(145deg, #1e1a2e, #1a1a1a);
        border-color: rgba(168,85,247,.4);
    }
    .plan-card.ultimate::before {
        content: '👑 PREMIUM';
        position: absolute; top: 0; right: 0;
        background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff;
        font-size: 10px; font-weight: 700; letter-spacing: 1px;
        padding: 6px 16px; border-bottom-left-radius: 12px;
    }
    .plan-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
        margin-bottom: 16px; align-self: flex-start;
    }
    .plan-badge.promo    { background: rgba(34,197,94,.12);  color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
    .plan-badge.standard { background: rgba(255,69,0,.12);   color: var(--primary); border: 1px solid rgba(255,69,0,.25); }
    .plan-badge.ultimate { background: rgba(168,85,247,.12); color: #a855f7; border: 1px solid rgba(168,85,247,.25); }

    .plan-name  { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px; }
    .plan-desc  { font-size: 12px; color: var(--muted); margin-bottom: 20px; line-height: 1.6; min-height: 40px; }
    .plan-price {
        display: flex; align-items: baseline; gap: 6px;
        margin-bottom: 24px;
    }
    .plan-price .currency { font-size: 16px; color: var(--muted); font-weight: 600; }
    .plan-price .amount   { font-size: 40px; font-weight: 800; color: #fff; line-height: 1; }
    .plan-price .period   { font-size: 12px; color: var(--muted); }
    .plan-features { list-style: none; padding: 0; margin: 0 0 28px; flex: 1; }
    .plan-features li {
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; color: var(--muted); padding: 7px 0;
        border-bottom: 1px solid rgba(255,255,255,.04);
    }
    .plan-features li:last-child { border-bottom: none; }
    .plan-features li i { font-size: 14px; flex-shrink: 0; }
    .feat-ok   { color: #22c55e; }
    .feat-no   { color: #444; text-decoration: line-through; }
    .feat-no i { color: #333; }

    .btn-plan {
        width: 100%; padding: 14px;
        border: none; border-radius: 10px;
        font-size: 14px; font-weight: 700; cursor: pointer;
        font-family: 'Poppins', sans-serif;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: transform .15s, box-shadow .15s, opacity .15s;
    }
    .btn-plan:hover:not(:disabled) { transform: translateY(-2px); }
    .btn-plan:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    .btn-plan.promo    { background: rgba(34,197,94,.15);  color: #22c55e;  border: 1px solid rgba(34,197,94,.3); }
    .btn-plan.promo:hover:not(:disabled)    { background: rgba(34,197,94,.25); box-shadow: 0 6px 20px rgba(34,197,94,.2); }
    .btn-plan.standard { background: linear-gradient(135deg, var(--primary), #ff6a33); color: #fff; box-shadow: 0 4px 16px rgba(255,69,0,.25); }
    .btn-plan.standard:hover:not(:disabled) { box-shadow: 0 8px 24px rgba(255,69,0,.4); }
    .btn-plan.ultimate { background: linear-gradient(135deg, #7c3aed, #a855f7); color: #fff; box-shadow: 0 4px 16px rgba(168,85,247,.25); }
    .btn-plan.ultimate:hover:not(:disabled) { box-shadow: 0 8px 24px rgba(168,85,247,.4); }
    .btn-plan.current-plan { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); cursor: default; }

    /* ── Flash messages ── */
    .payment-flash {
        padding: 14px 20px; border-radius: var(--radius); margin-bottom: 24px;
        display: flex; align-items: center; gap: 10px; font-size: 14px;
        animation: slideIn .3s ease;
    }
    .payment-flash.success   { background: rgba(34,197,94,.1);  border: 1px solid rgba(34,197,94,.25);  color: #86efac; }
    .payment-flash.cancelled { background: rgba(250,204,21,.1); border: 1px solid rgba(250,204,21,.25); color: #fde68a; }
    @keyframes slideIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

    /* ── Tabla historial ── */
    .payments-table-wrap {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);
    }
    .payments-table { width: 100%; border-collapse: collapse; }
    .payments-table thead { background: var(--surface2); }
    .payments-table th {
        padding: 14px 16px; font-size: 11px; text-transform: uppercase;
        letter-spacing: 1px; color: var(--muted); font-weight: 600; text-align: left;
    }
    .payments-table td { padding: 14px 16px; font-size: 13px; border-top: 1px solid var(--border); }
    .payments-table tbody tr { transition: background .15s; }
    .payments-table tbody tr:hover { background: var(--surface2); }
    .badge-status { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-paid      { background: rgba(34,197,94,.12);   color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
    .badge-completed { background: rgba(34,197,94,.12);   color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
    .badge-pending   { background: rgba(250,204,21,.12);  color: #facc15; border: 1px solid rgba(250,204,21,.25); }
    .badge-failed    { background: rgba(239,68,68,.12);   color: #ef4444; border: 1px solid rgba(239,68,68,.25); }
    .badge-cancelled { background: rgba(156,163,175,.12); color: #9ca3af; border: 1px solid rgba(156,163,175,.25); }
    .pagination-wrap { padding: 16px; display: flex; justify-content: center; }
    .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
    .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--border); display: block; }
    .empty-state p { font-size: 15px; }

    @media (max-width: 768px) {
        .plans-grid { grid-template-columns: 1fr; }
        .payments-table-wrap { overflow-x: auto; }
    }

    /* ── Modal de Selección de Pago ── */
    .payment-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px);
        display: none; align-items: center; justify-content: center;
        z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
    }
    .payment-modal-overlay.active {
        display: flex; opacity: 1;
    }
    .payment-modal-content {
        background: #121214; border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px; width: 90%; max-width: 480px; padding: 32px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }
    .payment-modal-overlay.active .payment-modal-content {
        transform: scale(1);
    }
    .payment-modal-close {
        position: absolute; top: 20px; right: 20px;
        background: none; border: none; color: var(--muted);
        font-size: 24px; cursor: pointer; transition: color 0.2s;
        line-height: 1;
    }
    .payment-modal-close:hover { color: #fff; }
    .payment-modal-title {
        font-size: 20px; font-weight: 700; color: #fff; text-align: center; margin-bottom: 8px;
    }
    .payment-modal-subtitle {
        font-size: 13px; color: var(--muted); text-align: center; margin-bottom: 24px;
    }
    .payment-plan-summary {
        background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.1);
        border-radius: 12px; padding: 12px 16px; margin-bottom: 24px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .payment-plan-summary .plan-info .name {
        font-weight: 700; color: #fff; font-size: 14px;
    }
    .payment-plan-summary .plan-info .desc {
        font-size: 11px; color: var(--muted);
    }
    .payment-plan-summary .plan-price {
        font-weight: 800; color: var(--primary); font-size: 18px;
    }
    
    .payment-methods-list {
        display: flex; flex-direction: column; gap: 16px;
    }
    .payment-method-card {
        background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 16px;
        cursor: pointer; transition: all 0.2s ease;
    }
    .payment-method-card:hover {
        background: rgba(255, 255, 255, 0.05); border-color: var(--primary);
        transform: translateY(-2px);
    }
    .payment-method-card.stripe:hover {
        border-color: #635bff;
    }
    .payment-method-card.mercadopago:hover {
        border-color: #009ee3;
    }
    .payment-method-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; background: rgba(255, 255, 255, 0.04);
        flex-shrink: 0;
    }
    .payment-method-card.stripe .payment-method-icon { color: #8f8bff; background: rgba(99, 91, 255, 0.1); }
    .payment-method-card.mercadopago .payment-method-icon { color: #00c4ff; background: rgba(0, 158, 227, 0.1); }
    
    .payment-method-info { flex: 1; }
    .payment-method-info .title { font-weight: 700; color: #fff; font-size: 14px; margin-bottom: 2px; }
    .payment-method-info .desc { font-size: 11px; color: var(--muted); }
    .payment-method-badge {
        font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px;
        background: rgba(255,255,255,0.08); color: var(--muted);
    }
    .payment-method-card.stripe:hover .payment-method-badge { background: rgba(99, 91, 255, 0.2); color: #8f8bff; }
    .payment-method-card.mercadopago:hover .payment-method-badge { background: rgba(0, 158, 227, 0.2); color: #00c4ff; }
</style>
@endsection

@section('content')

{{-- Flash por query string --}}
@if(request('status') === 'success')
    <div class="payment-flash success">
        <i class="bi bi-check-circle-fill"></i> ¡Pago completado exitosamente! Tu membresía ya está activa.
    </div>
@elseif(request('status') === 'cancelled')
    <div class="payment-flash cancelled">
        <i class="bi bi-exclamation-triangle-fill"></i> El pago fue cancelado. Puedes intentarlo de nuevo cuando quieras.
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- VISTA ADMINISTRADOR                                       --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if(auth()->user()->isAdmin())

<div class="pay-header">
    <div>
        <h2><i class="bi bi-bar-chart-line-fill"></i> Resumen de Ingresos</h2>
        <p>Panel de control de todos los pagos registrados en la plataforma.</p>
    </div>
</div>

@php
    $totalPagos      = $payments->total();
    $totalMonto      = \App\Models\Payment::sum('amount') + (\App\Models\Payment::sum('amount_cents') / 100);
    $pagosExitosos   = \App\Models\Payment::whereIn('status', ['paid', 'completed'])->count();
    $pagosPendientes = \App\Models\Payment::where('status', 'pending')->count();
@endphp
<div class="payments-stats">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-receipt-cutoff"></i></div>
        <div><div class="stat-value">{{ $totalPagos }}</div><div class="stat-label">Total pagos</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-cash-stack"></i></div>
        <div><div class="stat-value">${{ number_format($totalMonto, 0) }}</div><div class="stat-label">Total recaudado (COP)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-check2-circle"></i></div>
        <div><div class="stat-value">{{ $pagosExitosos }}</div><div class="stat-label">Pagos exitosos</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-hourglass-split"></i></div>
        <div><div class="stat-value">{{ $pagosPendientes }}</div><div class="stat-label">Pendientes</div></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- VISTA USUARIO: PLANES DE MEMBRESÍA                       --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@else

<div class="pay-header">
    <div>
        <h2><i class="bi bi-stars"></i> Planes de Membresía</h2>
        <p>Elige el plan que mejor se adapte a tus objetivos. ¡Transforma tu cuerpo hoy!</p>
    </div>
</div>

{{-- Membresía activa del usuario --}}
@php $activeMembership = auth()->user()->membership; @endphp
<div class="membership-active-banner">
    <div class="icon">{{ $activeMembership ? '🏆' : '⚡' }}</div>
    <div class="info">
        <h3>{{ $activeMembership ? $activeMembership->name : 'Sin membresía activa' }}</h3>
        <p>
            @if($activeMembership)
                Activa desde {{ auth()->user()->membership_start ? \Carbon\Carbon::parse(auth()->user()->membership_start)->format('d/m/Y') : 'N/A' }}.
                Duración: {{ $activeMembership->duration_days }} días.
            @else
                Selecciona uno de los planes a continuación para comenzar tu transformación.
            @endif
        </p>
    </div>
    @if($activeMembership)
        <span class="badge-active"><i class="bi bi-shield-check"></i> Activa</span>
    @else
        <span class="badge-none"><i class="bi bi-x-circle"></i> Inactiva</span>
    @endif
</div>

{{-- Sección planes --}}
<div class="section-title"><i class="bi bi-grid-3x3-gap-fill"></i> Nuestros Planes</div>
<p class="section-subtitle">Todos los planes incluyen acceso a las instalaciones del gimnasio · Sin contratos anuales</p>

@php
    // Planes hardcoded + los de BD combinados para garantizar que siempre se muestren
    $defaultPlans = collect([
        (object)['id' => 1, 'name' => 'Plan Promocional',      'price' => 70000, 'duration_days' => 30,
                 'description' => 'Acceso en horario promocional de 10:00 AM a 4:00 PM.',
                 'type' => 'promo',
                 'features' => [
                     ['ok' => true,  'text' => 'Acceso a sala de musculación'],
                     ['ok' => true,  'text' => 'Acceso a zona cardio'],
                     ['ok' => false, 'text' => 'Clases grupales'],
                     ['ok' => false, 'text' => 'Entrenador asignado'],
                     ['ok' => false, 'text' => 'Plan nutricional'],
                     ['ok' => false, 'text' => 'Zona VIP y toallas'],
                 ]],
        (object)['id' => 2, 'name' => 'Plan Mensual Estándar', 'price' => 85000, 'duration_days' => 30,
                 'description' => 'Acceso libre e ilimitado a todas las instalaciones y clases grupales.',
                 'type' => 'standard',
                 'features' => [
                     ['ok' => true, 'text' => 'Acceso a sala de musculación'],
                     ['ok' => true, 'text' => 'Acceso a zona cardio'],
                     ['ok' => true, 'text' => 'Clases grupales ilimitadas'],
                     ['ok' => true, 'text' => 'Acceso horario completo'],
                     ['ok' => false,'text' => 'Entrenador personal asignado'],
                     ['ok' => false,'text' => 'Plan nutricional con IA'],
                 ]],
        (object)['id' => 3, 'name' => 'Plan Ultimate',         'price' => 120000, 'duration_days' => 30,
                 'description' => 'Experiencia VIP completa: entrenador, nutrición y acceso exclusivo.',
                 'type' => 'ultimate',
                 'features' => [
                     ['ok' => true, 'text' => 'Acceso a sala de musculación'],
                     ['ok' => true, 'text' => 'Acceso a zona cardio'],
                     ['ok' => true, 'text' => 'Clases grupales ilimitadas'],
                     ['ok' => true, 'text' => 'Entrenador personal asignado'],
                     ['ok' => true, 'text' => 'Plan nutricional con IA'],
                     ['ok' => true, 'text' => 'Zona VIP · Toallas · Hidratación'],
                 ]],
    ]);

    // Si vienen membresías reales de BD, las usamos para cruzar IDs
    $dbMemberships = $memberships ?? collect();

    $defaultPlans = $defaultPlans->map(function ($plan) use ($dbMemberships) {
        $dbPlan = $dbMemberships->firstWhere('id', $plan->id);
        if ($dbPlan) {
            $plan->price = $dbPlan->price;
            $plan->name = $dbPlan->name;
            $plan->description = $dbPlan->description;
        }
        return $plan;
    });
@endphp

<div class="plans-grid">
    @foreach($defaultPlans as $plan)
    @php
        $isCurrentPlan = $activeMembership && $activeMembership->id == $plan->id;
        $cardClass = $plan->type === 'ultimate' ? 'ultimate' : ($plan->type === 'standard' ? 'featured' : '');
    @endphp
    <div class="plan-card {{ $cardClass }}">
        {{-- Badge --}}
        <span class="plan-badge {{ $plan->type }}">
            @if($plan->type === 'promo') <i class="bi bi-tag-fill"></i> OFERTA
            @elseif($plan->type === 'standard') <i class="bi bi-fire"></i> ESTÁNDAR
            @else <i class="bi bi-gem"></i> ULTIMATE @endif
        </span>

        {{-- Nombre y descripción --}}
        <div class="plan-name">{{ $plan->name }}</div>
        <div class="plan-desc">{{ $plan->description }}</div>

        {{-- Precio --}}
        <div class="plan-price">
            <span class="currency">$</span>
            <span class="amount">{{ number_format($plan->price, 0, ',', '.') }}</span>
            <span class="period">COP / mes</span>
        </div>

        {{-- Features --}}
        <ul class="plan-features">
            @foreach($plan->features as $feat)
            <li class="{{ $feat['ok'] ? '' : 'feat-no' }}">
                <i class="bi {{ $feat['ok'] ? 'bi-check-circle-fill feat-ok' : 'bi-x-circle-fill' }}"></i>
                {{ $feat['text'] }}
            </li>
            @endforeach
        </ul>

        {{-- Botón --}}
        @if($isCurrentPlan)
            <button class="btn-plan current-plan" disabled>
                <i class="bi bi-shield-check"></i> Plan Actual
            </button>
        @else
            <button class="btn-plan {{ $plan->type }}" onclick="openPaymentModal({{ $plan->id }}, '{{ $plan->name }}', '{{ number_format($plan->price, 0, ',', '.') }}', this)">
                <i class="bi bi-credit-card"></i> Adquirir Plan
            </button>
        @endif
    </div>
    @endforeach
</div>

@endif {{-- fin @if admin --}}

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- HISTORIAL DE PAGOS (común para todos)                    --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="section-title" style="margin-top: 8px;"><i class="bi bi-clock-history"></i> Historial de Pagos</div>
<p class="section-subtitle">Registro de todas tus transacciones realizadas en la plataforma.</p>

@if($payments->count())
    <div class="payments-table-wrap">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>#</th>
                    @if(auth()->user()->isAdmin())<th>Usuario</th>@endif
                    <th>Monto</th>
                    <th>Moneda</th>
                    <th>Estado</th>
                    <th>Método</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td style="color: var(--muted); font-size: 12px;">{{ $payment->id }}</td>
                        @if(auth()->user()->isAdmin())
                        <td>{{ $payment->user->name ?? ($payment->customer_id ?? '—') }}</td>
                        @endif
                        <td style="font-weight: 700; color: #fff;">
                            @if($payment->amount_cents)
                                ${{ number_format($payment->amount_cents / 100, 0, ',', '.') }}
                            @else
                                ${{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                        <td style="text-transform: uppercase; font-size: 11px; font-weight: 600; color: var(--muted);">
                            {{ $payment->currency ?? 'COP' }}
                        </td>
                        <td>
                            @php
                                $st = $payment->status;
                                $statusClass = 'badge-cancelled';
                                $statusLabel = ucfirst($st);
                                if (in_array($st, ['paid', 'completed'])) { $statusClass = 'badge-paid'; $statusLabel = 'Pagado'; }
                                elseif ($st === 'pending') { $statusClass = 'badge-pending'; $statusLabel = 'Pendiente'; }
                                elseif ($st === 'failed') { $statusClass = 'badge-failed'; $statusLabel = 'Fallido'; }
                                elseif ($st === 'cancelled') { $statusLabel = 'Cancelado'; }
                            @endphp
                            <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            @if($payment->payment_intent_id)
                                <span style="font-family:monospace; font-size:11px; color:var(--muted);">Stripe</span>
                            @elseif($payment->payment_method)
                                {{ ucfirst($payment->payment_method) }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="color: var(--muted); font-size: 12px;">
                            {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : $payment->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($payments->hasPages())
            <div class="pagination-wrap">{{ $payments->links() }}</div>
        @endif
    </div>
@else
    <div class="payments-table-wrap">
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No hay pagos registrados todavía.</p>
        </div>
    </div>
@endif

<!-- Modal de Selección de Pago -->
<div id="paymentModal" class="payment-modal-overlay" onclick="closePaymentModal(event)">
    <div class="payment-modal-content" onclick="event.stopPropagation()">
        <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
        <div class="payment-modal-title">Método de Pago</div>
        <div class="payment-modal-subtitle">Escoge cómo deseas adquirir tu membresía</div>

        <div class="payment-plan-summary">
            <div class="plan-info">
                <div class="name" id="modalPlanName">Plan</div>
                <div class="desc">Acceso inmediato por 30 días</div>
            </div>
            <div class="plan-price" id="modalPlanPrice">$0 COP</div>
        </div>

        <div class="payment-methods-list">
            <!-- Opción Stripe -->
            <div class="payment-method-card stripe" onclick="selectPaymentMethod('stripe')">
                <div class="payment-method-icon">
                    <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div class="payment-method-info">
                    <div class="title">Stripe</div>
                    <div class="desc">Tarjeta de crédito y débito internacional</div>
                </div>
                <span class="payment-method-badge">USD</span>
            </div>

            <!-- Opción Mercado Pago -->
            <div class="payment-method-card mercadopago" onclick="selectPaymentMethod('mercadopago')">
                <div class="payment-method-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="payment-method-info">
                    <div class="title">Mercado Pago</div>
                    <div class="desc">PSE, efecty, tarjetas locales y más</div>
                </div>
                <span class="payment-method-badge">COP</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripePublicKey = @json(config('stripe.public'));
const stripe = stripePublicKey ? Stripe(stripePublicKey) : null;

let selectedMembershipId = null;
let triggeringButton = null;

function openPaymentModal(membershipId, name, price, btn) {
    selectedMembershipId = membershipId;
    triggeringButton = btn;
    
    document.getElementById('modalPlanName').innerText = name;
    document.getElementById('modalPlanPrice').innerText = '$' + price + ' COP';
    
    const modal = document.getElementById('paymentModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('active'), 10);
}

function closePaymentModal(event) {
    if (event && event.target !== document.getElementById('paymentModal') && event.target !== document.querySelector('.payment-modal-close')) {
        return;
    }
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('active');
    setTimeout(() => modal.style.display = 'none', 300);
}

async function selectPaymentMethod(method) {
    closePaymentModal();
    if (!selectedMembershipId || !triggeringButton) return;

    if (method === 'stripe') {
        await initStripeCheckout(selectedMembershipId, triggeringButton);
    } else if (method === 'mercadopago') {
        await initMercadoPagoCheckout(selectedMembershipId, triggeringButton);
    }
}

async function initStripeCheckout(membershipId, btn) {
    if (!stripe) {
        alert('La pasarela de pago no está configurada en este momento. Contacta al administrador.');
        return;
    }

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Redirigiendo…';

    try {
        const res = await fetch('{{ route("stripe.create.session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ membership_id: membershipId })
        });

        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('El servidor no devolvió una respuesta válida.');
        }

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'Error en el servidor.');
        }

        if (data.sessionId) {
            const { error } = await stripe.redirectToCheckout({ sessionId: data.sessionId });
            if (error) { throw new Error(error.message); }
        } else {
            throw new Error('No se pudo crear la sesión de pago.');
        }
    } catch (err) {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

async function initMercadoPagoCheckout(membershipId, btn) {
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Redirigiendo…';

    try {
        const res = await fetch('{{ route("mercadopago.create.preference") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ membership_id: membershipId })
        });

        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('El servidor no devolvió una respuesta válida.');
        }

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || 'Error en el servidor.');
        }

        if (data.init_point) {
            window.location.href = data.init_point;
        } else {
            throw new Error('No se pudo crear la preferencia de Mercado Pago.');
        }
    } catch (err) {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

// Animación spin
const style = document.createElement('style');
style.textContent = '@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}.spin{animation:spin .6s linear infinite;}';
document.head.appendChild(style);
</script>
@endsection
