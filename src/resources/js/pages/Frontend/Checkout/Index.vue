<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import CheckoutValidationError from "@shopen/components/frontend/checkout/CheckoutValidationError.vue";
import CheckoutShippingAddressForm from "@shopen/components/frontend/checkout/CheckoutShippingAddressForm.vue";
import CheckoutBillingAddressForm from "@shopen/components/frontend/checkout/CheckoutBillingAddressForm.vue";
import CheckoutNotes from "@shopen/components/frontend/checkout/CheckoutNotes.vue";
import CheckoutSummary from "@shopen/components/frontend/checkout/CheckoutSummary.vue";
import CheckoutShippingMethods from "@shopen/components/frontend/checkout/CheckoutShippingMethods.vue";
import CheckoutPaymentMethods from "@shopen/components/frontend/checkout/CheckoutPaymentMethods.vue";

defineOptions({ layout: AppLayout })

defineProps(['shippingMethods', 'paymentMethods', 'summary', 'addresses', 'promoCode'])

</script>

<template>
    <div class="checkout flex flex-wrap xl:flex-no-wrap justify-center items-start">

        <div class="w-full xl:w-2/3 pr-4">

            <div class="bg-white px-4 py-6 mb-4">
                <div class="mb-4">
                    <CheckoutValidationError section="shippingAddress"/>
                    <CheckoutShippingAddressForm :max-addresses="2" :addresses="addresses.shipping"/>
                </div>
                <div class="">
                    <CheckoutValidationError section="billingAddress"/>
                    <CheckoutBillingAddressForm :max-addresses="2" :addresses="addresses.billing"/>
                </div>
            </div>

            <div class="bg-white py-6 px-4 mb-4">
                <CheckoutValidationError section="shippingMethod"/>
                <div class="checkout-section-title">Sposób dostawy</div>
                <CheckoutShippingMethods :methods="shippingMethods"/>
            </div>

            <div class="bg-white mb-4 px-4 py-6">
                <CheckoutValidationError section="paymentMethod"/>
                <div class="checkout-section-title">Płatność</div>
                <CheckoutPaymentMethods :methods="paymentMethods"/>
            </div>

            @if (config('checkout.notes_active'))
            <div class="bg-white mb-4 px-4 py-6">
                <div class="checkout-section-title">Dodatkowe informacje</div>
                <CheckoutNotes/>
            </div>
            @endif
        </div>

        <div class="w-full xl:w-1/3 sticky top-6 pl-4">
            <div class="bg-white px-4 py-6">
                <CheckoutSummary :summary="summary" :promoCode="promoCode"/>
            </div>
        </div>
    </div>
</template>