<script setup>
import {ref, computed, onUnmounted, watch} from 'vue'
import {Link} from "@inertiajs/vue3";
import Slider from '@vueform/slider'
import '@vueform/slider/themes/default.css'
import Filter from "./Filter.vue";

const props = defineProps({
    attributes: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
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
    },
    isMobile: {
        type: Boolean,
        default: false
    }
})

const emits = defineEmits(['filterChange', 'onRemoveFilter'])

// Stan filtra ceny
const priceSliderValue = ref([props.priceRange.min, props.priceRange.max])
const priceUpdateTimeout = ref(null)

const priceFiltersCount = computed(() => {
    return (props.activeFilters.price_min ? 1 : 0) + (props.activeFilters.price_max ? 1 : 0)
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
    emits('onRemoveFilter', attributeCode);
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
    if (!priceFiltersCount.value) {
        priceSliderValue.value = [newRange.min, newRange.max]
    }
}, {immediate: true, deep: true})

onUnmounted(() => {
    if (priceUpdateTimeout.value) clearTimeout(priceUpdateTimeout.value)
})

</script>

<template>
    <div class="filters-panel px-4">
        <form method="get" data-ai="filters">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-0 divide-y divide-border-light sm:divide-y-0">
                <Filter :attribute="{slug: 'price', name: 'Cena'}"
                        :is-mobile="isMobile"
                        @onClear="clearAttributeFilter('price')"
                        :active-filter-count="priceFiltersCount">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ priceSliderValue[0] }} zł</span>
                        <span>{{ priceSliderValue[1] }} zł</span>
                    </div>
                    <div class="px-2 my-2">
                        <Slider
                            v-model="priceSliderValue"
                            :min="priceRange.min"
                            :max="priceRange.max"
                            :step="1"
                            :format="(value) => `${Math.round(value)} zł`"
                            show-tooltip="drag"
                            @change="updatePriceFilter"
                            class="price-slider"
                        />
                    </div>
                    <div class="text-xs text-gray-500 text-center">
                        Zakres: {{ priceRange.min }} zł - {{ priceRange.max }} zł
                    </div>
                </Filter>

                <Filter :attribute="{slug: 'category', name: 'Kategorie'}"
                        :is-mobile="isMobile"
                        @onClear="clearAttributeFilter('category')"
                        :active-filter-count="0">
                    <div>
                        <Link v-for="category in categories"
                              :href="category.url"
                              class="block p-2 text-sm font-light hover:text-black hover:bg-accent transition-all">
                            {{category.name}}
                        </Link>
                    </div>
                </Filter>


                <!-- Filtry Atrybutów -->
                <Filter v-for="attribute in attributes"
                        :attribute="attribute"
                        :is-mobile="isMobile"
                        @onClear="clearAttributeFilter(attribute.slug)"
                        :active-filter-count="getActiveFilterCount(attribute.slug)"
                        :key="attribute.slug">
                    <div :class="attribute.options?.length >= 8 ? 'grid grid-cols-2 grid-gap-4' : ''">
                        <template v-for="option in attribute.options"
                                  :key="option.slug">
                            <label
                                v-if="option.count > 0"
                                :for="`${attribute.slug}_${option.slug}`"
                                :data-value="option.value"
                                :data-count="option.count"
                                class="flex items-center space-x-3 cursor-pointer hover:bg-accent p-2 transition-colors">
                                <input
                                    :id="`${attribute.slug}_${option.slug}`"
                                    type="checkbox"
                                    :checked="isFilterActive(attribute.slug, option.slug)"
                                    @change="toggleFilter(attribute.slug, option.slug)"
                                    class="w-4 h-4 text-black border-gray-300 rounded"
                                >
                                <span v-if="option.color" class="w-6 h-6 inline-block rounded border" :style="{'background-color': option.color}"></span>
                                <span class="flex-1 text-sm font-light text-gray-700">
                                    {{ option.value }}
                                    <data :value="option.count" class="text-gray-500 ml-1">({{ option.count }})</data>
                                </span>
                            </label>
                        </template>
                    </div>
                </Filter>
            </div>
        </form>
    </div>
</template>