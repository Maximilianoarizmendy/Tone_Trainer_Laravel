<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador API para gestionar las Fotos de Perfil.
 * 
 * Permite a los usuarios cargar, procesar y almacenar de manera segura
 * imágenes para su avatar, actualizando la base de datos automáticamente.
 */
class ProfilePhotoController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
        ], [
            'profile_photo.required' => 'No se seleccionó ninguna foto.',
            'profile_photo.image'    => 'El archivo debe ser una imagen.',
            'profile_photo.mimes'    => 'Solo se permiten imágenes JPG, PNG o GIF.',
            'profile_photo.max'      => 'La imagen no puede superar los 5MB.',
        ]);

        $user = auth()->user();

        // Eliminar foto anterior si existe
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $extension = $request->file('profile_photo')->getClientOriginalExtension();
        $filename  = $user->id . '_' . time() . '.' . $extension;
        $path      = $request->file('profile_photo')->storeAs('uploads', $filename, 'public');

        $user->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'path'    => Storage::disk('public')->url($path),
            'message' => 'Foto actualizada correctamente.',
        ]);
    }
}
