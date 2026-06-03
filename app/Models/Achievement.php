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
    protected $fillable = [
        'user_id', 'badge_name', 'badge_icon', 'description',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
