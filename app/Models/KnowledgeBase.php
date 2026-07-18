<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $table='knowledge_base';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'section_title',
        'section_type',
        'content',
        'is_active',
        'updated_at',
        'sort_order',
        'created_at',
    ];
}
