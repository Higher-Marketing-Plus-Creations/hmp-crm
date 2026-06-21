<div style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">New Lead Received</h2>
    <p style="margin-top: 0;">A new lead has been captured from <strong>{{ $lead->website_name }}</strong>.</p>

    <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-top: 16px;">
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Form</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->form_name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Name</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->visitor_name ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Email</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->visitor_email ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Phone</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->visitor_phone ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Page URL</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->page_url }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #e2e8f0;"><strong>Message</strong></td>
            <td style="border: 1px solid #e2e8f0;">{{ $lead->message ?: 'N/A' }}</td>
        </tr>
    </table>

    <h3 style="margin-top: 24px;">Raw Form Data</h3>
    <pre style="background: #0f172a; color: #d1fae5; padding: 16px; border-radius: 12px; overflow-x: auto;">{{ json_encode($lead->form_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
</div>
