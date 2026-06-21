@extends('layouts.app', ['title' => 'Leads', 'heading' => 'Leads'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Lead Inbox</h3>
                <p class="text-sm text-slate-500">Filter by website, form, date, or email status.</p>
            </div>
            <a href="{{ route('admin.leads.export', request()->query()) }}" class="crm-button">Export CSV</a>
        </div>

        <form method="GET" class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <select name="website_id" class="crm-select">
                <option value="">All Websites</option>
                @foreach ($websites as $website)
                    <option value="{{ $website->id }}" @selected(($filters['website_id'] ?? '') == $website->id)>{{ $website->website_name }}</option>
                @endforeach
            </select>
            <select name="form_id" class="crm-select">
                <option value="">All Forms</option>
                @foreach ($forms as $form)
                    <option value="{{ $form->id }}" @selected(($filters['form_id'] ?? '') == $form->id)>{{ $form->form_name }}</option>
                @endforeach
            </select>
            <select name="status" class="crm-select">
                <option value="">All Lead Statuses</option>
                @foreach (['new', 'read', 'spam'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="email_status" class="crm-select">
                <option value="">All Email Statuses</option>
                @foreach (['pending', 'sent', 'failed'] as $status)
                    <option value="{{ $status }}" @selected(($filters['email_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="crm-input">
            <div class="flex gap-3">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="crm-input">
                <button type="submit" class="crm-button">Filter</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Lead</th>
                        <th class="pb-3">Website / Form</th>
                        <th class="pb-3">Lead Status</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">Created</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($leads as $lead)
                        <tr>
                            <td class="py-4">
                                <p class="font-semibold text-slate-900">{{ $lead->visitor_name ?: 'Unnamed Lead' }}</p>
                                <p class="text-xs text-slate-500">{{ $lead->visitor_email ?: 'No email' }} {{ $lead->visitor_phone ? ' | ' . $lead->visitor_phone : '' }}</p>
                            </td>
                            <td class="py-4">
                                <p>{{ $lead->website_name }}</p>
                                <p class="text-xs text-slate-500">{{ $lead->form_name }}</p>
                            </td>
                            <td class="py-4">
                                <span class="{{ $lead->status === 'spam' ? 'crm-badge-danger' : ($lead->status === 'read' ? 'crm-badge-info' : 'crm-badge-warning') }}">{{ $lead->status }}</span>
                            </td>
                            <td class="py-4">
                                <span class="{{ $lead->email_status === 'sent' ? 'crm-badge-success' : ($lead->email_status === 'failed' ? 'crm-badge-danger' : 'crm-badge-warning') }}">{{ $lead->email_status }}</span>
                            </td>
                            <td class="py-4 text-slate-500">{{ $lead->created_at?->format('d M Y h:i A') }}</td>
                            <td class="py-4">
                                <a href="{{ route('admin.leads.show', $lead) }}" class="crm-button-secondary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $leads->links() }}</div>
    </div>
@endsection
