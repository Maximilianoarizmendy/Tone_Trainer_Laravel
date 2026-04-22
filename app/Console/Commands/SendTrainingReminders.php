<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPreference;
use App\Models\TrainingPlan;
use App\Models\TrainingCompletion;
use App\Notifications\TrainingReminder;
use Carbon\Carbon;

class SendTrainingReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Envía recordatorios de entrenamiento a usuarios con rutina asignada';

    public function handle(): int
    {
        $users = UserPreference::where('workout_reminders', true)
            ->whereHas('user', fn($q) => $q->where('active', true))
            ->with('user')
            ->get();

        $count = 0;
        $today = Carbon::today()->dayOfWeek;
        $dayMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miércoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sábado', 7 => 'domingo',
        ];
        $dayName = $dayMap[$today] ?? 'hoy';

        foreach ($users as $pref) {
            $userId = $pref->user_id;

            $exercises = TrainingPlan::where('user_id', $userId)->get();

            if ($exercises->isEmpty()) {
                continue;
            }

            $todayExercises = $exercises->filter(fn($e) => strtolower($e->day_group) === strtolower($dayName));

            if ($todayExercises->isEmpty()) {
                $todayExercises = $exercises->take(3);
                $dayName = 'hoy';
            }

            if ($todayExercises->isEmpty()) {
                continue;
            }

            $exerciseList = $todayExercises->pluck('exercise')->implode(', ');

            $completed = TrainingCompletion::where('user_id', $userId)
                ->whereDate('completed_at', Carbon::today())
                ->count();

            $message = "Día: {$dayName}. Ejercicios: {$exerciseList}. Completados: {$completed}/{$todayExercises->count()}";

            $pref->user->notify(new TrainingReminder(
                "Recordatorio - {$dayName}",
                $message
            ));

            $count++;
        }

        $this->info("Enviados {$count} recordatorios.");
        return Command::SUCCESS;
    }
}