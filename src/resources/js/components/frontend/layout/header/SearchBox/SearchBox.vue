<script setup>
import {computed, ref} from 'vue'
import {router} from '@inertiajs/vue3'
import IconSearch from "@shopen/components/icons/IconSearch.vue";
import ProductSearchResultItem from "./ProductSearchResultItem.vue";
import IconLoader from "../../../../icons/IconLoader.vue";
import {trackSearch} from "../../../../../utils/ga4";
import IconChevron from "../../../../icons/IconChevron.vue";
import IconX from "../../../../icons/IconX.vue";
import {useBodyScrollLock} from "../../../../../composables/useBodyScrollLock";

const searchQuery = ref('')
const searchResults = ref({products: [], categories: []})
const showResults = ref(false)
const isLoading = ref(false)
const mobileViewOpen = ref(false)
let debounceTimer = null
const bodyScrollLock = useBodyScrollLock()

const props = defineProps({
    minSearchLength: {type: Number, default: 2},
    debounceDelay: {type: Number, default: 300}
})

const emit = defineEmits(['search', 'productSelected', 'categorySelected'])

const searchProducts = async (query) => {
    if (query.length < props.minSearchLength) {
        searchResults.value = {products: [], categories: []}
        showResults.value = false
        return
    }
    isLoading.value = true

    try {
        const response = await fetch(
            `/api/szukaj?q=${encodeURIComponent(query)}`
        )
        const data = await response.json()
        searchResults.value = {
            products: data.products || [],
            categories: data.categories || []
        }
        showResults.value = true
        trackSearch(query, (data.products || []).length + (data.categories || []).length)
    } catch (error) {
        console.error('Błąd wyszukiwania:', error)
        searchResults.value = {products: [], categories: []}
    } finally {
        isLoading.value = false
    }
}

const handleInput = () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        if (searchQuery.value.trim()) {
            searchProducts(searchQuery.value.trim())
        } else {
            searchResults.value = {products: [], categories: []}
        }
    }, props.debounceDelay)
}

const handleBlur = () => {
    setTimeout(() => (showResults.value = false), 200)
}

const selectProduct = (product) => {
    searchQuery.value = product.attributes.name
    showResults.value = false
    emit('productSelected', product)
    router.visit(product.url)
}

const selectCategory = (category) => {
    searchQuery.value = category.name
    showResults.value = false
    emit('categorySelected', category)
    router.visit(category.url)
}

const performSearch = () => {
    if (searchQuery.value.trim()) {
        showResults.value = false
        emit('search', searchQuery.value.trim())
        router.visit(`/szukaj?q=${encodeURIComponent(searchQuery.value.trim())}`)
    }
}

const hasResults = computed(() => {
    return searchResults.value.products.length > 0 || searchResults.value.categories.length > 0
})

const showMobileView = () => {
    bodyScrollLock.lock()
    mobileViewOpen.value = true
}

const hideMobileView = () => {
    bodyScrollLock.unlock()
    mobileViewOpen.value = false
    resetSearch()
}

const resetSearch = () => {
    searchQuery.value = ''
    searchResults.value = {products: [], categories: []}
    showResults.value = false
}
</script>

<template>
    <div class="relative w-full h-full">
        <Teleport to="body" :disabled="!mobileViewOpen">
            <div class="border border-light rounded z-50 bg-header"
                 :class="mobileViewOpen ? 'fixed top-0 left-0 bottom-0 right-0 z-100' : 'relative'">
                <!-- input -->
                <div class="relative flex items-center">
                    <button
                        @click="hideMobileView"
                        class="absolute left-2 sm:hidden"
                        v-show="mobileViewOpen"
                    >
                        <IconChevron left size="4xl"/>
                    </button>
                    <input
                        v-model="searchQuery"
                        @input="handleInput"
                        @focus="showResults = true"
                        @blur="handleBlur"
                        type="text"
                        placeholder="Szukaj produktów i kategorii..."
                        class="w-full border-none shadow-none px-4 py-2 pr-10 transition-colors hidden sm:block"
                    />
                    <input
                        @click="showMobileView"
                        v-model="searchQuery"
                        @input="handleInput"
                        type="text"
                        placeholder="Szukaj produktów i kategorii..."
                        class="w-full border-none shadow-none pr-10 transition-colors sm:hidden"
                        :class="mobileViewOpen ? 'text-lg pl-16 py-6' : 'pl-4 py-2'"
                    />
                    <button
                        v-show="!mobileViewOpen"
                        @click="performSearch"
                        class="absolute right-2 text-gray-500 hover:text-blue-500"
                    >
                        <IconSearch size="2xl"/>
                    </button>
                    <button
                        v-show="mobileViewOpen"
                        @click="resetSearch"
                        class="absolute right-2"
                    >
                        <IconX size="3xl"/>
                    </button>
                </div>

                <!-- wyniki -->
                <div v-if="(showResults && hasResults) || mobileViewOpen"
                     class="search-box-results w-full sm:max-h-96 overflow-y-auto px-6 py-6 sm:mt-6">
                    <div v-if="showResults" class="flex flex-col sm:flex-row">
                        <!-- Kategorie -->
                        <div class="pr-4 w-full sm:w-[300px] order-2 sm:order-1">
                            <div class="px-3 py-2">
                                <h3 class="search-box-results-label">
                                    Kategorie
                                </h3>
                            </div>
                            <div class="divide-y divide-light">
                                <div v-if="searchResults.categories.length > 0"
                                     v-for="category in searchResults.categories"
                                     :key="`category-${category.id}`"
                                     @mousedown="selectCategory(category)"
                                     class="search-box-category-item py-2 px-4 cursor-pointer transition-all">
                                    <h4 class="search-box-category-name">
                                        {{ category.name }}
                                    </h4>
                                </div>
                                <div v-else>
                                    Brak wyników
                                </div>
                            </div>
                        </div>

                        <!-- Produkty -->
                        <div class="pl-4 w-full order-1 sm:order-2">
                            <div class="px-3 py-2">
                                <h3 class="search-box-results-label">
                                    Produkty
                                </h3>
                            </div>
                            <div v-if="searchResults.products.length > 0">
                                <ProductSearchResultItem
                                    v-for="product in searchResults.products"
                                    :product="product"
                                    :key="`product-${product.id}`"
                                    @select="selectProduct($event)"/>

                            </div>
                            <div v-else>Brak wyników</div>
                        </div>
                    </div>
                </div>

                <!-- loader -->
                <div v-if="isLoading" class="w-full bg-header mt-1 p-6 text-center flex justify-center items-center">
                    <IconLoader size="xl"/>
                </div>
            </div>
        </Teleport>
        <Teleport to="body">
            <transition
                enter-active-class="transition-opacity duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="showResults && hasResults && !mobileViewOpen"
                    class="fixed top-0 left-0 right-0 bottom-0 bg-black/20 z-2"
                ></div>
            </transition>
        </Teleport>
    </div>
</template>