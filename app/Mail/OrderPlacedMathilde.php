<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lunar\Models\Order;

class OrderPlacedMathilde extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[calatrava.fr] Nouvelle commande #{$this->order->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order-placed-mathilde',
            with: [
                'order' => $this->order,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(function () {
                return Pdf::loadView('lunarpanel::pdf.order', [
                    'record' => $this->order,
                ])->output();
            }, "facture-{$this->order->reference}.pdf")
        ];
    }
}
