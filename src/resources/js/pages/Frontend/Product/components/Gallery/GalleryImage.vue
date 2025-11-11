<script setup>
import {computed} from "vue";

const emits = defineEmits(['onClick'])
const props = defineProps({
    image: {type: Object},
    alt: {type: String},
    main: {type: Boolean},
})

const computedSrcset = computed(() => {
    if (!props.image || Object.keys(props.image).length === 0) {
        return '';
    }

    return Object.entries(props.image)
        .map(([key, url]) => {
            const width = key;
            return {width, url};
        })
        .map(item => `${item.url} ${item.width}`)
        .join(', ');
});
const fallbackSrc = computed(() => {
    return Object.values(props.image)[0] || '';
});
</script>

<template>
    <img :srcset="computedSrcset"
         :src="fallbackSrc"
         :fetchpriority="main ? 'high' : null"
         :loading="main ? 'eager' : 'lazy'"
         sizes="(min-width: 1285px) 572px, (min-width: 768px) calc(50vw - 72px), (min-width: 670px) 572px, (min-width: 640px) calc(100vw - 96px),  calc(100vw - 64px)"
         class="img cursor-zoom-in max-w-full max-h-[572px]"

         :alt="alt">
</template>