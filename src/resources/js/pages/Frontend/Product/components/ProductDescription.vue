<script setup>
    defineProps({
        description: {type: String},
    })
</script>

<template>
    <div>
        <div class="prose max-w-none" v-html="description"></div>
    </div>
</template>
