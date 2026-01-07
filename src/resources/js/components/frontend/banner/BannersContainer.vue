<script setup>
import Flicking from "@egjs/vue3-flicking";
import { defineProps, useTemplateRef } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { AutoPlay, Pagination  } from "@egjs/flicking-plugins";
import BannerContent from "@shopen/components/frontend/banner/BannerContent.vue";

const props = defineProps({
    banners: {type: Array},
    highPriority: {type: Boolean, default: false},
});
const flicking = useTemplateRef("flicking");
const page = usePage();

const plugins = [
    new AutoPlay({ duration: 4000, direction: "NEXT", stopOnHover: false }),
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
            ref="flicking"
            class="w-full">
            <div v-for="(banner, i) in banners"
                :key="banner.id"
                class="w-full">
                <component
                    :is="banner.link_url ? Link : 'div'"
                    :prefetch="banner.link_url ? true : null"
                    :href="banner.link_url || undefined"
                    @click="banner.link_url ? trackClick(banner) : undefined"
                    class="block flicking-panel bg-gray-100 w-full flex justify-center items-center">
                    <BannerContent
                        :banner="banner"
                        :index="i"
                        :high-priority="highPriority"
                    />
                </component>
            </div>
            <template #viewport>
                <div v-if="banners?.length > 1" class="flicking-pagination"></div>
            </template>
        </Flicking>
    </div>
</template>