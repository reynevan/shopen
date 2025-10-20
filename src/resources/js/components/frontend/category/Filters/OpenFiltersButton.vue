<script setup>

    defineProps(['totalActiveFiltersCount'])

    const emits = defineEmits(['onOpen'])

    const onOpen = () => {
        emits('onOpen');
    }
</script>

<template>
    <button @click="onOpen"
        class="filters-button w-full flex items-center justify-center space-x-2">
        <span v-if="totalActiveFiltersCount > 0"
              class="filters-button-count flex items-center justify-center">
                {{ totalActiveFiltersCount }}
        </span>
        <span class="filters-button-label">Filtry</span>
    </button>
</template>