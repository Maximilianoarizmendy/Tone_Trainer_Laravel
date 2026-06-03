<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

/**
 * Controlador API para la Gestión de Usuarios.
 * 
 * Permite a los administradores y al staff (entrenadores/nutricionistas)
 * listar, crear, modificar y desactivar cuentas de usuario. Incluye la
 * lógica para asignar automáticamente el entrenador o nutricionista
 * correspondiente al crear un usuario nuevo.
 */
class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $me = auth()->user();
        $query = User::where('id', '!=', $me->id)->where('active', true);

        if ($me->role === User::ROLE_TRAINER) {
            $query->where(function ($q) use ($me) {
                $q->where('trainer_id', $me->id)->orWhere('role', User::ROLE_USER);
            });
        } elseif ($me->role === User::ROLE_NUTRITIONIST) {
            $query->where(function ($q) use ($me) {
                $q->where('nutritionist_id', $me->id)->orWhere('role', User::ROLE_USER);
            });
        }

        $users = $query->orderBy('name')->get(['id', 'name', 'email', 'role', 'level', 'goal', 'phone', 'location']);

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $me = auth()->user();
        if (!in_array($me->role, [User::ROLE_ADMIN, User::ROLE_NUTRITIONIST, User::ROLE_TRAINER])) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'goal'     => 'nullable|string|max:255',
            'level'    => 'nullable|string|max:50',
            'weight'   => 'nullable|numeric|min:0|max:500',
            'height'   => 'nullable|numeric|min:0|max:300',
            'role'    => 'nullable|integer|in:1,3,4',
        ]);

        $userData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt('Temp' . rand(1000, 9999)),
            'role'    => $data['role'] ?? User::ROLE_USER,
            'active'  => true,
            'phone'   => $data['phone'] ?? null,
            'location' => $data['location'] ?? null,
            'goal'    => $data['goal'] ?? null,
            'level'   => $data['level'] ?? null,
            'weight'  => $data['weight'] ?? null,
            'height'  => $data['height'] ?? null,
        ];

        if ($me->role === User::ROLE_TRAINER) {
            $userData['trainer_id'] = $me->id;
        } elseif ($me->role === User::ROLE_NUTRITIONIST) {
            $userData['nutritionist_id'] = $me->id;
        }

        $user = User::create($userData);

        return response()->json(['success' => true, 'message' => 'Usuario creado', 'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $me = auth()->user();
        $user = User::findOrFail($id);

        if ($me->role !== User::ROLE_ADMIN && $user->id !== $me->id) {
            if ($me->role === User::ROLE_TRAINER && $user->trainer_id !== $me->id && $user->role !== User::ROLE_USER) {
                return response()->json(['success' => false, 'message' => 'No es tu usuario'], 403);
            }
            if ($me->role === User::ROLE_NUTRITIONIST && $user->nutritionist_id !== $me->id && $user->role !== User::ROLE_USER) {
                return response()->json(['success' => false, 'message' => 'No es tu usuario'], 403);
            }
        }

        $data = $request->validate([
            'name'     => 'nullable|string|max:100',
            'phone'    => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'goal'     => 'nullable|string|max:255',
            'level'    => 'nullable|string|max:50',
            'weight'   => 'nullable|numeric|min:0|max:500',
            'height'   => 'nullable|numeric|min:0|max:300',
        ]);

        $user->update(array_filter($data, fn($v) => $v !== null));

        return response()->json(['success' => true, 'message' => 'Usuario actualizado']);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                'role' => $user->role, 'phone' => $user->phone, 'location' => $user->location,
                'goal' => $user->goal, 'level' => $user->level, 'weight' => $user->weight,
                'height' => $user->height, 'birthdate' => $user->birthdate,
            ],
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $me = auth()->user();
        if ($me->role !== User::ROLE_ADMIN) {
            return response()->json(['success' => false, 'message' => 'Solo el admin puede desactivar'], 403);
        }

        $user = User::findOrFail($id);
        $user->active = false;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario desactivado']);
    }
}