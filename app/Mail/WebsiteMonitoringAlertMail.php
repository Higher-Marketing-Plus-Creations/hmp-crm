<?php

namespace App\Mail;

use App\Models\WebsiteMonitorAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteMonitoringAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WebsiteMonitorAlert $alert)
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
            view: 'emails.website-monitoring-alert',
        );
    }

    public function subjectLine(): string
    {
        $websiteName = $this->alert->website?->website_name ?: 'Website';
        $type = strtoupper($this->alert->type);

        return sprintf('%s Alert: %s', $type, $websiteName);
    }

    public function attachments(): array
    {
        return [];
    }
}
