<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeKaziTrust extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Bienvenue sur KaziTrust — votre espace est prêt',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-kazitrust',
            with: [
                'userName'    => $this->user->name,
                'companyName' => $this->user->tenant->name ?? '',
                'loginUrl'    => url('/management/login'),
                'docsUrl'     => url('/docs'),
                'supportUrl'  => url('/support'),
            ],
        );
    }
}