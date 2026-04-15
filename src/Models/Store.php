<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function getBaseUrlAttribute(): string
    {
        return rtrim($this->url, '/');
    }

    public function getFullUrlAttribute(): string
    {
        return rtrim(rtrim($this->url, '/') . '/' .  $this->url_code, '/');
    }
}
