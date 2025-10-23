<script setup>

import IconX from "@shopen/components/icons/IconX.vue";
import ActiveFilter from "./ActiveFilter.vue";

const props = defineProps(['hasActiveFilters', 'activeFilters', 'attributes'])
const emits = defineEmits(['onClearFilters', 'onRemoveFilter']);

const clearAllFilters = () => {
    emits('onClearFilters');
}
const removeFilter = (key, value) => {
    emits('onRemoveFilter', key, value)
}

</script>

<template>
    <div v-if="hasActiveFilters" class="mb-6 px-4 sm:px-0" aria-labelledby="applied-filters">
        <div class="flex flex-wrap items-center gap-2">
            <button @click="clearAllFilters"
                    class="sm:text-sm flex items-center gap-1 cursor-pointer border-2 border-accent-hover hover:bg-accent-hover transition-all duration-300 pl-2 pr-1 py-1 whitespace-nowrap">
                <span>Wyczyść wszystkie filtry</span>
                <span class="sm:pt-1"><IconX/></span>
            </button>
            <template v-for="(filter, key) in activeFilters" :key="key">
                <div class="flex items-center" >
                    <div class="flex flex-wrap gap-2">
                        <ActiveFilter v-for="option in filter.options"
                                      :key="`${key}-${option.value}`"
                                      :label="option.label"
                                      :attributeName="filter.name"
                                      @onClick="removeFilter(filter.slug, option.slug)"/>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>