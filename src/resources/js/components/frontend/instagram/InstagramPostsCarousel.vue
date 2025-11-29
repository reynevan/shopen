<script setup>
import Flicking from "@egjs/vue3-flicking";
import {ref, useTemplateRef} from "vue";
import IconChevron from "@shopen/components/icons/IconChevron.vue";
const props = defineProps({
    posts: {type: Array},
    instagramProfileUrl: {type: String},
    alt: {type: String},
})

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
    const newTargetIndex = Math.min(flicking.value.index + visiblePanelCount, panelCount - visiblePanelCount + 1);
    await flicking.value.moveTo(newTargetIndex);
    index.value = newTargetIndex;
};
</script>

<template>
    <div class="instagram-carousel relative z-0 sm:px-12">
        <button
            v-if="flicking && index > 0 && (flicking.visiblePanels.length < flicking.panelCount)"
            @click="handlePrev"
            class="hidden sm:block absolute left-2 top-1/2 -translate-y-1/2 z-10 instagram-carousel-button"
            aria-label="wstecz"
        >
            <IconChevron left size="4xl"/>
        </button>

        <Flicking
            @changed="onMoveEnd"
            @willChange="onWillChange"
            ref="flickingRef"
            :options="options"
            class="overflow-x-hidden"
        >
            <a class="bg-dark relative w-[100px] sm:w-[120px] flex items-center justify-center hover:shadow-lg transition-all duration-350 group"
               :href="instagramProfileUrl"
               target="_blank">
                <span class="block relative -rotate-90 whitespace-nowrap cursor-pointer">
                    <span class="block text-center text-2xl sm:text-3xl text-neutral-50 relative z-10 font-decorative group-hover:scale-105 transition-all duration-350">śledź nas</span>
                    <span class="block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-6xl sm:text-8xl text-neutral-500 font-bold opacity-30">NA</span>
                    <span class="block text-center text-lg sm:text-xl font-light text-neutral-50 relative z-10 mt-2 tracking-[3px] sm:tracking-[5px] group-hover:scale-105 transition-all duration-350">INSTAGRAMIE</span>
                </span>
            </a>
            <div v-for="post in posts" :key="post.id"
                 class="flex items-stretch justify-center mx-1 sm:mx-3 w-1/2 md:w-1/3 lg:w-1/5 lg:max-w-[300px] hover:shadow-lg transition-all duration-300">
                <a :href="post.post_url" target="_blank" class="flex">
                    <img
                        :src="post.media_url"
                        :srcset="`${post.media_url} 300w, ${post.media_2x_url} 600w`"
                        sizes="(min-width: 1024px) min(20vw, 300px),
                             (min-width: 768px) 33vw,
                             50vw"
                        :alt="props.alt"
                        loading="lazy"
                        width="300"
                        height="300"
                    />
                </a>
            </div>
            <div class="mx-1 sm:mx-3 w-1/2 md:w-1/3 lg:w-1/5 lg:max-w-[300px]"></div>
        </Flicking>

        <button
            v-if="flicking && (index <= flicking.panelCount - flicking.visiblePanels.length) && (flicking.visiblePanels.length < flicking.panelCount)"
            @click="handleNext"
            class="hidden sm:block absolute right-2 top-1/2 -translate-y-1/2 z-10 instagram-carousel-button"
            aria-label="dalej"
        >
            <IconChevron right size="4xl"/>
        </button>
    </div>
</template>