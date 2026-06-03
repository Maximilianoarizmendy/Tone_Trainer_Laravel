<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Progress
 * 
 * Registra el historial de las métricas físicas y corporales de un usuario,
 * como peso, porcentaje de grasa, masa muscular, IMC y consumo de agua/proteínas.
 * Permite a los entrenadores validar estas métricas y dejar retroalimentación.
 */
class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = [
        'user_id', 'weight', 'height', 'body_fat', 'muscle_mass',
        'bmi', 'water_intake', 'protein_intake', 'notes',
    ];

    protected $casts = [
        'weight'         => 'decimal:2',
        'body_fat'       => 'decimal:2',
        'muscle_mass'    => 'decimal:2',
        'bmi'            => 'decimal:2',
        'water_intake'   => 'decimal:2',
        'protein_intake' => 'decimal:2',
        'created_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
