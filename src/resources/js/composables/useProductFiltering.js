import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useProductFiltering(props) {

    const getActiveFilters = () => {
        let filters = {};
        Object.entries(props.activeFilters).forEach(
            ([key, filter]) => {
                if (key === 'price') {
                    for (let i = 1; i < filter.options; i++) {
                        filters[filter.options[i].slug] = filter.options[i].value
                    }
                } else {
                    filters[filter.slug] = filter.options.map((v) => v.slug)
                }
            }
        );

        return filters
    }

    const hasActiveFilters = computed(() => {
        return Object.keys(props.activeFilters).some(key => {
            const value = props.activeFilters[key]
            return Array.isArray(value) ? value.length > 0 : Boolean(value)
        })
    });

    const prepareFiltersForUrl = (filters, sort) => {
        const urlFilters = {};
        for (const key in filters) {
            if (filters[key] === null || (Array.isArray(filters[key]) && filters[key].length === 0)) {
                continue;
            }
            if (key === 'cena-od') {
                urlFilters['cena-od'] = filters[key];
            } else if (key === 'cena-do') {
                urlFilters['cena-do'] = filters[key]
            } else {
                urlFilters[key] = Array.isArray(filters[key]) ? filters[key].join(',') : filters[key];
            }
        }
        if (sort) {
            urlFilters.sort = sort;
        }
        if (props.searchQuery) {
            urlFilters.q = props.searchQuery;
        }
        return urlFilters
    }

    const onSortChange = (value) => {
        let sort = value === 'default' ? null : value;
        router.get(window.location.pathname, prepareFiltersForUrl(getActiveFilters(), sort), {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'activeSort']
        })
    }

    const onFilterChange = (filters) => {
        const urlFilters = prepareFiltersForUrl(filters, props.activeSort)
        router.get(window.location.pathname, urlFilters, {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'activeFilters', 'filters', 'searchQuery']
        })
    }

    const clearAllFilters = () => {
        const query = {};
        if (props.activeSort) {
            query.sort = props.activeSort;
        }
        router.get(window.location.pathname, query, {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'activeFilters', 'filters', 'searchQuery']
        })
    }

    const removeFilter = (filterKey, valueToRemove = null) => {
        const currentFilters = getActiveFilters()

        if (filterKey === 'cena-od') {
            delete currentFilters['cena-od']
        } else if (filterKey === 'cena-do') {
            delete currentFilters['cena-do']
        } else if (valueToRemove === null) {
            delete currentFilters[filterKey]
        } else {
            const currentValue = currentFilters[filterKey]
            if (Array.isArray(currentValue)) {
                const newValues = currentValue.filter(v => v.toString() !== valueToRemove.toString())
                currentFilters[filterKey] = newValues.length > 0 ? newValues : null;
            }
        }
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] === null || (Array.isArray(currentFilters[key]) && currentFilters[key].length === 0)) {
                delete currentFilters[key];
            }
        });

        onFilterChange(currentFilters);
    }

    return {
        hasActiveFilters,
        onSortChange,
        onFilterChange,
        clearAllFilters,
        removeFilter,
        getActiveFilters
    }
}