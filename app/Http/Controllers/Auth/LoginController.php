<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'El correo no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credentials = $request->only('email', 'password');

        // Solo usuarios activos
        $user = User::where('email', $credentials['email'])->where('active', 1)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->withInput($request->only('email'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Mostrar pantalla de carga y luego redirigir según rol
            $redirectUrl = $this->getRedirectByRole(Auth::user()->role);

            return view('auth.loading', [
                'userName'    => Auth::user()->name,
                'redirectUrl' => $redirectUrl,
            ]);
        }

        return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return view('auth.loading-out');
    }

    private function getRedirectByRole(int $role): string
    {
        return match($role) {
            User::ROLE_ADMIN => route('dashboard.users'),
            User::ROLE_NUTRITIONIST => route('dashboard.nutritionist'),
            User::ROLE_TRAINER => route('dashboard.trainer'),
            default => route('dashboard.index'),
        };
    }
}
