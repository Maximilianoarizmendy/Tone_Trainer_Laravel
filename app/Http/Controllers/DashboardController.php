<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Payment;
use App\Models\Message;
use App\Models\Challenge;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Controlador Web para el Panel Principal (Dashboard).
 * 
 * Gestiona las vistas renderizadas con Blade que consumen los
 * usuarios al iniciar sesión. Sincroniza el rol de la sesión actual
 * y redirige a la vista correcta de entrenamiento, nutrición o progreso.
 */
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
        $myId = $user->id;

        // Obtener IDs de contactos aceptados
        $contactIds = DB::table('contact_requests')
            ->where('status', 'accepted')
            ->where(function($q) use ($myId) {
                $q->where('sender_id', $myId)
                  ->orWhere('receiver_id', $myId);
            })
            ->get()
            ->map(function($row) use ($myId) {
                return $row->sender_id == $myId ? $row->receiver_id : $row->sender_id;
            })
            ->toArray();

        // Todos los usuarios aceptados como contactos
        $contacts = User::whereIn('id', $contactIds)
            ->where('active', true)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'profile_photo']);

        return view('dashboard.messages', compact('user', 'contacts'));
    }

    /**
     * Mostrar la lista de pagos y membresías.
     */
    public function payments()
    {
        $user = auth()->user();
        $query = Payment::orderBy('created_at', 'desc');

        if ($user->role != User::ROLE_ADMIN) {
            $query->where('user_id', $user->id);
        }

        $payments = $query->paginate(15);
        $memberships = \App\Models\Membership::all();
        return view('dashboard.payments', compact('payments', 'memberships'));
    }

    /**
     * Reportes de desempeño del gimnasio (Req 25).
     */
    public function reports()
    {
        // Usuarios
        $totalUsers     = User::where('role', User::ROLE_USER)->count();
        $activeUsers    = User::where('role', User::ROLE_USER)->where('active', true)->count();
        $totalTrainers  = User::where('role', User::ROLE_TRAINER)->count();

        // Pagos
        $totalRevenue   = Payment::whereNotNull('amount_cents')->sum('amount_cents') / 100;
        $totalPayments  = Payment::count();
        $lastPayments   = Payment::orderByDesc('created_at')->limit(5)->get();

        // Retos
        $totalChallenges  = Challenge::count();
        $activeChallenges = Challenge::where('is_active', true)->where('end_date', '>=', now()->toDateString())->count();
        $completedRetos   = DB::table('challenge_user')->where('completed', true)->count();

        // Insignias
        $totalBadges = Achievement::count();

        // Nuevos usuarios por mes (últimos 6 meses)
        $newUsersPerMonth = User::where('role', User::ROLE_USER)
            ->select(DB::raw('MONTH(membership_start) as month, YEAR(membership_start) as year, COUNT(*) as total'))
            ->whereNotNull('membership_start')
            ->where('membership_start', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        return view('dashboard.reports', compact(
            'totalUsers', 'activeUsers', 'totalTrainers',
            'totalRevenue', 'totalPayments', 'lastPayments',
            'totalChallenges', 'activeChallenges', 'completedRetos',
            'totalBadges', 'newUsersPerMonth'
        ));
    }

    public function profile()
    {
        $user = auth()->user()->fresh();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Ranking de usuarios por logros (Req 29).
     */
    public function ranking()
    {
        $ranking = User::where('role', User::ROLE_USER)
            ->where('active', true)
            ->withCount('achievements')
            ->orderByDesc('achievements_count')
            ->get();

        return view('dashboard.ranking', compact('ranking'));
    }

    public function settings()
    {
        $user = auth()->user();
        return view('dashboard.settings', compact('user'));
    }

    public function users()
    {
        $me = auth()->user();
        $myRole = match ($me->role) {
            User::ROLE_ADMIN => 'admin',
            User::ROLE_TRAINER => 'trainer',
            User::ROLE_NUTRITIONIST => 'nutritionist',
            default => 'user',
        };
        $users = User::where('id', '!=', $me->id)->where('active', true)->get();
        $trainers = User::where('role', User::ROLE_TRAINER)->where('active', true)->where('is_verified', true)->get();
        return view('dashboard.users', compact('users', 'me', 'myRole', 'trainers'));
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

    public function trainerPlans()
    {
        return view('dashboard.trainer_plans');
    }

    public function attendance()
    {
        return view('dashboard.attendance_report');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'goal' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
        ]);

        $data = $request->only(['name', 'phone', 'location', 'goal', 'level', 'weight', 'height']);

        $user->update($data);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Permite al Nutricionista (o Admin) guardar una recomendación de texto para un usuario.
     */
    public function saveNutritionistNote(Request $request)
    {
        // Sólo roles admin o nutricionista pueden usar este endpoint
        $user = auth()->user();
        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_NUTRITIONIST])) {
            return back()->with('error', 'No tienes permiso para crear notas.');
        }

        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'note' => 'nullable|string|max:2000',
        ]);

        $target = User::findOrFail($request->target_user_id);
        $target->nutritionist_notes = $request->note;
        $target->save();

        return back()->with('success', 'Recomendación guardada correctamente.');
    }

    /**
     * Permite al Admin registrar un entrenador manualmente.
     */
    public function storeTrainer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_TRAINER,
            'active' => true,
            'is_verified' => true, // Lo registramos verificado por ser el admin
        ]);

        return back()->with('success', 'Entrenador registrado correctamente.');
    }

    /**
     * Permite al Admin registrar un nutricionista manualmente.
     */
    public function storeNutritionist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_NUTRITIONIST,
            'active' => true,
            'is_verified' => true, // Verificado por ser admin
        ]);

        return back()->with('success', 'Nutricionista registrado correctamente.');
    }

    /**
     * Permite al Admin ver la lista de entrenadores pendientes.
     */
    public function trainersVerification()
    {
        $pendingTrainers = User::where('role', User::ROLE_TRAINER)
            ->where('verification_status', 'pending')
            ->get();
        return view('dashboard.trainer_verification', compact('pendingTrainers'));
    }

    /**
     * Permite al Admin verificar o rechazar a un entrenador.
     */
    public function verifyTrainer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:approve,reject',
        ]);

        $trainer = User::where('id', $request->user_id)->where('role', User::ROLE_TRAINER)->firstOrFail();
        
        if ($request->action === 'approve') {
            $trainer->update(['is_verified' => true, 'verification_status' => 'approved']);
            $trainer->notify(new \App\Notifications\AppNotification('Verificación Aprobada', 'Tu cuenta de entrenador ha sido verificada con éxito.'));
            return back()->with('success', 'Entrenador verificado y notificado.');
        } else {
            $trainer->update(['is_verified' => false, 'verification_status' => 'rejected']);
            $trainer->notify(new \App\Notifications\AppNotification('Verificación Rechazada', 'Tu solicitud de entrenador no cumple con los requisitos.'));
            return back()->with('error', 'Entrenador rechazado.');
        }
    }

    /**
     * Permite al Admin asignar un entrenador a un usuario.
     */
    public function assignTrainer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'trainer_id' => 'nullable|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($request->trainer_id) {
            // Verificar que el ID asignado realmente pertenece a un entrenador
            $trainer = User::where('id', $request->trainer_id)->where('role', User::ROLE_TRAINER)->firstOrFail();
            $user->update(['trainer_id' => $trainer->id]);
            return back()->with('success', "Entrenador {$trainer->name} asignado a {$user->name}.");
        } else {
            // Si viene vacío, desasignamos
            $user->update(['trainer_id' => null]);
            return back()->with('success', "Entrenador desasignado de {$user->name}.");
        }
    }
    public function generateNutritionistAINote(Request $request)
    {
        // Only admin or nutritionist can generate notes
        $user = auth()->user();
        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_NUTRITIONIST])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'target_user_id' => 'required|exists:users,id',
        ]);

        $target = User::findOrFail($request->target_user_id);

        // Build prompt with user data
        $prompt = "Genera una nota de nutrición personalizada para un usuario con los siguientes datos: peso {$target->weight} kg, altura {$target->height} cm, objetivo {$target->goal}, nivel {$target->level}. Proporciona recomendaciones breves de alimentación y ejercicios.";

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un asistente de nutrición profesional.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            $note = $response->json('choices.0.message.content') ?? 'No se pudo generar la nota.';

            $target->nutritionist_notes = $note;
            $target->save();

            return response()->json(['success' => true, 'note' => $note]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar la nota: ' . $e->getMessage()], 500);
        }
    }
}