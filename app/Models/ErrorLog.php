<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'session_id',
        'lead_id',
        'workflow_name',
        'workflow_id',
        'execution_id',
        'failed_node',
        'error_message',
        'error_stack',
        'last_user_message',
        'page_url',
        'page_title',
        'severity',
        'status',
        'raw_error',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_error' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
