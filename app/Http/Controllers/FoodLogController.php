<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FoodLog;
use Illuminate\Http\JsonResponse;

class FoodLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $date = $request->get('date', today()->toDateString());
        $logs = FoodLog::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->get();
        return response()->json(['success' => true, 'data' => $logs]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'food_name' => 'required|string|max:255',
            'servings' => 'nullable|numeric|min:0.1',
            'date' => 'nullable|date'
        ]);

        $log = FoodLog::create([
            'user_id' => auth()->id(),
            'food_name' => $request->food_name,
            'servings' => $request->servings ?? 1,
            'date' => $request->date ?? today()->toDateString(),
            'is_consumed' => false,
        ]);

        return response()->json(['success' => true, 'data' => $log, 'message' => 'Alimento agregado']);
    }

    public function consume(Request $request, $id): JsonResponse
    {
        $log = FoodLog::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $isConsumed = $request->boolean('is_consumed', true);

        $log->update([
            'is_consumed' => $isConsumed,
            'consumed_at' => $isConsumed ? now() : null,
        ]);

        return response()->json(['success' => true, 'message' => $isConsumed ? 'Marcado como consumido' : 'Desmarcado']);
    }

    public function destroy($id): JsonResponse
    {
        $log = FoodLog::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $log->delete();
        return response()->json(['success' => true, 'message' => 'Alimento eliminado']);
    }
}
