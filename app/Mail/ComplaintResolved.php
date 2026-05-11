<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintResolved extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Complaint #' . $this->complaint->grv_number . ' Has Been Resolved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint_resolved',
        );
    }
}
