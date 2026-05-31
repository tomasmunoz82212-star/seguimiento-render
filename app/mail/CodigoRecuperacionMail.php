<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoRecuperacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;
    public $nombre;

    public function __construct($codigo, $nombre)
    {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de contraseña - Sistema de Seguimiento CRU',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-recuperacion',
        );
    }
}