<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\UserPreference;

class SettingsController extends Controller
{
    public function show(): JsonResponse
    {
        $userId = auth()->id();

        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $userId],
            [
                'reminders'          => true,
                'push_notifications'=> true,
                'training_level'    => 'intermediate',
                'weekly_frequency'  => 3,
            ]
        );

        return response()->json([
            'success' => true,
            'data'   => $prefs,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'email_notifications'  => 'boolean',
            'workout_reminders'  => 'boolean',
            'training_level'     => 'nullable|string|in:beginner,intermediate,advanced',
            'weekly_frequency'    => 'nullable|integer|min:1|max:7',
            'preferred_schedule'    => 'nullable|string|in:morning,afternoon,evening',
            'goal'             => 'nullable|string|max:100',
            'current_password'  => 'nullable|string|required_with:new_password',
            'new_password'       => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W\d]).{8,}$/',
            'new_password_confirmation' => 'nullable|string|same:new_password',
        ]);

        $userId = auth()->id();

        if ($request->filled('new_password')) {
            $user = auth()->user();
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'La contraseña actual es incorrecta.'], 422);
            }
            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        $prefs = UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'email_notifications'  => $request->boolean('email_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
                'workout_reminders'   => $request->boolean('workout_reminders'),
                'training_level'     => $request->training_level,
                'weekly_frequency'    => $request->weekly_frequency,
                'preferred_schedule' => $request->preferred_schedule,
                'goal'              => $request->goal,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada.',
            'data'   => $prefs,
        ]);
    }

    public function destroyAccount(Request $request): JsonResponse
    {
        $request->validate([
            'confirm' => 'accepted',
        ]);

        $user = auth()->user();
        $user->active = false;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta desactivada.',
        ]);
    }
}