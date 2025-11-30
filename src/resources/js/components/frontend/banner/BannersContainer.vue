<script setup>
import Flicking from "@egjs/vue3-flicking";
import { defineProps, ref, useTemplateRef } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { AutoPlay, Pagination  } from "@egjs/flicking-plugins";

const props = defineProps({
    banners: {type: Array},
    highPriority: {type: Boolean, default: false},
});
const flicking = useTemplateRef("flicking");
const page = usePage();

const plugins = [
    //new AutoPlay({ duration: 4000, direction: "NEXT", stopOnHover: false }),
];
if (props.banners?.length > 1) {
    plugins.push(new Pagination({ type: 'bullet' }))
}
const options = {
    align: "prev",
    circularFallback: "bound",
    circular: true,
    preventDefaultOnDrag: true,
    duration: 200,
    deceleration: 1,
    threshold: 40,
    disableOnInit: props.banners?.length <= 1,
};

const previewIndex = ref(0);

const prevImage = async () => {
    if (previewIndex.value <= 0) return;
    previewIndex.value -= 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {}
};

const nextImage = async () => {
    if (previewIndex.value >= props.banners.length - 1) return;
    previewIndex.value += 1;
    try {
        await flicking.value.moveTo(previewIndex.value);
    } catch (e) {}
};

const onPreviewChange = (event) => {
    previewIndex.value = event.index;
};

const trackClick = (banner) => {
    if (navigator.sendBeacon) {
        const csrfToken = page.props.csrf_token;
        const formData = new FormData();
        formData.append("_token", csrfToken);
        navigator.sendBeacon(route("banners.track", { banner: banner.id }), formData);
    }
};
</script>

<template>
    <div v-if="banners?.length" class="relative w-full group overflow-hidden mb-4">
        <Flicking
            :options="options"
            :plugins="plugins"
            @changed="onPreviewChange"
            ref="flicking"
            class="w-full">
            <div v-for="(banner, i) in banners"
                :key="banner.id"
                class="w-full">
                <Link v-if="banner.link_url"
                    :href="banner.link_url"
                    @click="trackClick(banner)"
                    class="block flicking-panel bg-gray-100 w-full flex justify-center items-center">
                    <!-- Desktop -->
                    <img
                        class="max-w-full h-auto block"
                        :class="banner.image_url_mobile ? 'hidden sm:block' : ''"
                        :src="banner.image_url_desktop"
                        :alt="banner.alt_text"
                        :loading="i > 0 ? 'lazy' : 'eager'"
                        :fetchpriority="highPriority && i === 0 ? 'high' : null"
                        :width="banner.image_size_desktop?.width"
                        :height="banner.image_size_desktop?.height"
                    />
                    <!-- Mobile -->
                    <img v-if="banner.image_url_mobile"
                        class="block sm:hidden max-w-full h-auto"
                        :src="banner.image_url_mobile"
                        :alt="banner.alt_text"
                        :loading="i > 0 ? 'lazy' : 'eager'"
                        :fetchpriority="highPriority && i === 0 ? 'high' : null"
                        :width="banner.image_size_mobile?.width"
                        :height="banner.image_size_mobile?.height"
                    />
                </Link>

                <div v-else class="block flicking-panel bg-gray-100 w-full flex justify-center items-center">
                    <!-- Desktop -->
                    <img
                        class="max-w-full h-auto block"
                        :class="banner.image_url_mobile ? 'hidden sm:block' : ''"
                        :src="banner.image_url_desktop"
                        :alt="banner.alt_text"
                        :loading="i > 0 ? 'lazy' : 'eager'"
                        :fetchpriority="highPriority && i === 0 ? 'high' : null"
                        :width="banner.image_size_desktop?.width"
                        :height="banner.image_size_desktop?.height"
                    />
                    <!-- Mobile -->
                    <img v-if="banner.image_url_mobile"
                        class="block sm:hidden max-w-full h-auto"
                        :src="banner.image_url_mobile"
                        :alt="banner.alt_text"
                        :loading="i > 0 ? 'lazy' : 'eager'"
                        :fetchpriority="highPriority && i === 0 ? 'high' : null"
                        :width="banner.image_size_mobile?.width"
                        :height="banner.image_size_mobile?.height"
                    />
                </div>
            </div>
            <template #viewport>
                <div v-if="banners?.length > 1" class="flicking-pagination"></div>
            </template>
        </Flicking>
    </div>
</template>