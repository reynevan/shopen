<script setup>
import Flicking from "@egjs/vue3-flicking";
import {defineProps, ref, useTemplateRef} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import IconChevron from "../../icons/IconChevron.vue";

const props = defineProps(['banners']);
const flicking = useTemplateRef('flicking');
const page = usePage();

const options = {
    align: 'prev',
    circularFallback: 'bound',
    circular: true,
    preventDefaultOnDrag: true,
    duration: 200,
    deceleration: 1,
    threshold: 40,
    disableOnInit: props.banners.length <= 1
};

const previewIndex = ref(0);

const prevImage = async () => {
    if (previewIndex.value <= 0) {
        return;
    }
    previewIndex.value -= 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {
    }
}
const nextImage = async () => {
    if (previewIndex.value >= props.banners.length - 1) {
        return;
    }
    previewIndex.value += 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {
    }
}
const onPreviewChange = (event) => {
    previewIndex.value = event.index;
}

const trackClick = (banner) => {
    if (navigator.sendBeacon) {
        const csrfToken = page.props.csrf_token;
        const formData = new FormData();
        formData.append('_token', csrfToken);
        navigator.sendBeacon(route('banners.track', {banner: banner.id}), formData);
    }
};
</script>

<template>
    <div class="relative w-full mb-4 group">
        <div v-if="banners.length > 1" @click="prevImage"
             class="flex z-2 justify-center items-center absolute top-0 bottom-0 w-16 left-0 bg-white/30 opacity-0 duration-300 group-hover:opacity-100 transition-opacity cursor-pointer">
            <IconChevron left lg/>
        </div>
        <Flicking :options="options"
                  @changed="onPreviewChange"
                  ref="flicking">
            <Link :href="banner.link_url" v-for="(banner, i) in banners" :key="i"
                  @click="trackClick(banner)"
                  class="block flicking-panel bg-gray-100 w-full flex justify-center items-center">
                <img class="img hidden sm:block" :src="banner.image_url_desktop" :alt="banner.alt_text" :loading="i > 0 ? 'lazy' : 'eager'">
                <img class="img block sm:hidden" :src="banner.image_url_mobile" :alt="banner.alt_text" :loading="i > 0 ? 'lazy' : 'eager'">
            </Link>
        </Flicking>

        <div v-if="banners.length > 1" @click="nextImage"
             class="flex z-2 justify-center items-center absolute top-0 bottom-0 w-16 right-0 bg-white/30 opacity-0 duration-300 group-hover:opacity-100 transition-opacity cursor-pointer">
            <IconChevron right lg/>
        </div>
    </div>
</template>

<style scoped>

</style>