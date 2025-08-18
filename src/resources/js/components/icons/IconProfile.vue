<script setup>
import IconProfile from '~icons/mdi-light/account'

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
    <IconProfile :class="sizeClass"/>
</template>