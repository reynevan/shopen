<?php

namespace Shopen\Models\Instagram;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InstagramPost extends Model implements HasMedia
{
    const IMAGE_QUALITY = 80;
    use InteractsWithMedia;

    protected $fillable = [
        'media_id',
        'post_url',
        'timestamp'
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('media')
            ->quality(self::IMAGE_QUALITY)
            ->fit(Fit::Crop, 300, 300)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('media-2x')
            ->quality(self::IMAGE_QUALITY)
            ->fit(Fit::Crop, 600, 600)
            ->format('webp')
            ->nonQueued();
    }
}
