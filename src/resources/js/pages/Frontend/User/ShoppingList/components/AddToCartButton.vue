<script setup>

import AmountInput from "../../../../../components/frontend/input/AmountInput.vue";
import AddToCartButton from "../../../../../components/frontend/product/thumbnail/AddToCartButton.vue";
import {ref} from "vue";

const props = defineProps({
    product: {
        type: Object,
    }
})

const amount = ref(1);

const onChangeAmount = (value) => {
    amount.value = value;
}
</script>

<template>
    <div class="flex items-center gap-2">
        <AmountInput :value="amount" @onChange="onChangeAmount" :disabled="!product.in_stock"/>
        <AddToCartButton :product-id="product.id" :amount="amount" :disabled="!product.in_stock"/>
    </div>
</template>