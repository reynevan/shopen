<script setup>
    defineProps(['title', 'description'])
</script>

<template>
    <header class="my-4 pb-4">
        <h1 class="text-3xl">{{ title }}</h1>
        <p v-if="description">{{ description }}</p>
    </header>
</template>