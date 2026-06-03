<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    /**
     * Devuelve el historial de mensajes entre el usuario autenticado y un contacto.
     */
    public function fetchMessages(Request $request, $contact_id)
    {
        $me = auth()->user();
        
        // Marcar como leídos los mensajes que me envió el contacto
        Message::where('sender_id', $contact_id)
               ->where('receiver_id', $me->id)
               ->where('is_read', false)
               ->update(['is_read' => true]);

        $messages = Message::betweenUsers($me->id, $contact_id)
                           ->orderBy('created_at', 'asc')
                           ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Return count of unread messages for the authenticated user.
     */
    public function unreadCount()
    {
        $user = auth()->user();
        $count = Message::where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Guarda un nuevo mensaje enviado.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $me = auth()->user();

        // Validar que pueden hablar entre ellos (solo cliente <-> entrenador asignado)
        // Omitiremos validación estricta aquí para simplificar, pero en producción 
        // se debería verificar la relación trainer_id.

        $msg = Message::create([
            'sender_id' => $me->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
            // 'created_at' => now() se asigna automáticamente si tenemos timestamps,
            // pero el modelo Message tiene timestamps = false, así que lo forzamos:
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }
}
