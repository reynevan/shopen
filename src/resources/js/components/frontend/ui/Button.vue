<script setup>
import IconLoader from "@shopen/components/icons/IconLoader.vue";

const props = defineProps({
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    class: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'ghost', 'danger', 'success'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    role: {
        type: String,
        default: 'button'
    },
    fullWidth: {
        type: Boolean,
        default: false
    },
})

const sizeClasses = {
    sm: 'h-8 px-3 text-sm',
    md: 'h-10 px-4 text-base',
    lg: 'h-12 px-6 text-base',
    xl: 'h-14 px-8 text-lg'
}

const typeClasses = {
    primary: 'bg-accent-600 text-white hover:bg-accent-700 active:bg-accent-800 disabled:bg-accent-300',
    secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300 active:bg-gray-400 disabled:bg-gray-100 disabled:text-gray-400',
    ghost: 'bg-transparent text-gray-700 hover:bg-gray-100 active:bg-gray-200 disabled:text-gray-400',
    danger: 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 disabled:bg-red-300',
    success: 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800 disabled:bg-green-300'
}
</script>

<template>
    <button
        :class="[
      'inline-flex items-center justify-center rounded-lg transition-all duration-200 cursor-pointer',
      'focus:outline-none focus:ring-2 focus:ring-offset-2',
      'disabled:cursor-not-allowed disabled:opacity-50',
      sizeClasses[size],
      typeClasses[type],
      fullWidth && 'w-full',
      props.class
    ]"
        :type="role"
        :disabled="disabled || loading"
    >
        <IconLoader
            v-if="loading"
            :class="[
        size === 'sm' && 'w-4 h-4',
        size === 'md' && 'w-5 h-5',
        size === 'lg' && 'w-5 h-5',
        size === 'xl' && 'w-6 h-6'
      ]"
        />
        <span v-else>
      <slot />
    </span>
    </button>
</template>