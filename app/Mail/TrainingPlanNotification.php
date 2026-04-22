<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingPlanNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $action,   // 'add' | 'delete'
        public string $details = ''
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'add'    => '💪 Nuevo Ejercicio Asignado - Tone Trainer',
            'delete' => '🗑️ Ejercicio Retirado - Tone Trainer',
        ];

        return new Envelope(subject: $subjects[$this->action] ?? 'Actualización de Entrenamiento - Tone Trainer');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.training-plan');
    }
}
