<?php

namespace Shopen\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\User;

class NewUserMail extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Witamy w sklepie ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $view = 'emails.auth.new-user';
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'user' => $this->user
            ],
        );
    }
} 