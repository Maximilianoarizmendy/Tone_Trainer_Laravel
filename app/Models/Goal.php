<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    // Timestamps habilitados por defecto

    protected $fillable = [
        'user_id', 'title', 'description', 'category',
        'target_value', 'current_value', 'unit', 'deadline',
        'status', 'completed_at',
    ];

    protected $casts = [
        'deadline'      => 'date',
        'completed_at'  => 'datetime',
        'target_value'  => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    const STATUS_ACTIVE    = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED    = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        return min(100, round(($this->current_value / $this->target_value) * 100, 1));
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
