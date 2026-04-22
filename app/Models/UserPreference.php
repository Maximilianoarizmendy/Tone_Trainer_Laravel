<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'user_preferences';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'goal', 'training_level', 'weekly_frequency',
        'training_type', 'physical_restrictions', 'preferred_schedule',
        'reminders', 'push_notifications', 'email_notifications', 'workout_reminders', 'last_update',
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
