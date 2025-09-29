<script setup>
import { computed } from 'vue';

const props = defineProps({
    price: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
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

const sizeClasses = computed(() => {
    const sizes = {
        sm: {
            whole: 'text-sm font-semibold',
            decimal: 'text-xs',
            currency: 'text-xs'
        },
        md: {
            whole: 'text-xl font-semibold',
            decimal: 'text-sm',
            currency: 'text-sm'
        },
        lg: {
            whole: 'text-3xl font-semibold',
            decimal: 'text-lg',
            currency: 'text-lg'
        }
    };

    return sizes[props.size] || sizes.md;
});
</script>

<template>
  <span>
    <span :class="sizeClasses.whole">{{ splitPrice.whole }}</span
    ><span v-if="splitPrice.decimal">,<span :class="sizeClasses.decimal">{{ splitPrice.decimal }}</span></span>
    <span :class="sizeClasses.currency">{{ splitPrice.currency }}</span>
  </span>
</template>