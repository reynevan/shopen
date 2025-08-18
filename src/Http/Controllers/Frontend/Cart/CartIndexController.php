<?php

namespace Shopen\Http\Controllers\Frontend\Cart;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Cart\CartResource;
use Shopen\Services\CartService;
use Shopen\Services\RelatedProductsService;

readonly class CartIndexController
{
    public function __construct(
        protected RelatedProductsService $relatedProductsService,
        protected CartService $cartService,
    )
    {}

    public function index(): Response
    {
        $crossSellProducts = $this->relatedProductsService->getCrossSellProducts();
        return Inertia::render('Frontend/Cart/Index', [
            'crossSellProducts' => $crossSellProducts,
            'cart' => CartResource::make($this->cartService->getCart())
        ]);
    }


}