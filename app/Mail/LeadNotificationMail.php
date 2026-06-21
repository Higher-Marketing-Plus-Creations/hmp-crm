<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-notification',
        );
    }

    public function subjectLine(): string
    {
        return 'New Lead: ' . ($this->lead->website_name ?: 'Website') . ' - ' . ($this->lead->form_name ?: 'Form');
    }

    public function attachments(): array
    {
        return [];
    }
}
