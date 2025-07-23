<?php

namespace Shopen\Http\Controllers\Admin\Order;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Http\Requests\Admin\Order\ShipRequest;
use Shopen\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Models\Order\Order;
use Shopen\Repositories\Order\OrderRepository;

readonly class OrderShowController
{
    public function __construct(
        protected OrderRepository $orderRepository,
    )
    {}

    public function show(Order $order): Response
    {
        $order->load(['shippingAddress', 'billingAddress', 'items.product', 'statusHistoryItems', 'promoCode']);

        return Inertia::render('Admin/Order/Show', [
            'order' => OrderResource::make($order),
            'orderStatusOptions' => OrderStatus::options()
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $order->status = $data['status'];
        $order->save();
        $order->statusHistoryItems()->create($data);

        return back();
    }

    public function updateShipping(ShipRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->shipping_tracking_code = $data['shipping_tracking_code'] ?? null;
        $order->status = OrderStatus::SHIPPED;
        $order->shipped_at = Carbon::now();
        $order->save();

        return back();
    }
}