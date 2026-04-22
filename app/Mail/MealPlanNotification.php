<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MealPlanNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $action,   // 'add' | 'delete' | 'reset'
        public string $details = ''
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'add'    => '🥗 Nueva Comida Asignada - Tone Trainer',
            'delete' => '🗑️ Comida Retirada - Tone Trainer',
            'reset'  => '🔄 Reseteo de Plan Nutricional - Tone Trainer',
        ];

        return new Envelope(subject: $subjects[$this->action] ?? 'Actualización Nutricional - Tone Trainer');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.meal-plan');
    }
}
