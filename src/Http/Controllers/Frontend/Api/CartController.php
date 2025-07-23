<?php

namespace Shopen\Http\Controllers\Frontend\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Shopen\Http\Controller;
use Shopen\Http\Resources\Cart\CartItemResource;
use Shopen\Http\Resources\Cart\CartResource;
use Shopen\Models\Cart\CartItem;
use Shopen\Repositories\Product\ProductRepository;
use Shopen\Services\CartService;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService, private readonly ProductRepository $productRepository)
    {}

    public function addItem(): void
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
    }

    public function show()
    {
        return CartResource::make($this->cartService->getCart());
    }

    public function items(): AnonymousResourceCollection
    {
        return CartItemResource::collection($this->cartService->getCart()->items);
    }

    public function removeItem(CartItem $cartItem): Response
    {
        $this->cartService->removeFromCart($cartItem->id);

        return response()->noContent(200);
    }

    public function updateItem(CartItem $cartItem): Response
    {
        $this->cartService->updateItem($cartItem->id, request()->post('qty') ?? 1);

        return response()->noContent(200);
    }
}