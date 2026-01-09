<?php

namespace Shopen\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Shopen\Models\Order\Invoice\Invoice;
use Illuminate\Support\Carbon;

class InvoiceService
{
    public function getNextCorrectionNumber(bool $includeMonth = false, bool $includeDay = false): string
    {
        return $this->getNextInvoiceNumber($includeMonth, $includeDay, config('shopen.invoice.number.correction_prefix'), true);
    }

    public function getNextNumber(bool $includeMonth = false, bool $includeDay = false): string
    {
        return $this->getNextInvoiceNumber($includeMonth, $includeDay, config('shopen.invoice.number.prefix'));
    }

    protected function getNextInvoiceNumber(bool $includeMonth, bool $includeDay, $prefix , bool $isCorrection = false): string
    {
        $now   = Carbon::now();
        $year  = $now->format('Y');
        $month = $now->format('m');
        $day   = $now->format('d');

        if ($includeDay) {
            $suffix = "{$day}/{$month}/{$year}";
        } elseif ($includeMonth) {
            $suffix = "{$month}/{$year}";
        } else {
            $suffix = $year;
        }

        $numbers = Invoice::query()
            ->where('is_correction', $isCorrection)
            ->pluck('invoice_number');

        $maxSeq = $numbers
            ->map(function (string $number) use ($prefix) {
                $number = str_replace($prefix, '', $number);
                $number = trim($number, '/');
                $parts = explode('/', $number);
                $seqPart = $parts[0] ?? '0';

                return (int) $seqPart;
            })
            ->max() ?? 0;

        $nextSeq = $maxSeq + 1;

        return implode('/', [$prefix, "{$nextSeq}/{$suffix}"]);
    }

    public function createCorrection(Invoice $baseInvoice, $items, $data)
    {
        $total = round(collect($items)->sum('total'), 2);
        $totalNet = round(collect($items)->sum('total_net'), 2);
        $tax = round(collect($items)->sum('tax_amount'), 2);
        $invoice = Invoice::create([
            'invoice_number' => $data['number'] ?? $this->getNextCorrectionNumber(),
            'total_amount' => $total,
            'total_net_amount' => $totalNet,
            'tax_amount' => $tax,
            'correction_reason' => $data['correction_reason'],
            'correction_payment_method' => $data['payment_method'],
            'payment_due_date' => $data['payment_due_date'],
            'is_correction' => true,
            'order_id' => $baseInvoice->order_id,
            'base_invoice_id' => $baseInvoice->is_correction ? $baseInvoice->base_invoice_id : $baseInvoice->id,
            'left_to_pay_amount' => round($total - floatval($baseInvoice->total_amount), 2),
        ]);
        foreach ($items as $itemData) {
            $item = $invoice->items()->make();
            $item->sku = $itemData['sku'] ?? '';
            $item->base_invoice_item_id = $itemData['id'] ?? null;
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
        return $invoice;
    }

    public function savePdf(Invoice $invoice): void
    {
        $view = $invoice->is_correction ? 'correction' : 'invoice';
        $pdf = Pdf::loadView('shopen::pdf.invoice.' . $view, ['invoice' => $invoice]);
        $pdf->getFontMetrics()->registerFont([
            'family' => 'Roboto',
            'style' => 'normal',
            'weight' => 'normal'
        ], storage_path('fonts/Roboto-Regular.ttf'));
        $pdf->getFontMetrics()->registerFont([
            'family' => 'Roboto',
            'style' => 'normal',
            'weight' => '600'
        ], storage_path('fonts/Roboto-SemiBold.ttf'));
        $pdf->save($invoice->file_path, 'public');
    }
}