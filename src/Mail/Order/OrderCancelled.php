<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Order;

class OrderCancelled extends OrderStatusChanged
{
    use Queueable, SerializesModels;

    protected $template = 'cancelled';

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zamówienie zostało anulowane - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        $view = 'emails.orders.' . $this->template;
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'order' => $this->order,
                'isGuestOrder' => $this->order->isGuestOrder(),
                'logoPath' => public_path('img/mail-logo.png'),
            ],
        );
    }
} 