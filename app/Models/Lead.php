<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'website_id',
        'form_id',
        'website_name',
        'page_url',
        'form_name',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'message',
        'form_data',
        'ip_address',
        'user_agent',
        'referrer',
        'status',
        'email_status',
        'session_id',
        'full_name',
        'email',
        'phone',
        'intent',
        'source_page',
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
            'form_data' => 'array',
            'custom_data' => 'array',
            'emailed_at' => 'datetime',
        ];
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }
}
