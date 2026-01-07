<div style="border-top: 1px solid #666;border-bottom: 1px solid #666;padding: 8px 0 12px;margin-top: 24px;margin-bottom: 32px;position: relative;">
    <div style="font-size: 25px;">Faktura</div>
    <div style="position: absolute;top: 6px;right: 0;">
        <div class="semibold text-sm">Numer dokumentu: {{ $invoice->invoice_number }}</div>
        <div class="text-sm">Data wystawienia: {{ $invoice->created_at->format('Y-m-d') }}</div>
        <div class="text-sm">Data sprzedaży: {{ $invoice->order->placed_date }}</div>
    </div>
</div>