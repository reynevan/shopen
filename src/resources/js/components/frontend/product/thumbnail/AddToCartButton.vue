<script setup>

import {useCartStore} from "@shopen/stores/cart.js";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import IconCartPlus from "@shopen/components/icons/IconCartPlus.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {router} from "@inertiajs/vue3";

const cart = useCartStore();
const minicart = useMiniCartStore();

const props = defineProps({
    product: {
        type: Object
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
    if (cart.addingToCart[props.product.id] || props.disabled) {
        return
    }
    if (props.product.is_configurable) {
        router.visit(props.product.url)
        return;
    }
    await cart.addToCart(props.product, props.amount);
    minicart.open();
}
</script>

<template>
    <Button @click="addToCart"
            type="ghost"
            :disabled="cart.addingToCart[product.id] || disabled">
        <div class="flex items-center">
            <IconCartPlus sm v-if="!cart.addingToCart[product.id]"></IconCartPlus>
            <IconLoader sm v-if="cart.addingToCart[product.id]"></IconLoader>
        </div>
    </Button>
</template>