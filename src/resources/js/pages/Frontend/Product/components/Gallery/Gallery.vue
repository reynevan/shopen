<script setup>
import Flicking from "@egjs/vue3-flicking";
import {defineProps, ref, useTemplateRef, watch, onMounted, onUnmounted} from 'vue';
import IconChevron from "@shopen/components/icons/IconChevron.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import PreviewImage from "./PreviewImage.vue";
import GalleryImage from "./GalleryImage.vue";
import {useBodyScrollLock} from "../../../../../composables/useBodyScrollLock";

const props = defineProps(['images', 'product']);
const flicking = useTemplateRef('flicking');
const modalFlicking = useTemplateRef('modalFlicking');

const previewIndex = ref(0);
const isModalOpen = ref(false);
const maxPreviewImages = ref(5)

const scrollLock = useBodyScrollLock()

if (typeof window !== 'undefined' && window.innerWidth < 600) {
    maxPreviewImages.value = 4;
}
if (typeof window !== 'undefined' && window.innerWidth < 480) {
    maxPreviewImages.value = 3;
}

const selectImage = (index) => {
    flicking.value.moveTo(index);
    previewIndex.value = index;
};


function disableScroll() {
    scrollLock.lock()
}

function enableScroll() {
    scrollLock.unlock()
}

const openModal = (index) => {
    disableScroll();
    previewIndex.value = index;
    isModalOpen.value = true;
    setTimeout(() => {
        modalFlicking.value?.moveTo(index);
    }, 50);
};

const closeModal = () => {
    enableScroll();
    isModalOpen.value = false;
};

const prevImage = async () => {
    if (previewIndex.value <= 0) return;
    previewIndex.value -= 1;
    try {
        flicking.value?.moveTo(previewIndex.value);
        modalFlicking.value?.moveTo(previewIndex.value);
    } catch (e) {
    }
};

const nextImage = async () => {
    if (previewIndex.value >= props.images.length - 1) return;
    previewIndex.value += 1;
    try {
        flicking.value?.moveTo(previewIndex.value);
        modalFlicking.value?.moveTo(previewIndex.value);
    } catch (e) {
    }
};

const onPreviewChange = (event) => {
    previewIndex.value = event.index;
    modalFlicking.value?.moveTo(event.index);
};

const onModalPreviewChange = (event) => {
    previewIndex.value = event.index;
    flicking.value?.moveTo(event.index);
};

const handleKeydown = (e) => {
    if (!isModalOpen.value) return;

    switch (e.key) {
        case 'Escape':
            closeModal();
            break;
        case 'ArrowLeft':
            e.preventDefault();
            prevImage();
            break;
        case 'ArrowRight':
            e.preventDefault();
            nextImage();
            break;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});


</script>

<template>
    <div>
        <div class="relative w-full h-full sm:max-w-[572px] sm:max-h-[572px] mb-4 group aspect-square">
            <div v-if="previewIndex > 0" @click="prevImage"
                 class="flex z-2 justify-center items-center h-full absolute top-0 bottom-0 left-4">
                <div
                    class="p-4 rounded bg-white shadow hover:shadow-lg opacity-0 duration-300 group-hover:opacity-100 transition-all cursor-pointer">
                    <IconChevron left md/>
                </div>
            </div>
            <Flicking
                :options="{ align: 'prev', circular: false, bound: true, preventDefaultOnDrag: true, duration: 200, deceleration: 1, renderOnlyVisible: true}"
                @changed="onPreviewChange"
                ref="flicking">
                <div v-for="(image, i) in images"
                     :key="i"
                     @click="openModal(i)"
                     class="flicking-panel bg-gray-100 w-full flex justify-center items-center">
                    <GalleryImage :image="image.gallery_image" :alt="product.attributes?.name" :main="i === 0"/>
                </div>
            </Flicking>
            <div v-if="previewIndex < images.length - 1" @click="nextImage"
                 class="flex z-2 justify-center items-center h-full absolute top-0 bottom-0 right-4">
                <div
                    class="p-4 rounded bg-white shadow hover:shadow-lg opacity-0 duration-300 group-hover:opacity-100 transition-all cursor-pointer">
                    <IconChevron right md/>
                </div>
            </div>
        </div>
        <div class="flex" v-if="images && images.length > 1">
            <template v-for="(image, i) in images">
                <div v-if="i < (maxPreviewImages - 1 ) || i === (maxPreviewImages - 1) && images.length === maxPreviewImages"
                     @click="selectImage(i)"
                     :class="{'border-strong': i === previewIndex}"
                     class="mr-[5px] w-[109px] h-[109px] hover:shadow transition-all duration-300 border bg-gray-100
                            flex items-center justify-center cursor-pointer box-content">
                    <PreviewImage :image="image.gallery_preview" :alt="product.attributes?.name"/>
                </div>
                <div v-if="i === (maxPreviewImages - 1) && images.length > maxPreviewImages"
                    @click="openModal(i)"
                    class="mr-[5px] w-[109px] h-[109px] hover:shadow transition-all duration-300 border bg-gray-100
                    flex items-center justify-center cursor-pointer box-content relative">
                    <div class="absolute top-0 left-0 right-0 bottom-0 bg-white/80 flex items-center justify-center text-xl z-1">
                        + {{ images.length - (maxPreviewImages - 1 ) }}
                    </div>
                    <PreviewImage :image="image.gallery_preview" :alt="product.attributes?.name"/>
                </div>
            </template>
        </div>

        <transition name="fade">
            <div v-if="isModalOpen"
                 class="fixed inset-0 z-50 bg-white bg-opacity-90 flex flex-col md:flex-row">

                <button @click="closeModal"
                        class="absolute top-4 right-4 text-black text-3xl font-bold z-60 hover:scale-110 transition cursor-pointer">
                    <IconX lg/>
                </button>

                <div class="order-2 md:order-1 flex-shrink-0 flex flex-row md:flex-col justify-start md:justify-center w-full md:w-auto md:h-screen items-center gap-2 p-4 overflow-x-auto md:overflow-y-auto">
                    <div v-for="(image, i) in images"
                         :key="i"
                         @click="modalFlicking.moveTo(i); previewIndex = i"
                         :class="['cursor-pointer border-2 rounded transition-all', i === previewIndex ? 'border-strong' : 'border-transparent']"
                         class="w-20 h-20 flex-shrink-0 flex items-center justify-center bg-gray-100 box-content">

                        <PreviewImage :image="image.gallery_preview"/>
                    </div>
                </div>

                <div class="relative flex-1 order-1 md:order-2 flex items-center justify-center p-4 min-h-0">
                    <div v-if="previewIndex > 0"
                         @click="prevImage"
                         class="hidden md:block absolute left-8 top-1/2 -translate-y-1/2 z-60 cursor-pointer">
                        <div class="p-4 rounded bg-white shadow hover:shadow-lg duration-300 transition-all cursor-pointer">
                            <IconChevron left md/>
                        </div>
                    </div>
                    <div v-if="previewIndex < images.length - 1"
                         @click="nextImage"
                         class="hidden md:block absolute right-8 top-1/2 -translate-y-1/2 z-60 cursor-pointer">
                        <div class="p-4 rounded bg-white shadow hover:shadow-lg duration-300 transition-all cursor-pointer">
                            <IconChevron right md/>
                        </div>
                    </div>

                    <Flicking
                        :options="{ align: 'center', circular: false, bound: true, preventDefaultOnDrag: true, duration: 200, deceleration: 1 }"
                        @changed="onModalPreviewChange"
                        ref="modalFlicking"
                        class="w-full h-full"
                    >
                        <div v-for="(image, i) in images" :key="i"
                             class="flicking-panel flex items-center justify-center w-full h-full">
                            <img :src="image.original"
                                 class="max-h-full max-w-full object-contain rounded shadow-lg">
                        </div>
                    </Flicking>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
.flicking-camera {
    align-items: stretch;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>