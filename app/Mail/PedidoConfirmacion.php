<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pedido $pedido) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pedido ' . $this->pedido->numero . ' — 1310 Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedido.confirmacion',
        );
    }
}