<script setup>

import IconX from "@shopen/components/icons/IconX.vue";

const props = defineProps(['hasActiveFilters', 'activeFilters', 'attributes'])
const emits = defineEmits(['onClearFilters', 'onRemoveFilter']);

const clearAllFilters = () => {
    emits('onClearFilters');
}
const removeFilter = (key, value) => {
    emits('onRemoveFilter', key, value)
}

const getFilterDisplayName = (key, value) => {
    if (key === 'price_min') return value + ' zł'
    if (key === 'price_max') return value + ' zł'
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
    <div v-if="hasActiveFilters" class="mb-6 p-4 bg-accent rounded-lg" aria-labelledby="applied-filters">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-900" id="applied-filter">Aktywne filtry:</h3>
            <button @click="clearAllFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Wyczyść wszystkie
            </button>
        </div>

        <div class="flex items-center gap-2">
            <template v-for="(values, key) in activeFilters" :key="key">
                <div v-if="(Array.isArray(values) && values.length > 0) || (!Array.isArray(values) && values)"
                     class="flex items-center" >
                    <div class="text-sm font-medium text-gray-700 mr-2">
                        {{ getAttributeName(key) }}:
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <template v-if="Array.isArray(values)">
                            <button v-for="value in values"
                                    :key="`${key}-${value}`"
                                    :data-facet="getAttributeName(key)"
                                    :data-value="value"
                                    @click="removeFilter(key, value)"
                                    class="inline-flex group cursor-pointer items-center px-3 py-1 rounded text-sm  transition-colors border border-accent-200">
                                {{ getFilterDisplayName(key, value) }}
                                <IconX md/>

                            </button>
                        </template>
                        <template v-else >
                            <button @click="removeFilter(key, values)"
                                    :data-facet="getAttributeName(key)"
                                    :data-value="values"
                                    class="inline-flex group cursor-pointer items-center px-3 py-1 rounded text-sm  transition-colors border border-accent-200">
                                {{ getFilterDisplayName(key, values) }}
                                <IconX md/>
                          </button>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>