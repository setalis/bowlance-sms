<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый заказ #'.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.new-order',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
