<?php

namespace Shopen\Http\Controllers\Admin\Order\Invoice;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Core\Payment\PaymentMethodManager;
use Shopen\Http\Requests\Admin\Order\Invoice\CreateInvoiceRequest;
use Shopen\Http\Resources\Admin\Order\OrderResource;
use Shopen\Mail\Order\InvoiceCreated;
use Shopen\Models\Order\Order;
use Shopen\Models\Order\OrderItem;
use Shopen\Services\InvoiceService;

readonly class InvoiceCreateController
{

    public function __construct(
        private InvoiceService $invoiceService,
        private PaymentMethodManager $paymentMethodManager,
    )
    {}

    public function create(Order $order): Response
    {
        $order->load([
            'shippingAddress',
            'billingAddress',
            'payments',
            'items.product',
            'items.product.promoCode',
            'items.promoCodeCoupons',
            'promoCodeCoupon.promoCode'
        ]);

        if ($order->shipping_amount > 0) {
            $taxRate = $order->items->max('tax_rate') ?? 0;
            $shippingItem = new OrderItem();
            $shippingItem->name = config('shopen.invoice.shipping.name');
            $shippingItem->sku = config('shopen.invoice.shipping.sku');
            $shippingItem->quantity = 1;
            $shippingItem->price = $order->shipping_amount;
            $shippingItem->final_price = $order->shipping_amount;
            $shippingItem->total = $order->shipping_amount;
            $shippingItem->tax_rate = $taxRate;
            $shippingItem->tax_amount = $order->shipping_amount - ($order->shipping_amount / (1 + $taxRate / 100));
            $shippingItem->unit = config('shopen.invoice.shipping.unit');
            $order->items->push($shippingItem);
        }
        return Inertia::render('Admin/Order/Invoice/Create', [
            'order' => OrderResource::make($order),
            'number' => $this->invoiceService->getNextNumber(config('shopen.invoice.number.include_month'), config('shopen.invoice.number.include_day'))
        ]);
    }

    public function store(CreateInvoiceRequest $request, Order $order)
    {
        DB::beginTransaction();
        try {
            $paymentMethod = $this->paymentMethodManager->get($order->payment_method);
            $dueDate = new Carbon();
            if ($paymentMethod) {
                $dueDate->addDays($paymentMethod->getPaymentDueDays());
            }
            $paidAmount = $order->payments()->completed()->sum('amount');
            $paidAmount = min($paidAmount, $order->total_amount);
            $total = round(collect($request->items)->sum('total'), 2);
            $totalNet = round(collect($request->items)->sum('total_net'), 2);
            $tax = round(collect($request->items)->sum('tax_amount'), 2);
            $invoice = $order->invoices()->create([
                'invoice_number' => request('number'),
                'shipping_method' => $order->shipping_method,
                'shipping_amount' => $order->shipping_amount,
                'payment_method' => $order->payment_method,
                'payment_amount' => $order->payment_amount,
                'total_amount' => $total,
                'total_net_amount' => $totalNet,
                'tax_amount' => $tax,
                'payment_due_date' => $dueDate,
                'paid_amount' => $paidAmount,
                'left_to_pay_amount' => $total - $paidAmount
            ]);
            foreach ($request->items as $itemData) {
                $item = $invoice->items()->make();
                $item->sku = $itemData['sku'] ?? '';
                $item->name = $itemData['name'];
                $item->unit = $itemData['unit'];
                $item->tax_rate = $itemData['tax_rate'];
                $item->tax_amount = $itemData['tax_amount'];
                $item->quantity = $itemData['quantity'];
                $item->price = $itemData['price'];
                $item->price_net = $itemData['price_net'];
                $item->total = $itemData['total'];
                $item->total_net = $itemData['total_net'];
                $item->discount_amount = $itemData['discount_amount'] ?? 0;
                $item->discount_amount_net = $itemData['discount_amount_net'] ?? 0;
                $item->save();
            }
            $invoice->billingAddress()->create(Arr::except($request->billing_address, ['id', 'type']));
            $this->invoiceService->savePdf($invoice);
            if ($request->send_email) {
                Mail::to($invoice->order->getCustomerEmail())->queue(new InvoiceCreated($invoice));
            }
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('[Creating invoice] ' . $e->getMessage());
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }

        return redirect(route('admin.orders.invoices.show', $invoice));
    }
}