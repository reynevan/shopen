<?php

namespace Shopen\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Shopen\Models\Order\Order;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zamówienie zostało złożone - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'shopen::emails.orders.placed',
            with: [
                'order' => $this->order,
                'isGuestOrder' => $this->order->isGuestOrder(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
} 