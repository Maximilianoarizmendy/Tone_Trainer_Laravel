<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo WorkoutCalendar
 * 
 * Permite al usuario agendar rutinas específicas para un día particular.
 * Contiene un título, una nota opcional y la fecha/hora en la que
 * planea realizar el entrenamiento.
 */
class WorkoutCalendar extends Model
{
    protected $table = 'workout_calendar';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'workout_date', 'workout_type', 'title',
        'notes', 'completed', 'duration_minutes', 'calories_burned',
    ];

    protected $casts = [
        'workout_date'     => 'date',
        'completed'        => 'boolean',
        'duration_minutes' => 'integer',
        'calories_burned'  => 'integer',
        'created_at'       => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('workout_date', now()->month)
                     ->whereYear('workout_date', now()->year);
    }
}
