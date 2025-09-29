<?php

namespace Shopen\Http\Controllers\Frontend\User\Order;

use Shopen\Enums\Order\OrderStatus;
use Shopen\Models\Order\Order;
use Shopen\Services\OrderService;

class UserOrderCancelController
{
    public function __construct(protected OrderService $orderService)
    {}

    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Zamówienie nie może być anulowane');
        }

        $this->orderService->updateOrderStatus($order, OrderStatus::CANCELLED);

        return back()->with('success', 'Zamówienie zostało anulowane');
    }
}