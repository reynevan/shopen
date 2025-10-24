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
    noPaddingX: {
        type: Boolean,
        default: false
    },
    iconSize: {
        type: String
    }
})

const sizeClasses = {
    sm: 'py-0.5 text-sm',
    md: 'py-1 text-base',
    lg: 'py-3 text-base',
    xl: 'py-4 text-lg'
}
const paddingXClasses = {
    sm: props.noPaddingX ? '' : 'px-2',
    md: props.noPaddingX ? '' : 'px-4',
    lg: props.noPaddingX ? '' : 'px-6',
    xl: props.noPaddingX ? '' : 'px-8'
}
</script>

<template>
    <button
        class="button"
        :class="[
          'disabled:cursor-not-allowed disabled:opacity-50',
          type,
          paddingXClasses[size],
          sizeClasses[size],
          fullWidth && 'w-full',
          props.class
        ]"
        :type="role"
        :disabled="disabled || loading"
    >
        <IconLoader
            v-if="loading"
            :size="iconSize ?? 'md'"
        />
        <slot v-else/>
    </button>
</template>