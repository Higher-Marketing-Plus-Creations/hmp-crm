<?php

namespace App\Jobs;

use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLeadNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public Lead $lead)
    {
    }

    public function handle(): void
    {
        $lead = $this->lead->fresh(['website', 'emailLogs']);

        if (! $lead || ! $lead->website) {
            return;
        }

        $recipients = $lead->website->recipientList();

        if ($recipients === []) {
            $lead->update(['email_status' => 'failed']);
            $lead->emailLogs()->create([
                'sent_to' => '',
                'subject' => 'Lead notification not sent',
                'status' => 'failed',
                'error_message' => 'No notification email configured for this website.',
            ]);

            return;
        }

        $to = array_shift($recipients);
        $cc = $recipients;
        $mail = new LeadNotificationMail($lead);

        try {
            Mail::to($to)
                ->cc($cc)
                ->send($mail);

            $renderedBody = $mail->render();

            $lead->update(['email_status' => 'sent']);
            $lead->emailLogs()->create([
                'sent_to' => $to,
                'cc' => $cc !== [] ? implode(', ', $cc) : null,
                'subject' => $mail->subjectLine(),
                'body' => $renderedBody,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $lead->update(['email_status' => 'failed']);
            $lead->emailLogs()->create([
                'sent_to' => $to,
                'cc' => $cc !== [] ? implode(', ', $cc) : null,
                'subject' => $mail->subjectLine(),
                'body' => null,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
