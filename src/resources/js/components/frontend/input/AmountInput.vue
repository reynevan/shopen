<script setup>

import IconMinus from "@shopen/components/icons/IconMinus.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";

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
    }
});


const inc = () => {
    emit('onChange', props.value + 1);
}
const dec = () => {
    if (props.value > props.min) {
        emit('onChange', props.value - 1);
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
    <div class="flex border border-primary-border/70 rounded overflow-hidden">
        <button @click="dec"
                :disabled="disabled"
                :class="disabled ? 'cursor-not-allowed' : ''"
                class="w-8 flex justify-center items-center  border-none cursor-pointer hover:bg-accent hover:text-primary-text transition-colors duration-300">
            <IconMinus></IconMinus>
        </button>
        <input type="number"
               :value="value"
               :min="min"
               class="w-10 text-center py-0 px-2 border-none focus:border-none hover:border-none shadow-none"
               @input="onInput" >
        <button @click="inc"
                :disabled="disabled"
                :class="disabled ? 'cursor-not-allowed' : ''"
                class="w-8 flex justify-center items-center border-none cursor-pointer hover:bg-accent hover:text-primary-text transition-colors duration-300">
            <IconPlus></IconPlus>
        </button>
    </div>
</template>

<style scoped>

</style>