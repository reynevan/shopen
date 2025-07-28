<script setup>

import RatingDisplay from "@shopen/pages/Frontend/Product/components/RatingDisplay.vue";
import {computed} from "vue";

const props = defineProps(['product'])

const reviewsCountLabel = computed(() => {
    const count = props.product.reviews_count;
    if (count === 0) {
        return 'opinii';
    }
    if (count === 1) {
        return 'opinia'
    }
    if (count > 1 && count <= 4) {
        return 'opinie';
    }
    if (count <= 21) {
        return 'opinii';
    }
    if (count % 10 >= 2 && count % 10 <= 4) {
        return 'opinie';
    }
    return 'opinii';
})
</script>

<template>
    <div class="flex items-center gap-2">
        <RatingDisplay :rating="product.rating"/>
        <div>{{ product.rating }} ({{ product.reviews_count ?? 0 }} {{ reviewsCountLabel }})</div>
    </div>
</template>

<style scoped>

</style>