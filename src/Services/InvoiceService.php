<?php

namespace Shopen\Services;

use Barryvdh\DomPDF\Facade\Pdf;
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