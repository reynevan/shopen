<script setup>
import ProductAttributes from "./ProductAttributes.vue";

defineProps({
    product: {type: Object},
    attributes: {type: Object}
})


</script>

<template>

    <div>
        <div class="flex sm:divide-x sm:divide-x-light mb-6 mt-6 w-full flex-wrap sm:flex-nowrap">
            <div class="w-full pr-4">
                <h2 class="details-section-title">
                    Opis
                </h2>
                <div>
                    <div class="product-description prose max-w-none" v-html="product.attributes.description ?? ''"></div>
                </div>
            </div>
            <div class="w-full mt-6 sm:mt-0 sm:pl-4">
                <h2 class="details-section-title">
                    Szczegóły
                </h2>
                <div class="w-full2">
                    <ProductAttributes :product="product" :attributes="attributes"/>
                </div>
            </div>
        </div>
    </div>
</template>