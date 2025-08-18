<script setup>
import ProductThumbnailImage from "../../../../../components/admin/product/ProductThumbnailImage.vue";

defineProps({
    product: { type: Object }
})
</script>

<template>
    <div>
        <ProductThumbnailImage :product="product"/>
        <div class="text-sm">{{ product.name }}</div>
    </div>
</template>