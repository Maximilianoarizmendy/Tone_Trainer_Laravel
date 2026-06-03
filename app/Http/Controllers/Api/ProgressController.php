<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Progress;
use App\Models\User;

/**
 * Controlador API para Métricas y Progreso Físico.
 * 
 * Los usuarios pueden registrar y visualizar su historial de datos físicos
 * (peso, grasa, hidratación). El personal (entrenadores, nutricionistas)
 * puede revisar y validar oficialmente el progreso, dejando retroalimentación.
 */
class ProgressController extends Controller
{
    private function resolveTargetUser(Request $request): User
    {
        $me = auth()->user();

        if ($me->isStaff() && $request->has('user_id')) {
            return User::findOrFail((int) $request->user_id);
        }

        if ($request->has('user_id') && (int) $request->user_id !== $me->id) {
            abort(403, 'No tienes permiso para ver estos datos.');
        }

        return $me;
    }

    public function getMetrics(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $metrics = Progress::where('user_id', $targetUser->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($p) {
                return [
                    'id'             => $p->id,
                    'weight'         => $p->weight,
                    'body_fat'       => $p->body_fat,
                    'muscle_mass'    => $p->muscle_mass,
                    'bmi'            => $p->bmi,
                    'water_intake'   => $p->water_intake,
                    'protein_intake' => $p->protein_intake,
                    'is_validated'   => $p->is_validated,
                    'trainer_comment'=> $p->trainer_comment,
                    'date'           => $p->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json(['success' => true, 'data' => $metrics]);
    }

    public function compare(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $data = Progress::where('user_id', $targetUser->id)
            ->betweenDates($request->from . ' 00:00:00', $request->to . ' 23:59:59')
            ->orderBy('created_at')
            ->get(['weight', 'body_fat', 'muscle_mass', 'bmi', 'created_at']);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function updateMetrics(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $data = $request->validate([
            'weight'  => 'required|numeric|min:0',
            'fat'     => 'required|numeric|min:0',
            'muscle'  => 'required|numeric|min:0',
            'bmi'     => 'required|numeric|min:0',
            'water'   => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
        ]);

        Progress::create([
            'user_id'        => $targetUser->id,
            'weight'         => $data['weight'],
            'body_fat'       => $data['fat'],
            'muscle_mass'    => $data['muscle'],
            'bmi'            => $data['bmi'],
            'water_intake'   => $data['water'],
            'protein_intake' => $data['protein'],
        ]);

        return response()->json(['success' => true, 'message' => 'Datos guardados correctamente.']);
    }

    public function validateProgress(Request $request, int $id): JsonResponse
    {
        $me = auth()->user();
        if (!in_array($me->role, [User::ROLE_TRAINER, User::ROLE_NUTRITIONIST, User::ROLE_ADMIN])) {
            abort(403, 'Solo el personal puede validar el progreso.');
        }

        $progress = Progress::findOrFail($id);

        $data = $request->validate([
            'trainer_comment' => 'required|string',
        ]);

        $progress->is_validated = true;
        $progress->trainer_comment = $data['trainer_comment'];
        $progress->save();

        // Notificar al usuario (Req 19 feedback)
        $user = User::find($progress->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\AppNotification(
                'Progreso Validado', 
                'Tu entrenador ha revisado y comentado tus métricas recientes.'
            ));
        }

        return response()->json(['success' => true, 'message' => 'Progreso validado con éxito.']);
    }
}
