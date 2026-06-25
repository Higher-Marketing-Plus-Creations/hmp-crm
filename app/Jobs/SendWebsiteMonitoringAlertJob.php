<?php

namespace App\Jobs;

use App\Mail\WebsiteMonitoringAlertMail;
use App\Models\WebsiteMonitorAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWebsiteMonitoringAlertJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public WebsiteMonitorAlert $alert)
    {
    }

    public function handle(): void
    {
        $alert = $this->alert->fresh(['website', 'check']);

        if (! $alert || ! $alert->website) {
            return;
        }

        $recipients = $alert->recipients ?? [];

        if ($recipients === []) {
            $alert->update([
                'send_status' => 'skipped',
                'error_message' => 'No monitoring recipients configured for this website.',
            ]);

            return;
        }

        $to = array_shift($recipients);
        $cc = $recipients;
        $mail = new WebsiteMonitoringAlertMail($alert);

        try {
            Mail::to($to)
                ->cc($cc)
                ->send($mail);

            $alert->update([
                'send_status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $exception) {
            $alert->update([
                'send_status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
