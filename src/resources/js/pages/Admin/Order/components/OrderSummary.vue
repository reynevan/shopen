<script setup>

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
        <div v-if="order.discount_amount" class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Obniżki</div>
            <div>{{ -1 * order.discount_amount }}</div>
        </div>
        <div v-if="order.promoCode" class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Kod promocyjny</div>
            <div class="flex items-center gap-2">
                <div class="bg-accent/10 hover:bg-accent/30 transition-colors text-accent-600 text-xs">
                    <a href="" class="block px-2 py-1">
                        {{ order.promoCode.code }}
                    </a>
                </div>
                <div>
                    {{ order.promo_code_discount_amount }}
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between mb-1 py-1 border-b">
            <div>Suma</div>
            <div class="text-xl">{{ order.total_amount }}</div>
        </div>
    </div>
</template>