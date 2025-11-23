<?php

namespace Shopen\Models\Banner;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Shopen\Enums\Banner\Placement;
use Shopen\Enums\Banner\PlacementType;
use Shopen\Models\Category\Category;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model implements HasMedia
{
    const IMAGE_QUALITY = 80;
    use InteractsWithMedia;

    protected $casts = [
        'opens_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'placement_type' => PlacementType::class
    ];

    protected $fillable = [
        'title',
        'alt_text',
        'image_path_desktop',
        'image_path_mobile',
        'link_url',
        'opens_in_new_tab',
        'placement_type',
        'placement_key',
        'start_date',
        'end_date',
        'is_active',
        'sort_order',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('banner')
            ->quality(self::IMAGE_QUALITY)
            ->format('webp')
            ->nonQueued();
    }

    public function getDesktopFileUrl(): ?string
    {
        return $this->getFileUrl('desktop');
    }

    public function getMobileFileUrl(): ?string
    {
        return $this->getFileUrl('mobile');
    }

    protected function getFileUrl($type): ?string
    {
        $media = $this->getMedia($type)->first();
        if (!$media) {
            return null;
        }
        return $media->getUrl('banner');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function getPagePlacementKey(): string
    {
        $placementsPageTop = [
            Placement::HOME_PAGE_TOP->value,
            Placement::CATEGORY_PAGE_TOP->value,
            Placement::PRODUCT_PAGE_TOP->value,
        ];
        if (in_array($this->placement_key, $placementsPageTop)) {
            return 'page_top';
        }
        $placementsPageBottom = [
            Placement::HOME_PAGE_BOTTOM->value,
            Placement::CATEGORY_PAGE_BOTTOM->value,
            Placement::PRODUCT_PAGE_BOTTOM->value,
        ];
        if (in_array($this->placement_key, $placementsPageBottom)) {
            return 'page_bottom';
        }

        return $this->placement_key;
    }
}
