<?php

namespace Shopen\Models\Product;

use Illuminate\Support\Carbon;
use Shopen\Models\Order\OrderItem;
use Shopen\Repositories\Product\ProductAttributeRepository;

trait Searchable
{
    use \Elastic\ScoutDriverPlus\Searchable;

    public function shouldBeSearchable(): bool
    {
        return $this->is_active && $this->type === 'simple';
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->getCustomAttribute('name'),
            'description' => $this->getCustomAttribute('description'),
            'sku' => $this->sku,
            'category_id' => $this->categories->pluck('id')->toArray(),
            'brand_id' => $this->brand_id,
            'in_stock' => $this->isInStock(),
            'price' => $this->getFinalPrice(),
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'popularity' => $this->getPopularity(),
            'thumbnail_url' => $this->getThumbnails(),
            'searchable_attributes' => $this->getSearchableAttributeValue(),
            'list_attributes' => $this->getListAttributes(),
            ...$this->getIndexableAttributesValues()
        ];
    }

    protected function getPopularity()
    {
        return OrderItem::query()
            ->where('product_id', $this->id)
            ->where('created_at', '>', Carbon::now()->subDays(config('shopen.product.popularity_days')))
            ->count();
    }

    protected function getListAttributes()
    {
        $values = [];
        $attributes = app(ProductAttributeRepository::class)->getUsedInList();
        foreach ($attributes as $attribute) {
            $values[$attribute->code] = $this->getAttributeTextValue($attribute->code);
        }
        return $values;

    }

    protected function getIndexableAttributesValues()
    {
        $values = [];
        $attributes = app(ProductAttributeRepository::class)->getIndexable();
        foreach ($attributes as $attribute) {
            $values[$attribute->code] = $this->getCustomAttribute($attribute->code);
        }
        $values['is_active'] = $this->getCustomAttribute('is_active');
        return $values;
    }

    protected function getSearchableAttributeValue()
    {
        $attributes = app(ProductAttributeRepository::class)->getSearchable();
        $product = $this;
        return $attributes->reduce(function ($value, $attribute) use ($product) {
            $textValue = $product->getAttributeTextValue($attribute);
            if (is_array($textValue)) {
                $textValue = implode(',', $textValue);
            }
            return $value . ' ' . $textValue;
        });
    }
}