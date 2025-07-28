<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import Gallery from "@shopen/components/frontend/product/Gallery.vue";
import AddToCartButton from "@shopen/components/frontend/product/AddToCartButton.vue";
import VariantSelect from "@shopen/pages/Frontend/Product/components/VariantSelect.vue";
import ProductAttributes from "@shopen/pages/Frontend/Product/components/ProductAttributes.vue";
import BannersContainer from "@shopen/components/frontend/banner/BannersContainer.vue";
import ProductPrice from "./components/ProductPrice.vue";
import ProductReviews from "./components/ProductReviews.vue";
import ReviewsInfo from "./components/ReviewsInfo.vue";
import ProductsCarousel from "../../../components/frontend/product/ProductsCarousel.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    product: {type: Object, required: true},
    recentlyViewedProducts: {type: Object},
    reviews: {type: Object},
    reviewsEnabled: {type: Boolean},
    sort: {type: String},
    reviewSubmitted: {type: Boolean},
    attributes: {type: Object},
    images: {type: Array},
    variants: {type: Array},
    configurableAttributes: {type: Array},
    banners: {type: Object}
})

</script>

<template>
    <div class="product-show">
        <BannersContainer :banners="banners.product_page_top"/>
        <div class="flex flex-col sm:flex-row py-10 px-6">
            <section class="mr-0 sm:mr-6">
                <Gallery :images="images"/>
            </section>
            <section>
                <div class="text-xl">
                    {{ attributes.name }}
                </div>
                <ReviewsInfo :product="product"/>

                @block('product.show.stock-status')
                <div class="mt-4 mb-4">
                    <ProductPrice :price="product.price"/>
                </div>
                <VariantSelect :variants="variants"/>
                <AddToCartButton :productId="product"></AddToCartButton>
                <div>
                    <ProductAttributes :product="product" :attributes="attributes"/>
                </div>
            </section>
        </div>

        <section v-if="reviewsEnabled">
            <h2 class="section-title">
                Opinie
            </h2>
            <ProductReviews :product="product" :reviews="reviews" :reviewSubmitted="reviewSubmitted" :sort="sort"/>
        </section>

        <section v-if="recentlyViewedProducts && recentlyViewedProducts.length">
            <h2 class="section-title">Ostatnio oglądane</h2>
            <ProductsCarousel :products="recentlyViewedProducts"/>
        </section>

        <BannersContainer :banners="banners.product_page_bottom"/>
    </div>
</template>