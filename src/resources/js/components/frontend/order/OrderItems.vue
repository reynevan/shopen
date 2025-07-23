<script setup>
import ProductThumbnailImage from "../product/ProductThumbnailImage.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    'items': {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-col gap-y-3">
        <div v-for="item in items" :key="item.id"
             class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-3 mb-2">
            <div class="flex items-center">
                <ProductThumbnailImage :product="item.product" size="sm"/>
                <div class="ml-4">
                    <div class="font-medium">
                        <Link :href="item.product.url" class="hover:underline">{{ item.product.name }}</Link>
                    </div>
                    <div class="text-sm text-neutral-500">SKU: {{ item.sku }}</div>
                </div>
            </div>
            <div class="flex items-center mt-2 sm:mt-0">
                <div class="text-neutral-600 mr-4 text-sm">{{ item.quantity }} szt.</div>
                <div class="text-neutral-800 flex flex-col items-end">
                    <div v-if="item.final_price !== item.price" class="text-neutral-400 line-through text-sm">
                        {{ item.price }}
                    </div>
                    <div class="font-medium">{{ item.final_price }}</div>
                </div>
            </div>
        </div>
    </div>
</template>