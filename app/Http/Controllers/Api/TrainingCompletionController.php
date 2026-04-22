<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\TrainingPlan;
use App\Models\TrainingCompletion;
use App\Models\User;
use Carbon\Carbon;

class TrainingCompletionController extends Controller
{
    public function markCompleted(Request $request): JsonResponse
    {
        $request->validate(['exercise_id' => 'required|integer']);

        $exercise = TrainingPlan::where('id', $request->exercise_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        TrainingCompletion::create([
            'user_id'      => auth()->id(),
            'exercise_id'  => $exercise->id,
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Ejercicio marcado como completado.']);
    }

    public function unmark(Request $request): JsonResponse
    {
        $request->validate(['exercise_id' => 'required|integer']);

        TrainingCompletion::where('user_id', auth()->id())
            ->where('exercise_id', $request->exercise_id)
            ->whereDate('completed_at', today())
            ->delete();

        return response()->json(['success' => true, 'message' => 'Ejercicio desmarcado.']);
    }

    public function todayCompletions(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $completions = TrainingCompletion::where('user_id', $userId)
            ->today()
            ->select('exercise_id', \DB::raw('MAX(completed_at) as last_completed'))
            ->groupBy('exercise_id')
            ->get();

        return response()->json(['success' => true, 'data' => $completions]);
    }

    public function stats(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $totalExercises = TrainingPlan::where('user_id', $userId)->count();
        $completedToday = TrainingCompletion::where('user_id', $userId)->today()->distinct('exercise_id')->count('exercise_id');
        $completedWeek  = TrainingCompletion::where('user_id', $userId)->where('completed_at', '>=', now()->subDays(7))->count();
        $completedMonth = TrainingCompletion::where('user_id', $userId)->where('completed_at', '>=', now()->subDays(30))->count();

        // Racha actual
        $dates = TrainingCompletion::where('user_id', $userId)
            ->selectRaw('DATE(completed_at) as comp_date')
            ->distinct()
            ->orderByDesc('comp_date')
            ->limit(30)
            ->pluck('comp_date');

        $streak  = 0;
        $current = Carbon::today();
        foreach ($dates as $date) {
            if ($current->diffInDays(Carbon::parse($date)) == $streak) {
                $streak++;
            } else {
                break;
            }
        }

        $adherence = $totalExercises > 0 ? round(($completedToday / $totalExercises) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data'    => compact('totalExercises', 'completedToday', 'completedWeek', 'completedMonth', 'streak', 'adherence'),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $userId = $this->resolveUserId($request);

        $history = TrainingCompletion::where('training_completions.user_id', $userId)
            ->join('training_plan', 'training_completions.exercise_id', '=', 'training_plan.id')
            ->select(
                'training_completions.id',
                'training_completions.exercise_id',
                'training_plan.exercise as exercise_name',
                'training_plan.day_group',
                \DB::raw("DATE_FORMAT(training_completions.completed_at, '%Y-%m-%d %H:%i') as completed_at")
            )
            ->orderByDesc('training_completions.completed_at')
            ->limit(50)
            ->get();

        return response()->json(['success' => true, 'data' => $history]);
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
