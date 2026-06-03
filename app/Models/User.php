<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User
 * 
 * Representa a todos los actores del sistema Tone Trainer, incluyendo
 * clientes (usuarios regulares), entrenadores, nutricionistas y administradores.
 * Gestiona la autenticación, roles, perfil físico y las relaciones con
 * otras entidades como rutinas, dietas, pagos y retos.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role
 * @property bool $active
 * @property bool $is_verified
 * @property string|null $verification_document
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    // Rol constants
    const ROLE_USER         = 1;
    const ROLE_ADMIN        = 2;
    const ROLE_NUTRITIONIST = 3;
    const ROLE_TRAINER      = 4;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'active',
        'birthdate', 'profile_photo', 'medical_history',
        'phone', 'location', 'goal', 'level',
        'weight', 'height', 'imc', 'trainer',
        'membership_start', 'nutritionist_id', 'trainer_id',
        'reset_token', 'reset_expires', 'is_verified', 'verification_document',
        'verification_status', 'nutritionist_notes'
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
    ];

    // ── Mutators para cifrado (Req 35) ───────────────────────────
    public function getPhoneAttribute($value) {
        try { return $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : null; } 
        catch (\Illuminate\Contracts\Encryption\DecryptException $e) { return $value; }
    }
    public function setPhoneAttribute($value) {
        $this->attributes['phone'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null;
    }

    public function getMedicalHistoryAttribute($value) {
        try { return $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : null; } 
        catch (\Illuminate\Contracts\Encryption\DecryptException $e) { return $value; }
    }
    public function setMedicalHistoryAttribute($value) {
        $this->attributes['medical_history'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null;
    }

    public function getVerificationDocumentAttribute($value) {
        try { return $value ? \Illuminate\Support\Facades\Crypt::decryptString($value) : null; } 
        catch (\Illuminate\Contracts\Encryption\DecryptException $e) { return $value; }
    }
    public function setVerificationDocumentAttribute($value) {
        $this->attributes['verification_document'] = $value ? \Illuminate\Support\Facades\Crypt::encryptString($value) : null;
    }

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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function createdChallenges()
    {
        return $this->hasMany(Challenge::class, 'trainer_id');
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class)
            ->withPivot(['current_progress', 'completed', 'completed_at'])
            ->withTimestamps();
    }
}
