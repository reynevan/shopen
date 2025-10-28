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
        btn: 'w-6 h-6 text-sm',
        input: 'w-10 h-6 text-sm'
    },
    md: {
        btn: 'w-8 h-8 text-base',
        input: 'w-14 h-8 text-base'
    },
    lg: {
        btn: 'w-10 h-10 text-lg',
        input: 'w-20 h-10 text-lg'
    }
};

const cls = computed(() => sizeClasses[props.size] ?? sizeClasses.sm);

const inc = () => {
    emit('onChange', Number(props.value) + 1);
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
    emit('onChange', newValue);
}
</script>

<template>
    <div class="amount-input flex overflow-hidden">
        <button
            @click="dec"
            :disabled="disabled"
            :class="[
                disabled ? 'cursor-not-allowed opacity-60' : 'hover:bg-accent hover:text-primary-text',
                'flex justify-center items-center border-none cursor-pointer transition-colors duration-300',
                cls.btn
            ]">
            <IconMinus />
        </button>

        <input
            type="number"
            :value="value"
            :min="min"
            @input="onInput"
            :class="[
                'text-center py-0 px-2 border border-light shadow-none rounded-none font-light',
                cls.input
            ]"/>

        <button
            @click="inc"
            :disabled="disabled"
            :class="[
                disabled ? 'cursor-not-allowed opacity-60' : 'hover:bg-accent hover:text-primary-text',
                'flex justify-center items-center border-none cursor-pointer transition-colors duration-300',
                cls.btn
            ]">
            <IconPlus />
        </button>
    </div>
</template>