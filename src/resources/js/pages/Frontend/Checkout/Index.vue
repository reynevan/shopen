<script setup>
import CheckoutLayout from "@shopen/layouts/frontend/CheckoutLayout.vue";
import CheckoutValidationError from "@shopen/pages/Frontend/Checkout/components/CheckoutValidationError.vue";
import CheckoutShippingAddressForm from "@shopen/pages/Frontend/Checkout/components/CheckoutShippingAddressForm.vue";
import CheckoutBillingAddressForm from "@shopen/pages/Frontend/Checkout/components/CheckoutBillingAddressForm.vue";
import CheckoutNotes from "@shopen/pages/Frontend/Checkout/components/CheckoutNotes.vue";
import CheckoutSummary from "@shopen/pages/Frontend/Checkout/components/CheckoutSummary.vue";
import CheckoutShippingMethods from "@shopen/pages/Frontend/Checkout/components/CheckoutShippingMethods.vue";
import CheckoutPaymentMethods from "@shopen/pages/Frontend/Checkout/components/CheckoutPaymentMethods.vue";
import GeoWidget from "../../../components/frontend/shipping/GeoWidget.vue";

defineOptions({ layout: CheckoutLayout })

defineProps({
    shippingMethods: {
        type: Array,
    },
    paymentMethods: {
        type: Array,
    },
    summary: {
        type: Object
    },
    addresses: {
        type: Object
    },
    promoCode: {
        type: Object
    },
    notesEnabled: {
        type: Boolean
    },
    includeGeoWidget: {
        type: Boolean,
        default: false
    }
})

</script>

<template>
    <div class="checkout flex flex-wrap xl:flex-no-wrap justify-center items-start">

        <div class="w-full xl:w-3/5 pr-4">

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

            <div class="bg-white mb-4 px-4 py-6" v-if="notesEnabled">
                <div class="checkout-section-title">Dodatkowe informacje</div>
                <CheckoutNotes/>
            </div>
        </div>

        <div class="w-full xl:w-2/5 sticky top-6 pl-4">
            <div class="bg-white px-4 py-6">
                <CheckoutSummary :summary="summary" :promoCode="promoCode"/>
            </div>
        </div>
    </div>
    <template v-if="includeGeoWidget">
        <GeoWidget/>
    </template>
</template>