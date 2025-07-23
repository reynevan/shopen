<?php

namespace Shopen\Core\Support\Product\Price;

use Shopen\Models\Product\Price\ProductPriceRule;
use Shopen\Models\Product\Product;

class PriceProcessor
{
    public function processRule(Product $product, ProductPriceRule $rule)
    {
        if ($product->hasActiveSpecialPrice()) {
            $productPrice = $product->price;
            $productPrice->product_id = $product->id;
            $productPrice->rule_id = null;
            $productPrice->final_price = $productPrice->special_price;
            $productPrice->save();
            return;
        }
        if (!$product->shouldApplyPriceRule($rule)) {
            return;
        }
        $productPrice = $product->price;
        if (!$productPrice) {
            return;
        }
        $price = $product->price->price;
        $productPrice->product_id = $product->id;
        $productPrice->rule_id = $rule->id;
        $productPrice->final_price = $rule->calculateFinalPrice($price);
        $productPrice->save();
    }
}