<?php

namespace Shopen\Services;

use Shopen\Enums\Order\OrderStatus;
use Shopen\Models\Order\Order;
use Shopen\Models\Product\Product;
use Shopen\Models\User;

class ProductReviewService
{
    public function hasUserPurchasedProduct(User $user, Product $product): bool
    {
        return Order::query()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('order_items.product_id', $product->id)
            ->where('orders.status', OrderStatus::DELIVERED->value)
            ->exists();
    }
}