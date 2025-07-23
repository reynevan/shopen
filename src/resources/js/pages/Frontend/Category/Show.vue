<script setup>
import {computed, ref} from 'vue'
import {router, Link, Head} from '@inertiajs/vue3'
import OpenFiltersButton from "@shopen/components/frontend/category/Filters/OpenFiltersButton.vue";
import FiltersPanel from "@shopen/components/frontend/category/Filters/FiltersPanel.vue";
import ProductThumbnail from "@shopen/components/frontend/product/ProductThumbnail.vue";
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ActiveFilters from "@shopen/components/frontend/category/Filters/ActiveFilters.vue";
import IconSearch from "@shopen/components/icons/IconSearch.vue";
import BannersContainer from "../../../components/frontend/banner/BannersContainer.vue";
import SortSelect from "../../../components/frontend/category/SortSelect.vue";
import Pagination from "../../../components/frontend/ui/Pagination.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    products: {type: Object, required: true},
    filters: {type: Object, required: true},
    activeFilters: {type: Object, default: () => ({})},
    activeSort: {type: String},
    banners: {type: Object},
    category: {type: Object},
    subcategories: {type: Array},
    sortOptions: {type: Array}
})

const isMobileFiltersOpen = ref(false)
const openMobileFilters = () => isMobileFiltersOpen.value = true
const closeMobileFilters = () => isMobileFiltersOpen.value = false


const hasActiveFilters = computed(() => {
    return Object.keys(props.activeFilters).some(key => {
        const value = props.activeFilters[key]
        return Array.isArray(value) ? value.length > 0 : Boolean(value)
    })
})

const resultsCount = computed(() => {
    const total = props.products.meta.total;
    if (total === 0) {
        return 'Brak wyników';
    }
    if (total === 1) {
        return '1 wynik'
    }
    if (total > 1 && total <= 4) {
        return total + ' wyniki';
    }
    if (total <= 21) {
        return total + ' wyników';
    }
    if (total % 10 >= 2 && total % 10 <= 4) {
        return total + ' wyniki';
    }
    return total + ' wyników';
})

const prepareFiltersForUrl = (filters, sort) => {

    const urlFilters = [];
    for (const key in filters) {
        if (key === 'price_min') {
            urlFilters['cena_od'] = filters[key];
        } else if (key === 'price_max') {
            urlFilters['cena_do'] = filters[key]
        } else {
            urlFilters[key] = filters[key].join(',');
        }
    }
    if (sort) {
        urlFilters.sort = sort;
    }
    return urlFilters
}

const onSortChange = (value) => {
    let sort = value;
    if (value === 'default') {
        sort = null;
    }

    router.get(window.location.pathname, prepareFiltersForUrl(props.activeFilters, sort), {
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
        only: ['products', 'activeFilters', 'filters']
    })
}

const clearAllFilters = () => {
    // ZMIANA: Usuwamy wszystkie filtry, ale zachowujemy sortowanie, jeśli jest ustawione
    const query = {};
    if (props.activeSort) {
        query.sort = props.activeSort;
    }
    router.get(window.location.pathname, query, {
        preserveState: true,
        preserveScroll: true,
        only: ['products', 'activeFilters', 'filters']
    })
}

const removeFilter = (filterKey, valueToRemove = null) => {
    const currentFilters = {...props.activeFilters}

    if (filterKey === 'price') {
        delete currentFilters.price_min
        delete currentFilters.price_max
    } else if (valueToRemove === null) {
        delete currentFilters[filterKey]
    } else {
        const currentValue = currentFilters[filterKey]

        if (Array.isArray(currentValue)) {
            const newValues = currentValue.filter(v => v.toString() !== valueToRemove.toString())
            if (newValues.length === 0) {
                delete currentFilters[filterKey]
            } else {
                currentFilters[filterKey] = newValues
            }
        } else if (currentValue && currentValue.toString() === valueToRemove.toString()) {
            delete currentFilters[filterKey]
        }
    }

    onFilterChange(currentFilters);
}


const totalActiveFiltersCount = computed(() => {
    // ZMIANA: Ignorujemy parametr 'sort' przy liczeniu aktywnych filtrów
    return Object.keys(props.activeFilters).filter(key => key !== 'sort').length;
});

</script>

<template>
    <Head>
        <meta name="title" :content="category.seo.seo_title">
        <meta name="description" :content="category.seo.seo_description">
    </Head>
    <div class="container mx-auto px-4">
        <BannersContainer :banners="banners.category_page_top"/>

        <div class="sm:hidden mb-4">
            <OpenFiltersButton @onOpen="openMobileFilters" :totalActiveFiltersCount="totalActiveFiltersCount"/>
        </div>

        <div class="mb-4">
            <div class="flex items-end">
                <div class="text-3xl mr-2">{{ category.name }}</div>
                <div class="text-neutral-600">({{ resultsCount }})</div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-6">

            <!-- Filters Sidebar (Desktop) -->
            <div class="hidden sm:block sm:w-64 md:w-80 lg:flex-shrink-0 px-4">


                <div v-for="subcategory in subcategories">
                    <Link :href="subcategory.url">{{ subcategory.name }}</Link>
                </div>

                <BannersContainer :banners="banners.category_page_filters_top"/>
                <div class="hidden lg:block py-6">
                    <h2 class="text-xl font-semibold text-gray-900">Filtry</h2>
                </div>

                <FiltersPanel
                    @filterChange="onFilterChange"
                    :attributes="filters.attributes"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                />
                <BannersContainer :banners="banners.category_page_filters_bottom"/>
            </div>

            <div class="flex-1 min-w-0">

                <ActiveFilters
                    :activeFilters="activeFilters"
                    :hasActiveFilters="hasActiveFilters"
                    :attributes="filters.attributes"
                    @onClearFilters="clearAllFilters"
                    @onRemoveFilter="removeFilter"/>

                <div class="flex flex-col sm:flex-row justify-end sm:items-center gap-4 mb-4">

                    <SortSelect @onChange="onSortChange" :sortOptions="sortOptions"/>
                </div>


                <img class="hidden sm:block" :src="category.image_url_desktop" v-if="category.image_url_desktop"
                     :alt="category.name"/>
                <img class="block sm:hidden" :src="category.image_url_mobile" v-if="category.image_url_mobile"
                     :alt="category.name"/>

                <BannersContainer :banners="banners.category_page_products_top"/>

                <!-- Products Grid -->
                <Transition name="fade" mode="out-in">
                    <div v-if="products.data.length > 0" :key="products.data.length"
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">

                        <TransitionGroup name="list" tag="div" class="contents">
                            <ProductThumbnail
                                v-for="product in products.data"
                                :key="product.id"
                                :product="product"
                            />
                        </TransitionGroup>
                    </div>
                </Transition>

                <div v-if="!products.data.length" key="no-products" class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <div class="flex justify-center text-gray-400 mb-4">
                            <IconSearch lg/>
                        </div>
                        <p class="text-gray-500 text-lg mb-4">
                            Brak produktów spełniających kryteria.
                        </p>
                        <button
                            v-if="hasActiveFilters"
                            @click="clearAllFilters"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Wyczyść wszystkie filtry
                        </button>
                    </div>
                </div>

                <BannersContainer :banners="banners.category_page_products_bottom"/>

                <Pagination :links="products.meta.links" :only="['products']"/>
            </div>
        </div>

        <BannersContainer :banners="banners.category_page_bottom"/>

        <div v-if="category.description" v-html="category.description"></div>
    </div>
    <Teleport to="body">
        <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40"
             @click="closeMobileFilters"></div>
        <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-gray-50 z-50 flex flex-col sm:hidden">
            <header class="flex items-center justify-between p-4 border-b border-gray-200 bg-white sticky top-0">
                <h2 class="text-lg font-semibold">Filtry</h2>
                <button @click="closeMobileFilters" class="p-2 -mr-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </header>

            <div class="flex-1 overflow-y-auto">
                <FiltersPanel
                    :attributes="filters.attributes.data"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                />
            </div>

            <footer class="p-4 border-t border-gray-200 bg-white sticky bottom-0">
                <div class="flex gap-4">
                    <button @click="clearAllFilters"
                            class="flex-1 px-4 py-3 bg-white border border-gray-300 rounded-lg text-center font-medium shadow-sm hover:bg-gray-100">
                        Wyczyść
                    </button>
                    <button @click="closeMobileFilters"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg text-center font-medium shadow-sm hover:bg-blue-700">
                        Pokaż wyniki ({{ products.meta.total }})
                    </button>
                </div>
            </footer>
        </div>
    </Teleport>
</template>
<style scoped>
/* Smooth transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.list-enter-active {
    transition: all 0.3s ease;
}

.list-leave-active {
    transition: all 0.3s ease;
    position: absolute;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.list-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

.list-move {
    transition: transform 0.3s ease;
}
</style>