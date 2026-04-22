<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WorkoutCalendar;
use Carbon\Carbon;

class WorkoutCalendarController extends Controller
{
    public function index(): JsonResponse
    {
        $workouts = WorkoutCalendar::where('user_id', auth()->id())
            ->orderByDesc('workout_date')
            ->get();

        return response()->json(['success' => true, 'data' => $workouts]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'workout_date'     => 'required|date',
            'title'            => 'required|string|max:255',
            'workout_type'     => 'required|string|max:50',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned'  => 'nullable|integer|min:0',
            'notes'            => 'nullable|string',
            'completed'        => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();

        // ON DUPLICATE KEY UPDATE equivalent → updateOrCreate
        $workout = WorkoutCalendar::updateOrCreate(
            [
                'user_id'      => auth()->id(),
                'workout_date' => $data['workout_date'],
                'title'        => $data['title'],
            ],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Entrenamiento agregado', 'data' => $workout]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $workout = WorkoutCalendar::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'workout_type'     => 'sometimes|string|max:50',
            'duration_minutes' => 'sometimes|integer|min:1',
            'calories_burned'  => 'nullable|integer|min:0',
            'notes'            => 'nullable|string',
            'completed'        => 'sometimes|boolean',
        ]);

        $workout->update($data);

        return response()->json(['success' => true, 'message' => 'Entrenamiento actualizado']);
    }

    public function markComplete(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);

        WorkoutCalendar::where('id', $request->id)
            ->where('user_id', auth()->id())
            ->update(['completed' => true]);

        return response()->json(['success' => true, 'message' => 'Marcado como completado']);
    }

    public function destroy(int $id): JsonResponse
    {
        WorkoutCalendar::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Evento eliminado correctamente']);
    }

    public function stats(): JsonResponse
    {
        $userId = auth()->id();

        // Racha actual
        $dates = WorkoutCalendar::where('user_id', $userId)
            ->where('completed', true)
            ->orderByDesc('workout_date')
            ->pluck('workout_date');

        $streak = 0;
        $today  = Carbon::today();
        foreach ($dates as $date) {
            $diff = $today->diffInDays(Carbon::parse($date));
            if ($diff == $streak) {
                $streak++;
            } else {
                break;
            }
        }

        $thisMonth     = WorkoutCalendar::where('user_id', $userId)->completed()->thisMonth()->count();
        $totalMinutes  = WorkoutCalendar::where('user_id', $userId)->completed()->sum('duration_minutes');
        $totalWorkouts = WorkoutCalendar::where('user_id', $userId)->completed()->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'current_streak'  => $streak,
                'this_month'      => $thisMonth,
                'total_minutes'   => $totalMinutes ?? 0,
                'total_workouts'  => $totalWorkouts,
            ],
        ]);
    }
}
