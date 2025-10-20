<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductListing from '@shopen/components/frontend/product/ProductListing.vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    activeFilters: { type: Object, default: () => ({}) },
    activeSort: { type: String },
    sortOptions: { type: Array },
    searchQuery: { type: String, required: true }
})
</script>

<template>
    <Head>
        <title>Wyniki wyszukiwania dla: {{ searchQuery }}</title>
        <meta name="robots" content="noindex, follow">
    </Head>

    <ProductListing
        :products="products"
        :filters="filters"
        :active-filters="activeFilters"
        :active-sort="activeSort"
        :sort-options="sortOptions"
        :search-query="props.searchQuery"
    >

        <template #header="{ resultsCount }">
            <div class="flex items-end">
                <h1 class="text-3xl mr-2">Wyniki wyszukiwania dla: "{{ searchQuery }}"</h1>
                <div class="text-neutral-600">({{ resultsCount }})</div>
            </div>
        </template>


    </ProductListing>
</template>