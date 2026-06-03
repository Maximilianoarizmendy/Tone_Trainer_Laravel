<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** Devuelve notificaciones del usuario autenticado (AJAX) */
    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /** Marca todas las notificaciones del usuario como leídas */
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /** Admin envia un comunicado general a todos los usuarios */
    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $adminId = auth()->id();

        // Obtener todos los usuarios activos (excepto el admin)
        $userIds = \App\Models\User::where('active', true)
            ->where('id', '!=', $adminId)
            ->pluck('id');

        $now = now();
        $rows = $userIds->map(fn($uid) => [
            'user_id'      => $uid,
            'from_user_id' => $adminId,
            'type'         => 'comunicado',
            'message'      => $request->message,
            'is_read'      => false,
            'created_at'   => $now,
        ])->toArray();

        Notification::insert($rows);

        return back()->with('success', 'Comunicado enviado a ' . count($rows) . ' usuarios.');
    }
}
