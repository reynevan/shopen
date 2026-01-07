@php
    use Illuminate\Support\Number;
    use Slownie\Slownie;
@endphp
<div style="text-align: right">
    @if ($invoice->is_correction)
        @if ($invoice->left_to_pay_amount < 0)
            <p style="font-size: 18px;">Razem do zwrotu: {{ Number::currency(abs($invoice->left_to_pay_amount)) }}</p>
        @else
            <p style="font-size: 18px;">Razem do zapłaty: {{ Number::currency($invoice->left_to_pay_amount) }}</p>
        @endif
        <p style="font-size: 8px;">Słownie: {{ Slownie::convert(abs($invoice->left_to_pay_amount)) }}</p>
        <p style="font-size: 8px;">Przyczyna korekty: {{ $invoice->correction_reason }}</p>
    @else
        <p style="font-size: 18px;">Razem do zapłaty: {{ Number::currency($invoice->total_amount) }}</p>
        <p style="font-size: 8px;">Słownie: {{ Slownie::convert($invoice->total_amount) }}</p>
    @endif
    @if (!$invoice->is_correction)
        <p style="font-size: 8px;">Zapłacono: {{ Number::currency($invoice->paid_amount) }}</p>
        <p style="font-size: 8px;">Zostało do zapłaty: {{ Number::currency($invoice->left_to_pay_amount) }}</p>
    @endif
</div>