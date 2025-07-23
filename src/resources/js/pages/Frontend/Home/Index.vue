<script setup>

import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductsCarousel from "../../../components/frontend/product/ProductsCarousel.vue";

defineOptions({ layout: AppLayout })
defineProps(['bestsellers'])

</script>

<template>
 <ProductsCarousel :products="bestsellers"/>
</template>