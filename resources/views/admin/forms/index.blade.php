@extends('layouts.app', ['title' => 'Forms', 'heading' => 'Forms'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Tracked Forms</h3>
                <p class="text-sm text-slate-500">Link known forms to websites for better reporting.</p>
            </div>
            <a href="{{ route('admin.forms.create') }}" class="crm-button">Add Form</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                    <tr>
                        <th class="pb-3">Form</th>
                        <th class="pb-3">Website</th>
                        <th class="pb-3">Identifier</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($forms as $form)
                        <tr>
                            <td class="py-4">
                                <p class="font-semibold text-slate-900">{{ $form->form_name }}</p>
                                <p class="text-xs text-slate-500">{{ $form->page_url ?: 'No page URL' }}</p>
                            </td>
                            <td class="py-4">{{ $form->website?->website_name }}</td>
                            <td class="py-4">{{ $form->form_identifier ?: 'N/A' }}</td>
                            <td class="py-4">
                                <span class="{{ $form->status === 'active' ? 'crm-badge-success' : 'crm-badge-danger' }}">{{ $form->status }}</span>
                            </td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.forms.edit', $form) }}" class="crm-button-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.forms.destroy', $form) }}">
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

        <div class="mt-6">{{ $forms->links() }}</div>
    </div>
@endsection
