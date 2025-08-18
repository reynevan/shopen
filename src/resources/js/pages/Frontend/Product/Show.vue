<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import Gallery from "@shopen/pages/Frontend/Product/components/Gallery/Gallery.vue";
import AddToCartButton from "@shopen/components/frontend/product/AddToCartButton.vue";
import VariantSelect from "@shopen/pages/Frontend/Product/components/VariantSelect.vue";
import ProductAttributes from "@shopen/pages/Frontend/Product/components/ProductAttributes.vue";
import BannersContainer from "@shopen/components/frontend/banner/BannersContainer.vue";
import ProductPrice from "./components/ProductPrice.vue";
import ProductReviews from "./components/ProductReviews.vue";
import ReviewsInfo from "./components/ReviewsInfo.vue";
import ProductsCarousel from "@shopen/components/frontend/product/ProductsCarousel.vue";
import ProductStructredData from "@shopen/components/frontend/product/ProductStructredData.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import AddToShoppingListButton from "../../../components/frontend/shoppingList/AddToShoppingListButton.vue";

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

</script>

<template>
    <div class="product-show">
        <BannersContainer :banners="banners.product_page_top"/>


        <div class="flex flex-col sm:flex-row py-10 px-6">
            <section class="mr-0 sm:mr-6">
                <Gallery :images="images"/>
            </section>
            <section>
                <div>
                    <AddToShoppingListButton :product="product"/>
                </div>
                <div class="text-3xl mb-2">
                    {{ product.attributes.name }}
                </div>
                <ReviewsInfo :product="product"/>

                <div class="mt-4 mb-4">
                    <ProductPrice :price="product.price"/>
                </div>

                <VariantSelect :variants="variants"/>

                <AddToCartButton :productId="product.id" v-if="product.in_stock"></AddToCartButton>
                <div v-else>
                    <Button type="disabled" disabled>
                        Produkt chwilowo niedostępny
                    </Button>
                </div>
                <div>
                    <ProductAttributes :product="product" :attributes="attributes"/>
                </div>
            </section>
        </div>

        <section v-if="relatedProducts && relatedProducts.length">
            <h2 class="section-title">Zobacz też</h2>
            <ProductsCarousel :products="relatedProducts"/>
        </section>

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
        <ProductStructredData :product="product"/>
    </div>
</template>