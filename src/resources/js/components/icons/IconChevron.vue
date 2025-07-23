<script setup>
    const props = defineProps({
        lg: {
            type: Boolean,
            default: false
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

    const size = props.lg ? 32 : 16;
</script>

<template>
    <svg v-if="left" xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" fill="currentColor"
         class="bi bi-chevron-compact-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd"
              d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223"/>
    </svg>
    <svg v-if="right" xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" fill="currentColor"
         class="bi bi-chevron-compact-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd"
              d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671"/>
    </svg>
    <svg v-if="up" xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>
    </svg>
    <svg v-if="down" xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
    </svg>
</template>