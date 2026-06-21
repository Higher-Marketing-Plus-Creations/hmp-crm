@extends('layouts.app', ['title' => 'Email Logs', 'heading' => 'Email Logs'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Delivery Logs</h3>
                <p class="text-sm text-slate-500">Monitor sent and failed notification email attempts.</p>
            </div>
            <form method="GET" class="flex gap-3">
                <select name="status" class="crm-select">
                    <option value="">All Statuses</option>
                    <option value="sent" @selected(($filters['status'] ?? '') === 'sent')>Sent</option>
                    <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>Failed</option>
                </select>
                <button type="submit" class="crm-button">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Lead</th>
                        <th class="pb-3">To</th>
                        <th class="pb-3">Subject</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Error</th>
                        <th class="pb-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="py-4">
                                <a href="{{ $log->lead ? route('admin.leads.show', $log->lead) : '#' }}" class="font-semibold text-slate-900">
                                    {{ $log->lead?->website_name ?: 'Deleted Lead' }}
                                </a>
                            </td>
                            <td class="py-4">{{ $log->sent_to ?: 'N/A' }}</td>
                            <td class="py-4">{{ $log->subject }}</td>
                            <td class="py-4">
                                <span class="{{ $log->status === 'sent' ? 'crm-badge-success' : 'crm-badge-danger' }}">{{ $log->status }}</span>
                            </td>
                            <td class="py-4 text-rose-700">{{ $log->error_message ?: '-' }}</td>
                            <td class="py-4 text-slate-500">{{ $log->created_at?->format('d M Y h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $logs->links() }}</div>
    </div>
@endsection
