<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'lead_id',
        'role',
        'message',
        'intent',
        'page_url',
        'page_title',
        'collecting_field',
        'client_id',
        'selected_intent',
        'event_type',
        'created_at',
    ];
}
