<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormController extends Controller
{
    public function index(): View
    {
        return view('admin.forms.index', [
            'forms' => Form::query()->with('website')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.forms.form', [
            'form' => new Form(['status' => 'active']),
            'websites' => Website::query()->orderBy('website_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Form::query()->create($this->validatedData($request));

        return redirect()->route('admin.forms.index')->with('status', 'Form created successfully.');
    }

    public function edit(Form $form): View
    {
        return view('admin.forms.form', [
            'form' => $form,
            'websites' => Website::query()->orderBy('website_name')->get(),
        ]);
    }

    public function update(Request $request, Form $form): RedirectResponse
    {
        $form->update($this->validatedData($request));

        return redirect()->route('admin.forms.index')->with('status', 'Form updated successfully.');
    }

    public function destroy(Form $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.forms.index')->with('status', 'Form deleted successfully.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'website_id' => ['required', 'exists:websites,id'],
            'form_name' => ['required', 'string', 'max:255'],
            'form_identifier' => ['nullable', 'string', 'max:255'],
            'page_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
