<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Challenge
 * 
 * Representa un reto (semanal o mensual) creado por un entrenador para
 * motivar a sus clientes. Define una meta (ej. días de entrenamiento)
 * que el usuario debe alcanzar antes de la fecha límite.
 */
class Challenge extends Model
{
    protected $fillable = [
        'trainer_id',
        'title',
        'description',
        'type',
        'goal_type',
        'goal_value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['current_progress', 'completed', 'completed_at'])
            ->withTimestamps();
    }
}
