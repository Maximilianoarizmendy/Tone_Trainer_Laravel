@extends('layouts.dashboard')

@section('title', 'Pagos y Membresías')

@section('page-title', 'Pagos y Membresías')

@section('styles')
<style>
    /* ── Encabezado de pagos ── */
    .payments-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px; margin-bottom: 24px;
    }
    .payments-header h2 { color: var(--primary); font-size: 22px; font-weight: 700; margin: 0; }

    /* ── Tarjetas resumen ── */
    .payments-stats {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 28px;
    }
    .stat-card {
        background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 20px; display: flex; align-items: center; gap: 14px;
        box-shadow: var(--shadow); transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.5); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 22px;
    }
    .stat-icon.green  { background: rgba(34,197,94,.12); color: #22c55e; }
    .stat-icon.blue   { background: rgba(59,130,246,.12); color: #3b82f6; }
    .stat-icon.orange { background: rgba(255,69,0,.12);   color: var(--primary); }
    .stat-icon.purple { background: rgba(168,85,247,.12); color: #a855f7; }
    .stat-value { font-size: 24px; font-weight: 700; color: #fff; }
    .stat-label { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* ── Botón pagar ── */
    .btn-pay {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px; background: linear-gradient(135deg, var(--primary), #ff6a33);
        color: #fff; border: none; border-radius: var(--radius);
        font-size: 14px; font-weight: 600; cursor: pointer;
        transition: transform .15s, box-shadow .15s; font-family: 'Poppins', sans-serif;
    }
    .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,69,0,.35); }
    .btn-pay:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    .btn-pay i { font-size: 16px; }

    /* ── Tabla ── */
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

    /* Badges de estado */
    .badge-status {
        display: inline-block; padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; text-transform: capitalize;
    }
    .badge-paid      { background: rgba(34,197,94,.12);  color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
    .badge-completed { background: rgba(34,197,94,.12);  color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
    .badge-pending   { background: rgba(250,204,21,.12); color: #facc15; border: 1px solid rgba(250,204,21,.25); }
    .badge-failed    { background: rgba(239,68,68,.12);  color: #ef4444; border: 1px solid rgba(239,68,68,.25); }
    .badge-cancelled { background: rgba(156,163,175,.12);color: #9ca3af; border: 1px solid rgba(156,163,175,.25); }

    /* Paginación */
    .pagination-wrap { padding: 16px; display: flex; justify-content: center; }

    /* ── Estado vacío ── */
    .empty-state {
        text-align: center; padding: 60px 20px; color: var(--muted);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--border); display: block; }
    .empty-state p { font-size: 15px; }

    /* ── Flash de status via query string ── */
    .payment-flash {
        padding: 14px 20px; border-radius: var(--radius); margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px; font-size: 14px;
        animation: slideIn .3s ease;
    }
    .payment-flash.success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25); color: #86efac; }
    .payment-flash.cancelled { background: rgba(250,204,21,.1); border: 1px solid rgba(250,204,21,.25); color: #fde68a; }
    @keyframes slideIn { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

    /* Responsive */
    @media (max-width: 768px) {
        .payments-table-wrap { overflow-x: auto; }
        .payments-header { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')

{{-- Flash por query string --}}
@if(request('status') === 'success')
    <div class="payment-flash success">
        <i class="bi bi-check-circle-fill"></i> ¡Pago completado exitosamente! Gracias por tu compra.
    </div>
@elseif(request('status') === 'cancelled')
    <div class="payment-flash cancelled">
        <i class="bi bi-exclamation-triangle-fill"></i> El pago fue cancelado. Puedes intentarlo de nuevo.
    </div>
@endif

{{-- Encabezado --}}
<div class="payments-header">
    <h2><i class="bi bi-credit-card-2-front-fill"></i> Pagos y Membresías</h2>
    <button class="btn-pay" id="btn-stripe-pay">
        <i class="bi bi-credit-card"></i> Realizar nuevo pago
    </button>
</div>

{{-- Tarjetas resumen --}}
@if(auth()->user()->isAdmin())
@php
    $totalPagos      = $payments->total();
    $totalMonto      = \App\Models\Payment::sum('amount') + (\App\Models\Payment::sum('amount_cents') / 100);
    $pagosExitosos   = \App\Models\Payment::whereIn('status', ['paid', 'completed'])->count();
    $pagosPendientes = \App\Models\Payment::where('status', 'pending')->count();
@endphp
<div class="payments-stats">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-receipt-cutoff"></i></div>
        <div>
            <div class="stat-value">{{ $totalPagos }}</div>
            <div class="stat-label">Total pagos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="stat-value">${{ number_format($totalMonto, 2) }}</div>
            <div class="stat-label">Total recaudado</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-check2-circle"></i></div>
        <div>
            <div class="stat-value">{{ $pagosExitosos }}</div>
            <div class="stat-label">Pagos exitosos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $pagosPendientes }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
</div>
@else
{{-- Tarjeta de membresía actual para usuario normal --}}
<div class="payments-stats" style="grid-template-columns: 1fr;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-shield-check"></i></div>
        <div>
            <div class="stat-value">
                @if(auth()->user()->membership)
                    {{ auth()->user()->membership->name }}
                @else
                    Sin Membresía Activa
                @endif
            </div>
            <div class="stat-label">Membresía Actual</div>
        </div>
    </div>
</div>
@endif


{{-- Tabla de historial --}}
@if($payments->count())
    <div class="payments-table-wrap">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
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
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->user->name ?? ($payment->customer_id ?? '—') }}</td>
                        <td style="font-weight: 600; color: #fff;">
                            @if($payment->amount_cents)
                                ${{ number_format($payment->amount_cents / 100, 2) }}
                            @else
                                ${{ number_format($payment->amount ?? 0, 2) }}
                            @endif
                        </td>
                        <td style="text-transform: uppercase; font-size: 12px; font-weight: 600;">
                            {{ $payment->currency ?? 'USD' }}
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
                            @if($payment->payment_method)
                                {{ ucfirst($payment->payment_method) }}
                            @elseif($payment->payment_intent_id)
                                <span style="font-family:monospace; font-size:11px; color:var(--muted);">
                                    Stripe
                                </span>
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

        {{-- Paginación --}}
        @if($payments->hasPages())
            <div class="pagination-wrap">
                {{ $payments->links() }}
            </div>
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

@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
(function() {
    const stripePublicKey = @json(config('stripe.public'));
    const stripe = Stripe(stripePublicKey);
    const btn = document.getElementById('btn-stripe-pay');

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Redirigiendo…';

        try {
            const res = await fetch('{{ route("stripe.create.session") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({})
            });

            const contentType = res.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const textError = await res.text();
                console.error("Respuesta no es JSON:", textError);
                throw new Error("El servidor no devolvió una respuesta válida (no es JSON).");
            }

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Error al procesar la solicitud en el servidor');
            }

            if (data.sessionId) {
                const { error } = await stripe.redirectToCheckout({ sessionId: data.sessionId });
                if (error) {
                    alert('Error: ' + error.message);
                    resetBtn();
                }
            } else {
                alert('No se pudo crear la sesión de pago.');
                resetBtn();
            }
        } catch (err) {
            alert('Error de conexión: ' + err.message);
            resetBtn();
        }
    });

    function resetBtn() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-credit-card"></i> Realizar nuevo pago';
    }

    // Animación spin para el loading
    const style = document.createElement('style');
    style.textContent = '@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}.spin{animation:spin .6s linear infinite;}';
    document.head.appendChild(style);
})();
</script>
@endsection
