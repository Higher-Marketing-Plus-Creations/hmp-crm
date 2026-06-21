<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
    ];

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    public function forms(): HasManyThrough
    {
        return $this->hasManyThrough(Form::class, Website::class);
    }

    public function leads(): HasManyThrough
    {
        return $this->hasManyThrough(Lead::class, Website::class);
    }
}
