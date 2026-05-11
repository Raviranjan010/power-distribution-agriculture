<?php

namespace App\Mail;

use App\Models\Connection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConnectionApproved extends Mailable
{
    use Queueable, SerializesModels;

    public Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Electricity Connection Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.connection_approved',
        );
    }
}
