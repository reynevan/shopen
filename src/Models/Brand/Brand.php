<?php

namespace Shopen\Models\Brand;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shopen\Models\Traits\HasSeoDetails;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSeoDetails;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'show_on_homepage'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'show_on_homepage' => 'bool',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('logo-160')
            ->fit(Fit::Contain, null, 160)
            ->quality(100)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('logo-80')
            ->fit(Fit::Contain, null, 80)
            ->quality(100)
            ->format('webp')
            ->nonQueued();
    }

    public function getLogoUrl(): array
    {
        $logo = $this->getFirstMedia();
        if (!$logo) {
            return [];
        }
        return [
            '80w' => $logo->getFullUrl('logo-80'),
            '160w' => $logo->getFullUrl('logo-160'),
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    public function scopeVisibleOnHomepage(Builder $query): Builder
    {
        return $query->where('show_on_homepage', 1);
    }

    public function getUrl(): string
    {
        return route('brands.show', $this);
    }
}
