<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Goal;
use App\Models\Achievement;

/**
 * Controlador API para la gestión de Metas (Goals) e Insignias (Achievements).
 * 
 * Permite a los usuarios crear, leer, actualizar y eliminar sus objetivos de fitness.
 * Además, contiene la lógica para calcular el progreso de una meta y otorgar
 * recompensas virtuales (insignias) cuando el usuario alcanza el 100%.
 */
class GoalController extends Controller
{
    // Badges por categoría (igual que goals_api.php)
    private array $badges = [
        'peso'        => ['name' => 'Maestro del Peso',       'icon' => '⚖️', 'desc' => 'Alcanzaste tu meta de peso'],
        'musculo'     => ['name' => 'Constructor',             'icon' => '💪', 'desc' => 'Ganaste masa muscular'],
        'grasa'       => ['name' => 'Quema Grasa',             'icon' => '🔥', 'desc' => 'Redujiste tu grasa corporal'],
        'fuerza'      => ['name' => 'Fuerza Bruta',            'icon' => '🏋️', 'desc' => 'Aumentaste tu fuerza'],
        'resistencia' => ['name' => 'Resistencia Infinita',    'icon' => '🏃', 'desc' => 'Mejoraste tu resistencia'],
        'nutricion'   => ['name' => 'Nutricionista Pro',       'icon' => '🥗', 'desc' => 'Cumpliste tu plan nutricional'],
        'habitos'     => ['name' => 'Hábitos Saludables',      'icon' => '✨', 'desc' => 'Mantuviste buenos hábitos'],
        'otro'        => ['name' => 'Logro Especial',          'icon' => '🌟', 'desc' => 'Completaste una meta especial'],
    ];

    public function index(): JsonResponse
    {
        $goals = Goal::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $goals]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:50',
            'target_value' => 'required|numeric|min:0',
            'unit'         => 'required|string|max:20',
            'deadline'     => 'nullable|date',
        ], ['title.required' => 'El título es obligatorio.', 'target_value.required' => 'El valor objetivo es obligatorio.', 'unit.required' => 'La unidad es obligatoria.']);

        $data['user_id']  = auth()->id();
        $data['category'] = $data['category'] ?? 'otro';

        Goal::create($data);

        return response()->json(['success' => true, 'message' => 'Meta creada correctamente']);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $goal = Goal::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'nullable|string|max:50',
            'target_value' => 'required|numeric|min:0',
            'unit'         => 'required|string|max:20',
            'deadline'     => 'nullable|date',
        ]);

        $goal->update($data);

        return response()->json(['success' => true, 'message' => 'Meta actualizada']);
    }

    public function updateProgress(Request $request, int $id): JsonResponse
    {
        $request->validate(['current_value' => 'required|numeric|min:0']);

        $goal = Goal::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $currentValue = $request->current_value;
        $status       = $goal->status;
        $achievement  = null;

        if ($currentValue >= $goal->target_value && $status !== Goal::STATUS_COMPLETED) {
            $status      = Goal::STATUS_COMPLETED;
            $achievement = $this->unlockAchievement($goal);
        } elseif ($currentValue < $goal->target_value && $status === Goal::STATUS_COMPLETED) {
            $status = Goal::STATUS_ACTIVE;
        }

        $goal->update([
            'current_value' => $currentValue,
            'status'        => $status,
            'completed_at'  => $status === Goal::STATUS_COMPLETED ? now() : null,
        ]);

        $response = ['success' => true, 'message' => 'Progreso actualizado'];
        if ($achievement) {
            $response['achievement'] = $achievement;
        }

        return response()->json($response);
    }

    public function destroy(int $id): JsonResponse
    {
        Goal::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Meta eliminada']);
    }

    public function achievements(): JsonResponse
    {
        $achievements = Achievement::where('user_id', auth()->id())
            ->orderByDesc('earned_at')
            ->get();

        return response()->json(['success' => true, 'data' => $achievements]);
    }

    private function unlockAchievement(Goal $goal): ?string
    {
        $badge = $this->badges[$goal->category] ?? $this->badges['otro'];

        $exists = Achievement::where('user_id', auth()->id())
            ->where('badge_name', $badge['name'])
            ->exists();

        if (!$exists) {
            Achievement::create([
                'user_id'     => auth()->id(),
                'badge_name'  => $badge['name'],
                'badge_icon'  => $badge['icon'],
                'description' => $badge['desc'],
            ]);
            return $badge['name'];
        }

        return null;
    }
}
