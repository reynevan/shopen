<script setup>
import IconCartEmpty from '~icons/material-symbols-light/shopping-cart-off-outline-sharp'

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
    <IconCartEmpty :class="sizeClass"/>
</template>