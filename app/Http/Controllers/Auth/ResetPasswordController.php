<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token)
    {
        // Verificar que el token existe y no ha expirado
        $user = User::where('reset_token', $token)
            ->where('reset_expires', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'El enlace de recuperación es inválido o ha expirado.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $user->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'password'              => [
                'required', 'confirmed', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W\d]).{8,}$/',
            ],
        ], [
            'password.required'  => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'     => 'La contraseña debe tener al menos una mayúscula, una minúscula y un carácter especial o número.',
        ]);

        $user = User::where('reset_token', $request->token)
            ->where('reset_expires', '>', Carbon::now())
            ->first();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'El enlace de recuperación es inválido o ha expirado.']);
        }

        $user->update([
            'password'      => Hash::make($request->password),
            'reset_token'   => null,
            'reset_expires' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Contraseña restablecida exitosamente. Ya puedes iniciar sesión.');
    }
}
