<?php

namespace Shopen\Mail\ContactMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class ContactMessageResponse extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public \Shopen\Models\ContactMessage\ContactMessage $contactMessage,
        public \Shopen\Models\ContactMessage\ContactMessageResponse $response
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->contactMessage->subject,
        );
    }

    public function content(): Content
    {
        $view = 'emails.contact-message.response';
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'contactMessage' => $this->contactMessage,
                'response' => $this->response
            ],
        );
    }
} 