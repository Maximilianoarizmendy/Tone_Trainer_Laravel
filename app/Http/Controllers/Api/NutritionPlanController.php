<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\NutritionPlan;
use App\Models\User;
use App\Mail\MealPlanNotification;
use Illuminate\Support\Facades\Mail;

class NutritionPlanController extends Controller
{
    private function resolveTargetUser(Request $request): User
    {
        $me = auth()->user();

        if (($me->isAdmin() || $me->isNutritionist()) && $request->has('target_user_id')) {
            $targetId = (int) $request->target_user_id;
            abort_unless($targetId > 0, 422, 'ID de usuario inválido.');
            return User::findOrFail($targetId);
        }

        if ($request->has('target_user_id') && (int) $request->target_user_id !== $me->id) {
            abort(403, 'No tienes permiso para gestionar este plan.');
        }

        return $me;
    }

    public function index(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $meals = NutritionPlan::where('user_id', $targetUser->id)
            ->orderByRaw("CASE day_of_week WHEN 'Lunes' THEN 1 WHEN 'Martes' THEN 2 WHEN 'Miércoles' THEN 3 WHEN 'Jueves' THEN 4 WHEN 'Viernes' THEN 5 WHEN 'Sábado' THEN 6 WHEN 'Domingo' THEN 7 ELSE 8 END, meal_type")
            ->get();

        return response()->json(['success' => true, 'data' => $meals]);
    }

    public function store(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $data = $request->validate([
            'day'      => 'required|string',
            'mealTime' => 'required|string',
            'foodName' => 'required|string|max:100',
            'calories' => 'required|integer|min:0',
            'protein'  => 'required|integer|min:0',
            'carbs'    => 'required|integer|min:0',
            'fats'     => 'required|integer|min:0',
        ]);

        $meal = NutritionPlan::create([
            'user_id'    => $targetUser->id,
            'day_of_week'=> $data['day'],
            'meal_type'  => $data['mealTime'],
            'food_name'  => $data['foodName'],
            'calories'   => $data['calories'],
            'protein'    => $data['protein'],
            'carbs'      => $data['carbs'],
            'fats'       => $data['fats'],
        ]);

        // Notificación si el nutri/admin edita el plan de otro usuario
        if ($targetUser->id !== auth()->id()) {
            $details = "<strong>{$data['foodName']}</strong><br>"
                . "📅 Día: {$data['day']}<br>🍽️ Comida: {$data['mealTime']}<br>"
                . "🔥 Calorías: {$data['calories']} kcal<br>"
                . "🥩 Proteínas: {$data['protein']}g | 🍞 Carbos: {$data['carbs']}g | 🥑 Grasas: {$data['fats']}g";
            try {
                Mail::to($targetUser->email)->send(new MealPlanNotification($targetUser, 'add', $details));
            } catch (\Exception $e) {
                \Log::warning('Meal plan email failed: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'id' => $meal->id]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        $deleted = NutritionPlan::where('id', $id)->where('user_id', $targetUser->id)->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'error' => 'Comida no encontrada o ya eliminada.'], 404);
        }

        if ($targetUser->id !== auth()->id()) {
            try {
                Mail::to($targetUser->email)->send(new MealPlanNotification($targetUser, 'delete', ''));
            } catch (\Exception $e) {
                \Log::warning('Meal plan email failed: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }

    public function reset(Request $request): JsonResponse
    {
        $targetUser = $this->resolveTargetUser($request);

        NutritionPlan::where('user_id', $targetUser->id)->delete();

        if ($targetUser->id !== auth()->id()) {
            try {
                Mail::to($targetUser->email)->send(new MealPlanNotification($targetUser, 'reset', ''));
            } catch (\Exception $e) {
                \Log::warning('Meal plan email failed: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true]);
    }
}
