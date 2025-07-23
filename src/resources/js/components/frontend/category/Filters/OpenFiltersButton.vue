<script setup>

    defineProps(['totalActiveFiltersCount'])

    const emits = defineEmits(['onOpen'])

    const onOpen = () => {
        emits('onOpen');
    }
</script>

<template>
    <button @click="onOpen"
        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg flex items-center justify-center space-x-2 shadow-sm hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" /></svg>
        <span class="font-medium">Filtry</span>
        <span v-if="totalActiveFiltersCount > 0" class="bg-blue-600 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full">
                {{ totalActiveFiltersCount }}
        </span>
    </button>
</template>

<style scoped>

</style>