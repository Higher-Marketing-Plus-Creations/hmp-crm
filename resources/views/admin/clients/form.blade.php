@extends('layouts.app', ['title' => $client->exists ? 'Edit Client' : 'Add Client', 'heading' => $client->exists ? 'Edit Client' : 'Add Client'])

@section('content')
    <form method="POST" action="{{ $client->exists ? route('admin.clients.update', $client) : route('admin.clients.store') }}" class="crm-card p-6">
        @csrf
        @if ($client->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Name</label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" class="crm-input" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="crm-input">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="crm-input">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="crm-input">
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="crm-button">Save Client</button>
            <a href="{{ route('admin.clients.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>
@endsection
