<h2>Website Run Test Report</h2>

<p><strong>Website:</strong> {{ $website->website_name }}</p>
<p><strong>URL:</strong> {{ $website->website_url }}</p>
<p><strong>Status:</strong> {{ $check->website_status === 'online' ? 'Live' : 'Down' }}</p>
<p><strong>SSL:</strong> {{ $check->ssl_status === 'valid' ? 'Valid' : (in_array($check->ssl_status, ['expired', 'not_secure'], true) ? 'Not OK' : ucwords(str_replace('_', ' ', $check->ssl_status))) }}</p>
<p><strong>Google Analytics:</strong> {{ $check->google_analytics_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>GTM:</strong> {{ $check->google_tag_manager_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>Search Console:</strong> {{ $check->google_search_console_detected ? 'Detected' : 'Not detected' }}</p>
<p><strong>Microsoft Tracking:</strong> {{ $check->microsoft_tracking_detected ? 'Detected' : 'Not detected' }}</p>
@php
    $reportIssues = collect($check->issues ?? [])
        ->reject(fn ($issue) => str_contains(strtolower((string) $issue), 'website check failed'))
        ->reject(fn ($issue) => str_contains(strtolower((string) $issue), 'unable to read the ssl certificate'))
        ->values()
        ->all();
@endphp
<p><strong>Issues:</strong> {{ filled($reportIssues) ? implode(' | ', $reportIssues) : 'None' }}</p>
