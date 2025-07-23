<script setup>


import {useCartStore} from "@shopen/stores/cart.js";
import {useVariantStore} from "@shopen/stores/productVariant.js";
import {ref} from "vue";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import IconCartPlus from "@shopen/components/icons/IconCartPlus.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";

const cart = useCartStore();
const minicart = useMiniCartStore();

const props = defineProps(['productId'])

const variantStore = useVariantStore();

const qty = ref(1);

const addToCart = async () => {
    if (!variantStore.validate()) {
        return;
    }
    let productId = props.productId;

    if (variantStore.selectedVariant && variantStore.selectedVariant.product_id) {
        productId = variantStore.selectedVariant.product_id;
    }

    const added = await cart.addToCart(productId, qty.value);
    if (added) {
        minicart.open();
    }
}

const updateQty = (newQty) => {
    qty.value = newQty;
}
</script>

<template>
    <div class="flex items-stretch">
        <AmountInput :value="qty" @onChange="updateQty" :min="1"></AmountInput>
        <button @click="addToCart"
                :disabled="variantStore.selectedVariant && !variantStore.selectedVariant.in_stock || cart.addingToCart"
                class="button-primary ml-4">
            <div class="flex items-center">
                <IconCartPlus md v-if="!cart.addingToCart"></IconCartPlus>
                <IconLoader md v-if="cart.addingToCart"></IconLoader>
                <span class="ml-4">Dodaj do koszyka</span>
            </div>
        </button>
    </div>
</template>

<style scoped>

</style>