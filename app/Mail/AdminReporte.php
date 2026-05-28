<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminReporte extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pedido $pedido,
        public string $tipo,
        public string $descripcion,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Reporte — Pedido ' . $this->pedido->numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.reporte',
        );
    }
}