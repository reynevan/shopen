<script setup>
import { ref, computed } from 'vue'
import { useProductFiltering } from "@shopen/composables/useProductFiltering.js"; // Popraw ścieżkę!

// Importuj wszystkie potrzebne komponenty
import OpenFiltersButton from "@shopen/components/frontend/category/Filters/OpenFiltersButton.vue";
import FiltersPanel from "@shopen/components/frontend/category/Filters/FiltersPanel.vue";
import ProductThumbnail from "@shopen/components/frontend/product/thumbnail/ProductThumbnail.vue";
import ActiveFilters from "@shopen/components/frontend/category/Filters/ActiveFilters.vue";
import SortSelect from "@shopen/components/frontend/category/SortSelect.vue";
import Pagination from "@shopen/components/frontend/ui/Pagination.vue";

// Definiujemy propsy, które będą wspólne dla obu widoków
const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    activeFilters: { type: Object, default: () => ({}) },
    activeSort: { type: String },
    sortOptions: { type: Array },
    searchQuery: { type: String }
    // Nie definiujemy tutaj `category` ani `searchQuery`, bo będą one używane w komponentach nadrzędnych
});

// Używamy naszego composable do logiki
const { hasActiveFilters, onSortChange, onFilterChange, clearAllFilters, removeFilter } = useProductFiltering(props);

// Logika specyficzna dla UI (stan mobilnych filtrów) pozostaje tutaj
const isMobileFiltersOpen = ref(false)
const openMobileFilters = () => isMobileFiltersOpen.value = true
const closeMobileFilters = () => isMobileFiltersOpen.value = false

// Pozostałe computed properties
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
const totalActiveFiltersCount = computed(() => Object.keys(props.activeFilters).length);

</script>

<template>
    <div class="container mx-auto px-4">
        <!-- Slot na bannery na górze strony -->
        <slot name="page-top-banners"></slot>

        <div class="lg:hidden mb-4">
            <OpenFiltersButton @onOpen="openMobileFilters" :totalActiveFiltersCount="totalActiveFiltersCount"/>
        </div>

        <div class="mb-4">
            <!-- Slot na główny nagłówek strony (np. nazwa kategorii lub info o wyszukiwaniu) -->
            <slot name="header" :resultsCount="resultsCount"></slot>
        </div>

        <div class="flex flex-col sm:flex-row gap-6">
            <!-- Filters Sidebar -->
            <div class="hidden lg:block sm:w-64 md:w-80 lg:flex-shrink-0 px-4" aria-labelledby="filters-title">
                <!-- Slot na dodatkowe elementy w sidebarze (np. lista podkategorii) -->
                <slot name="sidebar-prepend"></slot>

                <div class="hidden lg:block py-6">
                    <h2 class="text-xl font-semibold text-gray-900" id="filters-title">Filtry</h2>
                </div>

                <FiltersPanel
                    @filterChange="onFilterChange"
                    :attributes="filters.attributes"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                />

                <!-- Slot na bannery pod filtrami -->
                <slot name="sidebar-append"></slot>
            </div>

            <!-- Główna kolumna z produktami -->
            <div class="flex-1 min-w-0">
                <ActiveFilters
                    :activeFilters="activeFilters"
                    :hasActiveFilters="hasActiveFilters"
                    :attributes="filters.attributes"
                    @onClearFilters="clearAllFilters"
                    @onRemoveFilter="removeFilter"/>

                <div class="flex flex-col sm:flex-row justify-end sm:items-center gap-4 mb-4">
                    <SortSelect @onChange="onSortChange" :sortOptions="sortOptions" :activeSort="activeSort"/>
                </div>

                <!-- Slot na treść przed listą produktów (np. obrazek kategorii) -->
                <slot name="before-products"></slot>

                <!-- Products Grid -->
                <Transition name="fade" mode="out-in">
                    <div v-if="products.data.length > 0" :key="products.data.length"
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <ProductThumbnail v-for="product in products.data" :key="product.id" :product="product"/>
                    </div>
                </Transition>

                <!-- No results -->
                <div v-if="!products.data.length" key="no-products" class="text-center py-12">
                    <!-- ... treść gdy brak wyników ... -->
                </div>

                <!-- Slot na treść po liście produktów (np. bannery, opis SEO kategorii) -->
                <slot name="after-products"></slot>

                <Pagination :links="products.meta.links" :only="['products']"/>
            </div>
        </div>

        <!-- Slot na bannery na dole strony -->
        <slot name="page-bottom-banners"></slot>
    </div>

    <!-- Mobile Filters Panel (Teleport) -->
    <Teleport to="body">
        <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="closeMobileFilters"></div>
        <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-body z-50 flex flex-col sm:hidden">
            <header class="flex items-center justify-between p-4 border-b border-light bg-white sticky top-0">
                <h2 class="text-lg font-semibold">Filtry</h2>
                <button @click="closeMobileFilters" class="p-2 mr-2 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </header>

            <div class="flex-1 overflow-y-auto">
                <FiltersPanel
                    @filterChange="onFilterChange"
                    :attributes="filters.attributes"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                />
            </div>

            <div class="p-4 border-t border-light bg-body sticky bottom-0">
                <div class="flex gap-4">
                    <button @click="clearAllFilters"
                            class="flex-1 px-4 py-3 border border-light rounded-lg text-center font-medium shadow-sm hover:bg-gray-100">
                        Wyczyść
                    </button>
                    <button @click="closeMobileFilters"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg text-center font-medium shadow-sm hover:bg-blue-700">
                        Pokaż wyniki ({{ products.meta.total }})
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>