<script setup>
import { computed } from 'vue';

const props = defineProps({
    price: {
        type: String,
        required: true
    }
});

const splitPrice = computed(() => {
    const priceString = props.price.toString();
    const parts = priceString.split(',');

    if (parts.length === 2) {
        const [whole, decimalWithCurrency] = parts;
        const decimalMatch = decimalWithCurrency.match(/(\d+)(.*)/) || ['', '', ''];

        return {
            whole: whole,
            decimal: decimalMatch[1],
            currency: decimalMatch[2]?.trim() || ''
        };
    }

    return { whole: priceString, decimal: '', currency: '' };
});
</script>

<template>
  <span>
    <span class="text-xl font-semibold">{{ splitPrice.whole }}</span><span v-if="splitPrice.decimal">,<span class="text-sm">{{ splitPrice.decimal }}</span></span>
    <span class="text-sm">{{ splitPrice.currency }}</span>
  </span>
</template>