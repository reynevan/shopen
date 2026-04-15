<?php

namespace Shopen\Models\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Laravel\Scout\Scout;
use Shopen\Jobs\MakeSearchable;
use Shopen\Models\Order\OrderItem;
use Shopen\Repositories\Product\ProductAttributeRepository;
use Shopen\Services\StoreManager;

trait Searchable
{
    use \Elastic\ScoutDriverPlus\Searchable;

    public function shouldBeSearchable(): bool
    {
        return !!$this->is_active && $this->visible_individually;
    }

    public function getScoutKey()
    {
        $store = app(StoreManager::class)->getCurrentStore();

        return $this->id . '_' . $store->id;

    }

    public function queueMakeSearchable($models)
    {
        if ($models->isEmpty()) {
            return;
        }

        if (! config('scout.queue')) {
            return $this->syncMakeSearchable($models);
        }

        dispatch((new MakeSearchable($models))
            ->onQueue($models->first()->syncWithSearchUsingQueue())
            ->onConnection($models->first()->syncWithSearchUsing()));
    }

    public function toSearchableArray()
    {
        $this->clearCustomAttributes();
        $this->refresh();
        $store = app(StoreManager::class)->getCurrentStore();
        return [
            'id' => $this->id,
            'store' => $store->id,
            'name' => $this->getCustomAttribute('name'),
            'description' => $this->getCustomAttribute('description'),
            'visible_individually' => $this->visible_individually,
            'sku' => $this->sku,
            'url' => $this->getUrl(),
            'category_id' => $this->categories->pluck('id')->toArray(),
            'brand_id' => $this->brand_id ?? ($this->parent ? $this->parent->brand_id : null),
            'in_stock' => $this->isInStock(),
            'price' => $this->getSearchPrice(),
            'omnibus_price' => $this->getSearchOmnibusPrice(),
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'popularity' => $this->getPopularity(),
            'thumbnail_url' => $this->getThumbnails(),
            'is_new_to' => $this->is_new_to,
            'is_new' => $this->is_new,
            'searchable_attributes' => $this->getSearchableAttributeValue(),
            'list_attributes' => $this->getListAttributes(),
            ...$this->getIndexableAttributesValues()
        ];
    }

    protected function getPopularity()
    {
        $orderItemsCount =  OrderItem::query()
            ->tap(function (Builder $query) {
                if ($this->isConfigurable()) {
                    $query->whereHas('product', function (Builder $query) {
                        $query->where('parent_id', $this->id);
                    });
                } else {
                    $query->where('product_id', $this->id);
                }
            })
            ->where('created_at', '>', Carbon::now()->subDays(config('shopen.product.popularity_days', 365)))
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

    protected function getSearchOmnibusPrice()
    {
        if ($this->isConfigurable()) {
            return $this->getPriceFrom()?->omnibus_price;
        } else {
            return $this->price?->omnibus_price ?? null;
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