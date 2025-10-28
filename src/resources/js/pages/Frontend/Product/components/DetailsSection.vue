<script setup>
import ProductDescription from "./ProductDescription.vue";
import ProductAttributes from "./ProductAttributes.vue";

defineProps({
    product: {type: Object},
    attributes: {type: Object}
})


const buttonClass = 'cursor-pointer text-lg tracking-wider w-full sm:w-auto';

</script>

<template>

    <div>
        <div class="flex divide-x divide-x-light gap-6 mb-6 mt-6 w-full flex-wrap sm:flex-nowrap">
            <div class="w-full">
                <div :class="buttonClass">
                    Opis
                </div>
                <div>
                    <div class="product-description prose max-w-none" v-html="product.attributes.description ?? ''"></div>
                </div>
            </div>
            <div class="w-full">
                <div :class="buttonClass">
                    Szczegóły
                </div>
                <div class="w-full lg:w-1/3 xl:w-1/2">
                    <ProductAttributes :product="product" :attributes="attributes"/>
                </div>
            </div>
        </div>
    </div>
</template>