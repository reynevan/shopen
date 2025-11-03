<?php

namespace Shopen\Mail\ContactMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public \Shopen\Models\ContactMessage\ContactMessage $contactMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nowa wiadomość kontaktowa',
        );
    }

    public function content(): Content
    {
        $view = 'emails.contact-message.new-message';
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'contactMessage' => $this->contactMessage
            ],
        );
    }
} 