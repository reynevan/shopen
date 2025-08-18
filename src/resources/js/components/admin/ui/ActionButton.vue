<script setup>

    const props = defineProps({
        type: {
            type: String,
            default: 'view'
        },
        size: {
            type: String,
            default: 'sm',
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        },
        disabled: {
            type: Boolean,
            default: false
        }
    });
    const emits = defineEmits(['click']);

    const classAttr = {
        'bg-blue-100 hover:bg-blue-200 text-blue-700': props.type === 'view',
        'bg-red-100 hover:bg-red-200 text-red-700': props.type === 'remove',
        'bg-green-100 hover:bg-green-200 text-green-700': props.type === 'accept',
        'bg-orange-100 hover:bg-orange-200 text-orange-700': props.type === 'cancel',
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
                classAttr,
                sizeClasses[size]
            ]"
            class="px-2 py-1 cursor-pointer transition-all shadow hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
        <template v-if="!$slots.default">
            <i v-if="type === 'view'" class="bi bi-eye"></i>
            <i v-if="type === 'accept'" class="bi bi-check2"></i>
            <i v-if="type === 'cancel'" class="bi bi-x-lg"></i>
            <i v-if="type === 'remove'" class="bi bi-trash"></i>
            <i v-if="type === 'next'" class="bi-chevron-right"></i>
            <i v-if="type === 'prev'" class="bi-chevron-left"></i>
        </template>
        <slot/>
    </button>
</template>

<style scoped>

</style>