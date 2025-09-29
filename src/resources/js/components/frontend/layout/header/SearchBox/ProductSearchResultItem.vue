<script setup>
import ProductImage from "@shopen/components/frontend/product/ProductImage.vue";
import RatingDisplay from "@shopen/components/frontend/product/RatingDisplay.vue";
import PriceDisplay from "@shopen/components/frontend/ui/PriceDisplay.vue";

const props = defineProps({
    product: {type: Object}
})

const emits = defineEmits(['select'])
</script>

<template>
    <div
        @mousedown="emits('select', product)"
        class="flex items-center p-3 cursor-pointer hover:bg-accent/30 transition-all">
        <div class="flex-shrink-0 mr-3">
            <ProductImage :urls="product.images ? product.images[0] : []" sizes="75px"
                   :alt="product.name"
                   width="75" height="75"/>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-md text-gray-900 truncate">
                {{ product.attributes.name }}
            </h4>
            <div class="flex items-center gap-2">
                <RatingDisplay :rating="product.rating" size="xs"/>
                <div>{{ product.rating }} ({{ product.reviews_count }})</div>
            </div>
            <p class="text-sm">
                <PriceDisplay :price="product.price.final_price" size="md"/>
            </p>
        </div>
    </div>
</template>