@extends('layouts.app', ['title' => 'Websites', 'heading' => 'Websites'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Website Connections</h3>
                <p class="text-sm text-slate-500">Each website keeps its own API key, forms, leads, and monitoring status inside the correct client workspace.</p>
            </div>
            <a href="{{ route('admin.websites.create') }}" class="crm-button">Add Website</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Website</th>
                        <th class="pb-3">Client</th>
                        <th class="pb-3">API Key</th>
                        <th class="pb-3">Forms</th>
                        <th class="pb-3">Leads</th>
                        <th class="pb-3">Monitor</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($websites as $website)
                        @php($check = $website->latestMonitorCheck)
                        <tr>
                            <td class="py-4">
                                <p class="font-semibold text-slate-900">{{ $website->website_name }}</p>
                                <p class="text-xs text-slate-500">{{ $website->website_url }}</p>
                            </td>
                            <td class="py-4">{{ $website->client?->name }}</td>
                            <td class="py-4">
                                <code class="rounded-xl bg-slate-100 px-3 py-2 text-xs text-slate-700">{{ $website->api_key }}</code>
                            </td>
                            <td class="py-4">{{ $website->forms_count }}</td>
                            <td class="py-4">{{ $website->leads_count }}</td>
                            <td class="py-4">
                                <p class="font-semibold text-slate-900">{{ $check?->website_status ? ucfirst($check->website_status) : 'Not tested' }}</p>
                                <p class="text-xs text-slate-500">{{ $check?->tested_at?->diffForHumans() ?: 'Run the first test' }}</p>
                            </td>
                            <td class="py-4">
                                <span class="{{ $website->status === 'active' ? 'crm-badge-success' : 'crm-badge-danger' }}">{{ $website->status }}</span>
                            </td>
                            <td class="py-4">
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
