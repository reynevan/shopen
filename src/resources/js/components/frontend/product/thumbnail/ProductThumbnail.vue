<script setup>
import {computed, defineProps} from 'vue';
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import {Link} from '@inertiajs/vue3'
import RatingDisplay from "@shopen/components/frontend/product/RatingDisplay.vue";
import PriceDisplay from "@shopen/components/frontend/ui/PriceDisplay.vue";
import AddToCartButton from "@shopen/components/frontend/product/thumbnail/AddToCartButton.vue";
import AddToShoppingListButton from "../../shoppingList/AddToShoppingListButton.vue";
import ProductImage from "../ProductImage.vue";
import ProductIcons from "../ProductIcons.vue";

const props = defineProps({
    product: {type: Object},
    size: {type: String, default: 'md'}
})
const imageWidth = props.size === 'sm' ? 150 : 250;
const mobileImageWidth = props.size === 'sm' ? 150 : 350;
const productSizes = `(min-width: 640px) ${imageWidth}px, 90vw`;
const widthClasses = `max-w-[${mobileImageWidth}px] sm:max-w-[${imageWidth}px]`
const nameClass = props.size === 'sm' ? 'text-sm' : 'text-md';

const emits = defineEmits(['onClick'])

const showReviews = computed(() => typeof props.product.rating !== 'undefined' || typeof props.product.reviews_count !== 'undefined')
</script>

<template>
    <div class="product-thumbnail-wrapper relative flex justify-center w-full group"
         :class="[product.in_stock ? 'in-stock' : 'out-of-stock']">
        <Link :href="product.url"
              @click="emits('onClick')"
              class="product-thumbnail relative flex flex-col justify-between w-full"
              :class="[widthClasses, size === 'md' ? 'gap-2' : '']">
            <div>
                <div class="relative cursor-pointer overflow-hidden">
                    <div class="absolute top-2 left-2 z-1">
                        <ProductIcons :product="product" :size="size"/>
                    </div>
                    <div class="group block relative w-full h-full">
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
                                :sizes="productSizes"
                                :alt="product.name"
                                loading="lazy"
                                class="w-full h-full object-cover"
                            />

                            <!-- Obrazek na hover -->
                            <div
                                v-if="product.images && product.images.length > 1"
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            >
                                <ProductImage
                                    :urls="product.images[1]"
                                    :sizes="productSizes"
                                    :alt="product.name"
                                    loading="lazy"
                                    class="w-full h-full object-cover"
                                />
                            </div>

                        </div>
                    </div>
                </div>
                <div class="px-2 py-2">
                    <div>
                        <div class="mb-2" :class="nameClass">
                            {{ product.attributes.name }}
                        </div>
                        <div v-if="size === 'md' && showReviews" class="flex items-center gap-2">
                            <RatingDisplay :rating="product.rating" size="sm"/>
                            <div class="product-rating-label">{{ product.rating }} ({{ product.reviews_count }})</div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-1 right-1 flex flex-col items-center transition-all duration-500">
                    <div :class="product.is_on_list ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 duration-500'"
                         class="bg-white flex items-center justify-center">
                        <AddToShoppingListButton :product="product" :label="false"/>
                    </div>
                </div>
            </div>
            <div class="px-2 pb-2">
                <div class="info-label" v-if="!product.in_stock">
                    <span v-if="size !== 'sm'">Chwilowo niedostępny</span>
                    <span v-else>Niedostępny</span>
                </div>
                <div class="info-label" v-if="product.in_stock && product.free_shipping">
                    Darmowa dostawa
                </div>
                <div class="info-label flex items-center gap-2" v-if="product.price.omnibus_price">
                    Najniższa cena: <span class="line-through">{{ product.price.omnibus_price }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <div v-if="product.price">
                        <div class="price flex items-end">
                            <div>
                                <PriceDisplay :price="product.price.final_price"/>
                            </div>
                            <div
                                class="bg-green-50 text-emerald-500 font-semibold text-sm px-1 ml-2 rounded"
                                v-if="product.price.discount_amount">
                                -{{ product.price.discount_amount }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Link>
    </div>
</template>