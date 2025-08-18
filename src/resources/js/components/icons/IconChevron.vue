<script setup>
import IconArrowRight from '~icons/material-symbols-light/keyboard-arrow-right'
import IconArrowLeft from '~icons/material-symbols-light/keyboard-arrow-left'
import IconArrowUp from '~icons/material-symbols-light/keyboard-arrow-up'
import IconArrowDown from '~icons/material-symbols-light/keyboard-arrow-down'

import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md'
    },
    right: {
        type: Boolean,
        default: false
    },
    left: {
        type: Boolean,
        default: false
    },
    up: {
        type: Boolean,
        default: false
    },
    down: {
        type: Boolean,
        default: false
    },
})

const sizeClass = computed(() => 'text-' + props.size);
</script>

<template>
    <IconArrowRight v-if="right" :class="sizeClass"/>
    <IconArrowLeft v-if="left" :class="sizeClass"/>
    <IconArrowUp v-if="up" :class="sizeClass"/>
    <IconArrowDown v-if="down" :class="sizeClass"/>
</template>