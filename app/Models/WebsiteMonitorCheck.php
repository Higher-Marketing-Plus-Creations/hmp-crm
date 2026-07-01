<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteMonitorCheck extends Model
{
    protected $fillable = [
        'website_id',
        'website_status',
        'last_checked_at',
        'email_delivery_status',
        'google_analytics_detected',
        'google_tag_manager_detected',
        'google_search_console_detected',
        'microsoft_tracking_detected',
        'forms_submitted_this_month',
        'last_successful_form_submitted_at',
        'failed_form_count',
        'response_time_ms',
        'site_load_time_ms',
        'issues',
        'tracking_detection_details',
        'run_test_status',
        'ssl_status',
        'ssl_expiry_date',
        'ssl_days_left',
        'uptime_percentage',
        'http_status_code',
        'check_summary',
        'last_error',
        'tested_at',
    ];

    protected function casts(): array
    {
        return [
            'issues' => 'array',
            'tracking_detection_details' => 'array',
            'tested_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'last_successful_form_submitted_at' => 'datetime',
            'ssl_expiry_date' => 'datetime',
            'uptime_percentage' => 'decimal:2',
            'google_analytics_detected' => 'boolean',
            'google_tag_manager_detected' => 'boolean',
            'google_search_console_detected' => 'boolean',
            'microsoft_tracking_detected' => 'boolean',
        ];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
