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
        validator: (value) => ['primary', 'secondary', 'ghost', 'disabled', 'danger', 'success'].includes(value)
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
    shadow: {
        type: Boolean,
        default: true
    }
})

const sizeClasses = {
    sm: 'py-1 px-2 text-sm',
    md: 'py-2 px-4 text-base',
    lg: 'py-3 px-6 text-base',
    xl: 'py-4 px-8 text-lg'
}

const typeClasses = {
    primary: 'bg-accent hover:bg-accent-hover disabled:bg-accent/70',
    secondary: 'bg-secondary text-white hover:text-gray-100 hover:bg-secondary-hover disabled:bg-secondary/50 disabled:text-gray-300',
    ghost: 'bg-transparent text-gray-700 hover:bg-accent active:bg-accent/90 disabled:text-gray-400',
    disabled: 'bg-gray-600 text-gray-200',
    danger: 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 disabled:bg-red-300',
    success: 'bg-green-600 text-white hover:bg-green-700 active:bg-green-800 disabled:bg-green-300'
}
</script>

<template>
    <button
        :class="[
          'inline-flex items-center justify-center duration-300 cursor-pointer transition-all',
          'disabled:cursor-not-allowed disabled:opacity-50',
          !disabled && shadow ? 'hover:shadow-lg' : '',
          sizeClasses[size],
          typeClasses[type],
          fullWidth && 'w-full',
          props.class,
          props.class.indexOf('rounded') >= 0 ? '' : 'rounded'
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
      <slot v-else />
    </button>
</template>