<script setup>
import {ref, computed, onUnmounted, watch} from 'vue'
import Slider from '@vueform/slider'
import '@vueform/slider/themes/default.css'

const props = defineProps({
    attributes: {
        type: Array,
        required: true
    },
    activeFilters: {
        type: Object,
        default: () => ({})
    },
    priceRange: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value.min === 'number' && typeof value.max === 'number'
        }
    }
})

const emits = defineEmits(['filterChange'])

// Stan filtra ceny
const priceSliderValue = ref([props.priceRange.min, props.priceRange.max])
const priceUpdateTimeout = ref(null)

const hasPriceFilter = computed(() => {
    return props.activeFilters.price_min || props.activeFilters.price_max
})


// --- Metody ---

const isFilterActive = (attributeCode, optionSlug) => {
    const activeValue = props.activeFilters[attributeCode]
    if (!activeValue || !Array.isArray(activeValue)) return false
    return activeValue.includes(optionSlug)
}

const toggleFilter = (attributeCode, optionSlug) => {
    const currentFilters = JSON.parse(JSON.stringify(props.activeFilters))

    if (!currentFilters[attributeCode]) {
        currentFilters[attributeCode] = [optionSlug]
    } else {
        const index = currentFilters[attributeCode].indexOf(optionSlug)
        if (index > -1) {
            currentFilters[attributeCode].splice(index, 1)
            if (currentFilters[attributeCode].length === 0) {
                delete currentFilters[attributeCode]
            }
        } else {
            currentFilters[attributeCode].push(optionSlug)
        }
    }

    delete currentFilters.page
    emits('filterChange', currentFilters);
}

const getActiveFilterCount = (attributeCode) => {
    const activeValue = props.activeFilters[attributeCode]
    return Array.isArray(activeValue) ? activeValue.length : 0
}

const clearAttributeFilter = (attributeCode) => {
    const currentFilters = {...props.activeFilters}
    delete currentFilters[attributeCode]
    delete currentFilters.page

    emits('filterChange', currentFilters);
}

const updatePriceFilter = () => {
    if (priceUpdateTimeout.value) clearTimeout(priceUpdateTimeout.value)

    priceUpdateTimeout.value = setTimeout(() => {
        const currentFilters = {...props.activeFilters}
        const [minPrice, maxPrice] = priceSliderValue.value

        if (minPrice === props.priceRange.min && maxPrice === props.priceRange.max) {
            delete currentFilters.price_min
            delete currentFilters.price_max
        } else {
            if (minPrice !== props.priceRange.min) {
                currentFilters.price_min = minPrice
            } else {
                delete currentFilters.price_min
            }
            if (maxPrice !== props.priceRange.max) {
                currentFilters.price_max = maxPrice
            } else {
                delete currentFilters.price_max
            }
        }

        delete currentFilters.page

        emits('filterChange', currentFilters);

    }, 200)
}

const clearPriceFilter = () => {
    priceSliderValue.value = [props.priceRange.min, props.priceRange.max]
    const currentFilters = {...props.activeFilters}
    delete currentFilters.price_min
    delete currentFilters.price_max
    delete currentFilters.page

    emits('filterChange', currentFilters);
}

// --- Watchers & Lifecycle ---

watch(() => props.activeFilters, (newFilters) => {
    const minPrice = newFilters.price_min ? Number(newFilters.price_min) : props.priceRange.min
    const maxPrice = newFilters.price_max ? Number(newFilters.price_max) : props.priceRange.max
    priceSliderValue.value = [minPrice, maxPrice]
}, {immediate: true, deep: true})

watch(() => props.priceRange, (newRange) => {
    if (!hasPriceFilter.value) {
        priceSliderValue.value = [newRange.min, newRange.max]
    }
}, {immediate: true, deep: true})

onUnmounted(() => {
    if (priceUpdateTimeout.value) clearTimeout(priceUpdateTimeout.value)
})

</script>

<template>
    <form method="get" data-ai="filters">
        <div class="pb-6 space-y-6 px-4">
            <!-- Filtr Ceny -->
            <fieldset data-facet="price" data-label="cena" class="space-y-3">
                <div class="flex items-center justify-between bg-accent px-3 py-2 rounded">
                    <legend class="font-medium text-gray-900">
                        Cena
                        <span v-if="hasPriceFilter"
                              class="ml-2 text-xs bg-blue-600 text-white px-2 py-1 rounded-full">1</span>
                    </legend>
                    <button v-if="hasPriceFilter" @click="clearPriceFilter"
                            class="text-xs text-red-600 hover:text-red-800 font-medium">
                        wyczyść
                    </button>
                </div>
                <div class="pl-3 space-y-4">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ priceSliderValue[0] }} zł</span>
                        <span>{{ priceSliderValue[1] }} zł</span>
                    </div>
                    <div class="px-2">
                        <Slider
                            v-model="priceSliderValue"
                            :min="priceRange.min"
                            :max="priceRange.max"
                            :step="1"
                            :format="(value) => `${Math.round(value)} zł`"
                            show-tooltip="drag"
                            @update="updatePriceFilter"
                            class="price-slider"
                        />
                    </div>
                    <div class="text-xs text-gray-500 text-center">
                        Zakres: {{ priceRange.min }} zł - {{ priceRange.max }} zł
                    </div>
                </div>
            </fieldset>

            <!-- Filtry Atrybutów -->
            <fieldset :data-facet="attribute.slug" :data-label="attribute.name" v-for="attribute in attributes" :key="attribute.slug" class="space-y-3">
                <div class="flex items-center justify-between bg-accent px-3 py-2 rounded">
                    <legend class="font-medium text-gray-900">
                        {{ attribute.name }}
                        <span v-if="getActiveFilterCount(attribute.slug) > 0"
                              class="ml-2 text-xs bg-blue-600 text-white px-2 py-1 rounded-full">
                        {{ getActiveFilterCount(attribute.slug) }}
                    </span>
                    </legend>
                    <button v-if="getActiveFilterCount(attribute.slug) > 0"
                            @click="clearAttributeFilter(attribute.slug)"
                            class="text-xs text-red-600 hover:text-red-800 font-medium">
                        wyczyść
                    </button>
                </div>
                <div class="space-y-2 pl-3">
                    <template v-for="option in attribute.options"
                              :key="option.slug">
                        <label
                               v-if="option.count > 0"
                               :for="`${attribute.slug}_${option.slug}`"
                               :data-value="option.value"
                               :data-count="option.count"
                               class="flex items-center space-x-3 cursor-pointer hover:bg-accent p-2 rounded transition-colors">
                            <input
                                :id="`${attribute.slug}_${option.slug}`"
                                type="checkbox"
                                :checked="isFilterActive(attribute.slug, option.slug)"
                                @change="toggleFilter(attribute.slug, option.slug)"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                            >
                            <span v-if="option.color" class="w-6 h-6 inline-block rounded border" :style="{'background-color': option.color}"></span>
                            <span class="flex-1 text-sm text-gray-700">
                                {{ option.value }}
                                <data :value="option.count" class="text-gray-500 ml-1">({{ option.count }})</data>
                            </span>
                        </label>
                    </template>
                </div>
            </fieldset>
        </div>
    </form>
</template>