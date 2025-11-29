<script setup>
import {computed, defineProps} from 'vue';
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import {Link} from '@inertiajs/vue3'
import PriceDisplay from "@shopen/components/frontend/ui/PriceDisplay.vue";
import ProductImage from "@shopen/components/frontend/product/ProductImage.vue";
import ProductIcons from "@shopen/components/frontend/product/ProductIcons.vue";

const props = defineProps({
    product: {type: Object}
})
const imageWidth = 250;
const mobileImageWidth = 350;
const productSizes = `(min-width: 640px) ${imageWidth}px, 90vw`;

const emits = defineEmits(['onClick'])

const showReviews = computed(() => typeof props.product.rating !== 'undefined' || typeof props.product.reviews_count !== 'undefined')
</script>

<template>
    <div class="carousel-product-wrapper relative flex justify-center w-full group"
         :class="[product.in_stock ? 'in-stock' : 'out-of-stock']">
        <Link :href="product.url"
              @click="emits('onClick')"
              class="carousel-product-thumbnail relative flex flex-col justify-between w-full max-w-350 sm:max-w-250">
            <div>
                <div class="relative cursor-pointer overflow-hidden">
                    <div class="absolute top-2 left-2 z-1">
                        <ProductIcons :product="product"/>
                    </div>
                    <div class="group block relative w-full h-full product-thumbnail-image">
                        <div class="relative w-full aspect-square">

                            <!-- Brak obrazka -->
                            <span v-if="!product.images || !product.images.length"
                                  class="text-no-image-icon bg-no-image-bg w-full h-full flex items-center justify-center">
                                <IconNoImage xl></IconNoImage>
                            </span>

                            <!-- Główny obrazek produktu -->
                            <ProductImage
                                v-if="product.images && product.images.length > 0"
                                :urls="product.images[0]"
                                sizes="(min-width: 1360px) 284px, (min-width: 1040px) calc(20vw + 16px), (min-width: 780px) calc(33.33vw - 48px), (min-width: 640px) calc(50vw - 72px), calc(50vw - 56px)"
                                :alt="product.attributes.name"
                                loading="lazy"
                                class="object-cover"
                            />

                            <!-- Obrazek na hover -->
                            <div
                                v-if="product.images && product.images.length > 1"
                                class="thumbnail-image-2"
                            >
                                <ProductImage
                                    :urls="product.images[1]"
                                    sizes="(min-width: 1360px) 284px, (min-width: 1040px) calc(20vw + 16px), (min-width: 780px) calc(33.33vw - 48px), (min-width: 640px) calc(50vw - 72px), calc(50vw - 56px)"
                                    :alt="product.attributes.name"
                                    loading="lazy"
                                    class="w-full h-full object-cover"
                                />
                            </div>

                        </div>
                    </div>
                </div>
                <div class="px-2 py-2">
                    <div class="product-name">
                        {{ product.attributes.name }}
                    </div>
                </div>
            </div>
            <div class="px-2 pb-2">
                <div class="info-label" v-if="!product.in_stock">
                    <span>Niedostępny</span>
                </div>
                <div class="info-label" v-if="product.in_stock && product.free_shipping">
                    Darmowa dostawa
                </div>
                <div class="info-label flex items-center gap-2" v-if="product.price.omnibus_price">
                    <PriceDisplay :price="product.price.omnibus_price" old size="sm"/>
                </div>
                <div class="flex justify-between items-start">
                    <div v-if="product.price">
                        <div class="price flex items-end">
                            <div>
                                <PriceDisplay :price="product.price.final_price"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Link>
    </div>
</template>