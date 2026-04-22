<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TrainingPlan;

class AssignRoutine extends Command
{
    protected $signature = 'routine:assign {email}';
    protected $description = 'Asigna rutina de prueba a un usuario';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario no encontrado: $email");
            return 1;
        }

        $routines = [
            ['day_group' => 'lunes', 'exercise' => 'Press de banca', 'series' => 4, 'reps' => 12],
            ['day_group' => 'lunes', 'exercise' => 'Press inclinado', 'series' => 3, 'reps' => 10],
            ['day_group' => 'martes', 'exercise' => 'Press militar', 'series' => 4, 'reps' => 8],
            ['day_group' => 'martes', 'exercise' => 'Elevaciones laterales', 'series' => 3, 'reps' => 15],
            ['day_group' => 'miércoles', 'exercise' => 'Dominadas', 'series' => 4, 'reps' => 8],
            ['day_group' => 'miércoles', 'exercise' => 'Polea alta', 'series' => 3, 'reps' => 12],
            ['day_group' => 'jueves', 'exercise' => 'Sentadilla', 'series' => 4, 'reps' => 10],
            ['day_group' => 'jueves', 'exercise' => 'Prensa', 'series' => 3, 'reps' => 12],
            ['day_group' => 'viernes', 'exercise' => 'curl bíceps', 'series' => 3, 'reps' => 12],
            ['day_group' => 'viernes', 'exercise' => 'Extensión tríceps', 'series' => 3, 'reps' => 15],
        ];

        foreach ($routines as $routine) {
            TrainingPlan::create([
                'user_id' => $user->id,
                'assigned_by' => $user->id,
                ...$routine,
            ]);
        }

        $this->info("Rutina asignada a: {$user->name}");
        return 0;
    }
}