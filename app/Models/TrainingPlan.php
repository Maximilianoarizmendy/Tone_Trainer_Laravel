<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    protected $table = 'training_plan';

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
