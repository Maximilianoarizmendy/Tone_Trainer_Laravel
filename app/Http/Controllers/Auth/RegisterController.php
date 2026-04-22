<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function showForm()
    {
        // El formulario de registro vive en la vista de login (tabs)
        return redirect()->route('login')->with('_tab', 'register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => [
                'required', 'confirmed', 'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W\d]).{8,}$/',
            ],
            'birthdate'        => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'location'         => 'nullable|string|max:100',
            'goal'             => 'nullable|string|max:255',
            'level'            => 'nullable|string|max:50',
            'weight'           => 'nullable|numeric|min:0|max:500',
            'height'           => 'nullable|numeric|min:0|max:300',
            'medical_history'  => 'nullable|string|max:255',
        ], [
            'name.required'              => 'El nombre es obligatorio.',
            'email.required'             => 'El correo es obligatorio.',
            'email.email'                => 'El correo no tiene un formato válido.',
            'email.unique'               => 'Este correo ya está registrado.',
            'password.required'          => 'La contraseña es obligatoria.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'password.min'               => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'             => 'La contraseña debe tener al menos una mayúscula, una minúscula y un carácter especial o número.',
        ]);

        User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'birthdate'       => $request->birthdate,
            'phone'           => $request->phone,
            'location'       => $request->location,
            'goal'            => $request->goal,
            'level'           => $request->level,
            'weight'          => $request->weight,
            'height'          => $request->height,
            'medical_history' => $request->medical_history,
            'role'            => User::ROLE_USER,
            'active'          => true,
        ]);

        return redirect()->route('login')
            ->with('success', 'Cuenta creada con éxito. Ahora puedes iniciar sesión.');
    }
}
