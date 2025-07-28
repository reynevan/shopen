<script setup>
import {defineProps} from 'vue';
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import {Link} from '@inertiajs/vue3'
import RatingDisplay from "../../../pages/Frontend/Product/components/RatingDisplay.vue";
import PriceDisplay from "../ui/PriceDisplay.vue";

const props = defineProps(['product'])
</script>

<template>
    <div
        class="product-thumbnail flex flex-col justify-between group sm:m-2 w-1/2 sm:w-[200px] border-b hover:shadow-lg transition-all duration-500 rounded"
        :class="{'in-stock': product.in_stock, 'out-of-stock': !product.in_stock}">
        <div>
            <div class="sm:w-[200px] sm:h-[200px] relative cursor-pointer overflow-hidden">
                <Link :href="product.url" class="flex items-center justify-center w-full h-full">
                    <span class="text-no-image-icon block bg-no-image-bg w-full h-full flex items-center justify-center"
                          v-if="!product.images || !product.images.length">
                        <IconNoImage xl></IconNoImage>
                    </span>
                    <div class="sm:hidden"
                         v-if="product.images && product.images.length > 0 && product.images[0].thumbnail_mobile">
                        <img :src="product.images[0].thumbnail_mobile" alt=""
                             loading="lazy">
                    </div>
                    <div class="hidden sm:block"
                         v-if="product.images && product.images.length > 0 && product.images[0].thumbnail">
                        <img :src="product.images[0].thumbnail" alt=""
                             loading="lazy">
                    </div>
                    <div
                        class="w-full h-full absolute top-0 hidden sm:flex items-center justify-center bg-no-image-bg opacity-0 group-hover:opacity-100 transition-all blur-2xl group-hover:blur-none duration-350"
                        v-if="product.images && product.images.length > 1 && product.images[1].thumbnail">
                        <img
                            loading="lazy"
                            :src="product.images[1].thumbnail" alt="">
                    </div>
                </Link>
            </div>
            <div class="px-2 py-2">
                <div>
                    <div class="product-name">
                        <Link :href="product.url">{{ product.attributes.name }}</Link>
                    </div>
                    <div class="flex items-center">
                        <RatingDisplay :rating="product.rating"/>
                        <div class="ml-2">{{ product.rating }} ({{ product.reviews_count }})</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-2 pb-2">
            <div class="mt-2">
                <div class="info-label" v-if="!product.in_stock">
                    Chwilowo niedostępny
                </div>
                <div class="info-label" v-if="product.in_stock && product.free_shipping">
                    Darmowa dostawa
                </div>
                <div class="info-label" v-if="product.price.omnibus_price">
                    Najniższa cena: <span class="line-through">{{ product.price.omnibus_price }}</span>
                </div>
                <div v-if="product.price" class="">
                    <div class="price flex items-end">
                        <div>
                            <PriceDisplay :price="product.price.final_price" />
                        </div>
                        <div
                            class="bg-green-50 text-emerald-500 font-semibold text-sm px-1 ml-2 rounded group-hover:hidden"
                            v-if="product.price.discount_amount">
                            -{{ product.price.discount_amount }}
                        </div>
                        <div
                            class="bg-green-50 text-emerald-500 font-semibold text-sm px-1 ml-2 rounded hidden group-hover:block"
                            v-if="product.price.discount_percent">
                            -{{ product.price.discount_percent }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>