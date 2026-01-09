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

        if ($invoice->correctionInvoices()->count()) {
            $invoice = $invoice->correctionInvoices()->latest()->first();
        }

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
            $correctionInvoice = $this->invoiceService->createCorrection($invoice, $request->items, $data);
            $correctionInvoice->billingAddress()->create(Arr::except($request->billing_address, ['id', 'type']));
            $this->invoiceService->savePdf($correctionInvoice);
            if ($request->send_email) {
                Mail::to($correctionInvoice->order->getCustomerEmail())->queue(new InvoiceCreated($correctionInvoice));
            }
            DB::commit();
        } catch (\Throwable $e) {
            Log::error('[Creating invoice] ' . $e->getMessage());
            DB::rollBack();
        }

        return redirect(route('admin.orders.invoices.show', $correctionInvoice));
    }
}