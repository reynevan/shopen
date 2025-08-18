<script setup>

import {useCartStore} from "@shopen/stores/cart.js";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import IconCartPlus from "@shopen/components/icons/IconCartPlus.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

const cart = useCartStore();
const minicart = useMiniCartStore();

const props = defineProps({
    productId: {
        type: Number
    },
    amount: {
        type: Number,
        default: 1
    },
    disabled: {
        type: Boolean,
        default: false
    }
})
const addToCart = async () => {
    await cart.addToCart(props.productId, props.amount);
    minicart.open();
}
</script>

<template>
    <Button @click="addToCart"
            type="ghost"
            :disabled="cart.addingToCart[productId] || disabled">
        <div class="flex items-center">
            <IconCartPlus sm v-if="!cart.addingToCart[productId]"></IconCartPlus>
            <IconLoader sm v-if="cart.addingToCart[productId]"></IconLoader>
        </div>
    </Button>
</template>