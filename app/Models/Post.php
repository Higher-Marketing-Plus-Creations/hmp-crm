<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'feature_image',
        'content',
        'website_id',
        'category',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
