<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSetting extends Model
{
    protected $fillable = [
        'client_id',
        'business_name',
        'industry',
        'assistant_name',
        'primary_cta',
        'booking_url',
        'tone',
        'fallback_message',
        'qualification_question',
        'is_active',
    ];
}
