<?php

namespace Shopen\Events\Product\Price;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Shopen\Models\Product\Price\ProductPriceRule;

class ProductPriceRuleUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProductPriceRule $productPriceRule)
    {}
}
