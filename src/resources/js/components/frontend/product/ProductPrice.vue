<script setup>

import {useVariantStore} from "../../../stores/productVariant";

const props = defineProps(['price', 'configurable'])

const variantStore = useVariantStore();

</script>

<template>
    <div class="flex items-end">
        <div class="text-xl" v-if="!configurable || !variantStore.selectedVariant">{{ price.final_price }}</div>
        <div class="text-xl" v-if="configurable && variantStore.selectedVariant">{{ variantStore.selectedVariant.price.final_price }}</div>
    </div>

</template>

<style scoped>

</style>