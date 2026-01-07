@php
    use Illuminate\Support\Number;
@endphp
<table class="tax-rates" style="width:100%;">
    <thead style="padding: 8px 0;">
    <tr>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs"></th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">VAT</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Wartość netto</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Kwota VAT</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Wartośc brutto</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($invoice->getTaxRates() as $taxRate)
        <tr class="border-b">
            <td style="text-align: left; padding: 2px 4px;" class="text-xs">W tym</td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ $taxRate['rate'] }}%</td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ $taxRate['amount_net'] }}</td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ $taxRate['tax_amount'] }}</td>
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ $taxRate['amount'] }}</td>
        </tr>
    @endforeach
    <tr class="border-b font-semibold">
        <td style="text-align: left; padding: 2px 4px;" class="text-xs">Suma</td>
        <td style="text-align: right; padding: 2px 4px;" class="text-xs"></td>
        <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ Number::currency($invoice->total_amount - $invoice->tax_amount) }}</td>
        <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ Number::currency($invoice->tax_amount) }}</td>
        <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ Number::currency($invoice->total_amount) }}</td>
    </tr>
    </tbody>
</table>