<script setup>

import {Link} from "@inertiajs/vue3";
import {useVariantStore} from "../../../stores/productVariant";

const props = defineProps(['variants'])

const store = useVariantStore();


</script>

<template>
    <div v-for="attribute in variants" :key="attribute.attribute.id" class="mb-6">

        <p class="font-semibold mb-2">{{ attribute.attribute.name }}</p>

        <div class="flex gap-2">
            <template v-for="product in attribute.products" :id="product.id">
                <div :class="[product.is_selected ? 'border' : '']">
                    <span v-if="product.is_selected">{{ product.attribute_value }}</span>
                    <Link v-if="!product.is_selected" :href="product.url">{{ product.attribute_value }}</Link>
                </div>
            </template>
        </div>


    </div>
</template>