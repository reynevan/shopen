<?php

namespace Shopen\Repositories\Order;

use Illuminate\Database\Eloquent\Builder;
use Shopen\Models\Order\Order;

class OrderRepository
{
    public function all($sortField, $sortDir)
    {
        return Order::query()
            ->with(['shippingAddress', 'billingAddress'])
            ->sort($sortField, $sortDir);
    }

    public function getPaginated($sortField, $sortDir, $searchQuery = null)
    {
        $products = Order::query()
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query->whereLike('sku', '%' . $searchQuery . '%');
            })
            ->with(['shippingAddress', 'billingAddress'])
            ->sort($sortField, $sortDir)
            ->paginate()
            ->withQueryString();

        return $products;
    }
}