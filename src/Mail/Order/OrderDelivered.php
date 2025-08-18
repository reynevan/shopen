<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Order;

class OrderDelivered extends OrderStatusChanged
{
    use Queueable, SerializesModels;

    protected $template = 'delivered';

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zamówienie zostało dostarczone - ' . $this->order->order_number,
        );
    }
} 