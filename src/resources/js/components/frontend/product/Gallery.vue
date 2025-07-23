<script setup>
import Flicking from "@egjs/vue3-flicking";
import {defineProps, ref, useTemplateRef} from 'vue';
import IconChevron from "@shopen/components/icons/IconChevron.vue";

const props = defineProps(['images']);
const flicking = useTemplateRef('flicking');

const previewIndex = ref(0);

const selectImage = (index) => {
    flicking.value.moveTo(index);
    previewIndex.value = index;
}
const prevImage = async () => {
    if (previewIndex.value <= 0) {
        return;
    }
    previewIndex.value -= 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {}
}
const nextImage = async () => {
    if (previewIndex.value >= props.images.length - 1) {
        return;
    }
    previewIndex.value += 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {}
}
const onPreviewChange = (event) => {
    previewIndex.value = event.index;
}
</script>

<template>
    <div>
        <div class="relative w-full sm:w-[700px] sm:h-[700px] mb-4 group">
            <div v-if="previewIndex > 0" @click="prevImage"
                class="flex z-2 justify-center items-center h-full absolute top-0 bottom-0 w-16 left-0 bg-white/30 opacity-0 duration-300 group-hover:opacity-100 transition-opacity cursor-pointer">
                <IconChevron left lg/>
            </div>
            <Flicking :options="{ align: 'prev', circular: false, bound: true, preventDefaultOnDrag: true, duration: 200, deceleration: 1 }"
                      @changed="onPreviewChange"
                      ref="flicking">
                <div v-for="(image, i) in images" :key="i"
                     class="flicking-panel bg-gray-100 w-full flex justify-center items-center">
                    <img class="img" :src="image.gallery_image" alt="">
                </div>
            </Flicking>

            <div v-if="previewIndex < images.length - 1" @click="nextImage"
                class="flex z-2 justify-center items-center h-full absolute top-0 bottom-0 w-16 right-0 bg-white/30 opacity-0 duration-300 group-hover:opacity-100 transition-opacity cursor-pointer">
                <IconChevron right lg/>
            </div>
        </div>

        <div class="flex">
            <div v-for="(image, i) in images"
                 @click="selectImage(i)"
                 :class="{'border-primary border': i === previewIndex}"
                 class="mr-4 w-[100px] h-[100px] bg-gray-100 flex items-center justify-center cursor-pointer">
                <img :src="image.gallery_preview" alt="">
            </div>
        </div>
    </div>
</template>
<style>
.flicking-camera {
    align-items: stretch;
}
</style>