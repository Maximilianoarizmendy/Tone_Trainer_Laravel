<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $user = auth()->user();

        // Eliminar foto anterior
        if (
            !empty($user->profile_photo) &&
            Storage::disk('public')->exists($user->profile_photo)
        ) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $file = $request->file('profile_photo');

        $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'uploads/profile_photos',
            $filename,
            'public'
        );

        $user->profile_photo = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto actualizada correctamente.',
            'profile_photo' => $path,
            'profile_photo_url' => asset('storage/' . $path)
        ]);
    }
}