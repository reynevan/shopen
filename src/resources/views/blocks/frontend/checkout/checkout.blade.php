<div class="checkout flex flex-wrap xl:flex-no-wrap justify-center items-start">

    <div class="w-full xl:w-2/3 pr-4">

        <div class="bg-white px-4 py-6 mb-4">
            <div class="mb-4">
                <checkout-validation-error section="shipping_address"></checkout-validation-error>
                <checkout-shipping-address-form
                        :max-addresses="{{ Auth::check() ? config('shopen.address.max_per_user') : 1 }}"></checkout-shipping-address-form>
            </div>
            <div class="">
                <checkout-validation-error section="billing_address"></checkout-validation-error>
                <checkout-billing-address-form
                        :max-addresses="{{ Auth::check() ? config('shopen.address.max_per_user') : 1 }}"></checkout-billing-address-form>
            </div>
        </div>

        <div class="bg-white py-6 px-4 mb-4">
            <checkout-validation-error section="shipping"></checkout-validation-error>
            <div class="checkout-section-title">Sposób dostawy</div>
            @foreach ($block->getShippingMethods() as $method)
                @block($method->getBlock(), ['shipping_method' => $method])
            @endforeach
        </div>

        <div class="bg-white mb-4 px-4 py-6">
            <checkout-validation-error section="payment"></checkout-validation-error>
            <div class="checkout-section-title">Płatność</div>
            @foreach ($block->getPaymentMethods() as $method)
                @block($method->getBlock(), ['payment_method' => $method])
            @endforeach
        </div>

        @if (config('checkout.notes_active'))
            <div class="bg-white mb-4 px-4 py-6">
                <div class="checkout-section-title">Dodatkowe informacje</div>
                <checkout-notes></checkout-notes>
            </div>
        @endif
    </div>

    <div class="w-full xl:w-1/3 sticky top-6 pl-4">
        <div class="bg-white px-4 py-6">
            <checkout-summary></checkout-summary>
        </div>
    </div>
</div>