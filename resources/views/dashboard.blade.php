@extends('layouts.app', ['title' => 'Dashboard', 'heading' => 'Client Monitoring Dashboard'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-4">
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Total Clients</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ number_format($stats['total_clients']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Connected Websites</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ number_format($stats['total_websites']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Total Leads</p>
            <p class="mt-3 text-4xl font-black text-teal-700">{{ number_format($stats['total_leads']) }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Failed Emails</p>
            <p class="mt-3 text-4xl font-black text-rose-600">{{ number_format($stats['failed_emails']) }}</p>
        </div>
    </div>

    <div class="crm-card mt-6 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Per-Client Workspaces</h3>
                <p class="text-sm text-slate-500">Data is now organized client-wise so websites, forms, delivery health, and monitoring stay separate.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.monitoring.index') }}" class="crm-button-secondary">Open Monitoring Checklist</a>
                <a href="{{ route('admin.clients.create') }}" class="crm-button">Create Client</a>
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
                                <p class="mt-2 text-sm text-slate-600">{{ $client->websites_count }} websites • {{ $client->leads_count }} leads</p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @forelse ($client->websites as $website)
                                        @php($status = $website->latestMonitorCheck?->website_status)
                                        <span class="{{ $status === 'online' ? 'crm-badge-success' : ($status === 'offline' ? 'crm-badge-danger' : 'crm-badge-info') }}">
                                            {{ $website->website_name }}: {{ $status ? ucfirst($status) : 'Not tested' }}
                                        </span>
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

        <div class="crm-card p-6">
            <h3 class="text-xl font-black text-slate-900">Recent Email Failures</h3>
            <div class="mt-5 space-y-4">
                @forelse ($recentEmailFailures as $log)
                    <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                        <p class="font-semibold text-rose-800">{{ $log->lead?->website?->client?->name }} • {{ $log->lead?->website_name }}</p>
                        <p class="mt-1 text-sm text-rose-700">{{ $log->error_message }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No recent failures.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
