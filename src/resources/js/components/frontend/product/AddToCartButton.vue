<script setup>
import {useCartStore} from "@shopen/stores/cart.js";
import {ref} from "vue";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import IconCartPlus from "@shopen/components/icons/IconCartPlus.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {usePage} from "@inertiajs/vue3";

const cart = useCartStore();
const minicart = useMiniCartStore();

const props = defineProps({
    product: {
        type: Object
    }
})

const page = usePage();

const maxQty = page.props.config?.max_cart_products ?? 10;

const emits = defineEmits(["onAddConfigurable"]);

const qty = ref(1);

const addToCart = async () => {
    if (props.product.is_configurable) {
        emits('onAddConfigurable')
        return
    }
    await cart.addToCart(props.product, qty.value);
    minicart.open();
}

const updateQty = (newQty) => {
    qty.value = newQty;
}
</script>

<template>
    <div class="flex items-center gap-4 flex-col sm:flex-row">
        <AmountInput :value="qty" @onChange="updateQty" :min="1" size="lg" :max="maxQty"/>
        <Button @click="addToCart"
                type="primary"
                size="lg"
                full-width
                :disabled="cart.addingToCart[product.id]">
            <div class="flex items-center">
                <IconCartPlus size="xl" v-if="!cart.addingToCart[product.id]"></IconCartPlus>
                <IconLoader md v-if="cart.addingToCart[product.id]"></IconLoader>
                <span class="ml-4">Dodaj do koszyka</span>
            </div>
        </Button>
    </div>
</template>