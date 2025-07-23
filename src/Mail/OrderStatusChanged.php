<?php

namespace Shopen\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Shopen\Models\Order\Order;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $oldStatus
    ) {}

    public function envelope(): Envelope
    {
        $statusLabels = [
            'pending' => 'Oczekujące',
            'processing' => 'W trakcie realizacji',
            'shipped' => 'Wysłane',
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane',
            'refunded' => 'Zwrócone',
        ];

        $newStatusLabel = $statusLabels[$this->order->status] ?? $this->order->status;

        return new Envelope(
            subject: 'Status zamówienia zmieniony - ' . $this->order->order_number . ' - ' . $newStatusLabel,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'shopen::emails.orders.status-changed',
            with: [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'isGuestOrder' => $this->order->isGuestOrder(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
} 