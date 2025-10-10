<script setup>

import {Link} from "@inertiajs/vue3";

const props = defineProps(['variants'])

</script>

<template>
    <div v-if="variants && variants.length" class="pt-4 mt-4 pb-6 mb-6 border-y border-light space-y-4">
        <div v-for="variant in variants" :key="variant.attribute.id">
            <p class="mb-2">{{ variant.attribute.name }}</p>

            <div class="flex divide-x gap-2" v-if="variant.attribute.is_color">
                <template v-for="product in variant.products" :id="product.id">
                    <div v-if="product.is_selected"
                         class="border border-light hover:border-border-strong hover:opacity-60 transition-all w-10 h-10"
                         :style="{background: product.color}"
                         :title="product.attribute_value"></div>
                    <Link v-if="!product.is_selected" :href="product.url">
                        <div class="border border-strong w-10 h-10" :style="{background: product.color}" :title="product.attribute_value"></div>
                    </Link>
                </template>
            </div>

            <div class="flex divide-x" v-if="!variant.attribute.is_color">
                <template v-for="product in variant.products" :id="product.id">
                    <div v-if="product.is_selected"
                         class="cursor-default bg-neutral-50 font-semibold py-2 px-4 transition-all duration-300">
                        {{ product.attribute_value }}
                    </div>
                    <Link v-if="!product.is_selected" :href="product.url">
                        <div class="hover:bg-neutral-100 py-2 px-4 transition-all duration-300">
                            {{ product.attribute_value }}
                        </div>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>