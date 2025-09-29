<script setup>

    import IconLoader from "../../icons/IconLoader.vue";

    const props = defineProps({
        type: {
            type: String,
            default: 'view',
            validator: (value) => ['view', 'edit', 'accept', 'cancel', 'remove', 'next', 'prev', 'up', 'down'].includes(value)
        },
        size: {
            type: String,
            default: 'sm',
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        },
        disabled: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        }
    });
    const emits = defineEmits(['click']);

    const classAttr = {
        'bg-transparent hover:bg-gray-200 text-gray-800': ['search', 'edit'].indexOf(props.type) >= 0,
        'bg-transparent text-gray-700 hover:bg-blue-200 hover:text-blue-800': props.type === 'view',
        'bg-transparent text-gray-700 hover:bg-red-200 hover:text-red-800': props.type === 'remove',
        'bg-transparent text-gray-700 hover:bg-green-200 hover:text-green-800': props.type === 'accept',
        'bg-transparent text-gray-700 hover:bg-orange-200 hover:text-orange-800': props.type === 'cancel',
    }

    const iClass = {
        'bi bi-search': props.type === 'search',
        'bi bi-eye': props.type === 'view',
        'bi-pencil-square': props.type === 'edit',
        'bi bi-check2': props.type === 'accept',
        'bi bi-x-lg': props.type === 'cancel',
        'bi bi-trash': props.type === 'remove',
        'bi bi-chevron-right': props.type === 'next',
        'bi bi-chevron-left': props.type === 'prev',
        'bi bi-chevron-up': props.type === 'up',
        'bi bi-chevron-down': props.type === 'down',
        'bi bi-plus-lg': props.type === 'add',
    }

    const sizeClasses = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-base',
        lg: 'px-6 py-3 text-lg'
    };

</script>

<template>
    <button @click="emits('click')"
            :disabled="disabled"
            :class="[
                disabled ? 'text-gray-500' : classAttr,
                disabled ? '' : 'hover:shadow-lg',
                sizeClasses[size]
            ]"
            class="flex items-center justify-center gap-2 cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed">
        <IconLoader
            v-if="loading"
            :class="[
        size === 'sm' && 'w-4 h-4',
        size === 'md' && 'w-5 h-5',
        size === 'lg' && 'w-5 h-5',
        size === 'xl' && 'w-6 h-6'
      ]"
        />
        <i v-if="!loading" :class="iClass"></i>
        <slot v-if="!loading"/>
    </button>
</template>

<style scoped>

</style>