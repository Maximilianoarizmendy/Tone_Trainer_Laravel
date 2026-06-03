<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingCompletion;
use App\Models\TrainingPlan;

class TrainingCompletionController extends Controller
{
    public function store(Request $request, $exerciseId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $exercise = TrainingPlan::findOrFail($exerciseId);
        $user = auth()->user();

        // Check if already completed today
        $alreadyCompleted = TrainingCompletion::where('user_id', $user->id)
            ->where('exercise_id', $exercise->id)
            ->whereDate('completed_at', today())
            ->exists();

        if ($alreadyCompleted) {
            return response()->json(['success' => false, 'message' => 'Ya completaste este ejercicio hoy.']);
        }

        TrainingCompletion::create([
            'user_id' => $user->id,
            'exercise_id' => $exercise->id,
            'completed_at' => now(),
            'notes' => $request->notes,
        ]);

        // Record attendance
        \App\Models\RoutineAttendance::updateOrCreate(
            ['user_id' => $user->id, 'training_plan_id' => $exercise->id, 'date' => today()],
            ['status' => 'present']
        );

        return response()->json(['success' => true, 'message' => 'Ejercicio marcado como completado.']);
    }
}
