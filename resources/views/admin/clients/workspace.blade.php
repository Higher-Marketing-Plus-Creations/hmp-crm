@extends('layouts.app', ['title' => $client->name . ' Workspace', 'heading' => $client->name . ' Workspace'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-4">
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Websites</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ $clientStats['total_websites'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Forms This Month</p>
            <p class="mt-3 text-4xl font-black text-teal-700">{{ $clientStats['forms_this_month'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">New Leads Today</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ $clientStats['new_leads_today'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Failed Forms</p>
            <p class="mt-3 text-4xl font-black text-rose-600">{{ $clientStats['failed_forms'] }}</p>
        </div>
    </div>

    <div class="crm-card mt-6 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Website Health And Leads</h3>
                <p class="text-sm text-slate-500">Use this page as the main daily workspace for this client: review health, run tests, verify tracking scripts, and jump into leads only when something needs attention.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.websites.create') }}" class="crm-button">Connect Website</a>
                <a href="{{ route('admin.leads.index') }}" class="crm-button-secondary">Open Leads</a>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            @forelse ($websites as $website)
                @php($check = $website->latestMonitorCheck)
                <div class="crm-card p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">{{ $website->website_url }}</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-900">{{ $website->website_name }}</h3>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $website->leads_count }} total leads | {{ $website->forms_submitted_this_month_count }} this month | {{ $website->new_leads_today_count }} today
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <form method="POST" action="{{ route('admin.websites.run-test', $website) }}">
                                @csrf
                                <button type="submit" class="crm-button">Run Test</button>
                            </form>
                            <a href="{{ route('admin.websites.edit', $website) }}" class="crm-button-secondary">Edit Website</a>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Website Status</p>
                            <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->website_status ? ucfirst($check->website_status) : 'Not tested' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $check?->http_status_code ? 'HTTP ' . $check->http_status_code : 'No probe result yet' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Email Delivery</p>
                            <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->email_delivery_status ? ucwords(str_replace('_', ' ', $check->email_delivery_status)) : 'Pending' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $website->failed_forms_count }} failed sends recorded</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">SSL And Speed</p>
                            <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->ssl_status ? ucwords(str_replace('_', ' ', $check->ssl_status)) : 'Unknown' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $check?->site_load_time_ms ? $check->site_load_time_ms . ' ms' : 'No speed result yet' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Last Successful Lead</p>
                            <p class="mt-3 text-lg font-black text-slate-900">{{ $website->lastSuccessfulLead?->form_name ?: 'No sent form yet' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $website->lastSuccessfulLead?->created_at?->diffForHumans() ?: 'Awaiting first success' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Tracking Detection</p>
                                <div class="mt-3 grid gap-2 text-sm text-slate-700 md:grid-cols-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $check && $check->google_analytics_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_analytics_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Google Analytics</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $check && $check->google_tag_manager_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_tag_manager_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Google Tag Manager</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $check && $check->google_search_console_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_search_console_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Search Console Verification</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $check && $check->microsoft_tracking_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->microsoft_tracking_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Microsoft Tracking</span>
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-slate-500">{{ $check ? 'Run Test scans the latest rendered HTML for gtag, GTM, verification meta tags, Clarity, and Bing markers.' : 'Run the first test to scan tracking markers.' }}</p>
                            </div>
                            <div class="text-sm text-slate-500">
                                Last test: {{ $check?->tested_at?->diffForHumans() ?: 'Ready to run' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Current Issues</p>
                                <p class="mt-3 text-sm font-semibold text-slate-700">{{ $check && filled($check->issues) ? implode(' ', $check->issues) : 'No active issues detected.' }}</p>
                            </div>
                            <div class="text-sm text-slate-500">
                                {{ $check?->tracking_detection_details ? 'Tracking markers saved with latest snapshot' : 'Tracking details will appear after Run Test' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="crm-badge-info">API key linked</span>
                        @if ($website->notification_emails)
                            <span class="crm-badge-success">{{ count($website->notification_emails) }} notification {{ count($website->notification_emails) === 1 ? 'email' : 'emails' }}</span>
                        @else
                            <span class="crm-badge-warning">No notification email configured</span>
                        @endif
                        @if ($check?->uptime_percentage !== null)
                            <span class="crm-badge-info">Uptime {{ number_format((float) $check->uptime_percentage, 2) }}%</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="crm-card p-8 text-center">
                    <h3 class="text-xl font-black text-slate-900">No website connected yet</h3>
                    <p class="mt-2 text-sm text-slate-500">Create the client first, then connect a website to start lead capture, health tracking, and script detection in one workspace.</p>
                </div>
            @endforelse
        </div>

        <div class="crm-card p-6">
            <h3 class="text-xl font-black text-slate-900">Recent Leads For This Client</h3>
            <div class="mt-5 space-y-4">
                @forelse ($recentLeads as $lead)
                    <a href="{{ route('admin.leads.show', $lead) }}" class="block rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="font-semibold text-slate-900">{{ $lead->visitor_name ?: 'Unnamed Lead' }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $lead->website_name }} | {{ $lead->form_name ?: 'Unknown form' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $lead->created_at?->diffForHumans() }} | {{ ucfirst($lead->email_status) }}</p>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No leads for this client yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
