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
    brand: { type: Object, required: true },
    sortOptions: { type: Array },
    title: { type: String }
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="brand.seo.seo_description">
    </Head>

    <ProductListing
        :products="products"
        :filters="filters"
        :active-filters="activeFilters"
        :active-sort="activeSort"
        :sort-options="sortOptions"
    >

        <template #page-top-banners>
            <BannersContainer :banners="banners.brand_page_top"/>
        </template>

        <template #header="{ resultsCount }">
            <div class="flex items-end">
                <div class="text-3xl mr-2">{{ brand.name }}</div>
                <div class="text-neutral-600">({{ resultsCount }})</div>
            </div>
        </template>

        <template #sidebar-prepend>
            <BannersContainer :banners="banners.brand_page_filters_top"/>
        </template>

        <template #sidebar-append>
            <BannersContainer :banners="banners.brand_page_filters_bottom"/>
        </template>

        <template #before-products>
            <BannersContainer :banners="banners.brand_page_products_top"/>
        </template>

        <template #after-products>
            <BannersContainer :banners="banners.brand_page_products_bottom"/>
            <div v-if="brand.description" v-html="brand.description" class="mt-8"></div>
        </template>

        <template #page-bottom-banners>
            <BannersContainer :banners="banners.brand_page_bottom"/>
        </template>

    </ProductListing>
</template>