<?php

namespace Shopen\Models\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Shopen\Core\Support\DB;
use Shopen\Database\Factories\ProductFactory;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Category\Category;
use Shopen\Models\Interfaces\HasCustomAttributesInterface;
use Shopen\Models\Product\Attribute\ProductAttribute;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Price\ProductPriceHistoryItem;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Review\ProductReview;
use Shopen\Models\Traits\HasCustomAttributes;
use Shopen\Models\Traits\HasUrl;
use Shopen\Models\UrlRewrite;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia, HasCustomAttributesInterface
{
    use HasFactory, HasCustomAttributes, HasUrl;
    use InteractsWithMedia;
    use Searchable;

    const TYPE_SIMPLE = 'simple';
    const TYPE_CONFIGURABLE = 'configurable';

    const ENTITY_TYPE = 'product';

    protected $fillable = [
        'sku',
        'ean',
        'stock_qty',
        'in_stock'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'uses_stock' => 'bool',
        'in_stock' => 'bool',
        'stock_qty' => 'int',
    ];

    protected function getAttributeClass(): string
    {
        return ProductAttribute::class;
    }


    protected function getOriginalAttributes(): array
    {
        return [
            'id',
            'sku',
            'ean',
            'stock_qty',
            'uses_stock',
            'in_stock',
            'parent_id',
            'type',
            'created_at',
            'updated_at',
        ];
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumbnail')
            ->fit(Fit::Fill, 200, 200)
            ->quality(100)
            ->nonQueued();

        $this
            ->addMediaConversion('thumbnail_mobile')
            ->fit(Fit::Fill, 300, 300)
            ->quality(100)
            ->nonQueued();

        $this
            ->addMediaConversion('gallery_preview')
            ->fit(Fit::Fill, 100, 100)
            ->quality(100)
            ->nonQueued();

        $this
            ->addMediaConversion('gallery_image')
            ->fit(Fit::Contain, 500, 500)
            ->quality(100)
            ->nonQueued();
    }

    public function getImagesUrls(): array
    {
        $conversions = ['thumbnail', 'gallery_preview', 'gallery_image', 'thumbnail_mobile'];
        $media = $this->getMedia();
        if ($media->isEmpty() && $this->parent) {
            $media = $this->parent->getMedia();
        }
        $images = [];
        foreach ($media as $mediaItem) {
            $image = [];
            foreach ($conversions as $conversion) {
                $image[$conversion] = $mediaItem->getFullUrl($conversion);
            }
            $image['original'] = $mediaItem->getFullUrl();
            $images[] = $image;
        }
        return $images;
    }

    public function getThumbnails($max = 2, $mobile = false)
    {
        $thumbnails = [];
        foreach ($this->getMedia() as $i => $mediaItem) {
            if ($i >= $max) {
                break;
            }
            $thumbnails[] = $mediaItem->getFullUrl($mobile ? 'thumbnail_mobile' : 'thumbnail');
        }
        if (!count($thumbnails) && $this->parent) {
            return $this->parent->getThumbnails();
        }
        return $thumbnails;
    }

    public function getThumbnailUrl(): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl('default', 'thumbnail');
        if (!$mediaUrl) {
           // return $this->parent ? $this->parent->getThumbnailUrl() : null;
        }
        return $mediaUrl;
    }

    public function price()
    {
        return $this->hasOne(ProductPrice::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function configurableAttributes()
    {
        return $this->belongsToMany(Attribute::class, 'configurable_attributes');
    }

    public function reviews() {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews() {
        return $this->reviews()->approved();
    }

    public function scopeSort(Builder $query, $sortField, $sortDir): Builder
    {
        return
            $query->when(in_array($sortField, ['id', 'sku']), function ($query) use ($sortField, $sortDir) {
                $query->orderBy($sortField, $sortDir);
            })->when($sortField && !in_array($sortField, ['id', 'sku', 'price', 'final_price']), function ($query) use ($sortField, $sortDir) {
                $query->orderByAttribute($sortField, $sortDir);
            })->when($sortField === 'price', function ($query) use ($sortDir) {
                $query->orderByBasePrice($sortDir);
            })->when($sortField === 'final_price', function ($query) use ($sortDir) {
                $query->orderByFinalPrice($sortDir);
            });
    }

    public function scopeOrderByBasePrice(Builder $query, $sortDir): Builder
    {
        return $query->leftJoin('product_prices', 'product_prices.product_id', '=', 'products.id')
            ->orderBy('product_prices.price', $sortDir);
    }

    public function scopeOrderByFinalPrice(Builder $query, $sortDir): Builder
    {
        return $query->leftJoin('product_prices', 'product_prices.product_id', '=', 'products.id')
            ->orderBy('product_prices.final_price', $sortDir);
    }

    public function setStockQtyAttribute($value)
    {
        $this->attributes['stock_qty'] = $value;
        $this->attributes['in_stock'] = $value > 0;
    }

    public function getReviewsCountAttribute() {
        return $this->approvedReviews()->count();
    }

    public function getRatingAttribute() {
        return (round((float)$this->approvedReviews()->avg('rating') * 10)) / 10;
    }

    public function shouldApplyPriceRule(ProductPriceRule $rule): bool
    {
        if ($this->hasActiveSpecialPrice()) {
            return false;
        }
        return !$this->price ||
            !$this->price->priceRule ||
            !$this->price->priceRule->isActive() ||
            $this->price->priceRule->priority < $rule->priority;
    }

    public function hasActiveSpecialPrice(): bool
    {
        if (!$this->price) {
            return false;
        }

        $from = $this->price->special_price_from;
        $to = $this->price->special_price_to;

        if (!$from && !$to) {
            return false;
        }

        return Carbon::now()->isBetween($from ?? '-infinity', $to ?? '+infinity');
    }

    public function setPrice($data): static
    {
        $from = ($data['special_price_from'] ?? null) ? Carbon::make($data['special_price_from']) : null;
        $to = ($data['special_price_to'] ?? null) ? Carbon::make($data['special_price_to']) : null;
        $data['final_price'] = $data['price'] ?? 0;

        if (Carbon::now()->isBetween($from ?? '-1 day', $to ?? '+1 day')) {
            $data['final_price'] = $data['special_price'] ?? $data['price'] ?? 0;
            $data['rule_id'] = null;
        }

        $currentPrice = $this->price;
        if (!$currentPrice) {
            $this->price()->create($data);
        } else {
            if ($data['final_price'] < $data['price']) {
                $lowestHistoryPrice = ProductPriceHistoryItem::query()
                    ->where('product_id', $this->id)
                    ->where('valid_to', '>', Carbon::now()->subDays(30))
                    ->min('price');
                if (!$lowestHistoryPrice || $currentPrice->final_price < $lowestHistoryPrice) {
                    $lowestHistoryPrice = $currentPrice->final_price;
                }
                $data['omnibus_price'] = $lowestHistoryPrice;
            } else {
                $data['omnibus_price'] = null;
            }
            $this->price->update($data);
        }
        return $this;
    }

    public function isConfigurable(): bool
    {
        return $this->type === 'configurable';
    }

    public function isInStock()
    {
        return !$this->uses_stock || $this->stock_qty > 0;
    }

    public function getFinalPrice()
    {
        return $this->price?->final_price ?? null;
    }

    public function getUrl()
    {
        return config('app.url') . '/' . $this->urlRewrite?->request_path;
    }

    public function getVariantAttributes()
    {
        $variantAttributes = [];
        if ($this->parent && $this->parent->isConfigurable()) {
            $attributes = $this->parent->configurableAttributes;
            foreach ($attributes as $attribute) {
                $variantAttributes[] = [
                    'name' => $attribute->name,
                    'value' => $this->getAttributeTextValue($attribute)
                ];
            }
        }
        return $variantAttributes;
    }

    public function createUrlRewrite($urlKey)
    {
        $urlRewrite = UrlRewrite::query()
            ->where('entity_id', $this->id)
            ->where('entity_type', self::ENTITY_TYPE)
            ->first();
        if (!$urlRewrite) {
            $urlRewrite = new UrlRewrite();
        }
        $slug = $urlKey;
        $i = 1;
        while (UrlRewrite::query()->where('request_path', $slug)->whereNot('entity_id', $this->id)->exists()) {
            $slug = $urlKey . '-' . $i++;
        }
        $urlRewrite->request_path = $slug;
        $urlRewrite->target_path = '/p/' . $this->id;
        $urlRewrite->entity_type = self::ENTITY_TYPE;
        $urlRewrite->entity_id = $this->id;
        $urlRewrite->store_id = 1;
        $urlRewrite->save();
    }
}
