<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EvaluacionInvitacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $voluntario;
    public string $token;
    public string $enlaceEvaluacion;

    /**
     * Create a new message instance.
     */
    public function __construct(User $voluntario, string $token)
    {
        $this->voluntario = $voluntario;
        $this->token = $token;
        $this->enlaceEvaluacion = url('/evaluacion-voluntario/' . $token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitación para Evaluación de Voluntarios - GEVOPI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluacion_invitacion',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
