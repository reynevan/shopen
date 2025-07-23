<script setup>

import IconLoader from "@shopen/components/icons/IconLoader.vue";

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    class: {
        type: String
    },
    type: {
        default: 'primary',
        type: String
    }
})
</script>

<template>
    <button :class="[props.class, 'button-' + type, 'flex items-center justify-center h-12']" :disabled="disabled || loading">
        <IconLoader md v-if="loading"></IconLoader>
        <span v-else>
            <slot/>
        </span>
    </button>
</template>