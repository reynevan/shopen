<script setup>
import { computed } from 'vue';
import IconStarFull from "@shopen/components/icons/IconStarFull.vue";
import IconStarHalf from "@shopen/components/icons/IconStarHalf.vue";

const props = defineProps({
    rating: {
        type: Number,
        required: true,
    },
    maxStars: {
        type: Number,
        default: 5,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    }
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

const sizeClasses = computed(() => {
    const sizes = {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-5 h-5',
        lg: 'w-6 h-6',
        xl: 'w-8 h-8'
    };
    return sizes[props.size] || sizes.md;
});
</script>

<template>
    <div class="flex items-center">
        <template v-for="(star, index) in stars" :key="index">
            <IconStarFull :class="['text-gray-300', sizeClasses]" v-if="star.is_empty"/>
            <IconStarHalf :class="['text-amber-500', sizeClasses]" v-if="star.is_half"/>
            <IconStarFull :class="['text-amber-500', sizeClasses]" v-if="star.is_full"/>
        </template>
    </div>
</template>