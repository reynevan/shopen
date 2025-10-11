<script setup>
import ProductThumbnailImage from "@shopen/components/admin/product/ProductThumbnailImage.vue";

defineProps({
    product: { type: Object }
})
</script>

<template>
    <div>
        <ProductThumbnailImage :product="product"/>
    </div>
</template>