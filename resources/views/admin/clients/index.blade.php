@extends('layouts.app', ['title' => 'Clients', 'heading' => 'Clients'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Client Management</h3>
                <p class="text-sm text-slate-500">Create each client first, then manage their websites and monitoring separately.</p>
            </div>
            <a href="{{ route('admin.clients.create') }}" class="crm-button">Add Client</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Name</th>
                        <th class="pb-3">Company</th>
                        <th class="pb-3">Contact</th>
                        <th class="pb-3">Websites</th>
                        <th class="pb-3">Leads</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($clients as $client)
                        <tr>
                            <td class="py-4 font-semibold text-slate-900">{{ $client->name }}</td>
                            <td class="py-4">{{ $client->company_name ?: 'N/A' }}</td>
                            <td class="py-4">
                                <p>{{ $client->email ?: 'No email' }}</p>
                                <p class="text-xs text-slate-500">{{ $client->phone ?: 'No phone' }}</p>
                            </td>
                            <td class="py-4">{{ $client->websites_count }}</td>
                            <td class="py-4">{{ $client->leads_count }}</td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.clients.workspace', $client) }}" class="crm-button-secondary">Workspace</a>
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="crm-button-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}">
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

        <div class="mt-6">{{ $clients->links() }}</div>
    </div>
@endsection
