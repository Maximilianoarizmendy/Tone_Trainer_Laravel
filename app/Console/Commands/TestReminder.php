<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TrainingPlan;
use App\Models\TrainingCompletion;
use App\Notifications\TrainingReminder;
use Carbon\Carbon;

class TestReminder extends Command
{
    protected $signature = 'reminders:test {email}';
    protected $description = 'Envía un recordatorio de prueba con rutina real';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario no encontrado: $email");
            return 1;
        }

        $exercises = TrainingPlan::where('user_id', $user->id)->get();

        if ($exercises->isEmpty()) {
            $this->warn("El usuario no tiene rutinas asignadas.");
            return 1;
        }

        $dayName = 'hoy';
        $todayExercises = $exercises->take(3);
        $exerciseList = $todayExercises->pluck('exercise')->implode(', ');

        $completed = TrainingCompletion::where('user_id', $user->id)
            ->whereDate('completed_at', Carbon::today())
            ->count();

        $message = "Día: {$dayName}. Ejercicios: {$exerciseList}. Completados: {$completed}/{$todayExercises->count()}";

        $user->notify(new TrainingReminder("Recordatorio - {$dayName}", $message));

        $this->info("Recordatorio enviado a: $email");
        return 0;
    }
}