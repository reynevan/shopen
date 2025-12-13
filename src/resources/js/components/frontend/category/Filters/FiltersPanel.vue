<script setup>
import {ref, computed, onUnmounted, watch} from 'vue'
import Slider from '@vueform/slider'
import '@vueform/slider/themes/default.css'
import Filter from "./Filter.vue";
import {useProductFiltering} from "../../../../composables/useProductFiltering";
import Checkbox from "../../input/Checkbox.vue";

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
    return props.activeFilters.price?.options?.length
})

const stepPrice = computed(() => props.priceRange.max < 100 ? 1 : props.priceRange.max < 1000 ? 10 : 100)

// --- Metody ---

const { getActiveFilters } = useProductFiltering(props)

const activeOptionIdsByAttr = computed(() => {
    const out = Object.create(null)

    for (const [attr, data] of Object.entries(props.activeFilters || {})) {
        out[attr] = new Set((data?.options || []).map(o => o.id))
    }

    return out
})

const isFilterActive = (attributeSlug, optionId) => {
    return activeOptionIdsByAttr.value[attributeSlug]?.has(optionId) ?? false
}

const toggleFilter = (attributeCode, optionSlug) => {
    const currentFilters = getActiveFilters()

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
    return props.activeFilters[attributeCode]?.options?.length ?? 0
}

const clearAttributeFilter = (attributeCode) => {
    emits('onRemoveFilter', attributeCode);
}

const updatePriceFilter = () => {
    if (priceUpdateTimeout.value) clearTimeout(priceUpdateTimeout.value)

    priceUpdateTimeout.value = setTimeout(() => {
        const currentFilters = getActiveFilters()
        const [minPrice, maxPrice] = priceSliderValue.value

        if (minPrice === props.priceRange.min && maxPrice === props.priceRange.max) {
            delete currentFilters.price
        } else {
            if (minPrice !== props.priceRange.min) {
                currentFilters['cena-od'] = minPrice
            } else {
                delete currentFilters['cena-od']
            }
            if (maxPrice !== props.priceRange.max) {
                currentFilters['cena-do'] = maxPrice
            } else {
                delete currentFilters['cena-do']
            }
        }

        delete currentFilters.page

        emits('filterChange', currentFilters);

    }, 200)
}

const clearPriceFilter = () => {
    priceSliderValue.value = [props.priceRange.min, props.priceRange.max]
    const currentFilters = getActiveFilters()
    delete currentFilters['cena-od']
    delete currentFilters['cena-do']

    emits('filterChange', currentFilters);
}

// --- Watchers & Lifecycle ---

watch(() => props.activeFilters, (newFilters) => {
    const minPriceOption = newFilters.price?.options.find(option => option.key === 'price_min')
    const maxPriceOption = newFilters.price?.options.find(option => option.key === 'price_max')
    const minPrice = minPriceOption ? Number(minPriceOption.value) : props.priceRange.min
    const maxPrice = maxPriceOption ? Number(maxPriceOption.value) : props.priceRange.max
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
    <div class="filters-panel">
        <form method="get" data-ai="filters">
            <div class="flex flex-col sm:flex-row sm:items-center flex-wrap gap-4 sm:gap-0 divide-y divide-light sm:divide-y-0 px-4 sm:px-0">
                <Filter :attribute="{slug: 'price', name: 'Cena'}"
                        :is-mobile="isMobile"
                        @onClear="clearPriceFilter()"
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
                            :step="stepPrice"
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


                <!-- Filtry Atrybutów -->
                <Filter v-for="attribute in attributes"
                        :attribute="attribute"
                        :is-mobile="isMobile"
                        @onClear="clearAttributeFilter(attribute.slug)"
                        :active-filter-count="getActiveFilterCount(attribute.slug)"
                        :key="attribute.slug">
                    <div :class="attribute.options?.length >= 8 ? 'sm:grid sm:grid-cols-2 sm:grid-gap-4' : ''">
                        <template v-for="option in attribute.options"
                                  :key="option.slug">
                            <label
                                v-if="option.count > 0"
                                :for="`${attribute.slug}_${option.slug}`"
                                :data-value="option.value"
                                :data-count="option.count"
                                class="flex items-center space-x-3 cursor-pointer hover:bg-accent p-2 transition-colors whitespace-nowrap">
                                <Checkbox
                                    :id="`${attribute.slug}_${option.slug}`"
                                    :checked="isFilterActive(attribute.slug, option.id)"
                                    @change="toggleFilter(attribute.slug, option.slug)"
                                />
                                <span v-if="option.color"
                                      class="w-6 h-6 inline-block rounded border"
                                      :style="{'background-color': option.color}"></span>
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