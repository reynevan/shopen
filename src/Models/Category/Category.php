<?php

namespace Shopen\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shopen\Models\Category\Attribute\CategoryAttribute;
use Shopen\Models\Interfaces\HasCustomAttributesInterface;
use Shopen\Models\Product\Product;
use Shopen\Models\Traits\HasCustomAttributes;
use Shopen\Models\Traits\HasUrl;
use Shopen\Models\UrlRewrite;
use Shopen\Models\Traits\HasSeoDetails;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia, HasCustomAttributesInterface
{
    use HasCustomAttributes, HasUrl, HasSeoDetails;
    use InteractsWithMedia;
    use Searchable;

    const ENTITY_TYPE = 'category';

    protected $casts = [
        'is_active' => 'bool',
        'display_in_menu' => 'bool',
        'level' => 'int',
        'parent_id' => 'int',
    ];

    protected $fillable = [
        'is_active',
    ];

    protected function getAttributeClass(): string
    {
        return CategoryAttribute::class;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('menu-image')
            ->fit(Fit::Crop, 443, 375)
            ->quality(100)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('menu-image-2x')
            ->fit(Fit::Crop, 886, 700)
            ->quality(100)
            ->format('webp')
            ->nonQueued();
    }

    public function getMenuMedia()
    {
        if (!$this->getFirstMedia('menu-image')) {
            return [];
        }
        $media = $this->getFirstMedia('menu-image');
        return [
            '443w' => $media->getFullUrl('menu-image'),
            '886w' => $media->getFullUrl('menu-image-2x'),
        ];
    }

    public function getMenuImageUrl()
    {
        return $this->getFirstMediaUrl('menu-image');
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_index');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }

    public function hasProduct(Product $product): bool
    {
        return $this->products()->where('product_id', $product->id)->exists();
    }

    public function urlRewrites()
    {
        return $this->hasMany(UrlRewrite::class, 'entity_id')->where('entity_type', self::ENTITY_TYPE);
    }

    public function generateUrlRewrite()
    {
        $rewrite = new UrlRewrite();
        $url = Str::slug($this->getCustomAttribute('name'));
        $parent = $this->parent;
        while ($parent) {
            $url = Str::slug($parent->getCustomAttribute('name')) . '/' . $url;
            $parent = $parent->parent;
        }
        $rewrite->request_path = $url;
        $rewrite->entity_type = self::ENTITY_TYPE;
        $rewrite->entity_id = $this->id;
        $rewrite->store_id = 1;
        $rewrite->target_path = '/categories/' . $this->id;
        $rewrite->save();

    }

    public function getUrl()
    {
        $urlRewrite = $this->urlRewrites()->first();
        if (!$urlRewrite) {
            return null;
        }
        return config('app.url') . '/' . $urlRewrite->request_path;
    }

    public function setParentId($parentId): static
    {
        if ($this->parent_id !== $parentId) {
            $parent = self::query()->where('id', $parentId)->first();
            $this->level = $parent ? $parent->level + 1 : 0;
            $this->sort_index = self::query()->where('parent_id', $parentId)->max('sort_index') + 1;
            $this->parent_id = $parentId;
        }

        return $this;
    }

    protected function getDefaultSeoTitle(): string
    {
        return "$this->name - kup online ";
    }

    protected function getDefaultSeoDescription(): string
    {
        $appName = config('app.name');
        return "$this->name  w $appName. Zobacz bogatą ofertę!";
    }

}
