<?php

namespace Shopen\Http\Controllers\Admin\Order\Return;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\OrderItem;
use Throwable;

readonly class ReturnCreateController
{

    public function __construct(
    )
    {}

    public function create(Order $order): Response
    {
        $order->load(['billingAddress', 'items.product', 'invoices']);
        return Inertia::render('Admin/Order/Return/Create', [
            'order' => OrderResource::make($order)
        ]);
    }

    public function store(Order $order)
    {
        DB::beginTransaction();
        try {
            foreach (request('items', []) as $item) {
                $orderItem = OrderItem::query()->find($item['id']);
                if (!$orderItem) {
                    continue;
                }
                $returnedQty = $item['quantity_to_return'] ?? 0;
                $orderItem->returned_quantity = $returnedQty;
                $orderItem->save();
                $order->returned_amount += $orderItem->returned_quantity * ($orderItem->final_price - $orderItem->promo_code_discount_amount);
                if ($item['restock'] ?? false) {
                    $this->restockItemProduct($orderItem, $returnedQty);
                }
            }
            $order->shipping_amount_returned = floatval(request('shipping_amount')) ?? 0;
            $order->save();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('[STORE ORDER RETURN] ' . $e->getMessage());
        }
    }

    protected function restockItemProduct(OrderItem $orderItem, $returnedQty): void
    {
        $product = $orderItem->product;
        if (!$product || !$product->uses_stock) {
            return;
        }
        $product->stock_qty += $returnedQty;
        if ($product->stock_qty > 0 && !$product->in_stock) {
            $product->in_stock = true;
        }
        $product->save();
    }
}