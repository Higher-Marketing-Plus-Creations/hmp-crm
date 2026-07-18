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
    ];

    protected function casts(): array
    {
        return [
            'custom_data' => 'array',
            'emailed_at' => 'datetime',
        ];
    }
}
