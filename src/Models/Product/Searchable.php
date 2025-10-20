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
        return !!$this->is_active && $this->visible_individually;
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->getCustomAttribute('name'),
            'description' => $this->getCustomAttribute('description'),
            'visible_individually' => $this->visible_individually,
            'sku' => $this->sku,
            'category_id' => $this->categories->pluck('id')->toArray(),
            'brand_id' => $this->brand_id ?? ($this->parent ? $this->parent->brand_id : null),
            'in_stock' => $this->isInStock(),
            'price' => $this->getSearchPrice(),
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
        $orderItemsCount =  OrderItem::query()
            ->where('product_id', $this->id)
            ->where('created_at', '>', Carbon::now()->subDays(config('shopen.product.popularity_days')))
            ->count();

        return $orderItemsCount + $this->reviews_count;
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

    protected function getSearchPrice()
    {
        if ($this->isConfigurable()) {
            return $this->getPriceFrom()?->final_price;
        } else {
            return $this->getFinalPrice();
        }
    }

    protected function getIndexableAttributesValues()
    {
        $values = [];
        $attributes = app(ProductAttributeRepository::class)->getIndexable();
        foreach ($attributes as $attribute) {
            if ($this->isConfigurable()) {
                $attrValues = [];
                foreach ($this->variants as $variant) {
                    $value = $variant->getCustomAttributeValue($attribute->code);
                    if (is_array($value)) {
                        $attrValues = array_merge($attrValues, $value);
                    } else {
                        $attrValues[] = $value;
                    }
                }
                $values[$attribute->code] = array_values(array_unique($attrValues));
            } else {
                $values[$attribute->code] = $this->getCustomAttributeValue($attribute->code);
            }
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