<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DashboardController extends Controller
{
    // Refrescar rol desde BD en cada request del dashboard
    private function syncRole(): void
    {
        $user = auth()->user()->fresh();
        if ($user) {
            session(['user_rol' => $user->role]);
        }
    }

    public function index()
    {
        $this->syncRole();
        $user = auth()->user();
        return view('dashboard.index', compact('user'));
    }

    public function training()
    {
        $user = auth()->user();
        return view('dashboard.training', compact('user'));
    }

    public function nutrition()
    {
        $user = auth()->user();
        return view('dashboard.nutrition', compact('user'));
    }

    public function progress()
    {
        $user = auth()->user();
        return view('dashboard.progress', compact('user'));
    }

    public function goals()
    {
        $user = auth()->user();
        return view('dashboard.goals', compact('user'));
    }

    public function messages()
    {
        $user = auth()->user();
        return view('dashboard.messages', compact('user'));
    }

    public function profile()
    {
        $user = auth()->user()->fresh();
        return view('dashboard.profile', compact('user'));
    }

    public function settings()
    {
        $user = auth()->user();
        return view('dashboard.settings', compact('user'));
    }

    public function users()
    {
        $me = auth()->user();
        $myRole = match($me->role) {
            User::ROLE_ADMIN => 'admin',
            User::ROLE_TRAINER => 'trainer',
            User::ROLE_NUTRITIONIST => 'nutritionist',
            default => 'user',
        };
        $users = User::where('id', '!=', $me->id)->where('active', true)->get();
        return view('dashboard.users', compact('users', 'me', 'myRole'));
    }

    public function admin()
    {
        return $this->users();
    }

    public function nutritionist()
    {
        return $this->users();
    }

    public function trainer()
    {
        return $this->users();
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'goal'     => 'nullable|string|max:255',
            'level'    => 'nullable|string|max:50',
            'weight'   => 'nullable|numeric|min:0|max:500',
            'height'   => 'nullable|numeric|min:0|max:300',
        ]);

        $data = $request->only(['name', 'phone', 'location', 'goal', 'level', 'weight', 'height']);

        $user->update($data);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
