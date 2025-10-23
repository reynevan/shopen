<script setup>
import { computed } from 'vue'

const model = defineModel()
const props = defineProps({
    required: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String
    },
    type: {
        type: String,
        default: 'text'
    },
    class: {
        type: String
    },
    size: {
        type: String,
        default: 'md',
        validator: v => ['sm', 'md', 'lg'].includes(v),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
    },
    min: {
        type: Number
    }
})

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'py-1.5 text-xs'
        case 'lg':
            return 'py-3 sm:py-4 text-base'
        default: // 'md'
            return 'py-2.5 sm:py-3 text-sm'
    }
})
</script>

<template>
    <input
        :type="type"
        :id="props.id"
        v-model="model"
        :required="!!props.required"
        :disabled="disabled"
        :min="min"
        class="input"
        :class="[baseClasses, sizeClasses, props.class, error ? 'border-red-400' : '']"
    />
</template>