<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo TrainingCompletion
 * 
 * Sirve como registro de asistencia virtual.
 * Almacena el historial exacto (fecha y hora) en que un usuario marcó
 * un ejercicio específico de su TrainingPlan como "completado".
 */
class TrainingCompletion extends Model
{
    protected $table = 'training_completions';

    // Timestamps habilitados

    protected $fillable = [
        'user_id', 'exercise_id', 'completed_at', 'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(TrainingPlan::class, 'exercise_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('completed_at', today());
    }
}
