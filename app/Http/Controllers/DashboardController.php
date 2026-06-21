<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Models\Website;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'total_clients' => Client::count(),
                'total_websites' => Website::count(),
                'total_leads' => Lead::count(),
                'failed_emails' => Lead::where('email_status', 'failed')->count(),
            ],
            'clients' => Client::query()
                ->withCount(['websites', 'leads'])
                ->with(['websites.latestMonitorCheck'])
                ->latest()
                ->limit(8)
                ->get(),
            'recentEmailFailures' => EmailLog::query()
                ->with('lead.website.client')
                ->where('status', 'failed')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
