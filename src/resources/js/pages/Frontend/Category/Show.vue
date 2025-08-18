<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductListing from '@shopen/components/frontend/product/ProductListing.vue'; // Popraw ścieżkę!
import BannersContainer from "@shopen/components/frontend/banner/BannersContainer.vue";

defineOptions({ layout: AppLayout })

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    activeFilters: { type: Object, default: () => ({}) },
    activeSort: { type: String },
    banners: { type: Object },
    category: { type: Object, required: true },
    subcategories: { type: Array },
    sortOptions: { type: Array },
    title: { type: String }
})
</script>

<template>
    <Head>
        <!-- Twoje tagi SEO specyficzne dla kategorii -->
        <title>{{ title }}</title>
        <meta name="description" :content="category.seo.seo_description">
        <!-- ... reszta tagów ... -->
    </Head>

    <ProductListing
        :products="products"
        :filters="filters"
        :active-filters="activeFilters"
        :active-sort="activeSort"
        :sort-options="sortOptions"
    >
        <!-- Wypełniamy sloty specyficzną treścią dla strony kategorii -->

        <template #page-top-banners>
            <BannersContainer :banners="banners.category_page_top"/>
        </template>

        <template #header="{ resultsCount }">
            <div class="flex items-end">
                <div class="text-3xl mr-2">{{ category.name }}</div>
                <div class="text-neutral-600">({{ resultsCount }})</div>
            </div>
        </template>

        <template #sidebar-prepend>
            <div v-for="subcategory in subcategories" :key="subcategory.id">
                <Link :href="subcategory.url">{{ subcategory.name }}</Link>
            </div>
            <BannersContainer :banners="banners.category_page_filters_top"/>
        </template>

        <template #sidebar-append>
            <BannersContainer :banners="banners.category_page_filters_bottom"/>
        </template>

        <template #before-products>
            <img class="hidden sm:block mb-4" :src="category.image_url_desktop" v-if="category.image_url_desktop" :alt="category.name"/>
            <img class="block sm:hidden mb-4" :src="category.image_url_mobile" v-if="category.image_url_mobile" :alt="category.name"/>
            <BannersContainer :banners="banners.category_page_products_top"/>
        </template>

        <template #after-products>
            <BannersContainer :banners="banners.category_page_products_bottom"/>
            <div v-if="category.description" v-html="category.description" class="mt-8"></div>
        </template>

        <template #page-bottom-banners>
            <BannersContainer :banners="banners.category_page_bottom"/>
        </template>

    </ProductListing>
</template>