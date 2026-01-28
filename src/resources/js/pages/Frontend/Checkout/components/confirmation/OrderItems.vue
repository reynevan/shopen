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
    <div class="flex w-full flex-col gap-y-3 divide-y">
        <div v-for="item in items"
             :key="item.id"
             class="order-item-thumbnail flex flex-col sm:flex-row sm:items-center justify-between">
            <div class="flex items-center w-full">
                <ProductThumbnailImage :product="item.product" size="sm"/>
                <div class="ml-4 w-full">
                    <Link :href="item.product.url" class="order-product-name">{{ item.product.name }}</Link>
                    <div class="item-sku">SKU: {{ item.sku }}</div>

                    <div class="flex justify-end w-full items-end mt-2 sm:mt-0">
                        <div class="item-qty">{{ item.quantity }} x</div>
                        <div class="flex flex-col items-end">
                            <div v-if="item.final_price !== item.price" class="price-old">
                                {{ item.price }}
                            </div>
                            <div class="price">{{ item.final_price }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>