<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPreference;

class EnableReminders extends Command
{
    protected $signature = 'reminders:enable {email}';
    protected $description = 'Habilita recordatorios para un usuario';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario no encontrado: $email");
            return 1;
        }

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            ['workout_reminders' => true, 'reminders' => true, 'email_notifications' => true, 'push_notifications' => true, 'last_update' => now()]
        );

        $this->info("Recordatorios habilitados para: {$user->name}");
        return 0;
    }
}