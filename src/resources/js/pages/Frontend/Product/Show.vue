<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import Gallery from "@shopen/pages/Frontend/Product/components/Gallery/Gallery.vue";
import AddToCartButton from "@shopen/components/frontend/product/AddToCartButton.vue";
import VariantSelect from "@shopen/pages/Frontend/Product/components/VariantSelect.vue";
import BannersContainer from "@shopen/components/frontend/banner/BannersContainer.vue";
import ProductPrice from "@shopen/pages/Frontend/Product/components/ProductPrice.vue";
import ProductReviews from "@shopen/pages/Frontend/Product/components/ProductReviews.vue";
import ReviewsInfo from "@shopen/pages/Frontend/Product/components/ReviewsInfo.vue";
import ProductsCarousel from "@shopen/components/frontend/product/carousel/ProductsCarousel.vue";
import ProductStructuredData from "@shopen/components/frontend/product/ProductStructuredData.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import AddToShoppingListButton from "@shopen./components/frontend/shoppingList/AddToShoppingListButton.vue";
import ProductDescription from "@shopen/pages/Frontend/Product/components/ProductDescription.vue";
import ProductBrand from "@shopen/pages/Frontend/Product/components/ProductBrand.vue";
import {ref} from "vue";
import {trackViewItem} from "@shopen/utils/ga4.js";
import DetailsSection from "@shopen/pages/Frontend/Product/components/DetailsSection.vue";
import ProductInfo from "@shopen/pages/Frontend/Product/components/ProductInfo.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    product: {type: Object, required: true},
    recentlyViewedProducts: {type: Array},
    relatedProducts: {type: Array},
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

const showVariantsError = ref(false);
const onAddConfigurableToCart = () => {
    showVariantsError.value = true
}
trackViewItem(props.product, props.variants)

</script>

<template>
    <BannersContainer :banners="banners.product_page_top"/>
    <div class="product-show">
        <div class="flex flex-col md:flex-row gap-6 sm:gap-4">
            <section class="mr-0 sm:mr-6 w-full">
                <Gallery :images="images"/>
            </section>
            <section class="section-main w-full">
                <div>
                    <AddToShoppingListButton :product="product"/>
                </div>
                <div class="product-name mb-2">
                    {{ product.attributes.name }}
                </div>
                <div v-if="reviewsEnabled">
                    <ReviewsInfo :product="product"/>
                </div>

                <div class="mt-4 mb-4">
                    <ProductPrice :price="product.price"/>
                </div>

                <ProductInfo/>

                <VariantSelect :variants="variants" :showError="showVariantsError"/>

                <section class="mb-8">
                    <AddToCartButton
                        v-if="product.in_stock"
                        @onAddConfigurable="onAddConfigurableToCart"
                        :product="product"
                    ></AddToCartButton>
                    <div v-else>
                        <div class="product-out-of-stock-info">
                            Produkt chwilowo niedostępny
                        </div>
                    </div>
                </section>

                <div>
                    <ProductBrand :product="product"/>

                    <ProductDescription v-if="product.attributes.short_description" :description="product.attributes.short_description"/>
                </div>
            </section>
        </div>

        <section>
            <DetailsSection :product="product" :attributes="attributes" />
        </section>

        <section class="related-products" v-if="relatedProducts && relatedProducts.length">
            <div class="section-title-wrapper">
                <h2 class="section-title">Zobacz też</h2>
            </div>
            <ProductsCarousel :products="relatedProducts" size="md"/>
        </section>

        <section class="reviews" v-if="reviewsEnabled">
            <ProductReviews :product="product" :reviews="reviews" :reviewSubmitted="reviewSubmitted" :sort="sort"/>
        </section>

        <section class="recently-viewed" v-if="recentlyViewedProducts && recentlyViewedProducts.length">
            <div class="section-title-wrapper">
                <h2 class="section-title">Ostatnio oglądane</h2>
            </div>
            <ProductsCarousel :products="recentlyViewedProducts" size="md"/>
        </section>

        <BannersContainer :banners="banners.product_page_bottom"/>

        <ProductStructuredData :product="product"/>
    </div>
</template>