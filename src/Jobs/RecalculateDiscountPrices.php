<?php

namespace Shopen\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Shopen\Events\Product\Price\ProductPriceRuleUpdated;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Product;
use Shopen\Repositories\Attribute\AttributeRepository;
use Shopen\Repositories\Product\ProductRepository;

class RecalculateDiscountPrices implements ShouldQueue
{
    use Queueable;

    const CHUNK_SIZE = 50;

    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly ProductRepository   $productRepository,
    )
    {}

    public function handle(ProductPriceRuleUpdated $event): void
    {
        $priceRule = $event->productPriceRule;

        if ($priceRule->isActive()) {
            $this->applyPriceRule($priceRule);
        } else {
            $this->cancelPriceRule($priceRule);
        }
    }

    protected function getMatchingProducts(ProductPriceRule $priceRule): Collection
    {
        return Product::query()
            ->where(function (Builder $query) use ($priceRule) {
                foreach ($priceRule->conditions['attributes'] as $attrCondition) {
                    $attribute = $this->attributeRepository->getById($attrCondition['attribute_id']);
                    $query->filterByAttribute($attribute, $attrCondition['value'], 'or');
                }
            })
            ->whereHas('categories', function ($query) use ($priceRule) {
                $query->whereIn('categories.id', $priceRule->conditions['categories']);
            })
            ->get();
    }

    protected function applyPriceRule(ProductPriceRule $priceRule): void
    {
        $products = $this->productRepository->getMatchingForPriceRule($priceRule);
        foreach ($products as $product) {
            if (!$product->shouldApplyPriceRule($priceRule)) {
                continue;
            }
            $productPrice = $product->price;
            if (!$productPrice) {
                $productPrice = new ProductPrice();
            }
            $price = $product->price->price;
            $productPrice->product_id = $product->id;
            $productPrice->rule_id = $priceRule->id;
            $productPrice->price = $price;
            $productPrice->final_price = $priceRule->calculateFinalPrice($price);
            $productPrice->save();
        }
    }

    protected function cancelPriceRule(ProductPriceRule $priceRule): void
    {
        $productsChunks = Product::query()
            ->whereHas('price', function (Builder $query) use ($priceRule) {
                $query->where('rule_id', $priceRule->id);
            })
            ->get()
            ->pluck('id')
            ->chunk(self::CHUNK_SIZE)
            ->toArray();

        foreach ($productsChunks as $productIds) {
            BatchRecalculateProductsPrices::dispatch($productIds);
        }

    }
}
