<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCompletion extends Model
{
    protected $table = 'training_completions';

    // Timestamps habilitados

    protected $fillable = [
        'user_id', 'exercise_id', 'completed_at',
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
