<script setup>
import { ref } from 'vue';
import IconStarFull from "../../../../components/icons/IconStarFull.vue";
import IconStarEmpty from "../../../../components/icons/IconStarEmpty.vue";

const props = defineProps({

    maxStars: {
        type: Number,
        default: 5,
    },
});
const model = defineModel();
const emit = defineEmits(['update:modelValue']);

// Przechowuje ocenę podczas najechania myszką
const hoverRating = ref(0);

const setRating = (rating) => {
    model.value = rating;
};
</script>

<template>
    <div class="flex items-center" @mouseleave="hoverRating = 0">
        <div
            v-for="star in maxStars"
            :key="star"
            class="cursor-pointer"
            @mouseenter="hoverRating = star"
            @click="setRating(star)"
        >
            <IconStarFull
                lg
                v-if="star <= (hoverRating || model)"
                class="text-yellow-400 transition-transform duration-250 hover:scale-120"
            />
            <IconStarEmpty
                lg
                v-else
                class="text-gray-300 transition-transform duration-250 hover:scale-120"
            />
        </div>
    </div>
</template>