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

        $contactIds = \Illuminate\Support\Facades\DB::table('contact_requests')
            ->where('status', 'accepted')
            ->where(function($q) use ($myId) {
                $q->where('sender_id', $myId)
                  ->orWhere('receiver_id', $myId);
            })
            ->get()
            ->map(function($row) use ($myId) {
                return $row->sender_id == $myId ? $row->receiver_id : $row->sender_id;
            })
            ->toArray();

        $contacts = User::whereIn('id', $contactIds)
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
            'after_id'     => 'nullable|integer',
        ]);

        $myId    = auth()->id();
        $otherId = (int) $request->with_user_id;

        $query = Message::betweenUsers($myId, $otherId)
            ->with(['sender:id,name'])
            ->orderBy('created_at');

        if ($request->filled('after_id')) {
            $query->where('id', '>', (int)$request->after_id);
        } elseif ($request->filled('after')) {
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

    /**
     * Buscar usuarios activos para enviar solicitud de contacto.
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1']);
        $q = $request->q;
        $myId = auth()->id();

        $users = User::where('id', '<>', $myId)
            ->where('active', true)
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'role', 'profile_photo']);

        $results = $users->map(function($user) use ($myId) {
            $req = \App\Models\ContactRequest::where(function($query) use ($myId, $user) {
                $query->where('sender_id', $myId)->where('receiver_id', $user->id);
            })->orWhere(function($query) use ($myId, $user) {
                $query->where('sender_id', $user->id)->where('receiver_id', $myId);
            })->first();

            return [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'profile_photo'  => $user->profile_photo,
                'request_status' => $req ? $req->status : null,
                'is_sender'      => $req ? ($req->sender_id === $myId) : false,
                'request_id'     => $req ? $req->id : null,
            ];
        });

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * Obtener solicitudes de contacto pendientes recibidas.
     */
    public function pendingRequests(): JsonResponse
    {
        $myId = auth()->id();

        $requests = \App\Models\ContactRequest::where('receiver_id', $myId)
            ->where('status', 'pending')
            ->with('sender:id,name,email,role,profile_photo')
            ->get();

        return response()->json(['success' => true, 'data' => $requests]);
    }

    /**
     * Enviar una solicitud de contacto a un usuario.
     */
    public function sendRequest(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        $myId = auth()->id();
        $receiverId = (int)$request->receiver_id;

        if ($myId === $receiverId) {
            return response()->json(['success' => false, 'message' => 'No puedes agregarte a ti mismo.'], 400);
        }

        $existing = \App\Models\ContactRequest::where(function($q) use ($myId, $receiverId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function($q) use ($myId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return response()->json(['success' => false, 'message' => 'Ya son contactos.'], 400);
            }
            if ($existing->status === 'pending') {
                return response()->json(['success' => false, 'message' => 'Ya existe una solicitud pendiente.'], 400);
            }
            $existing->delete();
        }

        $newRequest = \App\Models\ContactRequest::create([
            'sender_id'   => $myId,
            'receiver_id' => $receiverId,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada correctamente.',
            'data'    => $newRequest
        ]);
    }

    /**
     * Aceptar una solicitud de contacto recibida.
     */
    public function acceptRequest(int $id): JsonResponse
    {
        $myId = auth()->id();

        $req = \App\Models\ContactRequest::where('id', $id)
            ->where('receiver_id', $myId)
            ->firstOrFail();

        $req->update(['status' => 'accepted']);

        return response()->json(['success' => true, 'message' => 'Solicitud aceptada. Ahora pueden chatear.']);
    }

    /**
     * Rechazar o cancelar una solicitud de contacto.
     */
    public function declineRequest(int $id): JsonResponse
    {
        $myId = auth()->id();

        $req = \App\Models\ContactRequest::where('id', $id)
            ->where(function($query) use ($myId) {
                $query->where('receiver_id', $myId)
                      ->orWhere('sender_id', $myId);
            })
            ->firstOrFail();

        $req->delete();

        return response()->json(['success' => true, 'message' => 'Solicitud eliminada correctamente.']);
    }
}
