<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Controlador API para la Tabla de Clasificación (Ranking).
 * 
 * Calcula y devuelve una lista ordenada de usuarios según un puntaje.
 * El puntaje se basa en la cantidad de entrenamientos completados
 * y las insignias (logros) que el usuario ha obtenido.
 */
class RankingController extends Controller
{
    public function index(): JsonResponse
    {
        // Calculate points based on completed workouts and achievements
        $ranking = User::where('role', User::ROLE_USER)
            ->where('active', true)
            ->withCount(['trainingCompletions as points_from_workouts', 'achievements as points_from_badges'])
            ->get()
            ->map(function ($user) {
                $score = ($user->points_from_workouts * 5) + ($user->points_from_badges * 50);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_photo' => $user->profile_photo,
                    'score' => $score,
                    'badges' => $user->points_from_badges,
                ];
            })
            ->sortByDesc('score')
            ->values();

        return response()->json(['success' => true, 'data' => $ranking]);
    }
}
