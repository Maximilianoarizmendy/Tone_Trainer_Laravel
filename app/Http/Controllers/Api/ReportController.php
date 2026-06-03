<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Payment;
use App\Models\TrainingCompletion;

/**
 * Controlador API para Reportes del Gimnasio.
 * 
 * Exclusivo para administradores. Agrega estadísticas globales de la 
 * plataforma, tales como el total de usuarios, ingresos por membresías,
 * y los reportes de asistencia virtual (rutinas completadas recientemente).
 */
class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorizeAdmin();

        $totalUsers = User::where('role', User::ROLE_USER)->count();
        $totalTrainers = User::where('role', User::ROLE_TRAINER)->count();

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        $trainingsCompleted = TrainingCompletion::count();

        // Req 23: Reportes de asistencia virtual
        $recentCompletions = TrainingCompletion::with(['user:id,name', 'exercise:id,exercise'])
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get()
            ->map(function ($comp) {
                return [
                    'user' => $comp->user->name,
                    'exercise' => $comp->exercise ? $comp->exercise->exercise : 'N/A',
                    'date' => $comp->completed_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'total_trainers' => $totalTrainers,
                'total_revenue' => $totalRevenue,
                'trainings_completed' => $trainingsCompleted,
                'recent_attendance' => $recentCompletions,
            ]
        ]);
    }

    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== User::ROLE_ADMIN) {
            abort(403, 'No autorizado');
        }
    }

}
