<?php

namespace App\Services\WebsiteMonitoring;

use App\Mail\WebsiteMonitoringReportMail;
use App\Models\Website;
use App\Models\WebsiteMonitorCheck;
use Illuminate\Support\Facades\Mail;

class WebsiteMonitoringReportMailer
{
    public function send(Website $website, WebsiteMonitorCheck $check): void
    {
        $recipients = $website->recipientList();

        if ($recipients === []) {
            return;
        }

        Mail::to(array_shift($recipients))
            ->cc($recipients)
            ->send(new WebsiteMonitoringReportMail($website, $check));
    }
}
