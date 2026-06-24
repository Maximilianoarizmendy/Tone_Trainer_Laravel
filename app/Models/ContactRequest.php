<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo ContactRequest
 * 
 * Representa una solicitud de contacto entre dos usuarios.
 * La solicitud tiene un remitente (sender) y un destinatario (receiver)
 * con un estado (pending, accepted).
 */
class ContactRequest extends Model
{
    protected $table = 'contact_requests';

    protected $fillable = [
        'sender_id', 'receiver_id', 'status'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
