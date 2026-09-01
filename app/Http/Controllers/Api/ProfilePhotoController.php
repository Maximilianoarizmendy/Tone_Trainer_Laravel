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

        // Intentar subir a Cloudinary
        $cloudinaryService = new \App\Services\CloudinaryService();
        $cloudinaryUrl = $cloudinaryService->uploadFile($file, 'profile_photos');

        if ($cloudinaryUrl) {
            $photoPath = $cloudinaryUrl;
        } else {
            // Fallback a almacenamiento local si Cloudinary no responde
            $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('uploads/profile_photos', $filename, 'public');
        }

        $user->profile_photo = $photoPath;
        $user->save();

        $photoUrl = str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath);

        return response()->json([
            'success'           => true,
            'message'           => 'Foto de perfil actualizada correctamente.',
            'profile_photo'     => $photoPath,
            'profile_photo_url' => $photoUrl,
        ]);
    }
}