<script setup>
import { computed } from 'vue';

const props = defineProps({
    rating: {
        type: Number,
        required: true,
    },
    maxStars: {
        type: Number,
        default: 5,
    },
});

const stars = computed(() => {
    const result = [];
    const fullStars = Math.floor(props.rating);
    const hasHalfStar = (props.rating - fullStars) >= 0.5;

    for (let i = 0; i < fullStars; i++) {
        result.push({is_full: true});
    }

    if (hasHalfStar && result.length < props.maxStars) {
        result.push({is_half: true});
    }

    while (result.length < props.maxStars) {
        result.push({is_empty: true});
    }

    return result;
});
</script>

<template>
    <div class="flex items-center">
        <template v-for="(star, index) in stars" :key="index">
            <i class="text-yellow-400 bi bi-star" v-if="star.is_empty"></i>
            <i class="text-yellow-400 bi bi-star-half" v-if="star.is_half"></i>
            <i class="text-yellow-400 bi bi-star-fill" v-if="star.is_full"></i>
        </template>
    </div>
</template>