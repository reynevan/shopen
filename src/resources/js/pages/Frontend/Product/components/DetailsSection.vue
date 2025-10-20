<script setup>
import ProductDescription from "./ProductDescription.vue";
import ProductAttributes from "./ProductAttributes.vue";
import {ref} from "vue";

defineProps({
    product: {type: Object},
    attributes: {type: Object}
})

const activeSection = ref('description')

const buttonClass = 'cursor-pointer px-8 text-lg tracking-wider w-full sm:w-auto';
const buttonClassActive = 'font-semibold';

</script>

<template>

    <div>
        <div class="flex divide-x divide-x-light mb-6 mt-6 w-full sm:w-auto">
            <div @click="activeSection = 'description'" :class="[buttonClass, activeSection === 'description' ? buttonClassActive : '']">
                Opis
            </div>
            <div @click="activeSection = 'details'" :class="[buttonClass, activeSection === 'details' ? buttonClassActive : '']">
                Szczegóły
            </div>
        </div>
        <div v-show="activeSection === 'description'">
            <ProductDescription :description="product.attributes.description ?? ''"/>
        </div>
        <div v-show="activeSection === 'details'" class="w-full lg:w-1/3 xl:w-1/2">
            <ProductAttributes :product="product" :attributes="attributes"/>
        </div>
    </div>
</template>