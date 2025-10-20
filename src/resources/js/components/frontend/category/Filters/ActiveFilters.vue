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

const getFilterDisplayName = (key, value) => {
    if (key === 'price_min') return 'Od ' + value + ' zł'
    if (key === 'price_max') return 'Do ' + value + ' zł'
    const attribute = props.attributes.find(attr => attr.slug === key)
    if (!attribute) return value

    if (attribute.options) {
        const option = attribute.options.find(opt => opt.slug === value.toString())
        return option ? option.value : value
    }

    return value
}

const getAttributeName = (key) => {
    if (key === 'price_min') return 'Cena od'
    if (key === 'price_max') return 'Cena do'

    const attribute = props.attributes.find(attr => attr.slug === key)
    return attribute ? attribute.name : key
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
            <template v-for="(values, key) in activeFilters" :key="key">
                <div v-if="(Array.isArray(values) && values.length > 0) || (!Array.isArray(values) && values)"
                     class="flex items-center" >
                    <div class="flex flex-wrap gap-2">
                        <template v-if="Array.isArray(values)">
                            <ActiveFilter v-for="value in values"
                                          :key="`${key}-${value}`"
                                          :label="getFilterDisplayName(key, value)"
                                          :value="value"
                                          :attributeName="getAttributeName(key)"
                                          @onClick="removeFilter(key, value)"/>
                        </template>
                        <template v-else >
                            <ActiveFilter :label="getFilterDisplayName(key, values)"
                                          :value="values"
                                          :attributeName="getAttributeName(key)"
                                          @onClick="removeFilter(key, values)"/>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>