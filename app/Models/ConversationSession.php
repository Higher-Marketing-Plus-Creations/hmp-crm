<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationSession extends Model
{
    protected $fillable = [
        'session_id',
        'current_url',
        'page_title',
        'page_summary',
        'intent',
        'lead_id',
        'is_active',
        'last_activity_at',
        'client_id',
        'selected_intent',
        'last_event_type',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }
}
