<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductListing from '@shopen/components/frontend/product/ProductListing.vue';
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
        <link rel="canonical" :href="products.meta.path + (products.meta.current_page > 1 ? `?strona=${products.meta.current_page}` : '')">
        <link rel="next" v-if="products.links.next" :href="products.links.next">
        <link rel="prev" v-if="products.links.prev" :href="products.links.prev">
        <!-- ... reszta tagów ... -->
    </Head>

    <ProductListing
        :products="products"
        :categories="subcategories"
        :filters="filters"
        :active-filters="activeFilters"
        :active-sort="activeSort"
        :sort-options="sortOptions"
        :list-name="category.name"
        :list-id="category.id"
    >
        <!-- Wypełniamy sloty specyficzną treścią dla strony kategorii -->

        <template #page-top-banners>
            <BannersContainer :banners="banners.category_page_top"/>
        </template>

        <template #header="{ resultsCount }">
            <div class="flex items-start flex-col">
                <div class="text-3xl mr-2">{{ category.name }}</div>
                <div class="listing-products-count">{{ resultsCount }}</div>
            </div>
        </template>

        <template #sidebar-prepend>
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