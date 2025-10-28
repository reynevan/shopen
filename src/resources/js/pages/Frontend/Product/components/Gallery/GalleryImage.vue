<script setup>
import {computed} from "vue";

const emits = defineEmits(['onClick'])
const props = defineProps({
    image: {type: Object},
    alt: {type: String},
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
        .sort((a, b) => a.width < b.width ? -1 : 1)
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
         class="img cursor-zoom-in max-w-full max-h-[700px]"
         @click="emits('onClick')"
         :alt="alt">
</template>