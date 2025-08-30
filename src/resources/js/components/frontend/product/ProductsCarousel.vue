<script setup>
import Flicking from "@egjs/vue3-flicking";
import {ref, useTemplateRef} from "vue";
import IconChevron from "../../icons/IconChevron.vue";
import ProductThumbnail from "@shopen/components/frontend/product/thumbnail/ProductThumbnail.vue";

defineProps(['products'])

const flicking = useTemplateRef('flickingRef');
const targetIndex = ref(0);

const options = {
    align: "prev",
    moveType: 'snap',
    bound: true,
    preventDefaultOnDrag: true
};

const index = ref(0);

const onWillChange = (event) => {
    targetIndex.value = event.index;
}

const onMoveEnd = () => {
    index.value = flicking.value.index;
}

const handlePrev = async () => {
    if (!flicking.value) return;
    const visiblePanelCount = flicking.value.visiblePanels.length;
    const isPlaying = flicking.value.animating

    if (isPlaying) {
        return;
    }
    const newTargetIndex = Math.max(flicking.value.index - visiblePanelCount, 0);
    await flicking.value.moveTo(newTargetIndex);
};


const handleNext = async () => {
    if (!flicking.value) return;

    const visiblePanelCount = flicking.value.visiblePanels.length;
    const panelCount = flicking.value.panelCount;
    const isPlaying = flicking.value.animating

    if (isPlaying) {
        return;
    }
    const newTargetIndex = Math.min(flicking.value.index + visiblePanelCount, panelCount - visiblePanelCount);
    await flicking.value.moveTo(newTargetIndex);
    index.value = newTargetIndex;
};
</script>

<template>
    <div class="relative">
        <!-- Previous Button -->
        <button
            v-if="flicking && index > 0 && (flicking.visiblePanels.length < flicking.panelCount)"
            @click="handlePrev"
            class="absolute left-2 top-1/2 -translate-y-1/2 z-10 carousel-button"
        >
            <icon-chevron left/>
        </button>

        <!-- Carousel -->
        <Flicking
            @changed="onMoveEnd"
            @willChange="onWillChange"
            ref="flickingRef"
            :options="options"
            class="overflow-hidden"
        >
            <div v-for="product in products" :key="product.id"
                 class="flex items-stretch justify-center w-1/2 sm:w-1/4 md:w-1/5 lg:w-1/6 2xl:w-1/7">
                <ProductThumbnail :product="product" size="sm"/>
            </div>
        </Flicking>

        <!-- Next Button -->
        <button
            v-if="flicking && (index < flicking.panelCount - flicking.visiblePanels.length) && (flicking.visiblePanels.length < flicking.panelCount)"
            @click="handleNext"
            class="absolute right-2 top-1/2 -translate-y-1/2 z-10 carousel-button"
        >
            <icon-chevron right/>
        </button>
    </div>
</template>