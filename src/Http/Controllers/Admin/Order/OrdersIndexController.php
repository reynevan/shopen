<?php

namespace Shopen\Http\Controllers\Admin\Order;

use Inertia\Inertia;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Repositories\Order\OrderRepository;

readonly class OrdersIndexController
{
    public function __construct(
        protected OrderRepository $orderRepository,
    )
    {

    }

    public function index()
    {
        $products = $this->orderRepository->getPaginated(request('sort', 'id'), request('dir', 'asc'), request('q'));

        return Inertia::render('Admin/Order/Index', [
            'orders' => OrderResource::collection($products),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}