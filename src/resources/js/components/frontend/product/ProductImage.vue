<script setup>
import { computed } from 'vue';
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";

const props = defineProps({
    urls: {
        type: Object,
        required: false,
    },
    sizes: {
        type: String,
    },
    alt: {
        type: String,
        required: false,
    },
    loading: {
        type: String,
        default: 'lazy',
    },
    aspectRatio: {
        type: Number,
        default: 1,
    },
    width: {
        type: Number,
        default: 250
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
    return props.urls?.w300 || Object.values(props.urls)[0] || '';
});

const widthForSizing = props.width;
const heightForSizing = computed(() => Math.round(widthForSizing / props.aspectRatio));

</script>

<template>
    <img
        v-if="urls && computedSrcset"
        :src="fallbackSrc"
        :srcset="computedSrcset"
        :sizes="sizes"
        :alt="alt"
        :loading="loading"
        :width="widthForSizing"
        :height="heightForSizing"
        class="w-full h-auto"
    />
    <div v-else class="w-full h-full bg-neutral-50 text-neutral-300 flex items-center justify-center"
         :style="{width: width+'px', height: width+'px'}">
        <IconNoImage size="2xl"/>
    </div>
</template>