<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPagoController extends Controller
{
    /**
     * Crear una preferencia de pago de Mercado Pago y devolver init_point.
     */
    public function createPreference(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
        ]);

        $user = auth()->user();
        $membership = Membership::findOrFail($request->membership_id);

        try {
            MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
            $client = new PreferenceClient();

            // Guardamos user_id y membership_id en external_reference
            $externalReference = $user->id . '-' . $membership->id;

            $preference = $client->create([
                "items" => [
                    [
                        "title" => "Membresía Tone Trainer: " . $membership->name,
                        "quantity" => 1,
                        "unit_price" => (float) $membership->price,
                        "currency_id" => "COP"
                    ]
                ],
                "back_urls" => [
                    "success" => route('mercadopago.callback'),
                    "failure" => route('mercadopago.callback'),
                    "pending" => route('mercadopago.callback'),
                ],
                "auto_return" => "approved",
                "external_reference" => $externalReference,
            ]);

            return response()->json([
                'success' => true,
                'init_point' => $preference->init_point,
                'preferenceId' => $preference->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear preferencia de Mercado Pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al inicializar el pago con Mercado Pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manejar el callback cuando el usuario retorna al sitio.
     */
    public function paymentCallback(Request $request)
    {
        $status = $request->query('status') ?? $request->query('collection_status');
        $paymentId = $request->query('payment_id');
        $externalReference = $request->query('external_reference');
        $preferenceId = $request->query('preference_id');

        Log::info('Callback de Mercado Pago recibido:', $request->all());

        if ($status === 'approved' && $externalReference) {
            try {
                $parts = explode('-', $externalReference);
                if (count($parts) === 2) {
                    $userId = (int) $parts[0];
                    $membershipId = (int) $parts[1];

                    $user = User::find($userId);
                    $membership = Membership::find($membershipId);

                    if ($user && $membership) {
                        // Actualizar membresía del usuario
                        $user->update([
                            'membership_id' => $membership->id,
                            'membership_start' => Carbon::now(),
                        ]);

                        // Registrar pago si no existe
                        $exists = Payment::where('payment_intent_id', (string)$paymentId)->exists();

                        if (!$exists) {
                            Payment::create([
                                'user_id' => $user->id,
                                'membership_id' => $membership->id,
                                'amount' => $membership->price,
                                'status' => 'completed',
                                'payment_method' => 'mercadopago',
                                'payment_intent_id' => (string)$paymentId,
                                'customer_id' => $preferenceId,
                                'amount_cents' => (int)($membership->price * 100),
                                'currency' => 'COP',
                                'paid_at' => Carbon::now(),
                            ]);
                        }

                        return redirect()->route('dashboard.payments', ['status' => 'success']);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error procesando callback exitoso de Mercado Pago: ' . $e->getMessage());
            }
        }

        // Si falló o fue cancelado
        return redirect()->route('dashboard.payments', ['status' => 'cancelled']);
    }

    /**
     * Recibir notificaciones Webhook/IPN de Mercado Pago.
     */
    public function webhook(Request $request)
    {
        Log::info('Webhook de Mercado Pago recibido:', $request->all());

        $paymentId = $request->input('data.id') ?? $request->input('id') ?? $request->query('id') ?? $request->query('data_id');
        $type = $request->input('type') ?? $request->input('topic');

        if ($paymentId && ($type === 'payment' || !$type)) {
            try {
                MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
                $client = new PaymentClient();
                $payment = $client->get($paymentId);

                if ($payment && $payment->status === 'approved') {
                    $externalReference = $payment->external_reference;

                    if ($externalReference) {
                        $parts = explode('-', $externalReference);
                        if (count($parts) === 2) {
                            $userId = (int) $parts[0];
                            $membershipId = (int) $parts[1];

                            $user = User::find($userId);
                            $membership = Membership::find($membershipId);

                            if ($user && $membership) {
                                // Actualizar membresía del usuario
                                $user->update([
                                    'membership_id' => $membership->id,
                                    'membership_start' => Carbon::now(),
                                ]);

                                // Registrar pago si no existe
                                $exists = Payment::where('payment_intent_id', (string)$paymentId)->exists();

                                if (!$exists) {
                                    Payment::create([
                                        'user_id' => $user->id,
                                        'membership_id' => $membership->id,
                                        'amount' => $membership->price,
                                        'status' => 'completed',
                                        'payment_method' => 'mercadopago',
                                        'payment_intent_id' => (string)$paymentId,
                                        'customer_id' => $payment->preference_id ?? null,
                                        'amount_cents' => (int)($membership->price * 100),
                                        'currency' => 'COP',
                                        'paid_at' => Carbon::now(),
                                    ]);
                                    Log::info("✅ Pago por Webhook registrado con éxito para el usuario {$userId}");
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error procesando webhook de Mercado Pago: ' . $e->getMessage());
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => true], 200);
    }
}
