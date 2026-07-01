@extends('layouts.app', ['title' => 'Websites', 'heading' => 'Website Setup'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Website Connections</h3>
                <p class="text-sm text-slate-500">Connect a website once, add recipient emails, then let leads, health checks, and tracking detection flow into the client workspace automatically.</p>
            </div>
            <a href="{{ route('admin.websites.create') }}" class="crm-button">Add Website</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Website</th>
                        <th class="pb-3">Client</th>
                        <th class="pb-3">Setup</th>
                        <th class="pb-3">Activity</th>
                        <th class="pb-3">Health</th>
                        <th class="pb-3">Tracking</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($websites as $website)
                        @php($check = $website->latestMonitorCheck)
                        <tr>
                            <td class="py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $website->website_name }}</p>
                                <p class="text-xs text-slate-500">{{ $website->website_url }}</p>
                            </td>
                            <td class="py-4 align-top">{{ $website->client?->name }}</td>
                            <td class="py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $website->notification_emails ? count($website->notification_emails) . ' email(s)' : 'Needs recipients' }}</p>
                                <p class="text-xs text-slate-500">{{ filled($website->allowed_domains) ? 'Domain rules ready' : 'Using website host' }}</p>
                            </td>
                            <td class="py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $website->leads_count }} leads</p>
                                <p class="text-xs text-slate-500">{{ $website->forms_count }} form source(s) detected</p>
                            </td>
                            <td class="py-4 align-top">
                                <p class="font-semibold text-slate-900">{{ $check?->website_status ? ucfirst($check->website_status) : 'Not tested' }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $check?->ssl_status ? ucwords(str_replace('_', ' ', $check->ssl_status)) : 'SSL unknown' }} |
                                    {{ $check?->tested_at?->diffForHumans() ?: 'Run first test' }}
                                </p>
                            </td>
                            <td class="py-4 align-top">
                                <div class="grid gap-2 text-xs text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check && $check->google_analytics_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_analytics_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Google Analytics</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check && $check->google_tag_manager_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_tag_manager_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>GTM</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check && $check->google_search_console_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->google_search_console_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Search Console</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check && $check->microsoft_tracking_detected ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{!! $check && $check->microsoft_tracking_detected ? '&#10003;' : '&#10005;' !!}</span>
                                        <span>Microsoft Tracking</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">{{ $check ? 'Based on latest Run Test scan' : 'Run Test to scan tracking tags' }}</p>
                            </td>
                            <td class="py-4 align-top">
                                <span class="{{ $website->status === 'active' ? 'crm-badge-success' : 'crm-badge-danger' }}">{{ $website->status }}</span>
                            </td>
                            <td class="py-4 align-top">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.websites.run-test', $website) }}">
                                        @csrf
                                        <button type="submit" class="crm-button-secondary">Run Test</button>
                                    </form>
                                    <a href="{{ route('admin.clients.workspace', $website->client) }}" class="crm-button-secondary">Workspace</a>
                                    <a href="{{ route('admin.websites.edit', $website) }}" class="crm-button-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.websites.destroy', $website) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="crm-button-secondary">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $websites->links() }}</div>
    </div>
@endsection
