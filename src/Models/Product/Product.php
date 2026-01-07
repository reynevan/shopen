<?php

namespace Shopen\Models\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Shopen\Database\Factories\ProductFactory;
use Shopen\Models\Attribute\Attribute;
use Shopen\Models\Brand\Brand;
use Shopen\Models\Category\Category;
use Shopen\Models\Interfaces\HasCustomAttributesInterface;
use Shopen\Models\Product\Attribute\ProductAttribute;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Price\ProductPriceHistoryItem;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Review\ProductReview;
use Shopen\Models\PromoCode\PromoCode;
use Shopen\Models\ShoppingList\ShoppingList;
use Shopen\Models\Traits\HasCustomAttributes;
use Shopen\Models\Traits\HasSeoDetails;
use Shopen\Models\Traits\HasUrl;
use Shopen\Models\UrlRewrite;
use Shopen\Services\CartService;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Throwable;

class Product extends Model implements HasMedia, HasCustomAttributesInterface
{
    use HasFactory, HasCustomAttributes, HasUrl, HasSeoDetails;
    use InteractsWithMedia;
    use Searchable;

    const TYPE_SIMPLE = 'simple';
    const TYPE_CONFIGURABLE = 'configurable';

    const ENTITY_TYPE = 'product';

    const IMAGE_QUALITY = 80;

    protected $fillable = [
        'sku',
        'ean',
        'stock_qty',
        'in_stock',
        'uses_stock',
        'type',
        'brand_id',
        'visible_individually',
        'parent_id',
        'is_virtual',
        'is_voucher',
        'is_new',
        'is_new_to',
        'tax_class_id',
        'ceneo_category_id',
        'unit'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_voucher' => 'bool',
        'is_new' => 'bool',
        'is_virtual' => 'bool',
        'uses_stock' => 'bool',
        'in_stock' => 'bool',
        'visible_individually' => 'bool',
        'stock_qty' => 'int',
        'is_new_to' => 'date:Y-m-d'
    ];

    protected function getAttributeClass(): string
    {
        return ProductAttribute::class;
    }

    protected function getThumbnailSizes(): array
    {
        return [100, 150, 180, 250];
    }

    protected function getGalleryPreviewSize(): int
    {
        return 109;
    }

    protected function getGalleryImageSizes(): array
    {
        return [320, 480, 572, 620, 768, 960, 1144];
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    public function getEntityType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('meta')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach ($this->getThumbnailSizes() as $size) {
            $this
                ->addMediaConversion('thumbnail-' . $size)
                ->fit(Fit::Crop, $size, $size)
                ->quality(self::IMAGE_QUALITY)
                ->format('webp')
                ->nonQueued();

            $this
                ->addMediaConversion('thumbnail-' . ($size * 2))
                ->fit(Fit::Crop, ($size * 2), ($size * 2))
                ->quality(self::IMAGE_QUALITY)
                ->format('webp')
                ->nonQueued();
        }
        foreach ($this->getGalleryImageSizes() as $size) {
            $this
                ->addMediaConversion('gallery-image-' . $size)
                ->fit(Fit::Contain, $size, $size)
                ->quality(self::IMAGE_QUALITY)
                ->format('webp')
                ->nonQueued();
        }

        $this
            ->addMediaConversion('thumbnail-mail')
            ->fit(Fit::Crop, 65, 65)
            ->quality(self::IMAGE_QUALITY)
            ->format('jpg')
            ->nonQueued();

        $this
            ->addMediaConversion('gallery-preview-' . $this->getGalleryPreviewSize())
            ->fit(Fit::Crop, $this->getGalleryPreviewSize(), $this->getGalleryPreviewSize())
            ->quality(self::IMAGE_QUALITY)
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('gallery-preview-' . ($this->getGalleryPreviewSize() * 2))
            ->fit(Fit::Crop, $this->getGalleryPreviewSize() * 2, $this->getGalleryPreviewSize() * 2)
            ->quality(self::IMAGE_QUALITY)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('json-ld')
            ->fit(Fit::Crop, 1200, 1200)
            ->quality(100)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 600, 600)
            ->quality(100)
            ->format('jpg')
            ->nonQueued();
    }

    public function getImagesUrls(): array
    {
        $media = $this->getMedia('default', ['gallery' => true]);
        if ($media->isEmpty() && $this->parent) {
            $media = $this->parent->getMedia();
        }
        $images = [];
        foreach ($media as $mediaItem) {
            $galleryImages = [];
            foreach ($this->getGalleryImageSizes() as $size) {
                $galleryImages[$size . 'w'] = $mediaItem->getFullUrl('gallery-image-' . $size);
            }
            $image = [
                'gallery_preview' => [
                    $this->getGalleryPreviewSize() . 'w' => $mediaItem->getFullUrl('gallery-preview-' . $this->getGalleryPreviewSize()),
                    ($this->getGalleryPreviewSize() * 2) . 'w' => $mediaItem->getFullUrl('gallery-preview-' . ($this->getGalleryPreviewSize() * 2)),
                ],
                'gallery_image' => $galleryImages
            ];
            $image['original'] = $mediaItem->getFullUrl();
            $images[] = $image;
        }
        return $images;
    }

    public function getJsonLdImageUrl()
    {
        $metaImage = $this->getFirstMedia('default', ['meta' => true]);
        if ($metaImage) {
            return $metaImage->getFullUrl('json-ld');
        }
        $image = $this->getFirstMedia();
        if ($image) {
            return $image->getFullUrl('json-ld');
        }
        return null;
    }

    public function getOgImageUrl()
    {
        $metaImage = $this->getFirstMedia('default', ['meta' => true]);
        if ($metaImage) {
            return $metaImage->getFullUrl('og');
        }
        $image = $this->getFirstMedia();
        if ($image) {
            return $image->getFullUrl('og');
        }
        return null;
    }

    protected function getThumbnailsResponsiveSizes(): array
    {
        $sizes = [];
        foreach ($this->getThumbnailSizes() as $size) {
            $sizes[] = $size;
            $sizes[] = $size * 2;
        }
        sort($sizes);
        return $sizes;
    }

    public function getThumbnails($max = 2)
    {
        $thumbnails = [];
        $mediaItems = $this->getMedia('default', ['thumbnail' => true])->slice(0, $max);

        foreach ($mediaItems as $mediaItem) {
            $media = [];
            foreach ($this->getThumbnailsResponsiveSizes() as $size) {
                $media[$size . 'w'] = $mediaItem->getFullUrl('thumbnail-' . $size);
            }
            $thumbnails[] = $media;
        }
        if (!count($thumbnails) && $this->parent) {
            return $this->parent->getThumbnails($max);
        }
        return $thumbnails;
    }

    public function getThumbnail()
    {
        return $this->getThumbnails(1)[0] ?? null;
    }

    public function getThumbnailUrl($size = 250)
    {
        return $this->getFirstMediaUrl('default', 'thumbnail-' . $size);
    }

    public function getMailThumbnailUrl(): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl('default', 'thumbnail-mail');
        if (!$mediaUrl) {
            return $this->parent ? $this->parent->getThumbnailUrl() : null;
        }
        return $mediaUrl;
    }

    public function price(): HasOne
    {
        return $this->hasOne(ProductPrice::class);
    }

    public function categories(): BelongsToMany
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

    public function configurableAttributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'configurable_attributes');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function crossSells(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_cross_sells',
            'product_id',
            'cross_sell_product_id'
        );
    }

    public function upSells(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_up_sells',
            'product_id',
            'up_sell_product_id'
        );
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'related_products',
            'product_id',
            'related_product_id'
        );
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }

    public function shoppingLists(): BelongsToMany
    {
        return $this->belongsToMany(ShoppingList::class, 'shopping_list_product');
    }

    public function scopeSort(Builder $query, $sortField, $sortDir): Builder
    {
        return
            $query->when(in_array($sortField, ['id', 'sku', 'type']), function ($query) use ($sortField, $sortDir) {
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->filterByAttribute('is_active', true);
    }

    public function setStockQtyAttribute($value)
    {
        $this->attributes['stock_qty'] = $value;
        $this->attributes['in_stock'] = $value > 0;
    }

    public function getReviewsCountAttribute()
    {
        if (isset($this->attributes['reviews_count'])) {
            return $this->attributes['reviews_count'];
        }
        return $this->getApprovedReviews()->count();
    }

    public function getRatingAttribute()
    {
        if (isset($this->attributes['rating'])) {
            return $this->attributes['rating'];
        }
        return (round((float)$this->getApprovedReviews()->avg('rating') * 10)) / 10;
    }

    public function getApprovedReviews()
    {
        if ($this->parent_id) {
            $productIds = $this->parent->variants()->active()->pluck('id')->toArray();
            $productIds[] = $this->parent_id;
        }
        if ($this->isConfigurable()) {
            $productIds = $this->variants()->active()->pluck('id')->toArray();
        }
        $productIds[] = $this->id;
        return ProductReview::query()
            ->approved()
            ->whereIn('product_id', $productIds)
            ->get();
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

    public function getPriceFrom()
    {
        $variantIds = $this->variants()->active()->pluck('id')->toArray();
        return ProductPrice::query()->whereIn('product_id', $variantIds)->orderBy('final_price')->first();
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

    public function isNew()
    {
        if (!$this->is_new) {
            return false;
        }
        if (!$this->is_new_to) {
            return true;
        }
        return $this->is_new_to->isFuture();
    }

    public function isInStock()
    {
        return !$this->uses_stock || $this->stock_qty > 0;
    }

    public function getFinalPrice()
    {
        return $this->price?->final_price ?? null;
    }

    public function getMaxCartQty()
    {
        if (!$this->uses_stock) {
            return config('shopen.cart.max_item_qty', 10);
        }
        return min(config('shopen.cart.max_item_qty', 10), $this->stock_qty);
    }

    public function getMaxAddToCartQty()
    {
        if (!$this->uses_stock) {
            $qty = config('shopen.cart.max_item_qty', 10);
        } else {
            $qty = min(config('shopen.cart.max_item_qty', 10), $this->stock_qty);
        }
        $cartQty = app(CartService::class)->getProductQty($this->id);
        return max($qty - $cartQty, 0);
    }

    public function getUrl()
    {
        return config('app.url') . '/' . $this->urlRewrite?->request_path;
    }

    public function getCanonicalUrl()
    {
        if (!$this->parent_id) {
            return $this->getUrl();
        }
        $parent = $this->parent;
        if ($parent && $parent->visible_individually) {
            return $parent->getUrl();
        }
        return$this->getUrl();
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

    public function getTaxRate()
    {
        if ($this->taxClass) {
            return $this->taxClass->rate;
        }


        return config('shopen.product.default_tax_rate');
    }

    public function setIsNewToAttribute($value): void
    {
        try {
            if (is_string($value)) {
                $value = Carbon::parse($value);
            }
        } catch (Throwable $e) {}
        $this->attributes['is_new_to'] = $value;
    }

    public function createUrlRewrite($urlKey = null)
    {
        $urlRewrite = UrlRewrite::query()
            ->where('entity_id', $this->id)
            ->where('entity_type', self::ENTITY_TYPE)
            ->first();
        if (!$urlRewrite) {
            $urlRewrite = new UrlRewrite();
        }
        $slug = $urlKey ?? $this->sku;
        $slug = strtolower($slug);
        $slug = preg_replace('~[^a-z0-9\-]+~', '-', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
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

    protected function beforeSave(): true
    {
        if (!$this->is_new) {
            $this->is_new_to = null;
        }
        return true;
    }
}
