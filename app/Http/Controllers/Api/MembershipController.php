<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Membership;

/**
 * Controlador API para la gestión de Membresías.
 * 
 * Permite al administrador crear y configurar los planes del gimnasio
 * (precios, duración). Los usuarios pueden consultar los planes disponibles.
 */
class MembershipController extends Controller
{
    public function index(): JsonResponse
    {
        $memberships = Membership::all();
        return response()->json(['success' => true, 'data' => $memberships]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $membership = Membership::create($data);
        return response()->json(['success' => true, 'message' => 'Membresía creada', 'data' => $membership]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin();

        $membership = Membership::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $membership->update($data);
        return response()->json(['success' => true, 'message' => 'Membresía actualizada']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeAdmin();
        Membership::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Membresía eliminada']);
    }

    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
            abort(403, 'No autorizado');
        }
    }
}
