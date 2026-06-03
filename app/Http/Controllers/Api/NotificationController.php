<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralAnnouncement;
use App\Notifications\AppNotification;

/**
 * Controlador API para el Sistema de Notificaciones.
 * 
 * Gestiona las notificaciones In-App generadas por el sistema (alertas de rutinas,
 * progreso validado). También proporciona métodos exclusivos para el Administrador
 * para enviar comunicados masivos (broadcast) a todos los usuarios de la plataforma.
 */
class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $notifications = $user->notifications()->take(20)->get();
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true, 'message' => 'Notificación leída']);
    }

    public function broadcast(Request $request): JsonResponse
    {
        if (auth()->user()->role !== User::ROLE_ADMIN) {
            abort(403, 'No autorizado');
        }

        $data = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        $users = User::where('active', true)->get();
        Notification::send($users, new AppNotification($data['title'], $data['message']));

        return response()->json(['success' => true, 'message' => 'Comunicado enviado a todos los usuarios.']);
    }

    public function sendToUser(Request $request, int $userId): JsonResponse
    {
        $me = auth()->user();
        if ($me->role === User::ROLE_USER) {
            abort(403, 'No autorizado');
        }

        $data = $request->validate([
            'title' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        $user = User::findOrFail($userId);
        $user->notify(new AppNotification($data['title'], $data['message']));

        return response()->json(['success' => true, 'message' => 'Notificación enviada.']);
    }
}
