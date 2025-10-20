<script setup>

    defineProps(['totalActiveFiltersCount'])

    const emits = defineEmits(['onOpen'])

    const onOpen = () => {
        emits('onOpen');
    }
</script>

<template>
    <button @click="onOpen"
        class="w-full px-4 py-2 bg-white flex items-center justify-center space-x-2 transition-colors">
        <span v-if="totalActiveFiltersCount > 0"
              class="text-xs bg-accent px-2 py-1 flex items-center justify-center">
                {{ totalActiveFiltersCount }}
        </span>
        <span class="text-sm">Filtry</span>
    </button>
</template>

<style scoped>

</style>