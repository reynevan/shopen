<script setup>
import {computed} from "vue";

const props = defineProps({
    image: {type: Object, default: null},
    alt: {
        type: String,
        required: true,
    },
})

const computedSrcset = computed(() => {
    if (!props.image || Object.keys(props.image).length === 0) {
        return '';
    }

    return Object.entries(props.image)
        .map(([key, url]) => {
            const width = key;
            return { width, url };
        })
        .sort((a, b) => a.width - b.width)
        .map(item => `${item.url} ${item.width}`)
        .join(', ');
});
const fallbackSrc = computed(() => {
    return props.image?.w443 || Object.values(props.image)[0] || '';
});
</script>

<template>
    <img
        v-if="computedSrcset"
        :src="fallbackSrc"
        :srcset="computedSrcset"
        :alt="alt"
        width="443"
        height="375"
        class="object-cover w-full"
        loading="lazy"
    />
</template>