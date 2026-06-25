<h2>Website Monitoring Alert</h2>

<p><strong>Website:</strong> {{ $alert->website?->website_name }}</p>
<p><strong>URL:</strong> {{ $alert->website?->website_url }}</p>
<p><strong>Alert Type:</strong> {{ strtoupper($alert->type) }}</p>
<p><strong>Status:</strong> {{ $alert->state_label }}</p>

@if($alert->check)
    <p><strong>Website Status:</strong> {{ $alert->check->website_status }}</p>
    <p><strong>SSL Status:</strong> {{ str_replace('_', ' ', $alert->check->ssl_status) }}</p>
    <p><strong>Last Checked At:</strong> {{ optional($alert->check->last_checked_at)->format('d M Y h:i A') }}</p>
    @if($alert->check->response_time_ms !== null)
        <p><strong>Response Time:</strong> {{ $alert->check->response_time_ms }} ms</p>
    @endif
    @if($alert->check->ssl_expiry_date)
        <p><strong>SSL Expiry:</strong> {{ optional($alert->check->ssl_expiry_date)->format('d M Y h:i A') }}</p>
    @endif
    @if($alert->check->ssl_days_left !== null)
        <p><strong>SSL Days Left:</strong> {{ $alert->check->ssl_days_left }}</p>
    @endif
    @if($alert->check->last_error)
        <p><strong>Last Error:</strong> {{ $alert->check->last_error }}</p>
    @endif
    @if(($alert->check->issues ?? []) !== [])
        <p><strong>Issues:</strong></p>
        <ul>
            @foreach($alert->check->issues as $issue)
                <li>{{ $issue }}</li>
            @endforeach
        </ul>
    @endif
@endif
