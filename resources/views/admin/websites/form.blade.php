@extends('layouts.app', ['title' => $website->exists ? 'Edit Website' : 'Add Website', 'heading' => $website->exists ? 'Edit Website' : 'Add Website'])

@php
    $appUrl = rtrim(config('app.url'), '/');
    $apiKeyValue = old('api_key', $website->api_key ?: \App\Models\Website::generateApiKey());
    $websiteNameValue = old('website_name', $website->website_name ?: 'Your Website');
    $endpointValue = $appUrl . '/api/leads/submit';
    $scriptSource = $appUrl . '/js/lead-form.js';
    $integrationScript = "<script src=\"{$scriptSource}\"></script>\n<script>\nLeadFormTracker.init({\n  selector: '.hmp-crm-form',\n  endpoint: '{$endpointValue}',\n  apiKey: '{$apiKeyValue}',\n  websiteName: '{$websiteNameValue}',\n  honeypotField: 'website'\n});\n</script>";
    $integrationPrompt = "Attach the Higher Marketing Plus CRM lead tracking to the existing website form without changing the current design, layout, or field structure. Do not use a unique form id. Instead, add the class hmp-crm-form to the real form element and add a data-form-name attribute with the visible form name, for example data-form-name=\"Contact Form\". Keep the form fields as they are. Ensure the form contains normal name attributes like name, email, phone, message or the existing equivalents. Add one hidden honeypot input exactly like this: <input type=\"text\" name=\"website\" style=\"display:none\">. Then load this script on the page and initialize it exactly with the provided values so the form sends leads into the CRM email system. Use this endpoint: {$endpointValue}. Use this API key: {$apiKeyValue}. Use this website name: {$websiteNameValue}. Use selector .hmp-crm-form, not a unique id. If there are multiple forms that should send to this same website record, apply the same class hmp-crm-form to each real form and give each one its own data-form-name value. After attaching the script, keep the current submit button and UI behavior, but route the form through the CRM tracking so lead emails start working. If Google Analytics, GTM, Search Console verification, Clarity, or Bing tracking are already installed on the site, keep those snippets as real rendered script or meta tags in the final page source so the CRM monitoring scan can detect them during Run Test.";
    $wordpressGuideline = "WordPress multi-form implementation for this website record:\n\n1. Use the existing real form element, whether it comes from Elementor, a custom theme template, or a plugin-rendered frontend form.\n2. Add class=\"hmp-crm-form\" to every form that should send leads into this same CRM website record.\n3. Add a unique data-form-name value on each form, for example Contact Form, Quote Form, Footer Form, Popup Form.\n4. Keep the current fields and design. Only make sure the fields have usable name attributes such as name, email, phone, message, service, or their existing equivalents.\n5. Add this honeypot inside each form exactly once: <input type=\"text\" name=\"website\" style=\"display:none\">\n6. Load the shared CRM script once on the page, footer, theme options, or custom code area.\n7. Initialize LeadFormTracker once with selector .hmp-crm-form so all matching forms on that page are attached automatically.\n8. Do not rely on a unique id. The CRM setup is class-based so multiple WordPress forms can work together.\n9. If Elementor or a builder regenerates markup, re-apply the class hmp-crm-form and data-form-name on the final rendered form element.\n10. If the WordPress form already has its own success redirect or thank-you behavior, keep the visible UI but make sure the actual submit goes through the CRM script path.";
    $wordpressPrompt = "Implement Higher Marketing Plus CRM tracking on this WordPress website without changing the current design, layout, or field structure. The site may use Elementor forms, custom theme forms, or other frontend-rendered forms. For every real form that should send leads into the CRM for this website, add class hmp-crm-form and give it a meaningful data-form-name value such as Contact Form, Quote Form, Footer Form, or Popup Form. Do not depend on a unique form id because ids can change or be unique per builder instance. Keep the current fields, but confirm they have usable name attributes. Add one hidden honeypot field inside each form exactly like this: <input type=\"text\" name=\"website\" style=\"display:none\">. Load this script once on the page or through the WordPress footer/custom code area: {$scriptSource}. Then initialize it once so it targets all forms with selector .hmp-crm-form. Use this endpoint: {$endpointValue}. Use this API key: {$apiKeyValue}. Use this website name: {$websiteNameValue}. If there are multiple forms across the same WordPress site, keep the same API key and script setup, and only change each form's data-form-name value. Preserve the current submit button and styling while routing submissions through the CRM email and lead system. If the site already has GA4, GTM, Search Console verification, Microsoft Clarity, or Bing tracking, keep those snippets in the rendered head or footer output instead of hiding them behind delayed JavaScript injection so the CRM Run Test tracking scan can detect them reliably.";
@endphp

@section('content')
    <form method="POST" action="{{ $website->exists ? route('admin.websites.update', $website) : route('admin.websites.store') }}" class="crm-card p-6">
        @csrf
        @if ($website->exists)
            @method('PUT')
        @endif

        <div class="mb-6 rounded-3xl border border-teal-100 bg-teal-50 px-5 py-4">
            <h3 class="text-lg font-black text-slate-900">Simple Setup</h3>
            <p class="mt-1 text-sm text-slate-600">Only the client, website name, URL, and notification emails matter for day-to-day use. The CRM key is created automatically and the ready-to-paste script and setup prompts are shown below.</p>
        </div>

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
                <label class="mb-2 block text-sm font-semibold text-slate-600">Notification Emails</label>
                <textarea name="notification_emails" rows="5" class="crm-input" placeholder="sales@example.com&#10;owner@example.com">{{ old('notification_emails', implode(PHP_EOL, $website->notification_emails ?? [])) }}</textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-600">Status</label>
                <select name="status" class="crm-select" required>
                    <option value="active" @selected(old('status', $website->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $website->status) === 'inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-5">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Website Integration Pack</h3>
                    <p class="mt-1 text-sm text-slate-600">Copy the key, API, script, and prompts below. This setup uses the class <code class="rounded bg-white px-2 py-1 text-xs text-slate-700">hmp-crm-form</code> instead of a unique form id.</p>
                </div>
                @if (! $website->exists)
                    <span class="crm-badge-warning">Save the website once to lock this key to the website record</span>
                @else
                    <span class="crm-badge-success">Ready to attach on the website</span>
                @endif
            </div>

            <div class="mt-5 space-y-5">
                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="block text-sm font-semibold text-slate-600">CRM API Endpoint</label>
                            <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="crm-api-endpoint">Copy API</button>
                        </div>
                        <input id="crm-api-endpoint" type="text" value="{{ $endpointValue }}" class="crm-input font-mono text-xs" readonly>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="block text-sm font-semibold text-slate-600">CRM API Key</label>
                            <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="crm-api-key">Copy Key</button>
                        </div>
                        <input id="crm-api-key" type="text" name="api_key" value="{{ $apiKeyValue }}" class="crm-input font-mono text-xs" readonly>
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label class="block text-sm font-semibold text-slate-600">Script URL</label>
                            <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="crm-script-url">Copy URL</button>
                        </div>
                        <input id="crm-script-url" type="text" value="{{ $scriptSource }}" class="crm-input font-mono text-xs" readonly>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="block text-sm font-semibold text-slate-600">Ready-To-Paste Script</label>
                        <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="crm-ready-script">Copy Script</button>
                    </div>
                    <textarea id="crm-ready-script" rows="12" class="crm-input font-mono text-xs" readonly>{{ $integrationScript }}</textarea>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="block text-sm font-semibold text-slate-600">Copy-Paste Prompt For Any Normal Website</label>
                        <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="crm-normal-prompt">Copy Prompt</button>
                    </div>
                    <textarea id="crm-normal-prompt" rows="14" class="crm-input text-sm" readonly>{{ $integrationPrompt }}</textarea>
                </div>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">What to add on the website form</p>
                    <p class="mt-2">Use the real existing form and add <code class="rounded bg-slate-100 px-2 py-1 text-xs">class="hmp-crm-form"</code> plus <code class="rounded bg-slate-100 px-2 py-1 text-xs">data-form-name="Contact Form"</code>. No unique id is required.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-5">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900">WordPress Multi-Form Guideline</h3>
                    <p class="mt-1 text-sm text-slate-600">Use this for Elementor, custom theme forms, or multiple WordPress forms connected to the same website record.</p>
                </div>
                <span class="crm-badge-info">Elementor and custom form friendly</span>
            </div>

            <div class="mt-5 space-y-5">
                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="block text-sm font-semibold text-slate-600">WordPress Direct Implementation Guideline</label>
                        <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="wordpress-guideline">Copy Guideline</button>
                    </div>
                    <textarea id="wordpress-guideline" rows="14" class="crm-input text-sm" readonly>{{ $wordpressGuideline }}</textarea>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="block text-sm font-semibold text-slate-600">WordPress Copy-Paste Prompt For Developer Or Agent</label>
                        <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="wordpress-prompt">Copy WordPress Prompt</button>
                    </div>
                    <textarea id="wordpress-prompt" rows="14" class="crm-input text-sm" readonly>{{ $wordpressPrompt }}</textarea>
                </div>

                <div class="rounded-2xl border border-dashed border-amber-300 bg-white px-4 py-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">Multiple WordPress forms on the same website</p>
                    <p class="mt-2">Keep the same API endpoint, key, and script. Add <code class="rounded bg-slate-100 px-2 py-1 text-xs">hmp-crm-form</code> to each real form and only change the <code class="rounded bg-slate-100 px-2 py-1 text-xs">data-form-name</code> value per form, for example Hero Form, Contact Form, Popup Form, Footer Form.</p>
                </div>
            </div>
        </div>

        <details class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4">
            <summary class="cursor-pointer text-sm font-bold text-slate-700">Advanced Integration Settings</summary>
            <div class="mt-4 grid gap-5 md:grid-cols-1">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600">Allowed Domains</label>
                    <textarea name="allowed_domains" rows="5" class="crm-input" placeholder="example.com&#10;www.example.com">{{ old('allowed_domains', $website->allowed_domains) }}</textarea>
                </div>
            </div>
        </details>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="crm-button">Save Website</button>
            <a href="{{ route('admin.websites.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var copyButtons = document.querySelectorAll('[data-copy-target]');

            copyButtons.forEach(function (button) {
                button.addEventListener('click', async function () {
                    var targetId = button.getAttribute('data-copy-target');
                    var target = document.getElementById(targetId);

                    if (!target) {
                        return;
                    }

                    var value = 'value' in target ? target.value : target.textContent;
                    var originalText = button.textContent;

                    try {
                        await navigator.clipboard.writeText(value);
                        button.textContent = 'Copied';
                    } catch (error) {
                        target.focus();
                        target.select();
                        document.execCommand('copy');
                        button.textContent = 'Copied';
                    }

                    setTimeout(function () {
                        button.textContent = originalText;
                    }, 1500);
                });
            });
        });
    </script>
@endsection

