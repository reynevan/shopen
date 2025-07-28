<script setup>

import {Link} from "@inertiajs/vue3";

const props = defineProps(['variants'])

</script>

<template>
    <div v-for="attribute in variants" :key="attribute.attribute.id" class="mb-4">

        <p class="mb-2">{{ attribute.attribute.name }}</p>

        <div class="flex gap-2">
            <template v-for="product in attribute.products" :id="product.id">
                <div v-if="product.is_selected"
                    class="border-strong cursor-default py-2 px-4 rounded border">
                    {{ product.attribute_value }}
                </div>
                <Link v-if="!product.is_selected" :href="product.url">
                    <div class="hover:bg-neutral-100 py-2 px-4 rounded border">
                        {{ product.attribute_value }}
                    </div>
                </Link>
            </template>
        </div>
    </div>
</template>