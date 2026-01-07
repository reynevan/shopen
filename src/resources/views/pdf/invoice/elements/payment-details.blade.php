@php
    use Illuminate\Support\Number;
@endphp
<table class="payment-details" style="width:100%;">
    <thead class="bg-panel py-2">
    <tr>
        <th style="font-weight: 600; padding: 2px 4px; text-align: left" class="text-xs">Forma płatności</th>
        <th style="font-weight: 600; padding: 2px 4px; text-align: center" class="text-xs">Termin płatności</th>
        @if ($invoice->is_correction)
        <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">
            @if ($invoice->left_to_pay_amount < 0)
                Kwota do zwrotu
            @else
                Kwota do zapłaty
            @endif
        </th>
        @else
            <th style="font-weight: 600; padding: 2px 4px; text-align: right" class="text-xs">Kwota do zapłaty</th>
        @endif
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="text-align: left; padding: 2px 4px;" class="text-xs">
            @if ($invoice->is_correction)
                {{ $invoice->correction_payment_method }}
            @else
                {{ $invoice->payment_method_label }}
            @endif
        </td>
        <td style="text-align: center; padding: 2px 4px;" class="text-xs">{{ $invoice->payment_due_date->format('Y-m-d') }}</td>
        @if ($invoice->is_correction)
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ Number::currency(abs($invoice->left_to_pay_amount)) }}</td>
        @else
            <td style="text-align: right; padding: 2px 4px;" class="text-xs">{{ Number::currency($invoice->total_amount) }}</td>
        @endif
    </tr>
    </tbody>
</table>