<?php

namespace Shopen\Http\Controllers\Frontend\User\Order;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Order\OrderResource;
use Shopen\Models\Order\Order;

class UserOrderShowController
{
    public function show(Order $order): Response
    {
        $order->load(['items', 'items.product', 'promoCode', 'shippingAddress', 'billingAddress']);

        return Inertia::render('Frontend/User/Order/Show', [
            'order' => OrderResource::make($order),
        ]);
    }
}