<script setup>
import {Head, Link} from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import ProductListing from '@shopen/components/frontend/product/ProductListing.vue';
import BannersContainer from "@shopen/components/frontend/banner/BannersContainer.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    products: {type: Object, required: true},
    filters: {type: Object, required: true},
    activeFilters: {type: Object, default: () => ({})},
    activeSort: {type: String},
    banners: {type: Object},
    category: {type: Object, required: true},
    subcategories: {type: Array},
    sortOptions: {type: Array},
    title: {type: String},
})

</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="category.seo.seo_description">
        <meta name="title" :content="category.seo.seo_title">
        <link rel="canonical" :href="products.meta.path + (products.meta.current_page > 1 ? `?strona=${products.meta.current_page}` : '')">
        <link rel="next" v-if="products.links.next" :href="products.links.next">
        <link rel="prev" v-if="products.links.prev" :href="products.links.prev">
    </Head>
    <main class="main-container">
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

            <template #header="{ resultsCount }">
                <div class="flex items-start flex-col">
                    <div class="text-3xl mb-2">{{ category.name }}</div>
                    <div class="listing-products-count">{{ resultsCount }}</div>
                </div>
            </template>

            <template #before-products>
                <img class="hidden sm:block mb-4" :src="category.image_url_desktop" v-if="category.image_url_desktop"
                     :alt="category.name"/>
                <img class="block sm:hidden mb-4" :src="category.image_url_mobile" v-if="category.image_url_mobile"
                     :alt="category.name"/>
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
    </main>
</template>