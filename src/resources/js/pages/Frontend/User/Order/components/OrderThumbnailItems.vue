<script setup>
import ProductThumbnailImage from "@shopen/components/frontend/product/ProductThumbnailImage.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    'items': {
        type: Array,
        required: true
    },
    'limit': {
        type: Number,
        default: 0
    },
    'links': {
        type: Boolean,
        default: true
    }
});

const showAll = ref(false);

const displayedItems = computed(() => {
    if (!showAll.value && props.limit && props.items.length > props.limit) {
        return props.items.slice(0, props.limit - 1);
    }
    return props.items;
});

const remainingItemsCount = computed(() => {
    if (props.limit && props.items.length > props.limit) {
        return props.items.length - props.limit + 1;
    }
    return props.items.length - props.limit;
});

</script>

<template>
    <div class="flex flex-col gap-y-3">
        <div v-for="item in displayedItems" :key="item.id" class="flex items-center">
            <ProductThumbnailImage :product="item.product" size="sm" class="!w-10 !h-10 flex-shrink-0"/>
            <div class="ml-3 overflow-hidden">
                <div class="text-sm truncate">
                    <Link :href="item.product.url" class="hover:underline" prefetch>{{ item.product.name }}</Link>
                </div>
                <div class="text-xs text-gray-500">
                    {{ item.quantity }} szt. × {{ item.final_price }}
                </div>
            </div>
        </div>

        <div v-if="!showAll && remainingItemsCount > 0"
             @click.prevent="showAll = true"
             title="Pokaż wszystkie produkty"
             class="w-10 h-10 flex-shrink-0 cursor-pointer bg-gray-100 text-gray-500 hover:bg-gray-200 transition-all rounded-md flex items-center justify-center font-medium text-xs">
            +{{ remainingItemsCount }}
        </div>
    </div>
</template>