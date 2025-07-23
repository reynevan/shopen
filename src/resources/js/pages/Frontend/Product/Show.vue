<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import Gallery from "@shopen/components/frontend/product/Gallery.vue";
import AddToCartButton from "@shopen/components/frontend/product/AddToCartButton.vue";
import VariantSelect from "@shopen/components/frontend/product/VariantSelect.vue";
import ProductAttributes from "../../../components/frontend/product/ProductAttributes.vue";
import BannersContainer from "../../../components/frontend/banner/BannersContainer.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    product: {type: Object, required: true},
    attributes: {type: Object},
    images: {type: Array},
    variants: {type: Array},
    configurableAttributes: {type: Array},
    banners: {type: Object}
})

</script>

<template>
    <div>
        <BannersContainer :banners="banners.product_page_top"/>
        <div class="flex flex-col sm:flex-row py-10 px-6">
            <div class="mr-0 sm:mr-6">
                <Gallery :images="images"/>
            </div>
            <div>
                <div class="text-xl">
                    {{ attributes.name }}
                </div>
                @block('product.show.name')
                @block('product.show.stock-status')
                @block('product.show.price')
                <VariantSelect :variants="variants"/>
                <AddToCartButton :productId="product"></AddToCartButton>
                <div>
                    <ProductAttributes/>
                </div>
            </div>
        </div>
        <BannersContainer :banners="banners.product_page_bottom"/>
    </div>
</template>