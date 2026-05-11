<?php

namespace App\Mail;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public Bill $bill;

    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }

    public function envelope(): Envelope
    {
        $period = \Carbon\Carbon::create($this->bill->billing_year, $this->bill->billing_month)->format('F Y');
        return new Envelope(
            subject: "Your Electricity Bill for {$period} Has Been Generated",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bill_generated',
        );
    }
}
