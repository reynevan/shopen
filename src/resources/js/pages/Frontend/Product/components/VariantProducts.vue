<script setup>

import {Link} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    variant: {type: Object},
    showError: {type: Boolean, default: false},
})

const hasError = computed(() => {
    if (!props.showError) {
        return false
    }
    return !props.variant.products.some(product => product.is_selected)
})
</script>

<template>
    <div>
        <div class="flex gap-2 mb-2" :class="hasError ? 'text-red-400' : ''">
            <p class="variant-attribute">{{ variant.attribute.name }}</p>
            <p v-if="hasError"> - wybierz opcję</p>
        </div>

        <div class="variants-color" v-if="variant.attribute.is_color">
            <template v-for="product in variant.products" :id="product.id">
                <div v-if="product.is_selected"
                     class="border border-strong w-10 h-10"
                     :style="{background: product.color}"
                     :title="product.attribute_value"></div>
                <div v-else
                     class="cursor-pointer border border-light hover:border-border-strong hover:opacity-60 transition-all w-10 h-10"
                     :style="{background: product.color}"
                     :title="product.attribute_value">
                    <Link :href="product.url" class="block w-full h-full"/>
                </div>
            </template>
        </div>

        <div class="variants" v-if="!variant.attribute.is_color">
            <template v-for="product in variant.products" :id="product.id">
                <div v-if="product.is_selected"
                     class="variant selected">
                    {{ product.attribute_value }}
                </div>
                <Link v-if="!product.is_selected" :href="product.url" class="variant">
                        {{ product.attribute_value }}
                </Link>
            </template>
        </div>
    </div>
</template>