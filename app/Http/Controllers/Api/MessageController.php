<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function conversations(): JsonResponse
    {
        $myId = auth()->id();

        $contacts = User::where('id', '<>', $myId)->get();

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
                'unread_count'      => $unreadCount,
                'last_message'      => $lastMsg?->message,
                'last_message_time' => $lastMsg?->created_at,
            ];
        });

        // Ordenar por último mensaje
        $sorted = $conversations->sortByDesc('last_message_time')->values();

        return response()->json(['success' => true, 'data' => $sorted]);
    }

    public function thread(Request $request): JsonResponse
    {
        $request->validate(['with_user_id' => 'required|integer']);

        $myId     = auth()->id();
        $otherId  = (int) $request->with_user_id;

        $messages = Message::betweenUsers($myId, $otherId)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at')
            ->get();

        // Marcar como leídos
        Message::where('sender_id', $otherId)
            ->where('receiver_id', $myId)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

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
        ]);

        return response()->json([
            'success'    => true,
            'message_id' => $message->id,
            'created_at' => $message->created_at,
        ]);
    }

    public function edit(Request $request, int $id): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $message = Message::where('id', $id)->where('sender_id', auth()->id())->firstOrFail();
        $message->update(['message' => $request->message]);

        return response()->json(['success' => true, 'message' => 'Mensaje editado correctamente.']);
    }

    public function destroy(int $id): JsonResponse
    {
        Message::where('id', $id)->where('sender_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Mensaje eliminado correctamente.']);
    }

    public function unreadCount(): JsonResponse
    {
        $count = Message::where('receiver_id', auth()->id())->unread()->count();
        return response()->json(['success' => true, 'count' => $count]);
    }
}
