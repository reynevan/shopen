<script setup>
import { computed } from 'vue';

const props = defineProps({
    urls: {
        type: Object
    },
    sizes: {
        type: String
    },
    alt: {
        type: String
    },
    loading: {
        type: String,
        default: 'lazy',
    },
    width: {
        type: Number
    },
    height: {
        type: Number
    },
    class: {
        type: String,
    }
});

// Tworzymy string dla atrybutu srcset
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
});

const fallbackSrc = computed(() => {
    return Object.values(props.urls)[0] || '';
});


</script>

<template>
    <img
        v-if="urls && computedSrcset"
        :src="fallbackSrc"
        :srcset="computedSrcset"
        :sizes="sizes"
        :alt="alt"
        :loading="loading"
        :width="width"
        :height="height"
        :class="props.class"
    />
</template>