<script setup>
import Icon from '~icons/material-symbols-light/add-home-work-outline'

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
    <Icon :class="sizeClass"/>
</template>