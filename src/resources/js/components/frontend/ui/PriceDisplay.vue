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
    },
    old: {
        type: Boolean,
        default: false
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
            whole: 'text-sm',
            decimal: 'text-sm',
            currency: 'text-sm'
        },
        md: {
            whole: 'text-xl',
            decimal: 'text-sm',
            currency: 'text-sm'
        },
        lg: {
            whole: 'text-3xl',
            decimal: 'text-lg',
            currency: 'text-lg'
        }
    };

    return sizes[props.size] || sizes.md;
});
</script>

<template>
  <span :class="old ? 'price-old' : 'price'">
    <span :class="sizeClasses.whole" class="price-whole">{{ splitPrice.whole }}</span><span v-if="splitPrice.decimal && splitPrice.decimal !== '00'">,<span :class="sizeClasses.decimal" class="price-decimal">{{ splitPrice.decimal }}</span></span>
    <span :class="sizeClasses.currency" class="price-currency">{{ splitPrice.currency }}</span>
  </span>
</template>