<div>
    <div>Wysyłka</div>
    <div>
        <div>{{ $block->getShippingMethodName() }}</div>
        <div>{{ $block->getOrder()->delivery_point_code }}</div>
        @if($block->getOrder()->shipped_at)
            <div>Wysłano: {{ $block->getOrder()->shipped_at }}</div>
        @endif
        @if($block->getOrder()->shipping_tracking_code)
            <div>Numer przesyłki: {{ $block->getOrder()->shipping_tracking_code }}</div>
        @endif
    </div>
    <div>
        <form action="{{ route('admin.orders.ship', $block->getOrder()) }}" method="POST">
            @csrf
            <label for="shipping_tracking_code">Numer przesyłki</label>
            <input type="text" id="shipping_tracking_code" name="shipping_tracking_code">
            <button type="submit">Wyślij</button>
        </form>
    </div>
</div>