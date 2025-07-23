<script setup>
    const props = defineProps({
        lg: {
            type: Boolean,
            default: false
        }
    })

    const size = props.lg ? 32 : 16;
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/>
    </svg>
</template>