<script setup>
import IconMinus from "@shopen/components/icons/IconMinus.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";
import { computed } from 'vue';

const emit = defineEmits(['onChange'])

const props = defineProps({
    value: {
        required: true
    },
    min: {
        default: 0
    },
    max: {
        type: Number
    },
    disabled: {
        type: Boolean,
        default: false
    },
    // NOWE: rozmiar kontrolki
    size: {
        type: String,
        default: 'sm',
        validator: (v) => ['sm', 'md', 'lg'].includes(v)
    }
});

// mapowanie rozmiarów na klasy tailwindowe
const sizeClasses = {
    sm: {
        btn: 'text-base',
        input: 'w-10 h-6 text-sm'
    },
    md: {
        btn: 'text-xl',
        input: 'w-14 h-8 text-base'
    },
    lg: {
        btn: 'text-2xl',
        input: 'w-20 h-10 text-lg'
    }
};

const cls = computed(() => sizeClasses[props.size] ?? sizeClasses.sm);

const inc = () => {
    if (props.value < props.max) {
        emit('onChange', Number(props.value) + 1);
    }
}
const dec = () => {
    if (props.value > props.min) {
        emit('onChange', Number(props.value) - 1);
    }
}
const onInput = (event) => {
    let newValue = parseInt(event.target.value, 10);
    if (isNaN(newValue) || newValue < props.min) {
        newValue = props.min;
    }
    if (newValue && newValue > props.max) {
        newValue = props.max;
    }
    emit('onChange', newValue);
}
</script>

<template>
    <div class="amount-input">
        <button
            role="button"
            aria-label="Zwiększ ilość"
            @click="dec"
            :disabled="disabled || value <= min"
            :class="[
                disabled || value <= min ? 'disabled' : 'enabled',
                cls.btn
            ]">
            <IconMinus/>
        </button>

        <input
            type="number"
            aria-label="Ilość"
            :value="value"
            :min="min"
            :max="max"
            @input="onInput"
            :class="[
                'text-center py-0 px-2 border border-light shadow-none rounded-none font-light',
                cls.input
            ]"/>

        <button
            role="button"
            aria-label="Zmniejsz ilość"
            @click="inc"
            :disabled="disabled || value >= max"
            :class="[
                disabled || value >= max ? 'disabled' : 'enabled',
                cls.btn
            ]">
            <IconPlus/>
        </button>
    </div>
</template>