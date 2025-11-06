<script setup>
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import {useCoverStore} from "@shopen/stores/cover.js";
import IconCart from "@shopen/components/icons/IconCart.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const minicart = useMiniCartStore();
const cover = useCoverStore();

const page = usePage();
const itemsCount = computed(() => page.props.cart.items?.reduce((acc, item) => acc + item.quantity, 0));

cover.onClose(() => {
    minicart.isOpened = false;
})

const openMinicart = () => {
    minicart.open();
}
</script>

<template>
    <div @click="openMinicart" class="cursor-pointer relative" title="Koszyk">
        <div class="p-2">
            <IconCart size="2xl"/>
        </div>
        <div class="bg-secondary text-xs text-white rounded px-1 inline-flex items-center justify-center absolute right-0 top-0" v-if="itemsCount">
            {{ itemsCount }}
        </div>
    </div>
</template>