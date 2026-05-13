<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // Rol constants
    const ROLE_USER         = 1;
    const ROLE_ADMIN        = 2;
    const ROLE_NUTRITIONIST = 3;
    const ROLE_TRAINER      = 4;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'active',
        'birthdate', 'profile_photo', 'medical_history',
        'phone', 'location', 'goal', 'level',
        'weight', 'height', 'imc',
        'membership_start', 'nutritionist_id', 'trainer_id',
        'reset_token', 'reset_expires',
    ];

    protected $hidden = [
        'password', 'remember_token', 'reset_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'reset_expires'     => 'datetime',
        'birthdate'         => 'date',
        'membership_start'  => 'date',
        'active'            => 'boolean',
        'role'              => 'integer',
        'weight'            => 'decimal:2',
        'height'            => 'decimal:2',
        'imc'               => 'decimal:2',
        'trainer_id'        => 'integer',
        'nutritionist_id'   => 'integer',
    ];

    // ── Helpers de rol ───────────────────────────────────────────
    public function isUser(): bool        { return $this->role === self::ROLE_USER; }
    public function isAdmin(): bool       { return $this->role === self::ROLE_ADMIN; }
    public function isNutritionist(): bool { return $this->role === self::ROLE_NUTRITIONIST; }
    public function isTrainer(): bool     { return $this->role === self::ROLE_TRAINER; }
    public function isStaff(): bool       { return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_NUTRITIONIST, self::ROLE_TRAINER]); }

    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN        => 'Admin',
            self::ROLE_NUTRITIONIST => 'Nutricionista',
            self::ROLE_TRAINER      => 'Entrenador',
            default                 => 'Usuario',
        };
    }

    // Solo usuarios activos pueden autenticarse (manejado en attempt())

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeActive($query)         { return $query->where('active', 1); }
    public function scopeByRole($query, $role)  { return $query->where('role', $role); }

    // ── Relaciones ───────────────────────────────────────────────
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function nutritionPlans()
    {
        return $this->hasMany(NutritionPlan::class);
    }

    public function trainingPlans()
    {
        return $this->hasMany(TrainingPlan::class);
    }

    public function trainingCompletions()
    {
        return $this->hasMany(TrainingCompletion::class);
    }

    public function workoutCalendar()
    {
        return $this->hasMany(WorkoutCalendar::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(Progress::class);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    // Nutri/entrenador asignados
    public function nutritionist()
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }

    public function trainerUser()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Usuarios asignados (si el user actual es nutri/entrenador)
    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'trainer_id');
    }
}
