<script setup>
    defineProps(['title', 'description'])
</script>

<template>
    <header class="my-4 pb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl">{{ title }}</h1>
            <slot name="action"/>
        </div>
        <p v-if="description">{{ description }}</p>
    </header>
</template>