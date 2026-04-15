<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoDetail extends Model
{
    protected $fillable = [
        'store_id',
        'seo_title',
        'seo_description',
        'seoable_id',
        'seoable_type',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getSeoTitleAttribute()
    {
        return $this->attributes['seo_title'];
    }
}
