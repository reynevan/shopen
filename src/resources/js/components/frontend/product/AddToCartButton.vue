<script setup>


import {useCartStore} from "@shopen/stores/cart.js";
import {ref} from "vue";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import IconCartPlus from "@shopen/components/icons/IconCartPlus.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

const cart = useCartStore();
const minicart = useMiniCartStore();

const props = defineProps({
    productId: {
        type: Number
    }
})


const qty = ref(1);

const addToCart = async () => {
    await cart.addToCart(props.productId, qty.value);
    minicart.open();
}

const updateQty = (newQty) => {
    qty.value = newQty;
}
</script>

<template>
    <div class="flex items-stretch gap-4">
        <AmountInput :value="qty" @onChange="updateQty" :min="1"></AmountInput>
        <Button @click="addToCart"
                type="success"
                :disabled="cart.addingToCart[productId]">
            <div class="flex items-center">
                <IconCartPlus size="xl" v-if="!cart.addingToCart[productId]"></IconCartPlus>
                <IconLoader md v-if="cart.addingToCart[productId]"></IconLoader>
                <span class="ml-4">Dodaj do koszyka</span>
            </div>
        </Button>
    </div>
</template>

<style scoped>

</style>