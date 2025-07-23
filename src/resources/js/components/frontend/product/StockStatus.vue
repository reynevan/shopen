<script setup>

import {useVariantStore} from "../../../stores/productVariant";

const props = defineProps(['status', 'configurable']);

const variantStore = useVariantStore();
</script>

<template>
    <div v-if="!configurable || !variantStore.selectedVariant">
        <div v-if="status">Dostępny</div>
        <div v-else>Brak w magazynie</div>
    </div>
    <div v-else>
        <div v-if="variantStore.selectedVariant.in_stock">Dostępny</div>
        <div v-else>Brak w magazynie</div>
    </div>
</template>

<style scoped>

</style>