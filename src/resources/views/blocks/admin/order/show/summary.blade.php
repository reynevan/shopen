<div class="mb-4">
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Produkty</div>
        <div>{{ $block->formatPrice($order->subtotal) }}</div>
    </div>
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Koszt wysyłki</div>
        <div>{{ $block->formatPrice($order->shipping_amount) }}</div>
    </div>
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Koszt płatności</div>
        <div>{{ $block->formatPrice($order->payment_amount) }}</div>
    </div>
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Obniżki</div>
        <div>{{ $block->formatPrice(-1 * $order->discount_amount) }}</div>
    </div>
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Kod promocyjny</div>
        <div class="flex items-center gap-2">
            @if ($block->getOrder()->promoCode)
                <div class="bg-accent/10 hover:bg-accent/30 transition-colors text-accent-600 text-xs">
                    <a href="{{ route('admin.promo-codes.edit', $block->getOrder()->promoCode) }}" class="block px-2 py-1">
                        {{ $block->getOrder()->promoCode->code }}
                    </a>
                </div>
            @endif
            <div>
                {{ $block->formatPrice(-1 * $order->promo_code_discount_amount) }}
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between mb-1 py-1 border-b">
        <div>Suma</div>
        <div class="font-bold text-xl">{{ $block->formatPrice($order->total_amount) }}</div>
    </div>
</div>