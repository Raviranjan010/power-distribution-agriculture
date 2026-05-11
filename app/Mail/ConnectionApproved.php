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

    public Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
