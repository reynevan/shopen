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

const hasSelected = computed(() => props.variant.products.some(product => product.is_selected))

</script>

<template>
    <div>
        <div class="mb-2 pl-2">
            <p class="variant-attribute transition-colors duration-200" :class="hasError ? 'text-red-600 animate-shake' : ''">{{ variant.attribute.name }}</p>
            <p v-if="!hasSelected" class="text-xs transition-colors duration-200" :class="hasError ? 'text-red-600 animate-shake' : 'text-gray-400'">wybierz opcję</p>
        </div>

        <div class="variants">
            <template v-for="product in variant.products" :id="product.id">
                <div v-if="product.is_selected" class="variant selected flex gap-1 items-center">
                    <div v-if="variant.attribute.is_color"
                         class="border border-strong w-4 h-4"
                         :style="{background: product.color}"
                         :title="product.attribute_value"></div>
                    {{ product.attribute_value }}
                </div>
                <Link v-if="!product.is_selected" :href="product.url" class="variant flex gap-1 items-center">
                    <div v-if="variant.attribute.is_color"
                         class="border border-strong w-4 h-4"
                         :style="{background: product.color}"
                         :title="product.attribute_value"></div>
                        {{ product.attribute_value }}
                </Link>
            </template>
        </div>
    </div>
</template>