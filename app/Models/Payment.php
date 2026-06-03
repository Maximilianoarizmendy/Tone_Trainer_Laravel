<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'membership_id',
        'amount',
        'status',
        'payment_method',
        'payment_intent_id',
        'customer_id',
        'amount_cents',
        'currency',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount'       => 'decimal:2',
        'amount_cents' => 'integer',
        'paid_at'      => 'datetime',
        'created_at'   => 'datetime',
    ];

    /**
     * Relación con el usuario (si existe).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Obtener monto formateado (usa amount_cents si existe, sino amount).
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->amount_cents) {
            return number_format($this->amount_cents / 100, 2);
        }
        return number_format($this->amount ?? 0, 2);
    }
}
