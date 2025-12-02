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
use ThrowableThrowable as ThrowableThrowableAlias;

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

    protected function getDesktopImageSizes()
    {
        return [640, 768, 1024, 1440, 1920];
    }

    protected function getMobileImageSizes()
    {
        return [320, 480, 640, 960, 1280];
    }
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('banner')
            ->quality(self::IMAGE_QUALITY)
            ->format('webp')
            ->nonQueued();

        foreach ($this->getDesktopImageSizes() as $size) {
            $this
                ->addMediaConversion('banner-' . $size)
                ->performOnCollections('desktop')
                ->quality(self::IMAGE_QUALITY)
                ->format('webp')
                ->width($size)
                ->nonQueued();
        }

        foreach ($this->getMobileImageSizes() as $size) {
            $this
                ->addMediaConversion('banner-' . $size)
                ->performOnCollections('mobile')
                ->quality(self::IMAGE_QUALITY)
                ->format('webp')
                ->width($size)
                ->nonQueued();
        }
    }

    public function getDesktopFileUrls(): array
    {
        return $this->getFileUrls('desktop');
    }

    public function getMobileFileUrls(): array
    {
        return $this->getFileUrls('mobile');
    }

    protected function getFileUrls($type): array
    {
        $sizes = $type === 'desktop' ? $this->getDesktopImageSizes() : $this->getMobileImageSizes();
        $media = $this->getMedia($type)->first();
        $originalWidth = $this->getImageWidth($media->getPath('banner'));
        $urls = [];
        foreach ($sizes as $size) {
            $urls[$size . 'w'] = $media->getUrl('banner-' . $size);
        }
        if ($originalWidth > array_pop($sizes)) {
            $urls[$originalWidth . 'w'] = $media->getUrl('banner');
        }
        return $urls;
    }

    protected function getImageWidth(?string $absolutePath): ?int
    {
        if (!$absolutePath || !is_file($absolutePath)) {
            return null;
        }

        try {
            $size = @getimagesize($absolutePath);
            if ($size && isset($size[0])) {
                return (int) $size[0];
            }
        } catch (\Throwable $e) {}

        return null;
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
