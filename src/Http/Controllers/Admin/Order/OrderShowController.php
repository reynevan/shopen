<?php

namespace Shopen\Http\Controllers\Admin\Order;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Http\Requests\Admin\Order\ShipRequest;
use Shopen\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Mail\Order\OrderPlaced;
use Shopen\Mail\Order\OrderProcessing;
use Shopen\Mail\Order\OrderRefunded;
use Shopen\Mail\Order\OrderShipped;
use Shopen\Models\Order\Order;
use Shopen\Repositories\Order\OrderRepository;
use Shopen\Mail\Order\OrderCancelled;
use Shopen\Mail\Order\OrderDelivered;

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

        if (!$data['email_notification']) {
            return back();
        }

        $contactEmail = $order->getCustomerEmail();
        if (!$contactEmail) {
            return back();
        }
        switch ($order->status) {
            case OrderStatus::NEW:
                Mail::to($contactEmail)->queue(new OrderPlaced($order, $data['comment']));
                break;
            case OrderStatus::PROCESSING:
                Mail::to($contactEmail)->queue(new OrderProcessing($order, $data['comment']));
                break;
            case OrderStatus::SHIPPED:
                Mail::to($contactEmail)->queue(new OrderShipped($order, $data['comment']));
                break;
            case OrderStatus::DELIVERED:
                Mail::to($contactEmail)->queue(new OrderDelivered($order, $data['comment']));
                break;
            case OrderStatus::CANCELLED:
                Mail::to($contactEmail)->queue(new OrderCancelled($order, $data['comment']));
                break;
            case OrderStatus::REFUNDED:
                Mail::to($contactEmail)->queue(new OrderRefunded($order, $data['comment']));
                break;
        }

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