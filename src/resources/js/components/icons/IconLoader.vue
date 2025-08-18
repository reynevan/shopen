<script setup>
import IconSpinner from '~icons/svg-spinners/180-ring-with-bg'

import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md'
    }
})

const sizeClass = computed(() => 'text-' + props.size);
</script>

<template>
    <IconSpinner :class="sizeClass"/>
</template>