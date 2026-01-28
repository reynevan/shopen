<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";

import {Head, Link} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import {trackPurchase} from "../../../utils/ga4";
import {useAuthStore} from "../../../stores/auth";
import AddressDetails from "./components/confirmation/AddressDetails.vue";
import OrderItems from "./components/confirmation/OrderItems.vue";
import OrderSummary from "./components/confirmation/OrderSummary.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    order: {type: Object},
    payment_method: {type: Object}
})

const auth = useAuthStore()

trackPurchase(props.order)
</script>

<template>
    <Head>
        <title>Potwierdzenie złożenia zamówienia</title>
    </Head>
    <div class="my-12 py-6 px-4 sm:px-6 max-w-4xl mx-auto bg-body checkout-confirmation">
        <div class="mb-4">
            <div class="flex items-center justify-center pb-4 text-accent-hover">
                <IconCheckCircle size="8xl"/>
            </div>
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl mb-4">Dziękujemy za złożenie zamówienia!</h1>
                <p class="text-lg sm:text-xl mb-2">
                    Numer Twojego zamówienia to <span class="text-black">{{ order.order_number }}</span>.
                </p>
                <p class="text-lg sm:text-xl mb-6 pb-6 border-b">
                    Na adres {{ order.email }} zostało wysłane potwierdzenie zamówienia.
                </p>
            </div>

            <div v-if="!auth.isLoggedIn" class="my-6 py-6 text-center border-b">
                <h2 class="ext-lg sm:text-xl mb-2">Chcesz śledzić swoje zamówienie? Załóż konto!</h2>

                <p class="mb-4">Twoje dane zostaną automatycznie powiązane z tym zamówieniem.</p>

                <Link :href="route('sign-up')">
                    <Button type="secondary" size="lg">Załóż konto</Button>
                </Link>

                <div class="my-6 flex items-center gap-4 text-neutral-500 text-sm">
                    <div class="w-full h-[1px] border-b"></div>
                    <p class="text-xs uppercase whitespace-nowrap">
                        Masz już konto?
                    </p>
                    <div class="w-full h-[1px] border-b"></div>
                </div>

                <Link :href="route('login')">
                    <Button size="lg">Zaloguj się</Button>
                </Link>
            </div>

            <p class="text-lg sm:text-xl text-center mb-10">
                Informacje o zamówieniu
            </p>
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="w-full text-left flex flex-col sm:items-center">
                    <div>
                        <p class="font-semibold uppercase">Adres wysyłki</p>
                        <AddressDetails :address="order.shipping_address"/>

                        <p class="font-semibold uppercase mt-6">Metoda wysyłki</p>
                        <div>{{ order.shipping_method_label }}</div>
                    </div>
                </div>
                <div class="w-full text-left flex flex-col sm:items-center">
                    <div>
                        <p class="font-semibold uppercase">Adres rozliczeniowy</p>
                        <AddressDetails :address="order.billing_address"/>

                        <p class="font-semibold uppercase mt-6">Metoda płatności</p>
                        <div>{{ order.payment_method_label }}</div>
                        <div v-if="payment_method.additional_fields?.transfer_details?.value"
                             v-html="payment_method.additional_fields.transfer_details?.value"
                             class="whitespace-pre mt-2"></div>
                    </div>
                </div>
            </div>

            <p class="text-lg sm:text-xl mb-10">
                Zamówione produkty
            </p>
            <div class="order-items">
                <OrderItems :items="order.items"/>
            </div>

            <OrderSummary :order="order"/>

            <div class="flex justify-center mt-12">
                <Link href="/">
                    <Button type="primary" size="xl">Kontynuuj zakupy</Button>
                </Link>
            </div>
        </div>
    </div>
</template>