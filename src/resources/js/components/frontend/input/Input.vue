<script setup>

import {ref} from "vue";

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
    disabled: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
    },
    min: {
        type: Number
    },
    autocomplete: {
        type: String
    }
})

const el = ref(null);
defineExpose({
    focus: () => el.value?.focus(),
});
</script>

<template>
    <input
        ref="el"
        :type="type"
        :id="props.id"
        v-model="model"
        :required="!!props.required"
        :disabled="disabled"
        :min="min"
        class="input"
        :autocomplete="autocomplete"
        :autofocus="autofocus"
        :class="[props.class, error ? 'border-red-400' : '']"
    />
</template>