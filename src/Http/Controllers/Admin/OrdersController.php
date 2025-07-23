<?php

namespace Shopen\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Shopen\Core\Context;
use Shopen\Enums\Order\OrderStatus;
use Shopen\Http\Controller;
use Shopen\Http\Requests\Admin\Order\ShipRequest;
use Shopen\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use Shopen\Models\Order\Order;

class OrdersController extends Controller
{
    public function __construct(private Context $context)
    {

    }

    public function index(): View
    {
        return $this->view('admin.order.index');
    }

    public function show(Order $order): View
    {
        $this->context->setCurrentOrder($order);

        return $this->view('admin.order.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->statusHistoryItems()->create($data);

        return redirect(route('admin.orders.show', $order));
    }

    public function ship(ShipRequest $request, Order $order)
    {
        $data = $request->validated();
        $order->shipping_tracking_code = $data['shipping_tracking_code'] ?? null;
        $order->status = OrderStatus::SHIPPED;
        $order->shipped_at = Carbon::now();
        $order->save();

        return redirect(route('admin.orders.show', $order));
    }
}