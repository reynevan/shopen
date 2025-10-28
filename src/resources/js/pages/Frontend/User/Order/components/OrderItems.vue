<script setup>
import ProductThumbnailImage from "@shopen/components/frontend/product/ProductThumbnailImage.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    'items': {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-col gap-y-3 divide-y">
        <div v-for="item in items"
             :key="item.id"
             class="order-item-thumbnail flex flex-col sm:flex-row sm:items-center justify-between">
            <div class="flex items-center">
                <ProductThumbnailImage :product="item.product" size="sm"/>
                <div class="ml-4">
                    <Link :href="item.product.url" class="order-product-name">{{ item.product.name }}</Link>
                    <div class="item-sku">SKU: {{ item.sku }}</div>
                </div>
            </div>
            <div class="flex items-center mt-2 sm:mt-0">
                <div class="item-qty">{{ item.quantity }} szt.</div>
                <div class="flex flex-col items-end">
                    <div v-if="item.final_price !== item.price" class="item-old-price">
                        {{ item.price }}
                    </div>
                    <div class="item-price">{{ item.final_price }}</div>
                </div>
            </div>
        </div>
    </div>
</template>