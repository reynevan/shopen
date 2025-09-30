<script setup>

import {Link} from "@inertiajs/vue3";

const props = defineProps(['variants'])

</script>

<template>
    <div v-if="variants && variants.length" class="pt-4 mt-4 pb-6 mb-6 border-y border-light space-y-4">
        <div v-for="attribute in variants" :key="attribute.attribute.id">
            <p class="mb-2">{{ attribute.attribute.name }}</p>
            <div class="flex divide-x">
                <template v-for="product in attribute.products" :id="product.id">
                    <div v-if="product.is_selected"
                         class="cursor-default py-2 px-4">
                        {{ product.attribute_value }}
                    </div>
                    <Link v-if="!product.is_selected" :href="product.url">
                        <div class="hover:bg-neutral-100 py-2 px-4">
                            {{ product.attribute_value }}
                        </div>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>