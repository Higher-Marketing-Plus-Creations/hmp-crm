<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Services\WebsiteMonitoring\WebsiteMonitorRunner;
use Illuminate\Http\RedirectResponse;

class WebsiteMonitorController extends Controller
{
    public function store(Website $website, WebsiteMonitorRunner $runner): RedirectResponse
    {
        $runner->run($website);

        return back()->with('status', 'Monitoring test completed for ' . $website->website_name . '.');
    }
}
