<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    order: Object
})
</script>

<template>
    <div class="flex justify-between items-center mb-1 py-1 border-b">
        <div>Data złożenia</div>
        <div>{{ order.created_at }}</div>
    </div>
    <div class="flex justify-between mb-1 py-1 border-b">
        <div>Status</div>
        <div>{{ order.status_label }}</div>
    </div>
    <div class="flex justify-between mb-1 py-1 border-b">
        <div>Metoda wysyłki</div>
        <div>{{ order.shipping_method_label }}</div>
    </div>
    <div class="flex justify-between mb-1 py-1 border-b">
        <div>Koszt wysyłki</div>
        <div class="flex flex-col items-end">
            <div>{{ order.shipping_amount }}</div>
            <div v-if="order.shipping_amount_returned" class="whitespace-nowrap">
                Zwrócono: {{ order.shipping_amount_returned }}
            </div>
        </div>
    </div>
    <div class="flex justify-between mb-1 py-1 border-b">
        <div>Metoda płatności</div>
        <div>{{ order.payment_method_label }}</div>
    </div>
    <div class="flex justify-between mb-1 py-1 border-b" v-if="order.promo_code_coupon">
        <div>Kod rabatowy</div>
        <div class="flex gap-2 items-center">
            <div>
                <Link class="cursor-pointer underline"
                      :href="route('admin.promo-codes.edit', order.promo_code_coupon.promo_code.id)">
                    {{ order.promo_code_coupon.promo_code.name }}
                </Link>
            </div>
            <div class="text-sm border px-3">{{ order.promo_code_coupon.code }}</div>
        </div>
    </div>
</template>