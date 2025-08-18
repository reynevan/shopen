<script setup>
import IconCartPlus from '~icons/material-symbols-light/add-shopping-cart-sharp'

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
    <IconCartPlus :class="sizeClass"/>
</template>