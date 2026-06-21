<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function index(): View
    {
        return view('admin.websites.index', [
            'websites' => Website::query()
                ->with(['client', 'latestMonitorCheck'])
                ->withCount(['forms', 'leads'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.websites.form', [
            'website' => new Website(['status' => 'active']),
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Website::query()->create($this->validatedData($request));

        return redirect()->route('admin.websites.index')->with('status', 'Website created successfully.');
    }

    public function edit(Website $website): View
    {
        return view('admin.websites.form', [
            'website' => $website,
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Website $website): RedirectResponse
    {
        $website->update($this->validatedData($request, $website));

        return redirect()->route('admin.websites.index')->with('status', 'Website updated successfully.');
    }

    public function destroy(Website $website): RedirectResponse
    {
        $website->delete();

        return redirect()->route('admin.websites.index')->with('status', 'Website deleted successfully.');
    }

    protected function validatedData(Request $request, ?Website $website = null): array
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'website_name' => ['required', 'string', 'max:255'],
            'website_url' => ['required', 'url', 'max:2048'],
            'allowed_domains' => ['nullable', 'string'],
            'notification_emails' => ['nullable', 'string'],
            'api_key' => ['nullable', 'string', 'min:12', 'max:255', 'unique:websites,api_key' . ($website ? ',' . $website->id : '')],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $apiKey = trim((string) ($data['api_key'] ?? ''));
        $domains = preg_split('/[\r\n,]+/', (string) ($data['allowed_domains'] ?? '')) ?: [];
        $emails = preg_split('/[\r\n,;]+/', (string) ($data['notification_emails'] ?? '')) ?: [];

        $data['api_key'] = $apiKey !== '' ? $apiKey : Website::generateApiKey();
        $data['allowed_domains'] = implode(PHP_EOL, array_values(array_filter(array_map(function ($domain) {
            $domain = trim(Str::lower($domain));
            return $domain !== '' ? preg_replace('#^https?://#', '', $domain) : null;
        }, $domains))));
        $data['notification_emails'] = array_values(array_filter(array_map(function ($email) {
            $email = trim(Str::lower($email));
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }, $emails)));

        return $data;
    }
}
