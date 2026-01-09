<?php

namespace Shopen\Http\Controllers\Admin\Order\Return;


use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Enums\Payment\PaymentStatus;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\OrderItem;
use Shopen\Services\InvoiceService;
use Throwable;

readonly class ReturnCreateController
{

    public function __construct(
        private PaymentMethodManager $paymentMethodManager,
        private InvoiceService $invoiceService,
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
        foreach (request('items', []) as $item) {
            $orderItem = OrderItem::query()->find($item['id']);
            if (!$orderItem) {
                continue;
            }
            $returnedQty = $item['quantity_to_return'] ?? 0;
            if ($returnedQty > $orderItem->quantity - $orderItem->returned_quantity) {
                return back()->with('error', 'Nie można zwrócic więcej sztuk niż zostało kupionych');
            }
        }
        DB::beginTransaction();
        try {
            $returnedAmount = 0;
            foreach (request('items', []) as $item) {
                $orderItem = OrderItem::query()->find($item['id']);
                if (!$orderItem) {
                    continue;
                }
                $returnedQty = $item['quantity_to_return'] ?? 0;
                $orderItem->returned_quantity = $returnedQty;
                $orderItem->save();
                $returnedAmount += $orderItem->returned_quantity * ($orderItem->final_price - $orderItem->promo_code_discount_amount);
                if ($item['restock'] ?? false) {
                    $this->restockItemProduct($orderItem, $returnedQty);
                }
            }
            $order->returned_amount += $returnedAmount;
            $order->shipping_amount_returned = floatval(request('shipping_amount')) ?? 0;
            $order->save();
            $paymentMethod = $this->paymentMethodManager->get($order->payment_method);
            $payment = $order->payments()->where('status', PaymentStatus::COMPLETED)->first();
            if ($paymentMethod) {
                $paymentMethod->initializeReturnPayment($order, $payment, $returnedAmount);
            }
            DB::commit();
            return redirect(route('admin.orders.show', $order))->with('success', 'Zwrot został utworzony');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('[STORE ORDER RETURN] ' . $e->getMessage());
            return back()->with('error', 'Wystąpił błąd.');
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