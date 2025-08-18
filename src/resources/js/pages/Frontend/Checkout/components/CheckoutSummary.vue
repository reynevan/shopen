<script setup>

import {useCheckoutStore} from "@shopen/stores/checkout.js";
import {computed} from "vue";
import CheckoutItems from "@shopen/pages/Frontend/Checkout/components//CheckoutItems.vue";
import {useShippingStore} from "@shopen/stores/shipping.js";
import {usePaymentStore} from "@shopen/stores/payment.js";
import CheckoutPromoCode from "@shopen/pages/Frontend/Checkout/components/CheckoutPromoCode.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

const checkout = useCheckoutStore();
const shipping = useShippingStore();
const payment = usePaymentStore();

defineProps(['summary', 'promoCode'])

const placeOrder = async () => {
    checkout.placeOrder();
}

const isPlaceOrderEnabled = computed(() => {
    return !!shipping.selectedMethod && !!payment.selectedMethod && !!shipping.shippingAddress;
})

</script>

<template>
    <div>
        <div class="border-b">
            <CheckoutItems/>
        </div>
        <div class="border-b">
            <CheckoutPromoCode :promoCode="promoCode"/>
        </div>
        <!-- Komunikat błędu -->
        <div v-if="checkout.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ checkout.error }}
        </div>

        <!-- Komunikat sukcesu -->
        <div v-if="checkout.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ checkout.success.message }}
        </div>

        <div class="px-4 py-4 pb-2 border-b border-dashed">
            <div class="flex justify-between items-end mb-2">
                <div>
                    Produkty
                </div>
                <div class="flex flex-col items-end">
                    <div v-if="summary.productsSubtotal !== summary.productsTotal"
                         class="text-gray-400 text-sm line-through">
                        {{ summary.productsSubtotal }}
                    </div>
                    <div>{{ summary.productsTotal }}</div>
                </div>
            </div>

            <div class="flex justify-between mb-2">
                <div>
                    Dostawa
                </div>
                <div>
                    {{ summary.shipping }}
                </div>
            </div>

            <div class="flex justify-between mb-2" v-if="summary.payment">
                <div>
                    Płatność
                </div>
                <div>
                    {{ summary.payment }}
                </div>
            </div>

            <div class="flex justify-between" v-if="summary.hasDiscount">
                <div>
                    Kod promocyjny
                </div>
                <div>
                    -{{ summary.discount }}
                </div>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between p-4">
                <div class="mr-2 ">
                    Do zapłaty
                </div>
                <div class="text-xl">
                    {{ summary.total }}
                </div>
            </div>
        </div>
        <div class="mt-2">
            <Button type="secondary" full-width
                    @click="placeOrder"
                    :disabled="checkout.orderLoading"
                    :loading="checkout.orderLoading"
            >
                <span>Złóż zamówienie</span>
            </Button>
        </div>
    </div>
</template>

<style scoped>

</style>