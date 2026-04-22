<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionPlan extends Model
{
    protected $table = 'nutrition_plan';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

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
