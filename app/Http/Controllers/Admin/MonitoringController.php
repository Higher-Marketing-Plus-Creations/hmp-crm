<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\WebsiteMonitorCheck;
use Illuminate\Contracts\View\View;

class MonitoringController extends Controller
{
    public function __invoke(): View
    {
        $features = [
            [
                'title' => 'Website online/offline',
                'status' => 'implemented',
                'description' => 'Each website can run a live check and save the latest online status.',
            ],
            [
                'title' => 'Email delivery status',
                'status' => 'implemented',
                'description' => 'Per-website delivery health is derived from sent and failed lead emails.',
            ],
            [
                'title' => 'Forms submitted this month',
                'status' => 'implemented',
                'description' => 'Monthly form totals are shown separately inside each client workspace.',
            ],
            [
                'title' => 'Last successful form',
                'status' => 'implemented',
                'description' => 'Each website shows the latest lead whose email delivery succeeded.',
            ],
            [
                'title' => 'Failed form count',
                'status' => 'implemented',
                'description' => 'Failed lead submissions are counted per website instead of mixing all clients.',
            ],
            [
                'title' => 'Site load time',
                'status' => 'implemented',
                'description' => 'Run Test stores the website response time in milliseconds.',
            ],
            [
                'title' => 'Current issues',
                'status' => 'implemented',
                'description' => 'The latest monitoring snapshot lists SSL, email, submission, or availability issues.',
            ],
            [
                'title' => 'Run Test button',
                'status' => 'implemented',
                'description' => 'Admins can trigger a monitoring check from the client workspace or websites screen.',
            ],
            [
                'title' => 'SSL status',
                'status' => 'implemented',
                'description' => 'HTTPS and certificate expiry are inspected and saved on each monitoring check.',
            ],
            [
                'title' => 'Uptime percentage',
                'status' => 'implemented',
                'description' => 'A 30-day uptime percentage is calculated from each website monitoring history.',
            ],
        ];

        return view('admin.monitoring.index', [
            'features' => $features,
            'clients' => Client::query()
                ->withCount(['websites', 'leads'])
                ->latest()
                ->get(),
            'recentChecks' => WebsiteMonitorCheck::query()
                ->with('website.client')
                ->latest('tested_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
