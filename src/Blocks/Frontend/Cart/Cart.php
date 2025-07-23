<?php

namespace Shopen\Blocks\Frontend\Cart;

use Shopen\Blocks\Block;
use Shopen\Services\CartService;

class Cart extends Block
{
    public function __construct(private readonly CartService $cartService)
    {}

    public function isCartEmpty(): bool
    {
        return $this->cartService->getCart()->isEmpty();
    }
}