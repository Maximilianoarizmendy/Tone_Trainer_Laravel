<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoutineAttendance extends Model
{
    use HasFactory;

    protected $table = 'routine_attendance';

    protected $fillable = [
        'user_id', 'training_plan_id', 'date', 'status'
    ];
}
