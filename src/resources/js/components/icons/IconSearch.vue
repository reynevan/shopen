<script setup>
import IconSearch from '~icons/material-symbols-light/search-rounded'

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
    <IconSearch :class="sizeClass"/>
</template>