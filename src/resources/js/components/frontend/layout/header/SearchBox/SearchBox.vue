<script setup>
import {computed, ref} from 'vue'
import {router} from '@inertiajs/vue3'
import IconSearch from "@shopen/components/icons/IconSearch.vue";
import ProductSearchResultItem from "./ProductSearchResultItem.vue";
import IconLoader from "../../../../icons/IconLoader.vue";
import {trackSearch} from "../../../../../utils/ga4";

const searchQuery = ref('')
const searchResults = ref({products: [], categories: []})
const showResults = ref(false)
const isLoading = ref(false)
let debounceTimer = null

const props = defineProps({
    minSearchLength: {type: Number, default: 2},
    debounceDelay: {type: Number, default: 300}
})

const emit = defineEmits(['search', 'productSelected', 'categorySelected'])

const searchProducts = async (query) => {
    if (query.length < props.minSearchLength) {
        searchResults.value = {products: [], categories: []}
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
</script>

<template>
    <div class="relative w-full h-full">
        <div class="absolute top-0 right-0 left-0 border rounded z-50 bg-header">
            <!-- input -->
            <div class="relative flex items-center">
                <input
                    v-model="searchQuery"
                    @input="handleInput"
                    @focus="showResults = true"
                    @blur="handleBlur"
                    type="text"
                    placeholder="Szukaj produktów i kategorii..."
                    class="w-full border-none shadow-none px-4 py-2 pr-10 transition-colors"
                />
                <button
                    @click="performSearch"
                    class="absolute right-2 text-gray-500 hover:text-blue-500"
                >
                    <IconSearch size="2xl"/>
                </button>
            </div>

            <!-- wyniki -->
            <div v-if="showResults && hasResults"
                class="w-full bg-header max-h-96 overflow-y-auto px-6">
                <div class=" border-t border-light pt-6 my-6 flex">
                    <!-- Kategorie -->
                    <div class="pr-4 w-[300px]">
                        <div class="px-3 py-2">
                            <h3 class="text-xs text-gray-600 uppercase tracking-wide">
                                Kategorie
                            </h3>
                        </div>
                        <div class="divide-y divide-border-light">
                            <div v-if="searchResults.categories.length > 0"
                                 v-for="category in searchResults.categories"
                                 :key="`category-${category.id}`"
                                 @mousedown="selectCategory(category)"
                                 class="flex items-center p-3 cursor-pointer hover:bg-accent/30 transition-all">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        {{ category.name }}
                                    </h4>
                                </div>
                            </div>
                            <div v-else>
                                Brak wyników
                            </div>
                        </div>
                    </div>

                    <!-- Produkty -->
                    <div class="pl-4 w-full">
                        <div class="px-3 py-2">
                            <h3 class="text-xs text-gray-600 uppercase tracking-wide">
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
                    v-show="showResults && hasResults"
                    class="fixed top-0 left-0 right-0 bottom-0 bg-black/20 z-2"
                ></div>
            </transition>
        </Teleport>
    </div>
</template>