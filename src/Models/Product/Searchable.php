<?php

namespace Shopen\Models\Product;

use Illuminate\Support\Carbon;
use Shopen\Models\Order\OrderItem;
use Shopen\Repositories\Product\ProductAttributeRepository;

trait Searchable
{
    use \Elastic\ScoutDriverPlus\Searchable;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'category_id' => $this->categories->pluck('id')->toArray(),
            'in_stock' => $this->isInStock(),
            'price' => $this->getFinalPrice(),
            'popularity' => $this->getPopularity(),
            'thumbnail_url' => $this->getThumbnails(),
            'mobile_thumbnail_url' => $this->getThumbnails(max: config('shopen.product.thumbnail.max_images_count'), mobile: true),
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
            $values[$attribute->code] = $this->getAttribute($attribute->code);
        }
        return $values;
    }

    protected function getSearchableAttributeValue()
    {
        $attributes = app(ProductAttributeRepository::class)->getSearchable();
        $product = $this;
        return $attributes->reduce(function ($value, $attribute) use ($product) {
            return $value . ' ' . $product->getAttributeTextValue($attribute);
        });
    }
}