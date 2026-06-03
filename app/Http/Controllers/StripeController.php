<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeController extends Controller
{
    /**
     * Crear una Checkout Session de Stripe y devolver el sessionId al frontend.
     */
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => [
                        'name' => 'Membresía Tone Trainer',
                    ],
                    'unit_amount' => 5000, // 50.00 USD en centavos
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => url('/dashboard/payments?status=success'),
            'cancel_url'  => url('/dashboard/payments?status=cancelled'),
        ]);

        return response()->json(['sessionId' => $session->id]);
    }

    /**
     * Recibir webhooks de Stripe para registrar pagos completados.
     */
    public function handleWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::error('⚠️ Webhook de Stripe no válido: ' . $e->getMessage());
            return response('firma inválida', 400);
        }

        // Guardar pago cuando se completa el checkout
        if ($event->type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            Payment::create([
                'payment_intent_id' => $session->payment_intent ?? $session->id,
                'customer_id'       => $session->customer,
                'amount_cents'      => $session->amount_total,
                'currency'          => $session->currency,
                'status'            => $session->payment_status, // "paid"
                'paid_at'           => Carbon::now(),
            ]);

            Log::info('✅ Pago registrado, session_id: ' . $session->id);
        }

        return response('ok', 200);
    }
}
