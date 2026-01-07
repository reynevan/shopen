<div style="margin-bottom: 32px;">
    <div style="display: inline-block; width: 45%; vertical-align: top;" class="text-sm">
        <div class="semibold text-sm">Sprzedawca:</div>
        @foreach (config('shopen.invoice.company_data', []) as $line)
            <div>{{ $line }}</div>
        @endforeach
    </div>
    @includeFirst(['pdf.invoice.elements.billing-address', 'shopen::pdf.invoice.elements.billing-address'], ['address' => $invoice->billingAddress])
</div>