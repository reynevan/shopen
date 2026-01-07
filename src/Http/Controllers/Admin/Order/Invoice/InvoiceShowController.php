<?php

namespace Shopen\Http\Controllers\Admin\Order\Invoice;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\Order\Invoice\InvoiceResource;
use Shopen\Models\Order\Invoice\Invoice;

readonly class InvoiceShowController
{


    public function show(Invoice $invoice): Response
    {
        $invoice->load([
            'order',
            'billingAddress',
            'items.product',
            'baseInvoice',
            'baseInvoice.items.product',
            'baseInvoice.items.baseItem',
            'baseInvoice.billingAddress']);

        return Inertia::render($invoice->is_correction ? 'Admin/Order/Invoice/ShowCorrection' : 'Admin/Order/Invoice/Show', [
            'invoice' => InvoiceResource::make($invoice),
        ]);
    }

}