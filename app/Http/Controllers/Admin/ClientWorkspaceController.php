<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Contracts\View\View;

class ClientWorkspaceController extends Controller
{
    public function show(Client $client): View
    {
        $client->load([
            'websites.latestMonitorCheck',
            'websites' => fn ($query) => $query->withCount([
                'forms',
                'leads',
                'leads as forms_submitted_this_month_count' => fn ($leadQuery) => $leadQuery
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                'leads as failed_forms_count' => fn ($leadQuery) => $leadQuery
                    ->where('email_status', 'failed'),
            ])->orderBy('website_name'),
        ]);

        $websites = $client->websites->map(function ($website) {
            $website->setRelation(
                'lastSuccessfulLead',
                $website->leads()
                    ->where('email_status', 'sent')
                    ->latest('created_at')
                    ->first()
            );

            return $website;
        });

        return view('admin.clients.workspace', [
            'client' => $client,
            'websites' => $websites,
            'clientStats' => [
                'total_websites' => $websites->count(),
                'total_forms' => $websites->sum('forms_count'),
                'forms_this_month' => $websites->sum('forms_submitted_this_month_count'),
                'failed_forms' => $websites->sum('failed_forms_count'),
            ],
        ]);
    }
}
