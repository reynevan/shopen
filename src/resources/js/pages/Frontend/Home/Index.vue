<script setup>

import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductsCarousel from "../../../components/frontend/product/ProductsCarousel.vue";
import BrandsCarousel from "../../../components/frontend/brand/BrandsCarousel.vue";

defineOptions({layout: AppLayout})
defineProps({
    bestsellers: {type: Array},
    brands: {type: Array},
})

</script>

<template>
    <ProductsCarousel :products="bestsellers"/>

    <section class="mt-12">
        <h2 class="text-xl mb-2">Strefa marek</h2>
        <BrandsCarousel :brands="brands"/>
    </section>
</template>