<script setup>
import {Link} from "@inertiajs/vue3";

const props = defineProps(['order']);

</script>

<template>
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Produkty</div>
            <div>{{ order.subtotal }}</div>
        </div>
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Koszt wysyłki</div>
            <div>{{ order.shipping_amount }}</div>
        </div>
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Koszt płatności</div>
            <div>{{ order.payment_amount }}</div>
        </div>
        <div v-if="order.returned_amount || order.shipping_amount_returned" class="flex items-start justify-between mb-1 py-1 border-b">
            <div>Zwrot</div>
            <div class="flex flex-col items-end">
                <div>Produkty: {{ order.returned_amount }}</div>
                <div v-if="order.shipping_amount_returned">Wysyłka: {{ order.shipping_amount_returned }}</div>
            </div>
        </div>
        <div v-if="order.discount_amount" class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Obniżki</div>
            <div>{{ -1 * order.discount_amount }}</div>
        </div>
        <div v-if="order.promo_code_coupon" class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Kod rabatowy</div>
            <div class="flex items-center gap-2">
                <div>
                    {{ order.promo_code_discount_amount }}
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Podatek</div>
            <div>{{ order.tax_amount }}</div>
        </div>
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Suma</div>
            <div class="text-xl">{{ order.total_amount }}</div>
        </div>
    </div>
</template>