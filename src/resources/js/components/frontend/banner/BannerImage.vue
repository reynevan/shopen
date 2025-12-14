<script setup>
    import {computed} from "vue";

    const props = defineProps({
        className: String|Array,
        src: String,
        urls: Object,
        banner: Object,
        fetchPriority: {type: String, nullable: true},
        loading: String,
        sizes: String
    })

    const computedSrcset = computed(() => {
        if (!props.urls || Object.keys(props.urls).length === 0) {
            return '';
        }

        return Object.entries(props.urls)
            .map(([key, url]) => {
                const width = key;
                return { width, url };
            })
            .sort((a, b) => a.width - b.width)
            .map(item => `${item.url} ${item.width}`)
            .join(', ');
    })

    const fallbackSrc = computed(() => {
        return Object.values(props.urls)[0] || '';
    })
</script>

<template>
    <img
        :class="className"
        :src="fallbackSrc"
        :sizes="sizes"
        :srcset="computedSrcset"
        :alt="banner.alt_text"
        :loading="loading"
        :fetchpriority="fetchPriority"
        :width="banner.image_size_desktop?.width"
        :height="banner.image_size_desktop?.height"/>
</template>