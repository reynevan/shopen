<?php

namespace Shopen\Observers;

use Illuminate\Support\Carbon;
use Shopen\Models\Product\Price\ProductPrice;
use Shopen\Models\Product\Price\ProductPriceHistoryItem;

class ProductPriceObserver
{
    public function saved(ProductPrice $productPrice): void
    {
        $lastPrice = ProductPriceHistoryItem::query()->where('product_id', $productPrice->product_id)->latest()->first();
        if ($lastPrice && $lastPrice->price === $productPrice->final_price) {
            return;
        }
        if ($lastPrice) {
            $lastPrice->valid_to = Carbon::now();
            $lastPrice->save();
        }
        ProductPriceHistoryItem::create(
            [
                'product_id' => $productPrice->product_id,
                'valid_from' => Carbon::now(),
                'price' => $productPrice->final_price
            ]
        );
    }
}