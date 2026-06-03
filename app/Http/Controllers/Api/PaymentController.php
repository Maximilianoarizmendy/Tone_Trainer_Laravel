<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Payment;
use App\Models\User;

/**
 * Controlador API para la facturación y los pagos.
 * 
 * Exclusivo para administradores. Permite registrar transacciones de 
 * usuarios que adquieren membresías del gimnasio, así como actualizar
 * sus estados de pago (completado, fallido, pendiente).
 */
class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorizeAdmin();
        $payments = Payment::with(['user:id,name', 'membership:id,name'])->orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $payments]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_id' => 'nullable|exists:memberships,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,completed,failed',
            'payment_method' => 'nullable|string',
        ]);

        $payment = Payment::create($data);
        return response()->json(['success' => true, 'message' => 'Pago registrado', 'data' => $payment]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin();

        $payment = Payment::findOrFail($id);
        $data = $request->validate([
            'status' => 'required|string|in:pending,completed,failed',
        ]);

        $payment->update($data);
        return response()->json(['success' => true, 'message' => 'Estado del pago actualizado']);
    }

    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== User::ROLE_ADMIN) {
            abort(403, 'No autorizado');
        }
    }
}
