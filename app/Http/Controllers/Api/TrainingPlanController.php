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
            'reps'        => 'nullable|integer|min:1',
            'duration'    => 'nullable|string|max:50',
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

    /** GET /api/exercises-library — busca ejercicios en la biblioteca local JSON */
    public function library(Request $request): JsonResponse
    {
        $path = storage_path('app/exercises.json');
        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'La biblioteca de ejercicios no está disponible.'], 404);
        }

        $search = strtolower($request->query('search', ''));
        $category = strtolower($request->query('category', ''));

        $json = file_get_contents($path);
        $exercises = json_decode($json, true);

        if (!is_array($exercises)) {
            return response()->json(['success' => false, 'message' => 'Formato de biblioteca no válido.'], 500);
        }

        if ($search !== '') {
            $exercises = array_filter($exercises, function ($ex) use ($search) {
                $nameMatch = isset($ex['name']) && stripos($ex['name'], $search) !== false;
                $muscleMatch = false;
                if (isset($ex['primaryMuscles']) && is_array($ex['primaryMuscles'])) {
                    foreach ($ex['primaryMuscles'] as $m) {
                        if (stripos($m, $search) !== false) {
                            $muscleMatch = true;
                            break;
                        }
                    }
                }
                return $nameMatch || $muscleMatch;
            });
        }

        if ($category !== '') {
            $exercises = array_filter($exercises, function ($ex) use ($category) {
                return isset($ex['category']) && strtolower($ex['category']) === $category;
            });
        }

        // Limitar resultados a 50 para rapidez
        $exercises = array_slice(array_values($exercises), 0, 50);

        return response()->json(['success' => true, 'data' => $exercises]);
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
