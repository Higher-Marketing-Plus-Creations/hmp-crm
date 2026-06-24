<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\EmailLog;
use App\Models\Lead;
use App\Models\Website;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $websiteAttentionQuery = Website::query()
            ->with(['client', 'latestMonitorCheck'])
            ->withCount([
                'leads as new_leads_today_count' => fn (Builder $query) => $query->whereDate('created_at', today()),
                'leads as failed_leads_count' => fn (Builder $query) => $query->where('email_status', 'failed'),
            ]);

        return view('dashboard', [
            'stats' => [
                'websites_needing_attention' => (clone $websiteAttentionQuery)
                    ->where(function (Builder $query) {
                        $query
                            ->whereDoesntHave('latestMonitorCheck')
                            ->orWhereHas('latestMonitorCheck', fn (Builder $checkQuery) => $checkQuery
                                ->where('website_status', '!=', 'online')
                                ->orWhere('ssl_status', '!=', 'valid')
                                ->orWhere('email_delivery_status', 'in', ['failing', 'warning', 'not_configured'])
                                ->orWhere('failed_form_count', '>', 0));
                    })
                    ->count(),
                'failed_emails' => Lead::where('email_status', 'failed')->count(),
                'new_leads_today' => Lead::whereDate('created_at', today())->count(),
                'untested_websites' => Website::whereDoesntHave('latestMonitorCheck')->count(),
            ],
            'clients' => Client::query()
                ->withCount(['websites', 'leads'])
                ->with([
                    'websites.latestMonitorCheck',
                    'websites' => fn ($query) => $query->withCount([
                        'leads as failed_leads_count' => fn (Builder $leadQuery) => $leadQuery->where('email_status', 'failed'),
                        'leads as new_leads_today_count' => fn (Builder $leadQuery) => $leadQuery->whereDate('created_at', today()),
                    ])->orderBy('website_name'),
                ])
                ->latest()
                ->limit(8)
                ->get(),
            'recentEmailFailures' => EmailLog::query()
                ->with('lead.website.client')
                ->where('status', 'failed')
                ->latest()
                ->limit(5)
                ->get(),
            'recentLeads' => Lead::query()
                ->with('website.client')
                ->latest()
                ->limit(6)
                ->get(),
        ]);
    }
}
