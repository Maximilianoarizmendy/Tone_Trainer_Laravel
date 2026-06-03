<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChallengeController extends Controller
{
    /** Lista de retos disponibles (usuario) y retos creados (entrenador) */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === User::ROLE_TRAINER) {
            // El entrenador ve sus propios retos
            $challenges = Challenge::where('trainer_id', $user->id)
                ->withCount('users as participants')
                ->orderByDesc('created_at')->get();
            return view('dashboard.challenges', compact('challenges', 'user'));
        }

        // Usuario normal: ve los retos activos
        $challenges = Challenge::where('is_active', true)
            ->where('end_date', '>=', now()->toDateString())
            ->with('trainer')
            ->orderBy('end_date')
            ->get();

        // Retos en los que ya está inscrito
        $enrolledIds = $user->challenges()->pluck('challenges.id')->toArray();

        return view('dashboard.challenges', compact('challenges', 'user', 'enrolledIds'));
    }

    /** Entrenador crea un reto */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'type'        => 'required|in:semanal,mensual',
            'goal_type'   => 'required|string|max:60',
            'goal_value'  => 'required|numeric|min:1',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
        ]);

        Challenge::create([
            'trainer_id'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'goal_type'   => $request->goal_type,
            'goal_value'  => $request->goal_value,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'is_active'   => true,
        ]);

        return back()->with('success', '¡Reto creado exitosamente!');
    }

    /** Usuario se inscribe en un reto (AJAX) */
    public function join($id)
    {
        $user      = auth()->user();
        $challenge = Challenge::findOrFail($id);

        // Si ya está inscrito, no hacer nada
        if ($user->challenges()->where('challenge_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya estás inscrito.']);
        }

        $user->challenges()->attach($challenge->id, [
            'current_progress' => 0,
            'completed'        => false,
        ]);

        // Notificar al usuario
        \App\Models\Notification::create([
            'user_id'      => $user->id,
            'from_user_id' => $challenge->trainer_id,
            'type'         => 'reto',
            'message'      => "Te has inscrito en el reto: \"{$challenge->title}\"",
            'is_read'      => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Inscripción exitosa.']);
    }

    /** Usuario actualiza su progreso en un reto (AJAX) */
    public function updateProgress(Request $request, $id)
    {
        $request->validate(['progress' => 'required|numeric|min:0']);
        $user      = auth()->user();
        $challenge = Challenge::findOrFail($id);

        $pivot = $user->challenges()->where('challenge_id', $id)->first();
        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'No estás inscrito en este reto.']);
        }

        $newProgress = (float) $request->progress;
        $goalValue   = (float) $challenge->goal_value;
        $completed   = $newProgress >= $goalValue;

        $user->challenges()->updateExistingPivot($id, [
            'current_progress' => $newProgress,
            'completed'        => $completed,
            'completed_at'     => $completed ? now() : null,
        ]);

        // Si acaba de completarlo, otorgar insignia
        if ($completed) {
            $alreadyBadged = Achievement::where('user_id', $user->id)
                ->where('description', "Reto #{$id}")->exists();

            if (!$alreadyBadged) {
                Achievement::create([
                    'user_id'    => $user->id,
                    'badge_name' => "Reto Completado: {$challenge->title}",
                    'badge_icon' => '🏆',
                    'description'=> "Reto #{$id}",
                ]);

                // Notificación de logro
                \App\Models\Notification::create([
                    'user_id'      => $user->id,
                    'from_user_id' => $challenge->trainer_id,
                    'type'         => 'logro',
                    'message'      => "¡Completaste el reto \"{$challenge->title}\" y ganaste una insignia 🏆!",
                    'is_read'      => false,
                ]);
            }
        }

        return response()->json([
            'success'   => true,
            'completed' => $completed,
            'message'   => $completed ? '¡Felicidades! ¡Reto completado y insignia otorgada!' : 'Progreso actualizado.',
        ]);
    }

    /** Eliminar reto (entrenador) */
    public function destroy($id)
    {
        $challenge = Challenge::where('id', $id)->where('trainer_id', auth()->id())->firstOrFail();
        $challenge->delete();
        return back()->with('success', 'Reto eliminado.');
    }
}
