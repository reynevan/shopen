<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'neutral'
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'solid',
        validator: (value) => ['solid', 'outline'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    submit: {
        type: Boolean,
        default: false
    }
});

const emits = defineEmits(['click']);

const baseClasses = 'font-medium rounded-lg shadow hover:shadow-lg cursor-pointer transition-all duration-200 inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
};

const spinnerSizeClasses = {
    sm: 'w-4 h-4',
    md: 'w-5 h-5',
    lg: 'w-6 h-6'
};

const solidTypeClasses = {
    info: 'bg-blue-100 hover:bg-blue-200 text-blue-700',
    danger: 'bg-red-100 hover:bg-red-200 text-red-700',
    warning: 'bg-amber-100 hover:bg-amber-200 text-amber-700',
    success: 'bg-green-100 hover:bg-green-200 text-green-700',
    neutral: 'bg-neutral-600 hover:bg-neutral-700 text-white focus:ring-neutral-500',
};

const outlineTypeClasses = {
    info: 'border border-blue-500 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
    danger: 'border border-red-500 text-red-600 hover:bg-red-50 focus:ring-red-500',
    warning: 'border border-amber-500 text-amber-600 hover:bg-amber-50 focus:ring-amber-500',
    success: 'border border-green-500 text-green-600 hover:bg-green-50 focus:ring-green-500',
    neutral: 'border border-neutral-400 text-neutral-700 hover:bg-neutral-50 focus:ring-neutral-500',
};

const typeClasses = props.variant === 'outline' ? outlineTypeClasses : solidTypeClasses;
</script>

<template>
    <button
        :type="submit ? 'submit' : 'button'"
        @click="emits('click')"
        :disabled="disabled || loading"
        :class="[
            baseClasses,
            sizeClasses[size],
            typeClasses[type]
        ]">
        <svg
            v-if="loading"
            :class="['animate-spin', spinnerSizeClasses[size]]"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24">
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4">
            </circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <slot v-else />
    </button>
</template>