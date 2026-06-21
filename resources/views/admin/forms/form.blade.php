@extends('layouts.app', ['title' => $form->exists ? 'Edit Form' : 'Add Form', 'heading' => $form->exists ? 'Edit Form' : 'Add Form'])

@section('content')
    <form method="POST" action="{{ $form->exists ? route('admin.forms.update', $form) : route('admin.forms.store') }}" class="crm-card p-6">
        @csrf
        @if ($form->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Website</label>
                <select name="website_id" class="crm-select" required>
                    <option value="">Select Website</option>
                    @foreach ($websites as $website)
                        <option value="{{ $website->id }}" @selected(old('website_id', $form->website_id) == $website->id)>{{ $website->website_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Form Name</label>
                <input type="text" name="form_name" value="{{ old('form_name', $form->form_name) }}" class="crm-input" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Form Identifier</label>
                <input type="text" name="form_identifier" value="{{ old('form_identifier', $form->form_identifier) }}" class="crm-input">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Status</label>
                <select name="status" class="crm-select" required>
                    <option value="active" @selected(old('status', $form->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $form->status) === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-slate-600">Page URL</label>
                <input type="url" name="page_url" value="{{ old('page_url', $form->page_url) }}" class="crm-input">
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="crm-button">Save Form</button>
            <a href="{{ route('admin.forms.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>
@endsection
