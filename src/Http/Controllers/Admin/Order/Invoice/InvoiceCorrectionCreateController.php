<?php

namespace Shopen\Http\Controllers\Admin\Order\Invoice;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Admin\Order\Invoice\CreateInvoiceCorrectionRequest;
use Shopen\Http\Resources\Admin\Order\Invoice\InvoiceResource;
use Shopen\Mail\Order\InvoiceCreated;
use Shopen\Models\Order\Invoice\Invoice;
use Shopen\Services\InvoiceService;

readonly class InvoiceCorrectionCreateController
{

    public function __construct(
        private InvoiceService $invoiceService,
    )
    {}

    public function create(Invoice $invoice): Response
    {
        $invoice->load([
            'billingAddress',
            'items.product'
        ]);
        return Inertia::render('Admin/Order/Invoice/CreateCorrection', [
            'invoice' => InvoiceResource::make($invoice),
            'number' => $this->invoiceService->getNextCorrectionNumber(config('shopen.invoice.number.include_month'), config('shopen.invoice.number.include_day'))
        ]);
    }

    public function store(CreateInvoiceCorrectionRequest $request, Invoice $invoice)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $total = round(collect($request->items)->sum('total'), 2);
            $totalNet = round(collect($request->items)->sum('total_net'), 2);
            $tax = round(collect($request->items)->sum('tax_amount'), 2);
            $invoice = Invoice::create([
                'invoice_number' => $data['number'],
                'total_amount' => $total,
                'total_net_amount' => $totalNet,
                'tax_amount' => $tax,
                'correction_reason' => $data['correction_reason'],
                'correction_payment_method' => $data['payment_method'],
                'payment_due_date' => $data['payment_due_date'],
                'is_correction' => true,
                'order_id' => $invoice->order_id,
                'base_invoice_id' => $invoice->id,
                'left_to_pay_amount' => round($total - floatval($invoice->total_amount), 2),
            ]);
            foreach ($request->items as $itemData) {
                $item = $invoice->items()->make();
                $item->sku = $itemData['sku'] ?? '';
                $item->base_invoice_item_id = $itemData['id'] ?? '';
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
            dd($e);
            Log::error('[Creating invoice] ' . $e->getMessage());
            DB::rollBack();
        }

        return redirect(route('admin.orders.invoices.show', $invoice));
    }
}