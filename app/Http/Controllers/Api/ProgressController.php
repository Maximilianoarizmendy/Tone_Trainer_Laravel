<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Progress;
use App\Models\User;

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
                    'date'           => $p->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json(['success' => true, 'data' => $metrics]);
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
}
