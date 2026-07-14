<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Services\WebsiteMonitoring\WebsiteMonitorRunner;
use App\Services\WebsiteMonitoring\WebsiteMonitoringReportMailer;
use Illuminate\Http\RedirectResponse;

class WebsiteMonitorController extends Controller
{
    public function store(
        Website $website,
        WebsiteMonitorRunner $runner,
        WebsiteMonitoringReportMailer $reportMailer
    ): RedirectResponse
    {
        $check = $runner->run($website);
        $reportMailer->send($website, $check);

        return back()->with('status', 'Monitoring test completed for ' . $website->website_name . '.');
    }
}
