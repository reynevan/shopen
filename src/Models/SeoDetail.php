<?php

namespace Shopen\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Shopen\Models\Website;

class SeoDetail extends Model
{
    protected $fillable = [
        'website_id',
        'seo_title',
        'seo_description',
        'seoable_id',
        'seoable_type',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function getSeoTitleAttribute()
    {
        return $this->attributes['seo_title'] .  ' - ' . config('app.name');
    }
}
