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
    },
    max: {
        type: Number
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    placeholder: String
})

const emits = defineEmits(['input'])


const onInput = (event) => {
    if (props.type !== 'number') {
        emits('input', event.target.value);
        return;
    }

    let val = parseFloat(event.target.value);

    if (isNaN(val)) {
        emits('input', event.target.value);
        return;
    }

    if (typeof props.max === 'number' && val > props.max) {
        model.value = props.max;
        emits('input', props.max);
        return;
    }

    if (typeof props.min === 'number' && val < props.min) {
        model.value = props.min;
        emits('input', props.min);
        return;
    }

    emits('input', val);
}

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
        @input="onInput"
        :type="type"
        :id="props.id"
        v-model="model"
        :required="!!props.required"
        :disabled="disabled"
        :autofocus="autofocus"
        :min="min"
        :max="max"
        :placeholder="placeholder"
        class="input"
        :class="[sizeClasses, props.class, error ? 'border-red-400' : '']"
    />
</template>