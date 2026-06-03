<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    protected $table = 'food_logs';

    protected $fillable = [
        'user_id', 'food_name', 'servings', 'date', 'is_consumed', 'consumed_at'
    ];

    protected $casts = [
        'is_consumed' => 'boolean',
        'consumed_at' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
