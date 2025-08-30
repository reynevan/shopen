<script setup>
import {computed} from "vue";

const props = defineProps({
    brand: {type: Object}
})

const computedSrcset = computed(() => {
    if (!props.brand.logo || Object.keys(props.brand.logo).length === 0) {
        return '';
    }

    return Object.entries(props.brand.logo)
        .map(([key, url]) => {
            const width = key;
            return { width, url };
        })
        .sort((a, b) => a.width - b.width)
        .map(item => `${item.url} ${item.width}`)
        .join(', ');
});

const fallbackSrc = computed(() => {
    return Object.values(props.brand.logo)[0] || '';
});


</script>

<template>
    <div class="h-[80px] px-4 group">
        <img
            class="max-h-full grayscale group-hover:grayscale-0 transition-all"
            v-if="brand.logo && computedSrcset"
            :src="fallbackSrc"
            :srcset="computedSrcset"
            :alt="brand.name"
            :height="80"
        />
    </div>
</template>