<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Achievement
 * 
 * Gestiona las insignias, medallas y recompensas virtuales obtenidas
 * por los usuarios al completar metas, rutinas o retos asignados.
 */
class Achievement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'badge_name', 'badge_icon', 'description',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    const CREATED_AT = 'earned_at';
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
