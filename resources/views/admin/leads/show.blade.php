@extends('layouts.app', ['title' => 'Lead Detail', 'heading' => 'Lead Detail'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <div class="space-y-6">
            <div class="crm-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Lead #{{ $lead->id }}</p>
                        <h3 class="mt-3 text-2xl font-black text-slate-900">{{ $lead->visitor_name ?: 'Unnamed Lead' }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $lead->visitor_email ?: 'No email' }} {{ $lead->visitor_phone ? ' | ' . $lead->visitor_phone : '' }}</p>
                    </div>
                    <span class="{{ $lead->email_status === 'sent' ? 'crm-badge-success' : ($lead->email_status === 'failed' ? 'crm-badge-danger' : 'crm-badge-warning') }}">{{ $lead->email_status }}</span>
                </div>

                <div class="mt-6 space-y-3 text-sm text-slate-600">
                    <p><strong>Website:</strong> {{ $lead->website_name }}</p>
                    <p><strong>Form:</strong> {{ $lead->form_name }}</p>
                    <p><strong>Page URL:</strong> <a href="{{ $lead->page_url }}" target="_blank" class="text-teal-700">{{ $lead->page_url }}</a></p>
                    <p><strong>IP Address:</strong> {{ $lead->ip_address ?: 'N/A' }}</p>
                    <p><strong>Referrer:</strong> {{ $lead->referrer ?: 'N/A' }}</p>
                    <p><strong>User Agent:</strong> {{ $lead->user_agent ?: 'N/A' }}</p>
                    <p><strong>Message:</strong> {{ $lead->message ?: 'N/A' }}</p>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('admin.leads.update-status', $lead) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="read">
                        <button type="submit" class="crm-button-secondary">Mark Read</button>
                    </form>
                    <form method="POST" action="{{ route('admin.leads.update-status', $lead) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="spam">
                        <button type="submit" class="crm-button-secondary">Mark Spam</button>
                    </form>
                    @if ($lead->email_status === 'failed')
                        <form method="POST" action="{{ route('admin.leads.retry-email', $lead) }}">
                            @csrf
                            <button type="submit" class="crm-button">Retry Email</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="crm-card p-6">
                <h3 class="text-xl font-black text-slate-900">Email Logs</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($lead->emailLogs as $log)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-slate-900">{{ $log->subject }}</p>
                                <span class="{{ $log->status === 'sent' ? 'crm-badge-success' : 'crm-badge-danger' }}">{{ $log->status }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">To: {{ $log->sent_to ?: 'Not set' }}</p>
                            @if ($log->error_message)
                                <p class="mt-2 text-sm text-rose-700">{{ $log->error_message }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No email logs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="crm-card p-6">
            <h3 class="text-xl font-black text-slate-900">Captured Form Data</h3>
            <div class="mt-5 overflow-hidden rounded-3xl border border-slate-100 bg-slate-950">
                <pre class="overflow-x-auto p-6 text-sm text-emerald-300">{{ json_encode($lead->form_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
@endsection
