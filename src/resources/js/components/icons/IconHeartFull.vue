<script setup>
import IconHeartFull from '~icons/material-symbols-light/favorite'

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
    <IconHeartFull :class="sizeClass"/>
</template>