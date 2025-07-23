<?php

namespace Shopen\Http\Controllers\Frontend\User\Order;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Order\OrderResource;

class UserOrdersIndexController
{
    public function index(): Response
    {
        $orders = Auth::user()
            ->orders()
            ->with(['items', 'items.product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Frontend/User/Order/Index', [
            'orders' => OrderResource::collection($orders),
        ]);
    }
}