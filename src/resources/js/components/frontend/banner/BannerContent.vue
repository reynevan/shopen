<script setup>
import BannerImage from "@shopen/components/frontend/banner/BannerImage.vue";

defineProps({
    banner: Object,
    index: Number,
    highPriority: Boolean,
});
</script>

<template>
    <!-- Desktop -->
    <BannerImage
        :banner="banner"
        :class-name="[
            banner.image_url_mobile ? 'hidden sm:block' : 'block',
            'max-w-full h-auto'
        ]"
        :urls="banner.desktop_urls"
        sizes="(min-width: 1920px) 1920px, 100vw"
        :loading="index > 0 ? 'lazy' : 'eager'"
        :fetch-priority="highPriority && index === 0 ? 'high' : null"
    />
    <!-- Mobile -->
    <BannerImage
        v-if="Object.keys(banner.mobile_urls).length > 0"
        :banner="banner"
        class-name="block sm:hidden max-w-full h-auto"
        :urls="banner.mobile_urls"
        sizes="100vw"
        :loading="index > 0 ? 'lazy' : 'eager'"
        :fetch-priority="highPriority && index === 0 ? 'high' : null"
    />
</template>