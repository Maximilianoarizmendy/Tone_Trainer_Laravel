<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Membership
 * 
 * Representa los planes o membresías disponibles en el gimnasio (ej. Plan Mensual, Anual).
 * Define el precio, duración en días y beneficios.
 */
class Membership extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'description',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
