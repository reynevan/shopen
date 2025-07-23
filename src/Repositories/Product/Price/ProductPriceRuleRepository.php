<?php

namespace Shopen\Repositories\Product\Price;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Shopen\Models\Product\Price\ProductPriceRule;

class ProductPriceRuleRepository
{
    public function getActive(): Collection
    {
        $now = (new Carbon())->format('Y-m-d');
        return ProductPriceRule::query()
            ->where('is_enabled', true)
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->orderBy('priority', 'DESC')
            ->get();
    }
}