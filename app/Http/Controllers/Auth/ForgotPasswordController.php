<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email'    => 'Formato de correo inválido.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Por seguridad, siempre mostramos el mismo mensaje
        if (!$user) {
            return back()->with('status', 'Si ese correo existe, recibirás un enlace de recuperación.');
        }

        // Generar token y guardar
        $token   = Str::random(64);
        $expires = Carbon::now()->addHour();

        User::where('email', $request->email)->update([
            'reset_token'   => $token,
            'reset_expires' => $expires,
        ]);

        $resetUrl = route('password.reset', ['token' => $token]);

        // Enviar email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error('Error enviando reset email: ' . $e->getMessage());
        }

        return back()->with('status', 'Si ese correo existe, recibirás un enlace de recuperación.');
    }
}
