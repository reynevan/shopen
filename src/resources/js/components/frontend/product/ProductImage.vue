<script setup>
import { computed } from 'vue';

const props = defineProps({
    imageObject: {
        type: Object,
        required: true,
    },
    sizes: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        required: true,
    },
    loading: {
        type: String,
        default: 'lazy',
    },
    aspectRatio: {
        type: Number,
        default: 1,
    },
});

// Tworzymy string dla atrybutu srcset
const computedSrcset = computed(() => {
    if (!props.imageObject || Object.keys(props.imageObject).length === 0) {
        return '';
    }

    return Object.entries(props.imageObject)
        .map(([key, url]) => {
            const width = key;
            return { width, url };
        })
        .sort((a, b) => a.width - b.width)
        .map(item => `${item.url} ${item.width}`)
        .join(', ');
});

const fallbackSrc = computed(() => {
    return props.imageObject?.w300 || Object.values(props.imageObject)[0] || '';
});

const widthForSizing = 250;
const heightForSizing = computed(() => Math.round(widthForSizing / props.aspectRatio));

</script>

<template>
    <img
        v-if="computedSrcset"
        :src="fallbackSrc"
        :srcset="computedSrcset"
        :sizes="sizes"
        :alt="alt"
        :loading="loading"
        :width="widthForSizing"
        :height="heightForSizing"
        class="w-full h-auto"
    />
</template>