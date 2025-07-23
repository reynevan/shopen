<script setup>
import { Link } from '@inertiajs/vue3'
import OpenFiltersButton from "@shopen/components/frontend/category/Filters/OpenFiltersButton.vue";
import FiltersPanel from "@shopen/components/frontend/category/Filters/FiltersPanel.vue";
import ProductThumbnail from "@shopen/components/frontend/product/ProductThumbnail.vue";
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ActiveFilters from "@shopen/components/frontend/category/Filters/ActiveFilters.vue";
import IconSearch from "@shopen/components/icons/IconSearch.vue";
import BannersContainer from "../../../components/frontend/banner/BannersContainer.vue";

defineOptions({ layout: AppLayout })

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    activeFilters: { type: Object, default: () => ({}) },
    banners: {type: Object},
})

</script>

<template>
        <div class="container mx-auto px-4">
            <BannersContainer :banners="banners.category_page_top"/>

            <div class="sm:hidden mb-4">
                <OpenFiltersButton @onOpen="openMobileFilters" :totalActiveFiltersCount="totalActiveFiltersCount"/>
            </div>


            <div class="flex flex-col sm:flex-row gap-6">

                <!-- Filters Sidebar (Desktop) -->
                <div class="hidden sm:block sm:w-64 md:w-80 lg:flex-shrink-0">

                    <BannersContainer :banners="banners.category_page_filters_top"/>

                    <div class="hidden lg:block px-4 py-6">
                        <h2 class="text-xl font-semibold text-gray-900">Filtry</h2>
                    </div>

                    <FiltersPanel
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

                    <div class="mb-4 text-sm text-gray-600">
                        Znaleziono {{ products.meta.total }} produktów
                        <span v-if="hasActiveFilters">(po zastosowaniu filtrów)</span>
                    </div>


                    <BannersContainer :banners="banners.category_page_products_top"/>

                    <!-- Products Grid -->
                    <Transition name="fade" mode="out-in">
                        <div v-if="products.data.length > 0" :key="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">

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

                    <nav v-if="products.meta.links.length > 3" class="flex justify-center mt-8">
                        <div class="flex space-x-1">
                            <template v-for="(link, index) in products.meta.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    prefetch
                                    :class="[
                                        'px-4 py-2 text-sm rounded-md border transition-colors',
                                         link.active ? 'bg-blue-600 text-white border-blue-600'
                                            : link.url ? 'text-gray-700 border-gray-300 hover:bg-gray-50'
                                            : 'text-gray-400 border-gray-200 cursor-not-allowed'
                                      ]"
                                    :only="['products']"
                                    preserve-state
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </nav>
                </div>
            </div>

            <BannersContainer :banners="banners.category_page_bottom"/>
        </div>
        <Teleport to="body">
                <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="closeMobileFilters"></div>
                <div v-if="isMobileFiltersOpen" class="fixed inset-0 bg-gray-50 z-50 flex flex-col sm:hidden">
                    <header class="flex items-center justify-between p-4 border-b border-gray-200 bg-white sticky top-0">
                        <h2 class="text-lg font-semibold">Filtry</h2>
                        <button @click="closeMobileFilters" class="p-2 -mr-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                            <button @click="clearAllFilters" class="flex-1 px-4 py-3 bg-white border border-gray-300 rounded-lg text-center font-medium shadow-sm hover:bg-gray-100">
                                Wyczyść
                            </button>
                            <button @click="closeMobileFilters" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg text-center font-medium shadow-sm hover:bg-blue-700">
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