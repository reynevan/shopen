<?php

namespace Shopen\Blocks\Frontend\Product\Show;

use Shopen\Blocks\Block;
use Shopen\Core\Context;
use Shopen\Http\Resources\Product\ProductPriceResource;

class Price extends Block
{
    public function __construct(private readonly Context $context)
    {}

    public function getPrice(): ?ProductPriceResource
    {
        $product = $this->context->getCurrentProduct();
        return $product->price ? ProductPriceResource::make($product->price) : null;
    }
}