@extends('layouts.app', ['title' => 'Dashboard', 'heading' => 'Daily Overview'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-4">
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Websites Needing Attention</p>
            <p class="mt-3 text-4xl font-black text-rose-600">{{ number_format($stats['websites_needing_attention']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Failed Emails</p>
            <p class="mt-3 text-4xl font-black text-rose-600">{{ number_format($stats['failed_emails']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">New Leads Today</p>
            <p class="mt-3 text-4xl font-black text-teal-700">{{ number_format($stats['new_leads_today']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Websites Not Tested Yet</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ number_format($stats['untested_websites']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Call Records</p>
            <p class="mt-3 text-4xl font-black text-slate-900">Twilio</p>
            <a href="{{ route('admin.twilio.recordings.index') }}" class="mt-4 inline-flex text-sm font-bold text-teal-700">Open recordings</a>
        </div>
    </div>

    <div class="crm-card mt-6 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Shortest Daily Workflow</h3>
                <p class="text-sm text-slate-500">Open a client workspace, check unhealthy websites, run a test only where needed, and review new or failed leads from the same admin.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.clients.create') }}" class="crm-button">Create Client</a>
                <a href="{{ route('admin.websites.create') }}" class="crm-button-secondary">Connect Website</a>
                <a href="{{ route('admin.twilio.recordings.index') }}" class="crm-button-secondary">Call Records</a>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="crm-card p-6">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">Client Workspaces</h3>
                <a href="{{ route('admin.clients.index') }}" class="text-sm font-bold text-teal-700">View all</a>
            </div>
            <div class="space-y-4">
                @forelse ($clients as $client)
                    <div class="rounded-3xl border border-slate-100 bg-slate-50 px-5 py-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h4 class="text-lg font-black text-slate-900">{{ $client->name }}</h4>
                                <p class="text-sm text-slate-500">{{ $client->company_name ?: 'No company name' }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $client->websites_count }} websites | {{ $client->leads_count }} leads</p>
                                <div class="mt-4 space-y-3">
                                    @forelse ($client->websites as $website)
                                        @php($check = $website->latestMonitorCheck)
                                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="font-semibold text-slate-900">{{ $website->website_name }}</p>
                                                    <p class="text-xs text-slate-500">{{ $website->new_leads_today_count }} new today | {{ $website->failed_leads_count }} failed emails | {{ $check?->tested_at?->diffForHumans() ?: 'Not tested yet' }}</p>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="{{ $check?->website_status === 'online' ? 'crm-badge-success' : ($check?->website_status === 'offline' ? 'crm-badge-danger' : 'crm-badge-warning') }}">
                                                        {{ $check?->website_status ? ucfirst($check->website_status) : 'Needs test' }}
                                                    </span>
                                                    <span class="{{ in_array($check?->email_delivery_status, ['healthy', 'pending'], true) ? 'crm-badge-info' : 'crm-badge-warning' }}">
                                                        {{ $check?->email_delivery_status ? ucwords(str_replace('_', ' ', $check->email_delivery_status)) : 'Email unknown' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <span class="crm-badge-warning">No website connected</span>
                                    @endforelse
                                </div>
                            </div>
                            <a href="{{ route('admin.clients.workspace', $client) }}" class="crm-button-secondary">Open Workspace</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No clients available yet.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="crm-card p-6">
                <h3 class="text-xl font-black text-slate-900">Recent Lead Activity</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($recentLeads as $lead)
                        <a href="{{ route('admin.leads.show', $lead) }}" class="block rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="font-semibold text-slate-900">{{ $lead->visitor_name ?: 'Unnamed Lead' }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $lead->website?->client?->name }} | {{ $lead->website_name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $lead->created_at?->diffForHumans() }} | {{ ucfirst($lead->email_status) }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No recent leads yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="crm-card p-6">
                <h3 class="text-xl font-black text-slate-900">Recent Email Failures</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($recentEmailFailures as $log)
                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                            <p class="font-semibold text-rose-800">{{ $log->lead?->website?->client?->name }} | {{ $log->lead?->website_name }}</p>
                            <p class="mt-1 text-sm text-rose-700">{{ $log->error_message }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No recent failures.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
