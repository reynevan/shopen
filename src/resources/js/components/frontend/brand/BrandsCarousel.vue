<script setup>
import Flicking from "@egjs/vue3-flicking";
import {ref, useTemplateRef, computed} from "vue";
import IconChevron from "../../icons/IconChevron.vue";
import BrandCarouselItem from "./BrandCarouselItem.vue";

defineProps({
    brands: { type: Array },
})

const options = {
    align: "prev",
    moveType: "snap",
    preventDefaultOnDrag: true,
};

const flicking = useTemplateRef("brandsFlickingRef");
const index = ref(0);

const onMoveEnd = () => {
    index.value = flicking.value.index;
};

// --- sprawdzamy czy jest koniec/początek ---
const showPrev = computed(() => index.value > 0);

const showNext = computed(() => {
    if (!flicking.value) return false;
    const lastPanel = flicking.value.panels.at(-1);
    if (!lastPanel) return false;

    const cameraWidth = flicking.value.camera.size;
    const lastPanelEnd = lastPanel.position + lastPanel.size;

    // jeśli koniec ostatniego panelu > szerokość widocznej kamery → jeszcze można przewijać
    return lastPanelEnd > cameraWidth + flicking.value.camera.offset;
});

const handlePrev = async () => {
    if (!flicking.value) return;
    if (flicking.value.animating) return;

    const newTargetIndex = Math.max(flicking.value.index - 1, 0);
    await flicking.value.moveTo(newTargetIndex);
    index.value = newTargetIndex;
};

const handleNext = async () => {
    if (!flicking.value) return;
    if (flicking.value.animating) return;

    const newTargetIndex = Math.min(flicking.value.index + 1, flicking.value.panelCount - 1);
    await flicking.value.moveTo(newTargetIndex);
    index.value = newTargetIndex;
};
</script>

<template>
    <div class="relative z-0 px-[50px]">
        <button
            v-if="showPrev"
            @click="handlePrev"
            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 carousel-button"
        >
            <IconChevron left />
        </button>

        <Flicking
            @changed="onMoveEnd"
            ref="brandsFlickingRef"
            :options="options"
            class="overflow-hidden"
        >
            <div v-for="brand in brands" :key="brand.id" class="flex items-stretch justify-center items-center w-full sm:w-1/3 lg:w-1/4">
                <BrandCarouselItem :brand="brand" />
            </div>
        </Flicking>

        <button
            v-if="showNext"
            @click="handleNext"
            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 carousel-button"
        >
            <IconChevron right />
        </button>
    </div>
</template>