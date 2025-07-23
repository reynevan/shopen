import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useProductFiltersStore = defineStore('productFilters', () => {
    // State
    const filters = ref([])
    const priceFilters = ref({ min: null, max: null })
    const priceSliderValues = ref([0, 0])
    const attributes = ref([])
    const priceRange = ref({ min: 0, max: 0 })

    // Getters
    const hasActiveFilters = computed(() =>
        filters.value.length > 0 || priceFilters.value.min || priceFilters.value.max
    )

    const filtersPayload = computed(() => {
        const payload = {}

        filters.value.forEach(filter => {
            if (!payload[filter.attribute]) {
                payload[filter.attribute] = []
            }
            payload[filter.attribute].push(filter.value)
        })

        const pricePayload = {}
        if (priceFilters.value.min) pricePayload.min = priceFilters.value.min
        if (priceFilters.value.max) pricePayload.max = priceFilters.value.max

        if (pricePayload.min || pricePayload.max) {
            payload.price = pricePayload
        }

        return payload
    })

    // Actions
    const addFilter = (attribute, value, attributeLabel, label) => {
        const existingIndex = filters.value.findIndex(
            f => f.attribute === attribute && f.value === value
        )

        if (existingIndex === -1) {
            filters.value.push({
                attribute,
                value,
                attribute_label: attributeLabel,
                label
            })
        }
    }

    const removeFilter = (attribute, value) => {
        const index = filters.value.findIndex(
            f => f.attribute === attribute && f.value == value
        )

        if (index !== -1) {
            filters.value.splice(index, 1)
        }
    }

    const toggleFilter = (attribute, option) => {
        const existingIndex = filters.value.findIndex(
            f => f.attribute === attribute.code && f.value == option.id
        )

        if (existingIndex !== -1) {
            filters.value.splice(existingIndex, 1)
        } else {
            addFilter(attribute.code, option.id, attribute.name, option.value)
        }
    }

    const isFilterSelected = (attribute, option) => {
        return filters.value.some(
            f => f.attribute === attribute.code && f.value == option.id
        )
    }

    const setPriceFilters = (min, max) => {
        priceFilters.value = { min, max }
    }

    const setMinPrice = (value) => {
        priceFilters.value.min = value
    }

    const setMaxPrice = (value) => {
        priceFilters.value.max = value
    }

    const setPriceSliderValues = (values) => {
        priceSliderValues.value = [...values]
    }

    const updatePriceRange = (responsePrice) => {
        const wasEmpty = priceRange.value.min === 0 && priceRange.value.max === 0

        priceRange.value = responsePrice

        // Jeśli to pierwsze załadowanie i nie mamy wartości z URL
        if (wasEmpty && !priceFilters.value.min && !priceFilters.value.max) {
            priceSliderValues.value = [responsePrice.min, responsePrice.max]
        } else {
            // Synchronizuj slider z aktualnymi filtrami
            syncSliderWithFilters()
        }
    }

    const syncSliderWithFilters = () => {
        const minValue = priceFilters.value.min ?? priceRange.value.min
        const maxValue = priceFilters.value.max ?? priceRange.value.max
        priceSliderValues.value = [minValue, maxValue]
    }

    const clearAllFilters = () => {
        filters.value = []
        priceFilters.value = { min: null, max: null }
        priceSliderValues.value = [priceRange.value.min, priceRange.value.max]
    }

    const clearPriceFilters = () => {
        priceFilters.value = { min: null, max: null }
        priceSliderValues.value = [priceRange.value.min, priceRange.value.max]
    }

    const clearMinPrice = () => {
        priceFilters.value.min = null
        priceSliderValues.value[0] = priceRange.value.min
    }

    const clearMaxPrice = () => {
        priceFilters.value.max = null
        priceSliderValues.value[1] = priceRange.value.max
    }

    const loadAttributes = async (categoryId) => {
        try {
            const response = await axios.get(`/api/categories/${categoryId}/filters`, {
                params: { filters: filtersPayload.value }
            })

            attributes.value = response.data.attributes
            updatePriceRange(response.data.price)

            enrichFiltersWithLabels()
        } catch (error) {
            console.error('Failed to load filters:', error)
        }
    }

    const enrichFiltersWithLabels = () => {
        const findAttributeByCode = (code) =>
            attributes.value.find(attr => attr.code === code)

        const findOptionById = (attribute, optionId) =>
            attribute.options?.find(option => option.id == optionId)

        filters.value = filters.value
            .map(filter => {
                if (filter.attribute_label && filter.label) {
                    return filter
                }

                const attribute = findAttributeByCode(filter.attribute)
                if (!attribute) return filter

                return {
                    ...filter,
                    attribute_label: attribute.name,
                    label: findOptionById(attribute, filter.value)?.value || filter.label
                }
            })
            .filter(filter => !!filter.label)
    }

    const initializeFromUrl = () => {
        const url = new URL(window.location.href)

        for (const [attribute, value] of url.searchParams.entries()) {
            if (['page', 'sort'].includes(attribute)) continue

            if (attribute === 'cena_od') {
                priceFilters.value.min = parseInt(value)
            } else if (attribute === 'cena_do') {
                priceFilters.value.max = parseInt(value)
            } else {
                filters.value.push({ attribute, value })
            }
        }
    }

    return {
        // State
        filters,
        priceFilters,
        priceSliderValues,
        attributes,
        priceRange,

        // Getters
        hasActiveFilters,
        filtersPayload,

        // Actions
        addFilter,
        removeFilter,
        toggleFilter,
        isFilterSelected,
        setPriceFilters,
        setMinPrice,
        setMaxPrice,
        setPriceSliderValues,
        clearAllFilters,
        clearPriceFilters,
        clearMinPrice,
        clearMaxPrice,
        loadAttributes,
        initializeFromUrl,
        syncSliderWithFilters,
    }
})