@extends('layouts.app', ['title' => 'Monitoring', 'heading' => 'Monitoring Build Checklist'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="crm-card p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900">10 Feature Roadmap</h3>
                    <p class="text-sm text-slate-500">This page confirms what is already built for the client monitoring CRM.</p>
                </div>
                <a href="{{ route('admin.clients.create') }}" class="crm-button">Create Client</a>
            </div>

            <div class="space-y-4">
                @foreach ($features as $index => $feature)
                    <div class="rounded-3xl border border-slate-100 bg-slate-50 px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Feature {{ $index + 1 }}</p>
                                <h4 class="mt-1 text-lg font-black text-slate-900">{{ $feature['title'] }}</h4>
                                <p class="mt-2 text-sm text-slate-600">{{ $feature['description'] }}</p>
                            </div>
                            <span class="{{ $feature['status'] === 'implemented' ? 'crm-badge-success' : 'crm-badge-warning' }}">
                                {{ str_replace('_', ' ', $feature['status']) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="crm-card p-6">
                <h3 class="text-xl font-black text-slate-900">Client Workspaces</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($clients as $client)
                        <div class="rounded-2xl bg-slate-50 px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $client->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $client->company_name ?: 'No company name' }}</p>
                                    <p class="mt-2 text-xs text-slate-500">{{ $client->websites_count }} websites | {{ $client->leads_count }} leads</p>
                                </div>
                                <a href="{{ route('admin.clients.workspace', $client) }}" class="crm-button-secondary">Open</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No clients yet. Create the first client to start separate monitoring.</p>
                    @endforelse
                </div>
            </div>

            <div class="crm-card p-6">
                <h3 class="text-xl font-black text-slate-900">Recent Monitoring Checks</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($recentChecks as $check)
                        <div class="rounded-2xl border border-slate-100 bg-white px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $check->website?->website_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $check->website?->client?->name }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $check->check_summary }}</p>
                                </div>
                                <span class="{{ $check->website_status === 'online' ? 'crm-badge-success' : 'crm-badge-danger' }}">
                                    {{ $check->website_status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No monitoring test has been run yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
