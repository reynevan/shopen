<script setup>
import Icon from '~icons/mdi-light/phone'

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