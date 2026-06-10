<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MembershipSeeder::class,
        ]);

        // Administrador (role = 2)
        User::factory()->create([
            'name' => 'Administrador ToneTrainer',
            'email' => 'm.3@gmail.com',
            'password' => bcrypt('Maxi123.'),
            'role' => 2,
            'active' => true,
            'is_verified' => true,
        ]);

        // Entrenador / Trainer (role = 4)
        User::factory()->create([
            'name' => 'Entrenador ToneTrainer',
            'email' => 'trainer@tonetrainer.com',
            'password' => bcrypt('password123'),
            'role' => 4,
            'active' => true,
            'is_verified' => true,
        ]);

        // Nutricionista (role = 3)
        User::factory()->create([
            'name' => 'Nutricionista ToneTrainer',
            'email' => 'nutri@tonetrainer.com',
            'password' => bcrypt('password123'),
            'role' => 3,
            'active' => true,
            'is_verified' => true,
        ]);

        // Usuario Estándar (role = 1)
        User::factory()->create([
            'name' => 'Usuario Test',
            'email' => 'user@tonetrainer.com',
            'password' => bcrypt('password123'),
            'role' => 1,
            'active' => true,
            'is_verified' => true,
        ]);
    }
}
