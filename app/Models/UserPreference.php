<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo UserPreference
 * 
 * Guarda la configuración de la cuenta y preferencias de sistema
 * de un usuario, como por ejemplo la activación de notificaciones,
 * nivel de entrenamiento, frecuencia semanal y horarios preferidos.
 */
class UserPreference extends Model
{
    protected $table = 'user_preferences';
    protected $fillable = [
        'user_id', 'goal', 'training_level', 'weekly_frequency',
        'training_type', 'physical_restrictions', 'preferred_schedule',
        'reminders', 'push_notifications', 'email_notifications', 'workout_reminders',
    ];

    protected $casts = [
        'reminders'            => 'boolean',
        'push_notifications'  => 'boolean',
        'email_notifications' => 'boolean',
        'workout_reminders'   => 'boolean',
        'weekly_frequency'    => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
