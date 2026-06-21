@extends('layouts.app', ['title' => $client->name . ' Workspace', 'heading' => $client->name . ' Workspace'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-4">
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Websites</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ $clientStats['total_websites'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Tracked Forms</p>
            <p class="mt-3 text-4xl font-black text-slate-900">{{ $clientStats['total_forms'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Forms This Month</p>
            <p class="mt-3 text-4xl font-black text-teal-700">{{ $clientStats['forms_this_month'] }}</p>
        </div>
        <div class="crm-card p-6">
            <p class="text-sm font-semibold text-slate-500">Failed Forms</p>
            <p class="mt-3 text-4xl font-black text-rose-600">{{ $clientStats['failed_forms'] }}</p>
        </div>
    </div>

    <div class="crm-card mt-6 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Monitoring by Website</h3>
                <p class="text-sm text-slate-500">All 10 monitoring points are isolated inside this client workspace.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.websites.create') }}" class="crm-button">Connect Website</a>
                <a href="{{ route('admin.monitoring.index') }}" class="crm-button-secondary">View Checklist</a>
            </div>
        </div>
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($websites as $website)
            @php($check = $website->latestMonitorCheck)
            <div class="crm-card p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">{{ $website->website_url }}</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-900">{{ $website->website_name }}</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            API Key linked • {{ $website->forms_count }} forms • {{ $website->leads_count }} total leads
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

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">1. Website</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->website_status ? ucfirst($check->website_status) : 'Not tested' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">2. Email Delivery</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->email_delivery_status ? ucwords(str_replace('_', ' ', $check->email_delivery_status)) : 'Pending' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">3. This Month</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $website->forms_submitted_this_month_count }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">4. Last Success</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $website->lastSuccessfulLead?->form_name ?: 'No sent form yet' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $website->lastSuccessfulLead?->created_at?->diffForHumans() ?: 'Awaiting first success' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">5. Failed Forms</p>
                        <p class="mt-3 text-lg font-black text-rose-600">{{ $website->failed_forms_count }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">6. Load Time</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->site_load_time_ms ? $check->site_load_time_ms . ' ms' : 'Not tested' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">7. Current Issues</p>
                        <p class="mt-3 text-sm font-semibold text-slate-700">{{ $check && filled($check->issues) ? implode(' ', $check->issues) : 'No active issues detected.' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">8. Run Test</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->tested_at ? $check->tested_at->diffForHumans() : 'Ready to run' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">9. SSL Status</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->ssl_status ? ucwords(str_replace('_', ' ', $check->ssl_status)) : 'Unknown' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">10. Uptime</p>
                        <p class="mt-3 text-lg font-black text-slate-900">{{ $check?->uptime_percentage !== null ? number_format((float) $check->uptime_percentage, 2) . '%' : 'Not enough data' }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="crm-card p-8 text-center">
                <h3 class="text-xl font-black text-slate-900">No website connected yet</h3>
                <p class="mt-2 text-sm text-slate-500">Create the client first, then connect a website to start form and monitoring tracking separately.</p>
            </div>
        @endforelse
    </div>
@endsection
