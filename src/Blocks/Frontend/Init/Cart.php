<?php

namespace Shopen\Blocks\Frontend\Init;

use Shopen\Blocks\Block;
use Shopen\Http\Resources\Cart\CartResource;
use Shopen\Services\CartService;

class Cart extends Block
{
    public function __construct(private readonly CartService $cartService)
    {}

    public function getCart()
    {
        return CartResource::make($this->cartService->getCart());
    }
}