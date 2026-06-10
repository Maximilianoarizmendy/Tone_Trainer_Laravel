<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membership;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Membership::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Plan Promocional',
                'price' => 70000.00,
                'duration_days' => 30,
                'description' => 'Acceso a salas de musculación y cardio en horario promocional (de 10:00 AM a 4:00 PM).'
            ]
        );

        Membership::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Plan Mensual Estándar',
                'price' => 85000.00,
                'duration_days' => 30,
                'description' => 'Acceso libre e ilimitado a todas las instalaciones, máquinas y clases grupales.'
            ]
        );

        Membership::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Plan Ultimate',
                'price' => 120000.00,
                'duration_days' => 30,
                'description' => 'Acceso VIP ilimitado, toallas, entrenamiento personalizado asignado, plan nutricional con IA y zona de hidratación.'
            ]
        );
    }
}
