<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { useTopBarStore } from "../../../stores/topBar";

const page = usePage();
const topBarStore = useTopBarStore();
topBarStore.setSlides(page.props.textSlides);

const currentSlideIndex = ref(0);
const nextSlideIndex = ref(1);
const isTransitioning = ref(false);

const nextSlide = () => {
    nextSlideIndex.value = (currentSlideIndex.value + 1) % topBarStore.slides.length;
    isTransitioning.value = true;
    setTimeout(() => {
        currentSlideIndex.value = nextSlideIndex.value;
        isTransitioning.value = false;
    }, 700);
};

onMounted(() => {
    if (topBarStore.slides.length > 1) {
        intervalId = setInterval(nextSlide, 2000); // 5 sekund na slide
    }
});

let intervalId = null;

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>

<template>
    <div class="relative overflow-hidden min-h-[40px]">
        <div
            :key="currentSlideIndex"
            :style="{
                color: topBarStore.slides[currentSlideIndex]?.color,
                'background-color': topBarStore.slides[currentSlideIndex]?.background_color
            }"
            class="absolute inset-0 flex items-center justify-center w-full px-4 transition-opacity duration-700 ease-in-out z-10"
            :class="isTransitioning ? 'opacity-0' : 'opacity-100'"
            v-html="topBarStore.slides[currentSlideIndex]?.content"
        ></div>
        <div
            :key="nextSlideIndex"
            :style="{
                color: topBarStore.slides[nextSlideIndex]?.color,
                'background-color': topBarStore.slides[nextSlideIndex]?.background_color
            }"
            class="absolute inset-0 flex items-center justify-center w-full px-4 transition-opacity duration-700 ease-in-out z-0"
            :class="isTransitioning ? 'opacity-100' : 'opacity-0'"
            v-html="topBarStore.slides[nextSlideIndex]?.content"
        ></div>
    </div>
</template>