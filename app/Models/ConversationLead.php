<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationLead extends Model
{
    protected $fillable = [
        'session_id',
        'full_name',
        'email',
        'phone',
        'intent',
        'source_page',
        'status',
        'notes',
        'client_id',
        'business_name',
        'website_url',
        'service_interest',
        'custom_data',
        'emailed_at',
        'is_qualified',
        'qualified_at',
        'notification_sent',
        'notification_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'custom_data' => 'array',
            'emailed_at' => 'datetime',
            'is_qualified' => 'boolean',
            'qualified_at' => 'datetime',
            'notification_sent' => 'boolean',
            'notification_sent_at' => 'datetime',
        ];
    }
}
