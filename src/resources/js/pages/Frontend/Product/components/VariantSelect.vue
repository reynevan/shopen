<script setup>

import VariantProducts from "./VariantProducts.vue";

const props = defineProps({
    variants: {
        type: Array,
    },
    showError: {
        type: Boolean,
        default: false,
    }
})
</script>

<template>
    <div v-if="variants && variants.length" class="pt-4 mt-4 pb-6 mb-6 border-y border-light space-y-4">
        <div v-for="variant in variants" :key="variant.attribute.id">
            <VariantProducts :variant="variant" :showError="showError"/>
        </div>
    </div>
</template>