<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Message;
use App\Models\User;

/**
 * Controlador API para la Mensajería Interna.
 * 
 * Permite a todos los usuarios del gym (clientes, entrenadores, 
 * nutricionistas y administradores) comunicarse entre sí en tiempo real.
 * Usa polling eficiente basado en timestamp para minimizar la carga de red.
 */
class MessageController extends Controller
{
    /**
     * Lista de conversaciones con todos los usuarios del gym.
     * Devuelve última actividad y conteo de mensajes no leídos para cada contacto.
     */
    public function conversations(): JsonResponse
    {
        $myId = auth()->id();

        $contacts = User::where('id', '<>', $myId)
            ->where('active', true)
            ->get(['id', 'name', 'email', 'role', 'profile_photo']);

        $conversations = $contacts->map(function ($contact) use ($myId) {
            $lastMsg = Message::betweenUsers($myId, $contact->id)
                ->orderByDesc('created_at')
                ->first();

            $unreadCount = Message::where('sender_id', $contact->id)
                ->where('receiver_id', $myId)
                ->unread()
                ->count();

            return [
                'id'                => $contact->id,
                'name'              => $contact->name,
                'email'             => $contact->email,
                'role'              => $contact->role,
                'profile_photo'     => $contact->profile_photo,
                'unread_count'      => $unreadCount,
                'last_message'      => $lastMsg?->message,
                'last_message_time' => $lastMsg?->created_at,
            ];
        });

        // Ordenar: primero los que tienen mensajes, luego por recencia
        $sorted = $conversations->sortByDesc('last_message_time')->values();

        return response()->json(['success' => true, 'data' => $sorted]);
    }

    /**
     * Hilo de mensajes entre el usuario autenticado y otro usuario.
     * Marca automáticamente como leídos los mensajes recibidos.
     */
    public function thread(Request $request): JsonResponse
    {
        $request->validate(['with_user_id' => 'required|integer|exists:users,id']);

        $myId    = auth()->id();
        $otherId = (int) $request->with_user_id;

        $messages = Message::betweenUsers($myId, $otherId)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at')
            ->get();

        // Marcar como leídos los mensajes del otro hacia mí
        Message::where('sender_id', $otherId)
            ->where('receiver_id', $myId)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    /**
     * Endpoint de polling eficiente: devuelve solo mensajes NUEVOS 
     * desde un timestamp dado, para no recargar la conversación completa.
     * 
     * GET /api/messages/poll?with_user_id=X&after=2026-06-10T12:00:00.000000Z
     */
    public function poll(Request $request): JsonResponse
    {
        $request->validate([
            'with_user_id' => 'required|integer|exists:users,id',
            'after'        => 'nullable|string',
        ]);

        $myId    = auth()->id();
        $otherId = (int) $request->with_user_id;

        $query = Message::betweenUsers($myId, $otherId)
            ->with(['sender:id,name'])
            ->orderBy('created_at');

        if ($request->filled('after')) {
            try {
                $afterDate = \Carbon\Carbon::parse($request->after);
                $query->where('created_at', '>', $afterDate);
            } catch (\Exception $e) {
                // Si el timestamp es inválido, ignorar el filtro
            }
        }

        $messages = $query->get();

        // Marcar nuevos mensajes del otro como leídos
        if ($messages->isNotEmpty()) {
            Message::where('sender_id', $otherId)
                ->where('receiver_id', $myId)
                ->unread()
                ->update(['is_read' => true]);
        }

        // Conteo global de no leídos de TODOS los contactos (para badges)
        $unreadByContact = Message::where('receiver_id', $myId)
            ->unread()
            ->selectRaw('sender_id, COUNT(*) as cnt')
            ->groupBy('sender_id')
            ->pluck('cnt', 'sender_id');

        return response()->json([
            'success'           => true,
            'messages'          => $messages,
            'unread_by_contact' => $unreadByContact,
            'server_time'       => now()->toIso8601String(),
        ]);
    }

    /**
     * Envía un nuevo mensaje.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        $message->load('sender:id,name');

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'created_at' => $message->created_at,
        ]);
    }

    /**
     * Edita un mensaje (solo el propio).
     */
    public function edit(Request $request, int $id): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id())
            ->firstOrFail();

        $message->update(['message' => $request->message]);

        return response()->json(['success' => true, 'message' => 'Mensaje editado correctamente.']);
    }

    /**
     * Elimina un mensaje (solo el propio).
     */
    public function destroy(int $id): JsonResponse
    {
        Message::where('id', $id)->where('sender_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Mensaje eliminado correctamente.']);
    }

    /**
     * Conteo total de mensajes no leídos del usuario autenticado.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Message::where('receiver_id', auth()->id())->unread()->count();
        return response()->json(['success' => true, 'count' => $count]);
    }
}
