<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Order;

class OrderVouchers extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public array $vouchers
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (count($this->vouchers) > 1 ? 'Twoje bony podarunkowe' : 'Twój bon podarunkowy') . " - Zamówienie {$this->order->order_number}",
        );
    }

    public function content(): Content
    {
        $view = 'emails.orders.vouchers';
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'order' => $this->order,
                'vouchers' => $this->vouchers,
                'isGuestOrder' => $this->order->isGuestOrder(),
                'logoPath' => public_path('img/mail-logo.png'),
            ],
        );
    }
} 