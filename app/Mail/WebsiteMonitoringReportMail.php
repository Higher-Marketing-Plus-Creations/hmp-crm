<?php

namespace App\Mail;

use App\Models\Website;
use App\Models\WebsiteMonitorCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteMonitoringReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Website $website,
        public WebsiteMonitorCheck $check
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Run Test Report: %s', $this->website->website_name),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.website-monitoring-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
