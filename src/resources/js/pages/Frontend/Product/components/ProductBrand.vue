<script setup>
    import Image from "@shopen/components/frontend/product/Image.vue";

    defineProps({
        product: {type: Object}
    })
</script>

<template>
    <a href="" v-if="product.brand">
        <Image v-if="product.brand.logo" :urls="product.brand.logo" :alt="product.brand.name" height="80" class="h-[80px]"/>
    </a>
</template>