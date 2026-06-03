<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TrainingPlan;
use App\Models\User;

/**
 * Controlador API para Planes de Entrenamiento (Rutinas).
 * 
 * Permite a los entrenadores asignar ejercicios específicos, series
 * y repeticiones a los usuarios. Integra alertas automáticas al
 * usuario cuando la rutina sufre modificaciones.
 */
class TrainingPlanController extends Controller
{
    /** GET /api/training-plan  — lista ejercicios del usuario (o de otro si es staff) */
    public function index(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $exercises = TrainingPlan::where('user_id', $userId)
            ->orderBy('day_group')
            ->orderBy('id')
            ->get();

        return response()->json(['success' => true, 'data' => $exercises]);
    }

    /** POST /api/training-plan  — agrega ejercicio (solo entrenador/admin) */
    public function store(Request $request): JsonResponse
    {
        $me = auth()->user();
        if (!$me->isStaff()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos.'], 403);
        }

        $data = $request->validate([
            'user_id'     => 'required|integer|exists:users,id',
            'day_group'   => 'required|string|max:50',
            'exercise'    => 'required|string|max:100',
            'series'      => 'required|integer|min:1',
            'reps'        => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $data['assigned_by'] = $me->id;

        $exercise = TrainingPlan::create($data);

        // Notificar al usuario (Req 15)
        $user = User::find($request->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\AppNotification(
                'Rutina Actualizada',
                'Tu entrenador ha agregado o modificado ejercicios en tu plan de entrenamiento.'
            ));
        }

        return response()->json(['success' => true, 'message' => 'Ejercicio añadido.', 'data' => $exercise]);
    }

    /** DELETE /api/training-plan/{id}  — elimina ejercicio (staff) */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $me = auth()->user();
        if (!$me->isStaff()) {
            return response()->json(['success' => false, 'message' => 'Sin permisos.'], 403);
        }

        $userId = $this->resolveUserId($request);

        $deleted = TrainingPlan::where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Ejercicio no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Ejercicio eliminado.']);
    }

    private function resolveUserId(Request $request): int
    {
        $me = auth()->user();
        if ($me->isStaff() && $request->has('user_id')) {
            return (int) $request->user_id;
        }
        return $me->id;
    }
}
