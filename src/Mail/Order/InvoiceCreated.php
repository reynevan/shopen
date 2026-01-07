<?php

namespace Shopen\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Shopen\Models\Order\Invoice\Invoice;

class InvoiceCreated extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(
        public Invoice $invoice
    ) {}

    public function content(): Content
    {
        $view = 'emails.orders.invoice';
        return new Content(
            view: View::exists($view) ? $view : "shopen::$view",
            with: [
                'invoice' => $this->invoice
            ],
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Faktura do Twojego zamówienia nr ' . $this->invoice->order->order_number,
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(Storage::disk('public')->path($this->invoice->file_path))
                ->as($this->invoice->file_name)
                ->withMime('application/pdf'),
        ];
    }
} 