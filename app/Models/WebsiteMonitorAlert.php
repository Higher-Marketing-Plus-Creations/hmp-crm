<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteMonitorAlert extends Model
{
    protected $fillable = [
        'website_id',
        'website_monitor_check_id',
        'type',
        'state_key',
        'state_label',
        'recipients',
        'send_status',
        'error_message',
        'sent_at',
        'last_seen_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'sent_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function check(): BelongsTo
    {
        return $this->belongsTo(WebsiteMonitorCheck::class, 'website_monitor_check_id');
    }
}
