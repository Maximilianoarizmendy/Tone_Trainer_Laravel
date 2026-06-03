<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo TrainingPlan
 * 
 * Configura los ejercicios estáticos asignados por un entrenador a un usuario.
 * Detalla el nombre del ejercicio, número de series, repeticiones y al grupo
 * de entrenamiento diario al que pertenece.
 */
class TrainingPlan extends Model
{
    protected $table = 'training_plan';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'assigned_by', 'day_group', 'exercise',
        'series', 'reps', 'description', 'status',
    ];

    protected $casts = [
        'series'     => 'integer',
        'reps'       => 'integer',
        'status'     => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function completions()
    {
        return $this->hasMany(TrainingCompletion::class, 'exercise_id');
    }
}
