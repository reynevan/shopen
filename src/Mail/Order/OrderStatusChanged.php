<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Order;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    protected $template;

    public function __construct(
        public Order $order,
        public ?string $comment = ''
    ) {}

    public function content(): Content
    {
        $view = 'emails.orders.' . $this->template;
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'order' => $this->order,
                'comment' => $this->comment,
                'isGuestOrder' => $this->order->isGuestOrder(),
                'logoPath' => public_path('img/mail-logo.png'),
            ],
        );
    }
} 