<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Challenge;
use App\Models\Achievement;

/**
 * Controlador API para la gestión de Retos (Challenges).
 * 
 * Permite a los entrenadores crear retos y a los usuarios inscribirse,
 * registrar su progreso diario y desbloquear insignias automáticas al completar
 * la meta del reto.
 */
class ChallengeController extends Controller
{
    public function index(): JsonResponse
    {
        $challenges = Challenge::where('is_active', true)->with('trainer:id,name')->get();
        return response()->json(['success' => true, 'data' => $challenges]);
    }

    public function myChallenges(): JsonResponse
    {
        $user = auth()->user();
        $challenges = $user->challenges()->with('trainer:id,name')->get();
        return response()->json(['success' => true, 'data' => $challenges]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeTrainer();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:weekly,monthly',
            'goal_type' => 'required|string|max:100',
            'goal_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $data['trainer_id'] = auth()->id();
        $challenge = Challenge::create($data);

        return response()->json(['success' => true, 'message' => 'Reto creado', 'data' => $challenge]);
    }

    public function join(Request $request, int $id): JsonResponse
    {
        $challenge = Challenge::where('is_active', true)->findOrFail($id);
        $user = auth()->user();

        if ($user->challenges()->where('challenge_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya estás inscrito'], 400);
        }

        $user->challenges()->attach($id);

        return response()->json(['success' => true, 'message' => 'Inscrito en el reto']);
    }

    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $request->validate(['progress' => 'required|numeric|min:0']);

        $user = auth()->user();
        $challenge = $user->challenges()->where('challenge_id', $id)->firstOrFail();

        $newProgress = $challenge->pivot->current_progress + $request->progress;
        $completed = $newProgress >= $challenge->goal_value;

        $user->challenges()->updateExistingPivot($id, [
            'current_progress' => min($newProgress, $challenge->goal_value),
            'completed' => $completed,
            'completed_at' => $completed ? now() : null,
        ]);

        if ($completed && !$challenge->pivot->completed) {
            // Req 28: Otorgar insignia virtual
            Achievement::firstOrCreate([
                'user_id' => $user->id,
                'badge_name' => 'Reto: ' . $challenge->title,
                'description' => 'Completaste el reto ' . $challenge->title,
                'badge_icon' => '🏆',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Progreso actualizado']);
    }

    private function authorizeTrainer(): void
    {
        if (auth()->user()->role !== \App\Models\User::ROLE_TRAINER && auth()->user()->role !== \App\Models\User::ROLE_ADMIN) {
            abort(403, 'No autorizado');
        }
    }
}
