<h2>Website Monitoring Alert</h2>

<p><strong>Website:</strong> {{ $alert->website?->website_name }}</p>
<p><strong>URL:</strong> {{ $alert->website?->website_url }}</p>
<p><strong>Status:</strong> {{ $alert->check?->website_status === 'online' ? 'Live' : 'Down' }}</p>
<p><strong>SSL:</strong> {{ $alert->check?->ssl_status === 'valid' ? 'Valid' : (in_array($alert->check?->ssl_status, ['expired', 'not_secure'], true) ? 'Not OK' : ucwords(str_replace('_', ' ', (string) $alert->check?->ssl_status))) }}</p>
<p><strong>Google Analytics:</strong> {{ $alert->check?->google_analytics_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>GTM:</strong> {{ $alert->check?->google_tag_manager_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>Search Console:</strong> {{ $alert->check?->google_search_console_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>Microsoft Tracking:</strong> {{ $alert->check?->microsoft_tracking_detected ? 'Detected' : 'Not detected' }}</p>
@php
    $alertIssues = collect($alert->check->issues ?? [])
        ->reject(fn ($issue) => str_contains(strtolower((string) $issue), 'website check failed'))
        ->reject(fn ($issue) => str_contains(strtolower((string) $issue), 'unable to read the ssl certificate'))
        ->values()
        ->all();
@endphp
<p><strong>Issues:</strong> {{ filled($alertIssues) ? implode(' | ', $alertIssues) : 'None' }}</p>

@if($alert->check)
    <p><strong>HTTP:</strong> {{ $alert->check->http_status_code ?? 'N/A' }}</p>
    <p><strong>Tested At:</strong> {{ optional($alert->check->tested_at)->format('d M Y h:i A') }}</p>
@endif
