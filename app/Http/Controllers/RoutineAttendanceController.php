<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RoutineAttendance;
use Illuminate\Http\JsonResponse;

class RoutineAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $me = auth()->user();
        if (!$me->isTrainer() && !$me->isAdmin()) {
            abort(403, 'No autorizado');
        }

        $query = RoutineAttendance::with('user');

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('from') && $request->from) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->has('to') && $request->to) {
            $query->where('date', '<=', $request->to);
        }

        $records = $query->orderBy('date', 'desc')->get();

        // Calculate summary
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $total - $present;
        $adherence = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return response()->json([
            'success' => true, 
            'data' => [
                'records' => $records,
                'summary' => compact('total', 'present', 'absent', 'adherence')
            ]
        ]);
    }
}
