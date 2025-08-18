<?php

declare(strict_types=1);

namespace Shopen\Services\Admin;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Number;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Models\Order\Order;

class DashboardService
{
    public function getDashboardData(array $options = []): array
    {
        return [
            'total_sale_amount' => $this->getTotalSaleAmount(),
            'orders_amount' => $this->getOrdersAmount(),
            'latest_orders' => $this->getLatestOrders()
        ];
    }

    private function getTotalSaleAmount()
    {
        $totalAmount = Order::query()
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
            ->sum('total_amount');

        return Number::currency((float)$totalAmount);
    }

    private function getLatestOrders(): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->with('shippingAddress')
            ->limit(5)
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    private function getOrdersAmount(): int
    {
        return Order::query()
            ->whereNotIn('status', [OrderStatus::CANCELLED->value, OrderStatus::REFUNDED->value])
            ->count();
    }
}