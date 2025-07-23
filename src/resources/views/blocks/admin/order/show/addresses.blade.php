@php
    $shippingAddress = $block->getOrder()->shippingAddress;
    $billingAddress = $block->getOrder()->billingAddress;
@endphp
<div class="mb-4 px-4 flex flex-wrap items-start">
    <div class="w-full sm:w-1/2">
        <div class="font-semibold text-lg mb-2">Adres wysyłki</div>
        <div>
            <div class="">{{ $shippingAddress->first_name }} {{ $shippingAddress->last_name }}</div>
            <div class="">{{ $shippingAddress->postal_code }} {{ $shippingAddress->city }}</div>
            <div class="">{{ $shippingAddress->address_line }}</div>
            <div class="">tel.: {{ $shippingAddress->phone }}</div>
            @if ($shippingAddress->email)
                <div class="">{{ $shippingAddress->email }}</div>
            @endif
        </div>
    </div>
    <div class="w-full sm:w-1/2">
        <div class="font-semibold text-lg mb-2">Dane do płatności</div>
        <div>
            <div class="">{{ $billingAddress->first_name }} {{ $billingAddress->last_name }}</div>
            <div class="">{{ $billingAddress->postal_code }} {{ $billingAddress->city }}</div>
            <div class="">{{ $billingAddress->address_line }}</div>
            <div class="">tel.: {{ $billingAddress->phone }}</div>
            @if ($billingAddress->email)
                <div class="">{{ $billingAddress->email }}</div>
            @endif
        </div>
    </div>
</div>