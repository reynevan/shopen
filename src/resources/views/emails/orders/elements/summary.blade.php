<div class="total" style="text-align: right">
    <div>Wartość produktów: {{ Number::currency($order->subtotal) }}</div>
    <div>Dostawa: {{ Number::currency($order->shipping_amount) }}</div>
    @if($order->payment_amount > 0)
        <div>Koszt płatności: {{ Number::currency($order->payment_amount) }}</div>
    @endif
    <div style="font-size: 18px; margin-top: 10px;">
        <strong>Razem: {{ Number::currency($order->total_amount) }}</strong>
    </div>
</div>