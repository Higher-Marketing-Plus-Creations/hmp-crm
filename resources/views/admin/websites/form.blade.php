@extends('layouts.app', ['title' => $website->exists ? 'Edit Website' : 'Add Website', 'heading' => $website->exists ? 'Edit Website' : 'Add Website'])

@section('content')
    <form method="POST" action="{{ $website->exists ? route('admin.websites.update', $website) : route('admin.websites.store') }}" class="crm-card p-6">
        @csrf
        @if ($website->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Client</label>
                <select name="client_id" class="crm-select" required>
                    <option value="">Select Client</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id', $website->client_id) == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Website Name</label>
                <input type="text" name="website_name" value="{{ old('website_name', $website->website_name) }}" class="crm-input" required>
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-slate-600">Website URL</label>
                <input type="url" name="website_url" value="{{ old('website_url', $website->website_url) }}" class="crm-input" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">API Key</label>
                <input type="text" name="api_key" value="{{ old('api_key', $website->api_key) }}" class="crm-input" placeholder="Leave empty to auto-generate">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Status</label>
                <select name="status" class="crm-select" required>
                    <option value="active" @selected(old('status', $website->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $website->status) === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Allowed Domains</label>
                <textarea name="allowed_domains" rows="5" class="crm-input" placeholder="example.com&#10;www.example.com">{{ old('allowed_domains', $website->allowed_domains) }}</textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Notification Emails</label>
                <textarea name="notification_emails" rows="5" class="crm-input" placeholder="sales@example.com&#10;owner@example.com">{{ old('notification_emails', implode(PHP_EOL, $website->notification_emails ?? [])) }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="crm-button">Save Website</button>
            <a href="{{ route('admin.websites.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>
@endsection
