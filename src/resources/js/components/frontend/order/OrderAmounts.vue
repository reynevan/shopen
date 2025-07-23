<script setup>
defineProps(['order'])
</script>

<template>
    <div class="flex justify-end">
        <div class="w-full max-w-[350px]">
            <div class="flex items-center justify-between mb-2 text-neutral-600">
                <div>Wartość koszyka:</div>
                <div>{{ order.subtotal }}</div>
            </div>
            <div class="flex items-center justify-between mb-2 text-neutral-600" v-if="order.promo_code">
                <div>Kod promocyjny:</div>
                <div class="text-green-600">-{{ order.promo_code_discount_amount }}</div>
            </div>
            <div class="flex items-center justify-between mb-2 text-neutral-600">
                <div>Koszt dostawy:</div>
                <div>{{ order.shipping_amount }}</div>
            </div>
            <div class="flex items-center justify-between mb-2 text-neutral-600">
                <div>Płatność:</div>
                <div>{{ order.payment_amount }}</div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t font-semibold text-lg">
                <div>Razem:</div>
                <div>{{ order.total_amount }}</div>
            </div>
        </div>
    </div>
</template>