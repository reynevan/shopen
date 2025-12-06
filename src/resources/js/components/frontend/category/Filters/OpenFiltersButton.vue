<script setup>

    import IconFilter from "../../../icons/IconFilter.vue";

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
        <span class="filters-button-label flex gap-2 items-center">
            <IconFilter size="xl"/>
            Filtry
        </span>
    </button>
</template>