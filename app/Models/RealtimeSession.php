<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealtimeSession extends Model
{
    protected $fillable = [
        'client_id',
        'session_id',
        'current_url',
        'page_title',
        'realtime_session_id',
        'client_secret',
        'expires_at',
        'request_payload',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'expires_at' => 'integer',
        ];
    }
}
