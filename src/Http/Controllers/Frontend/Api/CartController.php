<?php

namespace Shopen\Http\Controllers\Frontend\Api;

use Illuminate\Http\RedirectResponse;
use Shopen\Http\Controller;
use Shopen\Models\Cart\CartItem;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\CartService;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService, private readonly ProductRepository $productRepository)
    {}

    public function addItem(): RedirectResponse
    {
        $product = $this->productRepository->getById(request()->post('id'));
        if (!$product) {
            abort(404);
        }
        $price = $product->price;
        if (is_null($price)) {
            abort(500);
        }
        $this->cartService->addToCart($product->id, request()->post('qty') ?? 1, $price->price, $price->final_price);

        return back();
    }

    public function removeItem(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeFromCart($cartItem->id);

        return back();
    }

    public function updateItem(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->updateItem($cartItem->id, request()->post('qty') ?? 1);

        return back();
    }
}