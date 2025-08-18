<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Order;

class OrderRefunded extends OrderStatusChanged
{
    use Queueable, SerializesModels;

    protected $template = 'returned';

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zamówienie zostało zwrócone - ' . $this->order->order_number,
        );
    }
} 