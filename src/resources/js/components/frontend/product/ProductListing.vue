<script setup>
import {ref, computed, watch, onBeforeUnmount, onMounted, onUnmounted} from 'vue'
import {useProductFiltering} from "@shopen/composables/useProductFiltering.js";

import OpenFiltersButton from "@shopen/components/frontend/category/Filters/OpenFiltersButton.vue";
import FiltersPanel from "@shopen/components/frontend/category/Filters/FiltersPanel.vue";
import ProductThumbnail from "@shopen/components/frontend/product/thumbnail/ProductThumbnail.vue";
import ActiveFilters from "@shopen/components/frontend/category/Filters/ActiveFilters.vue";
import SortSelect from "@shopen/components/frontend/category/SortSelect.vue";
import Pagination from "@shopen/components/frontend/ui/Pagination.vue";
import {trackSelectItem, trackViewItemList} from "../../../utils/ga4";
import {router} from "@inertiajs/vue3";
import Button from "../ui/Button.vue";
import IconX from "../../icons/IconX.vue";

const props = defineProps({
    products: {type: Object, required: true},
    categories: {type: Array},
    filters: {type: Object, required: true},
    activeFilters: {type: Object, default: () => ({})},
    activeSort: {type: String},
    sortOptions: {type: Array},
    searchQuery: {type: String},
    listName: {type: String, default: ''},
    listId: {type: String},
});

let routerTrackListener = null;
onMounted(() => {
    if (props.listName || props.listId) {
        routerTrackListener = router.on('success', (e) => {
            trackViewItemList(props.products.data, props.listName, props.listId)
        })
    }
})
onBeforeUnmount(() => {
    routerTrackListener && routerTrackListener()
})

const trackProductSelect = (product) => {
    if (props.listName) {
        trackSelectItem(product, props.listName)
    }
}

const {hasActiveFilters, onSortChange, onFilterChange, clearAllFilters, removeFilter} = useProductFiltering(props);

const isMobileFiltersOpen = ref(false)

const openMobileFilters = () => {
    isMobileFiltersOpen.value = true
    document.body.classList.add('overflow-hidden');
}
const closeMobileFilters = () => {
    isMobileFiltersOpen.value = false
    document.body.classList.remove('overflow-hidden');
}

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


import { useScrollDirection } from '@shopen/composables/useScrollDirection.js'
const { isScrollingUp } = useScrollDirection()

const stickyElement = ref(null)
const targetElement = ref(null)
const isOverTarget = ref(false)

const checkOverlap = () => {
    if (!stickyElement.value || !targetElement.value) return

    const stickyRect = stickyElement.value.getBoundingClientRect()
    const targetRect = targetElement.value.getBoundingClientRect()

    // Sprawdź czy sticky element nakłada się na target element
    isOverTarget.value = (
        stickyRect.bottom > targetRect.top &&
        stickyRect.top < targetRect.bottom
    )
}

onMounted(() => {
    window.addEventListener('scroll', checkOverlap)
    checkOverlap()
})

onUnmounted(() => {
    window.removeEventListener('scroll', checkOverlap)
})

</script>

<template>
    <div class="product-listing">
        <!-- Slot na bannery na górze strony -->
        <slot name="page-top-banners"></slot>

        <div class="mb-4">
            <slot name="header" :resultsCount="resultsCount"></slot>
        </div>

        <div>
            <!-- Filters Sidebar -->
            <div class="hidden sm:block">
                <slot name="sidebar-prepend"></slot>
            </div>
            <div class="hidden sm:block top-0 z-1" ref="stickyElement"
                 :class="[
                     isOverTarget ? 'shadow-lg' : '',
                     isScrollingUp ? 'relative' : 'sticky'
                 ]">
                <FiltersPanel
                    @filterChange="onFilterChange"
                    @onRemoveFilter="removeFilter"
                    :attributes="filters.attributes"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                    :categories="categories"
                />
            </div>
            <div class="hidden sm:block">
                <slot name="sidebar-append"></slot>
            </div>

            <!-- Główna kolumna z produktami -->
            <div>
                <div class="sm:hidden mb-4 sticky top-0 z-10">
                    <OpenFiltersButton @onOpen="openMobileFilters" :totalActiveFiltersCount="totalActiveFiltersCount"/>
                </div>

                <div class="flex justify-center sm:justify-between items-start gap-6">
                    <div class="hidden sm:block">
                        <ActiveFilters
                            :activeFilters="activeFilters"
                            :hasActiveFilters="hasActiveFilters"
                            :attributes="filters.attributes"
                            @onClearFilters="clearAllFilters"
                            @onRemoveFilter="removeFilter"/>
                    </div>
                    <div v-if="products.data?.length"
                         class="flex justify-end items-center w-full sm:w-sm mb-4">
                        <SortSelect @onChange="onSortChange" :sortOptions="sortOptions" :activeSort="activeSort"/>
                    </div>
                </div>

                <!-- Slot na treść przed listą produktów (np. obrazek kategorii) -->
                <slot name="before-products"></slot>

                <!-- Products Grid -->
                <Transition name="fade" mode="out-in">
                    <div v-if="products.data.length > 0"
                         :key="products.data.length"
                         ref="targetElement"
                         class="grid grid-cols-1 sm:grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-1 mb-8">
                        <ProductThumbnail v-for="product in products.data"
                                          :key="product.id"
                                          @onClick="trackProductSelect(product)"
                                          :product="product"/>
                    </div>
                </Transition>

                <!-- No results -->
                <div v-if="!products.data.length" key="no-products" class="text-center py-12">
                    <!-- ... treść gdy brak wyników ... -->
                </div>

            </div>
        </div>
        <div>
            <div>
                <Pagination :links="products.meta.links" :only="['products']"/>

                <slot name="after-products"></slot>
            </div>
        </div>

        <!-- Slot na bannery na dole strony -->
        <slot name="page-bottom-banners"></slot>
    </div>

    <!-- Mobile Filters Panel (Teleport) -->
    <Teleport to="body">
        <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-body z-50 flex flex-col sm:hidden justify-between overflow-y-auto">
            <div>
                <div class="sm:hidden px-4 py-2 mb-4 border-b border-light flex items-center justify-between">
                    <span class="text-2xl">Filtry</span>
                    <IconX size="2xl" @click="closeMobileFilters"/>
                </div>

                <ActiveFilters
                    :activeFilters="activeFilters"
                    :hasActiveFilters="hasActiveFilters"
                    :attributes="filters.attributes"
                    @onClearFilters="clearAllFilters"
                    @onRemoveFilter="removeFilter"/>

                <FiltersPanel
                    is-mobile
                    @filterChange="onFilterChange"
                    :attributes="filters.attributes"
                    :active-filters="activeFilters"
                    :price-range="filters.priceRange"
                    :categories="categories"
                />
            </div>
            <div class="p-4 border-t border-light bg-body sticky bottom-0">
                <div class="flex gap-4">
                    <Button type="primary" full-width size="md" @click="clearAllFilters">
                        Wyczyść
                    </Button>
                    <Button type="secondary" full-width size="md" @click="closeMobileFilters">
                        Pokaż wyniki ({{ products.meta.total }})
                    </Button>
                </div>
            </div>
        </div>
    </Teleport>
</template>