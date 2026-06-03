<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo NutritionPlan
 * 
 * Gestiona el plan alimenticio de un usuario. Define qué debe comer
 * un cliente específico detallando el día de la semana, momento (comida, cena),
 * calorías y los macros (proteínas, carbohidratos, grasas).
 */
class NutritionPlan extends Model
{
    protected $table = 'nutrition_plans';

    protected $fillable = [
        'user_id', 'day_of_week', 'meal_type', 'food_name',
        'calories', 'protein', 'carbs', 'fats',
    ];

    protected $casts = [
        'calories'   => 'integer',
        'protein'    => 'integer',
        'carbs'      => 'integer',
        'fats'       => 'integer',
        'created_at' => 'datetime',
    ];

    const DAYS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const MEAL_TYPES = ['Desayuno', 'Media Mañana', 'Almuerzo', 'Merienda', 'Cena'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
